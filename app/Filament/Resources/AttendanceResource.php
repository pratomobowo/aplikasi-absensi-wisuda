<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use App\Models\Attendance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Kehadiran';

    protected static ?string $pluralLabel = 'Kehadiran';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with([
                'graduationTicket.mahasiswa',
                'graduationTicket.graduationEvent',
                'scannedBy'
            ]))
            ->columns([
                Tables\Columns\TextColumn::make('graduationTicket.mahasiswa.nama')
                    ->label('Mahasiswa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('graduationTicket.mahasiswa.nim')
                    ->label('NIM')
                    ->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'mahasiswa' => 'Mahasiswa',
                        'pendamping1' => 'Pendamping 1',
                        'pendamping2' => 'Pendamping 2',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'mahasiswa' => 'primary',
                        'pendamping1' => 'success',
                        'pendamping2' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('graduationTicket.graduationEvent.name')
                    ->label('Acara')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('scanned_at')
                    ->label('Waktu Scan')
                    ->dateTime('d M Y H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('scannedBy.name')
                    ->label('Dipindai Oleh')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('graduation_event_id')
                    ->label('Acara')
                    ->relationship('graduationTicket.graduationEvent', 'name'),
                Tables\Filters\SelectFilter::make('role')
                    ->label('Role')
                    ->options([
                        'mahasiswa' => 'Mahasiswa',
                        'pendamping1' => 'Pendamping 1',
                        'pendamping2' => 'Pendamping 2',
                    ]),
                Tables\Filters\Filter::make('scanned_at')
                    ->label('Tanggal Scan')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('scanned_at', '>=', $date),
                            )
                            ->when(
                                $data['scanned_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('scanned_at', '<=', $date),
                            );
                    }),
                Tables\Filters\SelectFilter::make('scanned_by')
                    ->label('Scanner')
                    ->relationship('scannedBy', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('scanned_at', 'desc');
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
            'index' => Pages\ListAttendances::route('/'),
        ];
    }
}
