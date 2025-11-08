<?php

namespace App\Filament\Widgets;

use App\Models\Mahasiswa;
use App\Models\GraduationEvent;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MissingPhotosStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

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
        $totalStudents = $activeEvent->graduationTickets()
            ->whereHas('mahasiswa')
            ->count();

        // Get students with photos
        $studentsWithPhotos = $activeEvent->graduationTickets()
            ->whereHas('mahasiswa', function ($query) {
                $query->whereNotNull('foto_wisuda');
            })
            ->count();

        // Get students without photos
        $studentsWithoutPhotos = $totalStudents - $studentsWithPhotos;

        // Calculate percentages
        $withPhotosPercentage = $totalStudents > 0 ? ($studentsWithPhotos / $totalStudents * 100) : 0;
        $withoutPhotosPercentage = $totalStudents > 0 ? ($studentsWithoutPhotos / $totalStudents * 100) : 0;

        return [
            Stat::make('Foto Wisuda Lengkap', $studentsWithPhotos)
                ->description(sprintf('%.1f%% sudah upload', $withPhotosPercentage))
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Foto Wisuda Belum Upload', $studentsWithoutPhotos)
                ->description(sprintf('%.1f%% belum upload', $withoutPhotosPercentage))
                ->descriptionIcon('heroicon-o-exclamation-circle')
                ->color('danger'),
        ];
    }

    protected function getPollingInterval(): ?string
    {
        return '30s'; // Update every 30 seconds
    }
}
