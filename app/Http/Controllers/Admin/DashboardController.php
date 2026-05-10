<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\GraduationEvent;
use App\Models\GraduationTicket;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $activeEvent = GraduationEvent::where('status', 'active')->first();
        
        // Get all non-completed events with their stats
        $events = GraduationEvent::where('status', '!=', 'completed')
            ->withCount([
                'graduationTickets as tickets_count' => function ($q) {
                    $q->whereNull('archived_at');
                },
                'graduationTickets as distributed_count' => function ($q) {
                    $q->whereNull('archived_at')->where('is_distributed', true);
                },
                'graduationTickets as konsumsi_count' => function ($q) {
                    $q->whereNull('archived_at')->where('konsumsi_diterima', true);
                }
            ])
            ->orderBy('date', 'desc')
            ->get();

        // Calculate attendance per event
        foreach ($events as $event) {
            $event->attendance_count = Attendance::notArchived()
                ->whereHas('graduationTicket', function ($q) use ($event) {
                    $q->where('graduation_event_id', $event->id);
                })
                ->count();
                
            $event->attendance_today = Attendance::notArchived()
                ->whereHas('graduationTicket', function ($q) use ($event) {
                    $q->where('graduation_event_id', $event->id);
                })
                ->today()
                ->count();
        }

        // Overall stats (real data)
        $stats = [
            'total_events' => GraduationEvent::where('status', '!=', 'completed')->count(),
            'total_wisudawan' => Mahasiswa::whereHas('graduationTickets.graduationEvent', function ($q) {
                $q->where('status', '!=', 'completed');
            })->count(),
            'total_tickets' => GraduationTicket::notArchived()->count(),
            'distributed_tickets' => GraduationTicket::notArchived()->where('is_distributed', true)->count(),
            'total_attendance' => Attendance::notArchived()->count(),
            'today_attendance' => Attendance::notArchived()->today()->count(),
            'konsumsi_received' => GraduationTicket::notArchived()->where('konsumsi_diterima', true)->count(),
            'konsumsi_pending' => GraduationTicket::notArchived()->where(function ($q) {
                $q->where('konsumsi_diterima', false)->orWhereNull('konsumsi_diterima');
            })->count(),
            'wisudawan_with_photos' => Mahasiswa::whereHas('graduationTickets.graduationEvent', function ($q) {
                $q->where('status', '!=', 'completed');
            })->whereNotNull('foto_wisuda')->count(),
            'wisudawan_without_photos' => Mahasiswa::whereHas('graduationTickets.graduationEvent', function ($q) {
                $q->where('status', '!=', 'completed');
            })->whereNull('foto_wisuda')->count(),
        ];

        // Recent activities with event info
        $recentAttendances = Attendance::notArchived()
            ->with(['graduationTicket.mahasiswa', 'graduationTicket.graduationEvent', 'scannedBy'])
            ->latest('scanned_at')
            ->take(10)
            ->get();

        $recentKonsumsi = GraduationTicket::notArchived()
            ->with(['mahasiswa', 'graduationEvent'])
            ->where('konsumsi_diterima', true)
            ->latest('konsumsi_at')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'events', 'recentAttendances', 'recentKonsumsi', 'activeEvent'));
    }
}