# Security Runtime Bugfix Design

Date: 2026-05-05

## Goal

Reduce the highest operational and security risks in the wisuda app without mixing in broad schema cleanup. This batch focuses on QR token security, scanner validation, admin route access, student login safety, duplicate-scan race handling, and sensitive token logging.

## Scope

Included:

- Secure QR token generation and decoding.
- Backward-compatible QR decode for existing plain JSON QR tokens during migration.
- QR regeneration path for existing tickets.
- Attendance validation hardening.
- Konsumsi validation hardening.
- Duplicate attendance and konsumsi race handling.
- Admin-only access for custom admin buku wisuda routes.
- Student login redirect/rate-limit fixes.
- Sensitive token logging cleanup.

Excluded from this batch:

- Full schema drift cleanup for `fakultas`, obsolete `qr_code`, and test fixture fields.
- Broad Pint/style cleanup across the repository.
- Redesigning scanner UI.
- Reworking the PDF storage model unless needed to avoid token exposure in this batch.

## Approach

Use incremental compatibility. New tickets and regenerated tickets will use QR token v2. The decoder will still accept existing plain JSON tokens temporarily so current distributed QR codes do not break immediately. This gives us a safe migration path while closing the issue for newly generated/regenerated tickets.

## QR Token Design

QR token v2 will use Laravel authenticated encryption through `Crypt::encryptString()` and `Crypt::decryptString()`. The encrypted payload will contain:

- `version`: `2`
- `ticket_id`
- `role`
- `event_id`
- `timestamp`

`QRCodeService` will decode tokens in this order:

1. Try decrypting v2 token.
2. If decrypt fails, try legacy JSON decode.
3. Mark decoded payload as legacy or v2 for logging/migration awareness.

No scanner path should mutate the QR token before decoding. Sanitization belongs only at display/log boundaries.

## Attendance Validation

Attendance service will validate:

- Required payload fields exist.
- `role` is one of `mahasiswa`, `pendamping1`, `pendamping2`.
- Ticket exists.
- QR `event_id` matches `ticket.graduation_event_id`.
- Ticket event exists and is active.
- Ticket is not expired.
- Pendamping scan only succeeds after mahasiswa attendance.
- Duplicate attendance returns a domain duplicate error, including when the database unique constraint catches a race.

Success response should include `ticket_id` and `event_id` so scanner logs are useful.

## Konsumsi Validation

Konsumsi service will validate:

- Required payload fields exist.
- `role` exists and must be `mahasiswa`.
- Ticket exists.
- QR `event_id` matches `ticket.graduation_event_id`.
- Ticket event exists and is active.
- Ticket is not expired.
- Duplicate konsumsi returns a domain duplicate error, including race cases.

This keeps the current one-consumption-per-ticket model but prevents pendamping QR tokens from claiming the student's konsumsi.

## Duplicate Race Handling

Attendance and konsumsi both already have database uniqueness guarantees. This batch will treat those guarantees as the final source of truth:

- Keep pre-checks for user-friendly fast feedback.
- Catch duplicate-key `QueryException` during writes.
- Return the existing duplicate domain error instead of a generic system error.
- For konsumsi, prefer re-checking state inside the transaction or row locking if the existing flow can absorb it with minimal change.

## Admin And Student Auth

Custom admin buku wisuda routes under `/admin/buku-wisuda/...` will require explicit admin access, not just `auth`.

Student routes should redirect unauthenticated mahasiswa users to `/student/login`. Admin/Filament routes should continue redirecting guests to `/admin/login`.

Student login will receive throttling to slow brute force attempts against NPM/password combinations.

## Logging

The app must not log full magic link tokens or raw QR token payloads. Logs should use token fingerprints, short prefixes, ticket IDs after validation, or non-sensitive status metadata.

## Testing And Verification

Targeted verification should include:

- QR v2 round-trip decode.
- Legacy JSON QR decode still works during migration.
- Invalid/tampered QR is rejected.
- Attendance rejects mismatched event IDs.
- Attendance rejects inactive events.
- Konsumsi rejects pendamping QR roles.
- Duplicate attendance/konsumsi race paths return duplicate errors.
- Scanner passes exact QR payload to services.
- Custom admin buku wisuda routes reject scanner users.
- Student protected routes redirect to student login.
- Student login throttles repeated attempts.

General verification:

- `php artisan about`
- `npm run build`
- Targeted Laravel tests where possible
- Full test command if the project test runner is repaired or produces normal output

Known limitation: current `php artisan test` output is abnormal (`clean — nothing to commit`), so test status must not be claimed until that is fixed or targeted tests produce normal PHPUnit output.
