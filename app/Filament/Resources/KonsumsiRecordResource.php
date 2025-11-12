<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KonsumsiRecordResource\Pages;
use App\Models\GraduationTicket;
use App\Models\KonsumsiRecord;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KonsumsiRecordResource extends Resource
{
    protected static ?string $model = GraduationTicket::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-circle';

    protected static ?string $navigationLabel = 'Data Konsumsi';

    protected static ?string $label = 'Data Konsumsi';

    protected static ?string $pluralLabel = 'Data Konsumsi';

    protected static ?int $navigationSort = 6;

    public static function table(Table $table): Table
    {
        return $table
            ->query(GraduationTicket::query()->with('mahasiswa'))
            ->columns([
                TextColumn::make('mahasiswa.nama')
                    ->label('Nama Mahasiswa')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('mahasiswa.npm')
                    ->label('NPM')
                    ->searchable()
                    ->sortable(),

                BadgeColumn::make('konsumsi_diterima')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => $state ? 'Sudah Diterima' : 'Belum Diterima')
                    ->color(fn ($state) => $state ? 'success' : 'warning')
                    ->sortable(),

                TextColumn::make('konsumsi_at')
                    ->label('Waktu Scan')
                    ->dateTime('d M Y H:i:s')
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('konsumsiRecord.scannedBy.name')
                    ->label('Scan Oleh')
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('graduationEvent.name')
                    ->label('Event Wisuda')
                    ->visible(fn () => false), // Hidden but available for filtering
            ])
            ->filters([
                SelectFilter::make('konsumsi_diterima')
                    ->label('Status Konsumsi')
                    ->options([
                        true => 'Sudah Diterima',
                        false => 'Belum Diterima',
                    ]),

                SelectFilter::make('graduation_event_id')
                    ->label('Event Wisuda')
                    ->relationship('graduationEvent', 'name')
                    ->preload(),

                Filter::make('scanned_at')
                    ->form([
                        Forms\Components\DatePicker::make('scanned_from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('scanned_until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['scanned_from'],
                                fn (Builder $q, $date) => $q->whereDate('konsumsi_at', '>=', $date),
                            )
                            ->when(
                                $data['scanned_until'],
                                fn (Builder $q, $date) => $q->whereDate('konsumsi_at', '<=', $date),
                            );
                    }),
            ])
            ->defaultSort('konsumsi_at', 'desc')
            ->paginated([50, 100, 200])
            ->striped()
            ->actions([
                Action::make('toggle_konsumsi')
                    ->label('Toggle Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->action(function (GraduationTicket $record) {
                        $oldStatus = $record->konsumsi_diterima;
                        $newStatus = !$oldStatus;

                        // Update ticket
                        if ($newStatus) {
                            // Mark as received
                            $record->update([
                                'konsumsi_diterima' => true,
                                'konsumsi_at' => now(),
                            ]);

                            // Create konsumsi record if doesn't exist
                            if (!$record->konsumsiRecord()->exists()) {
                                KonsumsiRecord::create([
                                    'graduation_ticket_id' => $record->id,
                                    'scanned_by' => auth()->id(),
                                    'scanned_at' => now(),
                                ]);
                            }

                            Log::info('KonsumsiRecord: Manual toggle to received', [
                                'ticket_id' => $record->id,
                                'mahasiswa_id' => $record->mahasiswa_id,
                                'admin_id' => auth()->id(),
                                'previous_status' => $oldStatus,
                            ]);
                        } else {
                            // Mark as not received
                            $record->update([
                                'konsumsi_diterima' => false,
                                'konsumsi_at' => null,
                            ]);

                            // Delete konsumsi record if exists
                            $record->konsumsiRecord()->delete();

                            Log::info('KonsumsiRecord: Manual toggle to not received', [
                                'ticket_id' => $record->id,
                                'mahasiswa_id' => $record->mahasiswa_id,
                                'admin_id' => auth()->id(),
                                'previous_status' => $oldStatus,
                            ]);
                        }
                    })
                    ->successNotification(
                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Status Konsumsi Diperbarui')
                            ->body(function (GraduationTicket $record) {
                                return $record->konsumsi_diterima
                                    ? 'Konsumsi ditandai sebagai sudah diterima'
                                    : 'Konsumsi ditandai sebagai belum diterima';
                            })
                    ),
            ])
            ->bulkActions([
                BulkAction::make('mark_received')
                    ->label('Tandai Sudah Diterima')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(function (Collection $records) {
                        DB::transaction(function () use ($records) {
                            foreach ($records as $record) {
                                if (!$record->konsumsi_diterima) {
                                    $record->update([
                                        'konsumsi_diterima' => true,
                                        'konsumsi_at' => now(),
                                    ]);

                                    if (!$record->konsumsiRecord()->exists()) {
                                        KonsumsiRecord::create([
                                            'graduation_ticket_id' => $record->id,
                                            'scanned_by' => auth()->id(),
                                            'scanned_at' => now(),
                                        ]);
                                    }
                                }
                            }
                        });

                        Log::info('KonsumsiRecord: Bulk mark as received', [
                            'count' => $records->count(),
                            'admin_id' => auth()->id(),
                        ]);
                    })
                    ->successNotification(
                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Konsumsi Ditandai Diterima')
                            ->body(fn (Collection $records) => "{$records->count()} mahasiswa ditandai sudah menerima konsumsi")
                    ),

                BulkAction::make('mark_not_received')
                    ->label('Tandai Belum Diterima')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->action(function (Collection $records) {
                        DB::transaction(function () use ($records) {
                            foreach ($records as $record) {
                                if ($record->konsumsi_diterima) {
                                    $record->update([
                                        'konsumsi_diterima' => false,
                                        'konsumsi_at' => null,
                                    ]);

                                    $record->konsumsiRecord()->delete();
                                }
                            }
                        });

                        Log::info('KonsumsiRecord: Bulk mark as not received', [
                            'count' => $records->count(),
                            'admin_id' => auth()->id(),
                        ]);
                    })
                    ->successNotification(
                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Konsumsi Ditandai Belum Diterima')
                            ->body(fn (Collection $records) => "{$records->count()} mahasiswa ditandai belum menerima konsumsi")
                    ),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageKonsumsiRecords::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Konsumsi can only be created via scanner
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false; // Read-only
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false; // Cannot delete
    }
}
