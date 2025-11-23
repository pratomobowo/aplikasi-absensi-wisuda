<?php

namespace App\Filament\Resources\AttendanceResource\Actions;

use App\Services\AttendanceService;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ManualCheckInAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->name('manualCheckIn')
            ->label('Manual Check-In')
            ->icon('heroicon-o-check-circle')
            ->color('primary')
            ->form([
                Section::make('Manual Check-In Mahasiswa')
                    ->description('Masukkan NIM/NPM mahasiswa untuk melakukan check-in manual')
                    ->schema([
                        TextInput::make('nim')
                            ->label('NIM/NPM Mahasiswa')
                            ->placeholder('Masukkan NIM atau NPM')
                            ->required()
                            ->autofocus()
                            ->helperText('Cek-in manual untuk mahasiswa dan kedua pendampingnya'),
                    ]),
            ])
            ->action(function (array $data) {
                $attendanceService = app(AttendanceService::class);
                $result = $attendanceService->recordManualAttendance(
                    $data['nim'],
                    null,
                    Auth::user()
                );

                if ($result['success']) {
                    Notification::make()
                        ->title('Berhasil')
                        ->body($result['message'])
                        ->success()
                        ->send();
                } else {
                    Notification::make()
                        ->title('Gagal')
                        ->body($result['message'])
                        ->danger()
                        ->send();
                }
            });
    }
}
