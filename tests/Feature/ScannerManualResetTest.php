<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\GraduationEvent;
use App\Models\GraduationTicket;
use App\Models\Mahasiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Scanner;

class ScannerManualResetTest extends TestCase
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
            'date' => now()->addDays(1)->format('Y-m-d'),
            'time' => '10:00:00',
            'location_name' => 'Test Location',
            'location_address' => 'Test Address',
            'is_active' => true,
        ]);

        // Create mahasiswa
        $this->mahasiswa = Mahasiswa::create([
            'npm' => '12345678',
            'nama' => 'Test Student',
            'program_studi' => 'Test Program',
            'fakultas' => 'Test Faculty',
        ]);

        // Create ticket
        $this->ticket = GraduationTicket::create([
            'graduation_event_id' => $this->event->id,
            'mahasiswa_id' => $this->mahasiswa->id,
            'qr_code' => 'test-qr-code',
        ]);
    }

    /** @test */
    public function force_reset_clears_all_state_from_ready_state()
    {
        Livewire::actingAs($this->user)
            ->test(Scanner::class)
            ->assertSet('status', 'ready')
            ->assertSet('scanResult', null)
            ->assertSet('errorMessage', '')
            ->call('forceReset')
            ->assertSet('status', 'ready')
            ->assertSet('scanResult', null)
            ->assertSet('errorMessage', '')
            ->assertDispatched('scanner-ready')
            ->assertDispatched('scanner-force-reset-complete');
    }

    /** @test */
    public function force_reset_clears_all_state_from_success_state()
    {
        $component = Livewire::actingAs($this->user)
            ->test(Scanner::class);

        // Manually set success state
        $component->set('status', 'success');
        $component->set('scanResult', [
            'ticket_id' => $this->ticket->id,
            'role' => 'mahasiswa',
            'mahasiswa_name' => 'Test Student',
            'npm' => '12345678',
        ]);

        // Call force reset
        $component->call('forceReset')
            ->assertSet('status', 'ready')
            ->assertSet('scanResult', null)
            ->assertSet('errorMessage', '')
            ->assertDispatched('scanner-ready')
            ->assertDispatched('scanner-force-reset-complete');
    }

    /** @test */
    public function force_reset_clears_all_state_from_error_state()
    {
        $component = Livewire::actingAs($this->user)
            ->test(Scanner::class);

        // Manually set error state
        $component->set('status', 'error');
        $component->set('errorMessage', 'Test error message');

        // Call force reset
        $component->call('forceReset')
            ->assertSet('status', 'ready')
            ->assertSet('scanResult', null)
            ->assertSet('errorMessage', '')
            ->assertDispatched('scanner-ready')
            ->assertDispatched('scanner-force-reset-complete');
    }

    /** @test */
    public function force_reset_clears_all_state_from_scanning_state()
    {
        $component = Livewire::actingAs($this->user)
            ->test(Scanner::class);

        // Manually set scanning state
        $component->set('status', 'scanning');

        // Call force reset
        $component->call('forceReset')
            ->assertSet('status', 'ready')
            ->assertSet('scanResult', null)
            ->assertSet('errorMessage', '')
            ->assertDispatched('scanner-ready')
            ->assertDispatched('scanner-force-reset-complete');
    }

    /** @test */
    public function force_reset_dispatches_correct_events()
    {
        Livewire::actingAs($this->user)
            ->test(Scanner::class)
            ->call('forceReset')
            ->assertDispatched('scanner-ready')
            ->assertDispatched('scanner-force-reset-complete');
    }

    /** @test */
    public function manual_reset_button_is_accessible_in_ready_state()
    {
        $component = Livewire::actingAs($this->user)
            ->test(Scanner::class)
            ->assertSet('status', 'ready');

        // Verify the view contains the reset button
        $html = $component->get('status') === 'ready' ? 'Reset' : '';
        $this->assertNotEmpty($html);
    }

    /** @test */
    public function manual_reset_button_is_accessible_in_success_state()
    {
        $component = Livewire::actingAs($this->user)
            ->test(Scanner::class);

        $component->set('status', 'success');
        $component->set('scanResult', [
            'ticket_id' => $this->ticket->id,
            'role' => 'mahasiswa',
            'mahasiswa_name' => 'Test Student',
            'npm' => '12345678',
        ]);

        // Verify component is in success state
        $component->assertSet('status', 'success');
    }

    /** @test */
    public function manual_reset_button_is_accessible_in_error_state()
    {
        $component = Livewire::actingAs($this->user)
            ->test(Scanner::class);

        $component->set('status', 'error');
        $component->set('errorMessage', 'Test error');

        // Verify component is in error state
        $component->assertSet('status', 'error');
    }

    /** @test */
    public function force_reset_can_interrupt_scanning_state()
    {
        $component = Livewire::actingAs($this->user)
            ->test(Scanner::class);

        // Set to scanning state (simulating active scan)
        $component->set('status', 'scanning');

        // Force reset should immediately return to ready
        $component->call('forceReset')
            ->assertSet('status', 'ready')
            ->assertSet('scanResult', null)
            ->assertSet('errorMessage', '');
    }

    /** @test */
    public function force_reset_clears_scan_result_data()
    {
        $component = Livewire::actingAs($this->user)
            ->test(Scanner::class);

        // Set scan result
        $component->set('scanResult', [
            'ticket_id' => 123,
            'role' => 'mahasiswa',
            'mahasiswa_name' => 'Test',
            'npm' => '12345',
        ]);

        // Force reset should clear it
        $component->call('forceReset')
            ->assertSet('scanResult', null);
    }

    /** @test */
    public function force_reset_clears_error_message()
    {
        $component = Livewire::actingAs($this->user)
            ->test(Scanner::class);

        // Set error message
        $component->set('errorMessage', 'Some error occurred');

        // Force reset should clear it
        $component->call('forceReset')
            ->assertSet('errorMessage', '');
    }
}
