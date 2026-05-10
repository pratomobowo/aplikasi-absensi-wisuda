# Security Runtime Bugfix Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Harden the wisuda scanner and auth runtime by securing QR tokens, validating event ownership, preventing role abuse, reducing sensitive token exposure, and fixing access/rate-limit bugs.

**Architecture:** Keep the current Laravel service-oriented flow. Add authenticated QR token v2 support in `QRCodeService` while temporarily preserving legacy plain JSON decode. Apply minimal validation and middleware changes around existing scanner, attendance, konsumsi, and route code.

**Tech Stack:** Laravel 12, Livewire 3, Filament 3, Eloquent, Laravel `Crypt`, Laravel `RateLimiter`, PHPUnit/Pest-compatible Laravel tests.

---

## Pre-Flight

### Task 0: Confirm Working Tree And Baseline

**Files:**
- Read only: working tree

**Step 1: Check current changes**

Run: `git status --short`

Expected: Note existing user changes. Do not revert unrelated changes.

**Step 2: Confirm app boots**

Run: `php artisan about`

Expected: Laravel environment output.

**Step 3: Confirm frontend build baseline**

Run: `npm run build`

Expected: Vite build completes.

**Step 4: Check test runner behavior**

Run: `php artisan test`

Expected: If output remains `clean — nothing to commit`, record it as abnormal and rely on targeted direct test execution where possible. Do not claim full suite passes.

---

## QR Token Security

### Task 1: Add QR v2 Encryption With Legacy Decode

**Files:**
- Modify: `app/Services/QRCodeService.php`
- Test: `tests/Feature/QRCodeServiceSecurityTest.php`

**Step 1: Write failing tests**

Create `tests/Feature/QRCodeServiceSecurityTest.php` with tests for:

- `encryptQRData()` returns a non-JSON token for new payloads.
- `decryptQRData()` decodes v2 token and returns original fields plus `version` metadata.
- `decryptQRData()` still decodes legacy JSON.
- Tampered tokens return `null`.

Use this structure:

```php
<?php

namespace Tests\Feature;

use App\Services\QRCodeService;
use Illuminate\Support\Str;
use Tests\TestCase;

class QRCodeServiceSecurityTest extends TestCase
{
    public function test_encrypt_qr_data_generates_non_json_v2_token(): void
    {
        $service = app(QRCodeService::class);

        $token = $service->encryptQRData([
            'ticket_id' => 123,
            'role' => 'mahasiswa',
            'event_id' => 456,
        ]);

        $this->assertIsString($token);
        $this->assertNull(json_decode($token, true));
        $this->assertNotSame(JSON_ERROR_NONE, json_last_error());
    }

    public function test_decrypt_qr_data_decodes_v2_token(): void
    {
        $service = app(QRCodeService::class);

        $token = $service->encryptQRData([
            'ticket_id' => 123,
            'role' => 'mahasiswa',
            'event_id' => 456,
        ]);

        $data = $service->decryptQRData($token);

        $this->assertSame(123, $data['ticket_id']);
        $this->assertSame('mahasiswa', $data['role']);
        $this->assertSame(456, $data['event_id']);
        $this->assertSame(2, $data['version']);
        $this->assertFalse($data['_legacy']);
    }

    public function test_decrypt_qr_data_accepts_legacy_json_temporarily(): void
    {
        $service = app(QRCodeService::class);

        $data = $service->decryptQRData(json_encode([
            'ticket_id' => 123,
            'role' => 'mahasiswa',
            'event_id' => 456,
        ]));

        $this->assertSame(123, $data['ticket_id']);
        $this->assertSame('mahasiswa', $data['role']);
        $this->assertSame(456, $data['event_id']);
        $this->assertTrue($data['_legacy']);
    }

    public function test_decrypt_qr_data_rejects_tampered_token(): void
    {
        $service = app(QRCodeService::class);
        $token = $service->encryptQRData([
            'ticket_id' => 123,
            'role' => 'mahasiswa',
            'event_id' => 456,
        ]);

        $tampered = Str::replaceLast(substr($token, -1), substr($token, -1) === 'a' ? 'b' : 'a', $token);

        $this->assertNull($service->decryptQRData($tampered));
    }
}
```

**Step 2: Run test to verify it fails**

Run: `php artisan test --filter=QRCodeServiceSecurityTest`

Expected: At least the non-JSON and v2 metadata tests fail before implementation. If the project test runner is abnormal, try `./vendor/bin/phpunit --filter QRCodeServiceSecurityTest` and record actual output.

**Step 3: Implement minimal QR v2**

Modify `app/Services/QRCodeService.php`:

- Add `use Illuminate\Support\Facades\Crypt;`
- Add `use Illuminate\Contracts\Encryption\DecryptException;`
- In `encryptQRData()`, add `version => 2`, `timestamp`, then return `Crypt::encryptString(json_encode($data, JSON_THROW_ON_ERROR))`.
- In `decryptQRData()`, first try `Crypt::decryptString($token)` then JSON decode.
- If decrypt fails, fallback to legacy `json_decode($token, true)`.
- Add `_legacy => false` for v2 and `_legacy => true` for legacy.
- Return `null` for invalid payloads.

**Step 4: Run test to verify it passes**

Run: `php artisan test --filter=QRCodeServiceSecurityTest`

Expected: QR service tests pass or test runner issue is documented.

**Step 5: Commit**

Only commit if the user explicitly asks for commits. Otherwise skip commit and continue.

---

### Task 2: Ensure Ticket Generation And QR Regeneration Use V2 Safely

**Files:**
- Modify: `app/Services/TicketService.php`
- Modify: `app/Console/Commands/RegenerateQRTokens.php`
- Test: `tests/Feature/TicketQrGenerationTest.php`

**Step 1: Write failing tests**

Create `tests/Feature/TicketQrGenerationTest.php` to verify `TicketService::generateQRTokens()` returns v2-decodable tokens for all roles. Keep fixtures aligned with current schema: `location_name`, `location_address`, no `fakultas`.

**Step 2: Run test to verify current behavior**

Run: `php artisan test --filter=TicketQrGenerationTest`

Expected: With Task 1 implemented, it may already pass. If it passes, keep it as regression coverage.

**Step 3: Minimal code fixes**

Modify `app/Services/TicketService.php`:

- Change `$event->date->addDays(1)` to `$event->date->copy()->addDay()` to avoid mutating the event model date.

Modify `app/Console/Commands/RegenerateQRTokens.php`:

- Keep existing command flow.
- Ensure verification checks `version === 2`, matching `ticket_id`, matching `role`, matching `event_id`.
- Update messaging from “proper encryption” only if needed to accurately say v2 encrypted QR tokens.

**Step 4: Run targeted test and command dry check**

Run: `php artisan test --filter=TicketQrGenerationTest`

Run: `php artisan tickets:regenerate-qr --ticket=0`

Expected: Test passes. Command reports ticket not found and exits without fatal error.

---

## Scanner And Service Validation

### Task 3: Stop Mutating QR Payload In Scanner

**Files:**
- Modify: `app/Livewire/Scanner.php:119-130`
- Modify: `app/Livewire/Scanner.php:303-314`
- Test: `tests/Feature/ScannerQrPayloadTest.php`

**Step 1: Write failing test**

Create a Livewire test that mocks `AttendanceService` and asserts the exact QR string is passed to `recordAttendance()`. Include a string containing `<tag>` so current `strip_tags()` behavior would fail.

**Step 2: Run test to verify it fails**

Run: `php artisan test --filter=ScannerQrPayloadTest`

Expected: Fails because scanner mutates payload.

**Step 3: Implement minimal code**

Remove the `strip_tags()` mutation blocks from both attendance and konsumsi scan paths. Keep length/empty validation. Do not log raw QR contents.

**Step 4: Run test to verify it passes**

Run: `php artisan test --filter=ScannerQrPayloadTest`

Expected: Exact payload reaches the service.

---

### Task 4: Harden Attendance Event Validation And Duplicate Race Handling

**Files:**
- Modify: `app/Services/AttendanceService.php`
- Test: `tests/Feature/AttendanceServiceSecurityTest.php`

**Step 1: Write failing tests**

Create tests for:

- Mismatched QR `event_id` returns `invalid_event`.
- Inactive ticket event returns `event_not_active`.
- Successful scan response includes `ticket_id` and `event_id`.
- Duplicate DB unique violation returns duplicate reason, not transaction failure.

Use current schema fields. Generate QR through `QRCodeService`.

**Step 2: Run tests to verify failures**

Run: `php artisan test --filter=AttendanceServiceSecurityTest`

Expected: Event validation and success data tests fail before implementation.

**Step 3: Implement minimal validation**

Modify `validateQRCode()` after ticket lookup:

- Cast payload `ticket_id` and `event_id` to integers for comparison.
- If `(int) $data['event_id'] !== (int) $ticket->graduation_event_id`, return `ERROR_INVALID_EVENT`.
- If missing `$ticket->graduationEvent` or inactive, return `ERROR_EVENT_NOT_ACTIVE`.

Modify success response in `recordAttendance()`:

- Add `ticket_id => $ticketId`.
- Add `event_id => $ticket->graduation_event_id`.

Modify `QueryException` catch:

- Detect duplicate key for unique attendance constraint. For MySQL, check `$e->errorInfo[1] ?? null` equals `1062`; for SQLite, check SQLSTATE/code/message contains `UNIQUE constraint failed`.
- If duplicate, log with reason `ERROR_DUPLICATE` and return `buildErrorResponse(ERROR_DUPLICATE, $duration)`.
- Otherwise preserve existing transaction failed response.

**Step 4: Run targeted tests**

Run: `php artisan test --filter=AttendanceServiceSecurityTest`

Expected: Tests pass or runner abnormality is documented.

---

### Task 5: Harden Konsumsi Role/Event Validation And Duplicate Race Handling

**Files:**
- Modify: `app/Services/KonsumsiService.php`
- Test: `tests/Feature/KonsumsiServiceSecurityTest.php`

**Step 1: Write failing tests**

Create tests for:

- Pendamping QR is rejected with a clear invalid role/missing fields reason.
- Mismatched event QR is rejected.
- Inactive event is rejected.
- Duplicate database race returns `ERROR_KONSUMSI_DUPLICATE`, not `ERROR_SYSTEM`.

**Step 2: Run tests to verify failures**

Run: `php artisan test --filter=KonsumsiServiceSecurityTest`

Expected: Pendamping QR test fails before implementation.

**Step 3: Implement minimal code**

Modify `KonsumsiService`:

- Add error code/message for invalid role, or reuse missing fields if you want fewer names. Prefer `ERROR_INVALID_ROLE` with message `QR Code konsumsi harus milik mahasiswa`.
- Require `ticket_id`, `event_id`, and `role` in decrypted payload.
- Reject any role other than `mahasiswa`.
- Cast and compare event IDs as integers.
- In `recordKonsumsi()`, catch `QueryException` duplicate key separately and return duplicate message.
- Inside transaction, re-fetch the ticket with `lockForUpdate()` and re-check `konsumsi_diterima` before creating the record if the change stays small.

**Step 4: Run targeted tests**

Run: `php artisan test --filter=KonsumsiServiceSecurityTest`

Expected: Tests pass or runner abnormality is documented.

---

## Auth, Routes, And Logging

### Task 6: Protect Custom Admin Buku Wisuda Routes

**Files:**
- Create: `app/Http/Middleware/EnsureAdminUser.php`
- Modify: `bootstrap/app.php:22-26`
- Modify: `routes/web.php:38-45`
- Test: `tests/Feature/AdminRouteAccessTest.php`

**Step 1: Write failing tests**

Create route tests:

- Scanner user cannot access `/admin/buku-wisuda/pdf/{slug}` and is redirected or receives 403.
- Admin user can access the route far enough to avoid auth rejection. Use an existing/factory `BukuWisuda` if available; otherwise assert middleware behavior on viewer route.

**Step 2: Run test to verify failure**

Run: `php artisan test --filter=AdminRouteAccessTest`

Expected: Scanner currently passes `auth`, so test fails.

**Step 3: Implement middleware**

Create `EnsureAdminUser`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || $request->user()->role !== 'admin') {
            abort(403);
        }

        return $next($request);
    }
}
```

Register alias in `bootstrap/app.php`:

```php
'admin.only' => \App\Http\Middleware\EnsureAdminUser::class,
```

Update route group:

```php
Route::middleware(['auth', 'admin.only'])->group(function () {
    ...
});
```

**Step 4: Run test**

Run: `php artisan test --filter=AdminRouteAccessTest`

Expected: Scanner receives 403; admin does not.

---

### Task 7: Fix Student Redirect And Add Login Throttling

**Files:**
- Modify: `bootstrap/app.php:19-20`
- Modify: `app/Providers/AppServiceProvider.php:61-70`
- Modify: `routes/web.php:47-52`
- Modify: `app/Livewire/StudentLogin.php`
- Test: `tests/Feature/StudentAuthRuntimeTest.php`

**Step 1: Write failing tests**

Create tests for:

- Guest accessing `/student/dashboard` redirects to `/student/login`.
- Repeated failed Livewire login attempts are throttled.

**Step 2: Run tests to verify failures**

Run: `php artisan test --filter=StudentAuthRuntimeTest`

Expected: Redirect test fails because current redirect target is `/admin/login`; throttle test fails because no limiter exists.

**Step 3: Implement redirect fix**

Modify `bootstrap/app.php`:

```php
$middleware->redirectGuestsTo(function ($request) {
    return $request->is('student') || $request->is('student/*')
        ? route('student.login')
        : '/admin/login';
});
```

**Step 4: Add route limiter**

Modify `AppServiceProvider::configureRateLimiting()`:

```php
RateLimiter::for('student-login', function (Request $request) {
    return Limit::perMinute(5)->by($request->ip());
});
```

Update route:

```php
Route::get('/login', App\Livewire\StudentLogin::class)
    ->middleware('throttle:student-login')
    ->name('login');
```

**Step 5: Add Livewire action limiter**

In `StudentLogin::login()`:

- Import `Illuminate\Support\Facades\RateLimiter` and `Illuminate\Validation\ValidationException` if needed.
- Use key `student-login:'.strtolower($this->npm).'|'.request()->ip()`.
- Allow 5 attempts per minute.
- On too many attempts, add a validation error with available seconds.
- Clear attempts on successful login.

**Step 6: Run targeted tests**

Run: `php artisan test --filter=StudentAuthRuntimeTest`

Expected: Tests pass or runner abnormality is documented.

---

### Task 8: Remove Sensitive Token Logging

**Files:**
- Modify: `app/Http/Controllers/InvitationController.php:46-51`
- Modify: `app/Services/AttendanceService.php:280-284`
- Search: `Log::` call sites that include raw `token`, `qrData`, or raw QR preview
- Test: `tests/Feature/SensitiveLoggingTest.php` if feasible

**Step 1: Write failing test if feasible**

Use `Log::spy()` around `InvitationController::show()` to assert full token is not logged. If route-level test is too brittle, skip automated test and document manual grep verification.

**Step 2: Implement minimal logging cleanup**

In `InvitationController::show()`:

- Replace `'token' => $token` with a fingerprint such as:

```php
'token_hash' => hash('sha256', $token),
'token_prefix' => substr($token, 0, 8),
```

In `AttendanceService::validateQRCode()`:

- Remove `data_preview` logging entirely, or replace it with `qr_length` and `token_hash`.

Search for raw QR/token logging:

Run: `rg "token|qrData|data_preview|Raw QR" app routes config database tests`

Expected: No full token/QR payload logs remain in scanner/invitation paths.

**Step 3: Run targeted verification**

Run: `php artisan about`

Run: `php artisan test --filter=SensitiveLoggingTest` if test was added.

Expected: App boots; logging test passes if present.

---

## Final Verification

### Task 9: Run Verification Commands And Document Gaps

**Files:**
- No code changes unless fixing issues found in this task.

**Step 1: Run app boot check**

Run: `php artisan about`

Expected: Laravel environment output.

**Step 2: Run frontend build**

Run: `npm run build`

Expected: Vite build completes.

**Step 3: Run targeted tests**

Run the filters added in this plan:

```bash
php artisan test --filter=QRCodeServiceSecurityTest
php artisan test --filter=TicketQrGenerationTest
php artisan test --filter=ScannerQrPayloadTest
php artisan test --filter=AttendanceServiceSecurityTest
php artisan test --filter=KonsumsiServiceSecurityTest
php artisan test --filter=AdminRouteAccessTest
php artisan test --filter=StudentAuthRuntimeTest
```

Expected: All targeted tests pass, unless project test runner remains abnormal. If abnormal, run direct PHPUnit filters and document exact output.

**Step 4: Run full test command**

Run: `php artisan test`

Expected: Normal Laravel test output. If it still prints `clean — nothing to commit`, report it as an unresolved test-runner issue and do not claim full suite passes.

**Step 5: Run style check**

Run: `./vendor/bin/pint --test`

Expected: This may fail because baseline already has 67 style issues. Report whether this batch introduces new style issues if you can isolate them; do not run global auto-fix unless explicitly requested.

**Step 6: Summarize**

Report:

- Files changed.
- Security/runtime bugs fixed.
- Verification command outputs.
- Any remaining known issues, especially schema/test drift and abnormal full test runner.
