<?php

namespace App\Filament\Widgets;

use App\Models\GraduationEvent;
use App\Services\KonsumsiService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class KonsumsiStatsOverview extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $activeEvent = GraduationEvent::where('is_active', true)->first();

        if (!$activeEvent) {
            return [
                Stat::make('Total Konsumsi Tersedia', 0)
                    ->description('Tidak ada acara aktif')
                    ->descriptionIcon('heroicon-o-information-circle')
                    ->color('gray'),
            ];
        }

        $konsumsiService = app(KonsumsiService::class);
        $stats = $konsumsiService->getKonsumsiStats($activeEvent->id);

        return [
            Stat::make('Total Konsumsi Tersedia', $stats['total'])
                ->description($activeEvent->name)
                ->descriptionIcon('heroicon-o-cube')
                ->color('primary'),

            Stat::make('Konsumsi Sudah Diambil', $stats['received'])
                ->description(sprintf('%.1f%% dari total', $stats['percentage']))
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Konsumsi Belum Diambil', $stats['pending'])
                ->description(sprintf('%.1f%% dari total', (100 - $stats['percentage'])))
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),
        ];
    }

    protected function getPollingInterval(): ?string
    {
        return '10s'; // Real-time update every 10 seconds
    }
}
