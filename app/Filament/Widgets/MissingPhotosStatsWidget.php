<?php

namespace App\Filament\Widgets;

use App\Models\Mahasiswa;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MissingPhotosStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        // Get total mahasiswa (regardless of event registration)
        $totalStudents = Mahasiswa::count();

        if ($totalStudents === 0) {
            return [
                Stat::make('Total Mahasiswa', 0)
                    ->description('Tidak ada data mahasiswa')
                    ->descriptionIcon('heroicon-o-information-circle')
                    ->color('gray'),
            ];
        }

        // Get students with photos
        $studentsWithPhotos = Mahasiswa::whereNotNull('foto_wisuda')->count();

        // Get students without photos
        $studentsWithoutPhotos = $totalStudents - $studentsWithPhotos;

        // Calculate percentages
        $withPhotosPercentage = $totalStudents > 0 ? ($studentsWithPhotos / $totalStudents * 100) : 0;
        $withoutPhotosPercentage = $totalStudents > 0 ? ($studentsWithoutPhotos / $totalStudents * 100) : 0;

        return [
            Stat::make('Foto Wisuda Lengkap', $studentsWithPhotos)
                ->description(sprintf('%.1f%% dari %d mahasiswa', $withPhotosPercentage, $totalStudents))
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Foto Wisuda Belum Upload', $studentsWithoutPhotos)
                ->description(sprintf('%.1f%% dari %d mahasiswa', $withoutPhotosPercentage, $totalStudents))
                ->descriptionIcon('heroicon-o-exclamation-circle')
                ->color('danger'),
        ];
    }

    protected function getPollingInterval(): ?string
    {
        return '30s'; // Update every 30 seconds
    }
}
