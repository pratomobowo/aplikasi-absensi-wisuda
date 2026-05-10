<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\GraduationEvent;
use App\Models\GraduationTicket;
use App\Models\KonsumsiRecord;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $activeEvent = GraduationEvent::where('status', 'active')->first();

        $stats = [
            'total_mahasiswa' => Mahasiswa::count(),
            'total_events' => GraduationEvent::where('status', '!=', 'completed')->count(),
            'active_event' => $activeEvent ? $activeEvent->name : 'Tidak ada',
            'total_tickets' => GraduationTicket::notArchived()->count(),
            'distributed_tickets' => GraduationTicket::notArchived()->where('is_distributed', true)->count(),
            'total_attendance' => Attendance::notArchived()->count(),
            'today_attendance' => Attendance::notArchived()->today()->count(),
            'konsumsi_received' => GraduationTicket::notArchived()->where('konsumsi_diterima', true)->count(),
            'konsumsi_pending' => GraduationTicket::notArchived()->where('konsumsi_diterima', false)->orWhereNull('konsumsi_diterima')->count(),
            'mahasiswa_with_photos' => Mahasiswa::whereNotNull('foto_wisuda')->count(),
            'mahasiswa_without_photos' => Mahasiswa::whereNull('foto_wisuda')->count(),
        ];

        if ($activeEvent) {
            $stats['active_event_tickets'] = GraduationTicket::notArchived()->where('graduation_event_id', $activeEvent->id)->count();
            $stats['active_event_attendance'] = Attendance::notArchived()->whereHas('graduationTicket', function ($q) use ($activeEvent) {
                $q->where('graduation_event_id', $activeEvent->id);
            })->count();
        }

        $recentAttendances = Attendance::notArchived()->with(['graduationTicket.mahasiswa', 'scannedBy'])
            ->latest('scanned_at')
            ->take(10)
            ->get();

        $recentKonsumsi = GraduationTicket::notArchived()->with('mahasiswa')
            ->where('konsumsi_diterima', true)
            ->latest('konsumsi_at')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentAttendances', 'recentKonsumsi', 'activeEvent'));
    }
}