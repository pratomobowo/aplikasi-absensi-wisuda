<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Livewire\Scanner;
use App\Models\User;
use App\Models\GraduationEvent;
use App\Models\Mahasiswa;
use App\Models\GraduationTicket;
use App\Services\AttendanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

class ScannerAutoResetTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected GraduationEvent $event;
    protected Mahasiswa $mahasiswa;
    protected GraduationTicket $ticket;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test user
        $this->user = User::factory()->create([
            'role' => 'scanner',
        ]);

        // Create graduation event
        $this->event = GraduationEvent::create([
            'name' => 'Test Graduation',
            'date' => now()->addDays(1),
            'time' => '10:00:00',
            'location' => 'Test Location',
            'is_active' => true,
        ]);

        // Create mahasiswa
        $this->mahasiswa = Mahasiswa::create([
            'npm' => '123456',
            'nama' => 'Test Student',
            'program_studi' => 'Test Program',
            'fakultas' => 'Test Faculty',
        ]);

        // Create ticket
        $this->ticket = GraduationTicket::create([
            'graduation_event_id' => $this->event->id,
            'mahasiswa_id' => $this->mahasiswa->id,
            'qr_code' => 'test_qr_code',
        ]);
    }

    /** @test */
    public function auto_reset_clears_state_after_success()
    {
        $this->actingAs($this->user);

        $component = Livewire::test(Scanner::class);

        // Simulate successful scan
        $component->set('status', 'success');
        $component->set('scanResult', [
            'ticket_id' => $this->ticket->id,
            'role' => 'mahasiswa',
            'mahasiswa_name' => 'Test Student',
            'npm' => '123456',
        ]);
        $component->set('errorMessage', '');

        // Call doReset
        $component->call('doReset');

        // Verify state is cleared
        $component->assertSet('status', 'ready');
        $component->assertSet('scanResult', null);
        $component->assertSet('errorMessage', '');

        // Verify scanner-ready event was dispatched
        $component->assertDispatched('scanner-ready');
    }

    /** @test */
    public function auto_reset_clears_state_after_error()
    {
        $this->actingAs($this->user);

        $component = Livewire::test(Scanner::class);

        // Simulate error state
        $component->set('status', 'error');
        $component->set('errorMessage', 'Test error message');
        $component->set('scanResult', null);

        // Call doReset
        $component->call('doReset');

        // Verify state is cleared
        $component->assertSet('status', 'ready');
        $component->assertSet('scanResult', null);
        $component->assertSet('errorMessage', '');

        // Verify scanner-ready event was dispatched
        $component->assertDispatched('scanner-ready');
    }

    /** @test */
    public function auto_reset_is_triggered_after_successful_scan()
    {
        $this->actingAs($this->user);

        // Mock AttendanceService to return success
        $this->mock(AttendanceService::class, function ($mock) {
            $mock->shouldReceive('recordAttendance')
                ->once()
                ->andReturn([
                    'success' => true,
                    'message' => 'Success',
                    'data' => [
                        'ticket_id' => $this->ticket->id,
                        'role' => 'mahasiswa',
                        'mahasiswa_name' => 'Test Student',
                        'npm' => '123456',
                        'mahasiswa' => [
                            'nama' => 'Test Student',
                        ],
                    ],
                ]);
        });

        $component = Livewire::test(Scanner::class);

        // Simulate QR scan
        $component->call('scanQRCode', 'valid_qr_data');

        // Verify status changed to success
        $component->assertSet('status', 'success');

        // Verify auto-reset event was dispatched with 3 second delay
        $component->assertDispatched('scanner-auto-reset', delay: 3000);
    }

    /** @test */
    public function auto_reset_is_triggered_after_failed_scan()
    {
        $this->actingAs($this->user);

        // Mock AttendanceService to return error
        $this->mock(AttendanceService::class, function ($mock) {
            $mock->shouldReceive('recordAttendance')
                ->once()
                ->andReturn([
                    'success' => false,
                    'message' => 'QR Code tidak valid',
                    'reason' => 'invalid_qr',
                ]);
        });

        $component = Livewire::test(Scanner::class);

        // Simulate QR scan
        $component->call('scanQRCode', 'invalid_qr_data');

        // Verify status changed to error
        $component->assertSet('status', 'error');
        $component->assertSet('errorMessage', 'QR Code tidak valid');

        // Verify auto-reset event was dispatched with 3 second delay
        $component->assertDispatched('scanner-auto-reset', delay: 3000);
    }

    /** @test */
    public function do_reset_only_accepts_success_or_error_states()
    {
        $this->actingAs($this->user);

        $component = Livewire::test(Scanner::class);

        // Set to ready state (unexpected for auto-reset)
        $component->set('status', 'ready');

        // Call doReset - should still work but log warning
        $component->call('doReset');

        // Should still reset to ready
        $component->assertSet('status', 'ready');
    }

    /** @test */
    public function force_reset_clears_all_state_from_ready()
    {
        $this->actingAs($this->user);

        $component = Livewire::test(Scanner::class);

        // Start in ready state
        $component->assertSet('status', 'ready');

        // Call forceReset
        $component->call('forceReset');

        // Verify state is cleared
        $component->assertSet('status', 'ready');
        $component->assertSet('scanResult', null);
        $component->assertSet('errorMessage', '');

        // Verify events were dispatched
        $component->assertDispatched('scanner-ready');
        $component->assertDispatched('scanner-force-reset-complete');
    }

    /** @test */
    public function force_reset_clears_all_state_from_scanning()
    {
        $this->actingAs($this->user);

        $component = Livewire::test(Scanner::class);

        // Set to scanning state
        $component->set('status', 'scanning');

        // Call forceReset
        $component->call('forceReset');

        // Verify state is cleared and returned to ready
        $component->assertSet('status', 'ready');
        $component->assertSet('scanResult', null);
        $component->assertSet('errorMessage', '');

        // Verify events were dispatched
        $component->assertDispatched('scanner-ready');
        $component->assertDispatched('scanner-force-reset-complete');
    }

    /** @test */
    public function force_reset_clears_all_state_from_success()
    {
        $this->actingAs($this->user);

        $component = Livewire::test(Scanner::class);

        // Set to success state with data
        $component->set('status', 'success');
        $component->set('scanResult', [
            'ticket_id' => $this->ticket->id,
            'role' => 'mahasiswa',
            'mahasiswa_name' => 'Test Student',
            'npm' => '123456',
        ]);

        // Call forceReset
        $component->call('forceReset');

        // Verify all state is cleared
        $component->assertSet('status', 'ready');
        $component->assertSet('scanResult', null);
        $component->assertSet('errorMessage', '');

        // Verify events were dispatched
        $component->assertDispatched('scanner-ready');
        $component->assertDispatched('scanner-force-reset-complete');
    }

    /** @test */
    public function force_reset_clears_all_state_from_error()
    {
        $this->actingAs($this->user);

        $component = Livewire::test(Scanner::class);

        // Set to error state with message
        $component->set('status', 'error');
        $component->set('errorMessage', 'Test error message');

        // Call forceReset
        $component->call('forceReset');

        // Verify all state is cleared
        $component->assertSet('status', 'ready');
        $component->assertSet('scanResult', null);
        $component->assertSet('errorMessage', '');

        // Verify events were dispatched
        $component->assertDispatched('scanner-ready');
        $component->assertDispatched('scanner-force-reset-complete');
    }

    /** @test */
    public function force_reset_dispatches_feedback_event()
    {
        $this->actingAs($this->user);

        $component = Livewire::test(Scanner::class);

        // Set to any state
        $component->set('status', 'error');
        $component->set('errorMessage', 'Some error');

        // Call forceReset
        $component->call('forceReset');

        // Verify scanner-force-reset-complete event was dispatched for UI feedback
        $component->assertDispatched('scanner-force-reset-complete');
    }
}
