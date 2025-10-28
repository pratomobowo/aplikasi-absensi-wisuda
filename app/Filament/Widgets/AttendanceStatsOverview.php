<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\Mahasiswa;
use App\Models\GraduationEvent;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AttendanceStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $activeEvent = GraduationEvent::where('is_active', true)->first();

        if (!$activeEvent) {
            return [
                Stat::make('Total Mahasiswa', 0)
                    ->description('Tidak ada acara aktif')
                    ->descriptionIcon('heroicon-o-information-circle')
                    ->color('gray'),
            ];
        }

        // Get total registered students for active event
        $totalStudents = $activeEvent->graduationTickets()->count();

        // Get attendance counts by role
        $mahasiswaAttended = Attendance::whereHas('graduationTicket', function ($query) use ($activeEvent) {
            $query->where('graduation_event_id', $activeEvent->id);
        })
        ->where('role', 'mahasiswa')
        ->count();

        $pendamping1Attended = Attendance::whereHas('graduationTicket', function ($query) use ($activeEvent) {
            $query->where('graduation_event_id', $activeEvent->id);
        })
        ->where('role', 'pendamping1')
        ->count();

        $pendamping2Attended = Attendance::whereHas('graduationTicket', function ($query) use ($activeEvent) {
            $query->where('graduation_event_id', $activeEvent->id);
        })
        ->where('role', 'pendamping2')
        ->count();

        return [
            Stat::make('Total Mahasiswa Terdaftar', $totalStudents)
                ->description($activeEvent->name)
                ->descriptionIcon('heroicon-o-academic-cap')
                ->color('primary'),
            
            Stat::make('Mahasiswa Hadir', $mahasiswaAttended)
                ->description(sprintf('%.1f%% dari total', $totalStudents > 0 ? ($mahasiswaAttended / $totalStudents * 100) : 0))
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),
            
            Stat::make('Pendamping 1 Hadir', $pendamping1Attended)
                ->description(sprintf('%.1f%% dari total', $totalStudents > 0 ? ($pendamping1Attended / $totalStudents * 100) : 0))
                ->descriptionIcon('heroicon-o-user')
                ->color('info'),
            
            Stat::make('Pendamping 2 Hadir', $pendamping2Attended)
                ->description(sprintf('%.1f%% dari total', $totalStudents > 0 ? ($pendamping2Attended / $totalStudents * 100) : 0))
                ->descriptionIcon('heroicon-o-user')
                ->color('warning'),
        ];
    }

    protected function getPollingInterval(): ?string
    {
        return '10s'; // Real-time update every 10 seconds
    }
}
