<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GraduationTicketResource\Pages;
use App\Filament\Resources\GraduationTicketResource\RelationManagers;
use App\Models\GraduationTicket;
use App\Models\Mahasiswa;
use App\Models\GraduationEvent;
use App\Services\TicketService;
use App\Exports\GraduationTicketsExport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;

class GraduationTicketResource extends Resource
{
    protected static ?string $model = GraduationTicket::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationLabel = 'Tiket Wisuda';

    protected static ?int $navigationSort = 4;

    protected static ?string $pluralLabel = 'Tiket Wisuda';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('mahasiswa_id')
                    ->label('Mahasiswa')
                    ->relationship('mahasiswa', 'nama')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('graduation_event_id')
                    ->label('Acara Wisuda')
                    ->relationship('graduationEvent', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['mahasiswa', 'graduationEvent', 'attendances']))
            ->columns([
                Tables\Columns\TextColumn::make('mahasiswa.nama')
                    ->label('Mahasiswa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mahasiswa.npm')
                    ->label('NPM')
                    ->searchable(),
                Tables\Columns\TextColumn::make('graduationEvent.name')
                    ->label('Acara')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_distributed')
                    ->label('Terdistribusi')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
                Tables\Columns\TextColumn::make('distributed_at')
                    ->label('Waktu Distribusi')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('attendance_status')
                    ->label('Status Kehadiran')
                    ->badge()
                    ->getStateUsing(function (GraduationTicket $record) {
                        $status = $record->getAttendanceStatus();
                        return "{$status['mahasiswa']}/{$status['pendamping1']}/{$status['pendamping2']}";
                    })
                    ->color(fn (string $state): string => $state === '✓/✓/✓' ? 'success' : 'warning'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('graduation_event_id')
                    ->label('Acara')
                    ->relationship('graduationEvent', 'name'),
                Tables\Filters\TernaryFilter::make('is_distributed')
                    ->label('Status Distribusi')
                    ->placeholder('Semua')
                    ->trueLabel('Sudah Terdistribusi')
                    ->falseLabel('Belum Terdistribusi'),
                Tables\Filters\Filter::make('attendance_status')
                    ->label('Status Kehadiran')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->options([
                                'all_attended' => 'Semua Hadir',
                                'partial_attended' => 'Sebagian Hadir',
                                'not_attended' => 'Belum Hadir',
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!isset($data['status'])) {
                            return $query;
                        }

                        return match ($data['status']) {
                            'all_attended' => $query->whereHas('attendances', function ($q) {
                                $q->havingRaw('COUNT(*) = 3');
                            }),
                            'partial_attended' => $query->whereHas('attendances', function ($q) {
                                $q->havingRaw('COUNT(*) > 0 AND COUNT(*) < 3');
                            }),
                            'not_attended' => $query->doesntHave('attendances'),
                            default => $query,
                        };
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('copy_magic_link')
                    ->label('Copy Link')
                    ->icon('heroicon-o-clipboard-document')
                    ->color('primary')
                    ->action(function (GraduationTicket $record) {
                        $url = route('invitation.show', ['token' => $record->magic_link_token]);
                        // Copy to clipboard using JavaScript
                        Notification::make()
                            ->title('Link berhasil disalin!')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(false)
                    ->modalContent(fn (GraduationTicket $record) => view('filament.actions.copy-link', [
                        'url' => route('invitation.show', ['token' => $record->magic_link_token])
                    ]))
                    ->modalSubmitActionLabel('Tutup')
                    ->modalCancelAction(false),
                Tables\Actions\Action::make('send_whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->url(function (GraduationTicket $record) {
                        $url = route('invitation.show', ['token' => $record->magic_link_token]);
                        $message = "Halo {$record->mahasiswa->nama},\n\n";
                        $message .= "Selamat! Anda telah terdaftar untuk mengikuti {$record->graduationEvent->name}.\n\n";
                        $message .= "Silakan akses undangan digital Anda melalui link berikut:\n";
                        $message .= $url . "\n\n";
                        $message .= "Tunjukkan QR code pada halaman undangan saat acara berlangsung.\n\n";
                        $message .= "Terima kasih.";
                        
                        return 'https://wa.me/?text=' . urlencode($message);
                    })
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('view_invitation')
                    ->label('Lihat Undangan')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn (GraduationTicket $record) => route('invitation.show', ['token' => $record->magic_link_token]))
                    ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('create_tickets')
                        ->label('Buat Tiket')
                        ->icon('heroicon-o-ticket')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\Select::make('graduation_event_id')
                                ->label('Acara Wisuda')
                                ->options(GraduationEvent::pluck('name', 'id'))
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $ticketService = app(TicketService::class);
                            $event = GraduationEvent::find($data['graduation_event_id']);

                            foreach ($records as $record) {
                                if ($record instanceof Mahasiswa) {
                                    $ticketService->createTicket($record, $event);
                                }
                            }

                            Notification::make()
                                ->title('Tiket berhasil dibuat!')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('generate_missing_tickets')
                        ->label('Generate Missing Tickets')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Generate Missing Tickets')
                        ->modalDescription('Buat tiket untuk mahasiswa yang belum memiliki tiket untuk event yang dipilih')
                        ->form([
                            Forms\Components\Select::make('graduation_event_id')
                                ->label('Acara Wisuda')
                                ->options(GraduationEvent::pluck('name', 'id'))
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $ticketService = app(TicketService::class);
                            $event = GraduationEvent::find($data['graduation_event_id']);

                            $mahasiswaIds = $records->pluck('id')->toArray();
                            $result = $ticketService->generateTicketsForEvent($event, $mahasiswaIds, true);

                            $title = $result['failed'] === 0 ? 'Tiket Berhasil Dibuat' : 'Tiket Dibuat (Ada Kesalahan)';
                            $color = $result['failed'] === 0 ? 'success' : 'warning';

                            Notification::make()
                                ->title($title)
                                ->body("✓ Dibuat: {$result['created']} | ⊘ Lewat: {$result['skipped']} | ✗ Gagal: {$result['failed']}")
                                ->color($color)
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('export_tickets')
                        ->label('Export Excel')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $ticketIds = $records->pluck('id')->toArray();
                            $fileName = 'Tiket-Wisuda-' . now()->format('Y-m-d-His') . '.xlsx';

                            return Excel::download(
                                new GraduationTicketsExport(null, $ticketIds),
                                $fileName
                            );
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Mahasiswa')
                    ->schema([
                        Infolists\Components\TextEntry::make('mahasiswa.nama')
                            ->label('Nama'),
                        Infolists\Components\TextEntry::make('mahasiswa.npm')
                            ->label('NPM'),
                        Infolists\Components\TextEntry::make('mahasiswa.program_studi')
                            ->label('Program Studi'),
                        Infolists\Components\TextEntry::make('mahasiswa.fakultas')
                            ->label('Fakultas'),
                    ])
                    ->columns(2),
                Infolists\Components\Section::make('Informasi Acara')
                    ->schema([
                        Infolists\Components\TextEntry::make('graduationEvent.name')
                            ->label('Nama Acara'),
                        Infolists\Components\TextEntry::make('graduationEvent.date')
                            ->label('Tanggal')
                            ->date('d M Y'),
                        Infolists\Components\TextEntry::make('graduationEvent.time')
                            ->label('Waktu')
                            ->time('H:i'),
                        Infolists\Components\TextEntry::make('graduationEvent.location_name')
                            ->label('Lokasi'),
                    ])
                    ->columns(2),
                Infolists\Components\Section::make('QR Codes')
                    ->schema([
                        Infolists\Components\ImageEntry::make('qr_mahasiswa')
                            ->label('QR Code Mahasiswa')
                            ->getStateUsing(function (GraduationTicket $record) {
                                $qrService = app(\App\Services\QRCodeService::class);
                                return $qrService->generateQRCode($record->qr_token_mahasiswa);
                            }),
                        Infolists\Components\ImageEntry::make('qr_pendamping1')
                            ->label('QR Code Pendamping 1')
                            ->getStateUsing(function (GraduationTicket $record) {
                                $qrService = app(\App\Services\QRCodeService::class);
                                return $qrService->generateQRCode($record->qr_token_pendamping1);
                            }),
                        Infolists\Components\ImageEntry::make('qr_pendamping2')
                            ->label('QR Code Pendamping 2')
                            ->getStateUsing(function (GraduationTicket $record) {
                                $qrService = app(\App\Services\QRCodeService::class);
                                return $qrService->generateQRCode($record->qr_token_pendamping2);
                            }),
                    ])
                    ->columns(3),
                Infolists\Components\Section::make('Status Distribusi')
                    ->schema([
                        Infolists\Components\IconEntry::make('is_distributed')
                            ->label('Terdistribusi')
                            ->boolean(),
                        Infolists\Components\TextEntry::make('distributed_at')
                            ->label('Waktu Distribusi')
                            ->dateTime('d M Y H:i'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGraduationTickets::route('/'),
            'create' => Pages\CreateGraduationTicket::route('/create'),
            'view' => Pages\ViewGraduationTicket::route('/{record}'),
            'edit' => Pages\EditGraduationTicket::route('/{record}/edit'),
        ];
    }
}
