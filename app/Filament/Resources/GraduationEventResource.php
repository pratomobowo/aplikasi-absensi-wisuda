<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GraduationEventResource\Pages;
use App\Filament\Resources\GraduationEventResource\RelationManagers;
use App\Models\GraduationEvent;
use App\Services\TicketService;
use App\Exports\GraduationTicketsExport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;

class GraduationEventResource extends Resource
{
    protected static ?string $model = GraduationEvent::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Acara Wisuda';

    protected static ?int $navigationSort = 2;

    protected static ?string $pluralLabel = 'Acara Wisuda';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Acara')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('date')
                    ->label('Tanggal')
                    ->required()
                    ->native(false),
                Forms\Components\TimePicker::make('time')
                    ->label('Waktu')
                    ->required()
                    ->native(false),
                Forms\Components\TextInput::make('location_name')
                    ->label('Nama Lokasi')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('location_address')
                    ->label('Alamat Lokasi')
                    ->required()
                    ->rows(3),
                Forms\Components\TextInput::make('location_lat')
                    ->label('Latitude')
                    ->numeric()
                    ->step(0.00000001),
                Forms\Components\TextInput::make('location_lng')
                    ->label('Longitude')
                    ->numeric()
                    ->step(0.00000001),
                Forms\Components\TextInput::make('maps_url')
                    ->label('URL Maps')
                    ->url()
                    ->placeholder('https://maps.google.com/...')
                    ->helperText('Masukkan URL lengkap dari Google Maps atau aplikasi maps lainnya'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['graduationTickets']))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Acara')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('time')
                    ->label('Waktu')
                    ->time('H:i'),
                Tables\Columns\TextColumn::make('location_name')
                    ->label('Lokasi')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif'),
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('date_until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Action::make('generate_tickets')
                    ->label('Generate Tiket')
                    ->icon('heroicon-o-ticket')
                    ->color('info')
                    ->action(function (GraduationEvent $record) {
                        $ticketService = app(TicketService::class);
                        $result = $ticketService->generateTicketsForEvent($record, null, true);

                        $title = $result['failed'] === 0 ? 'Tiket Berhasil Dibuat' : 'Tiket Dibuat (Ada Kesalahan)';
                        $color = $result['failed'] === 0 ? 'success' : 'warning';

                        Notification::make()
                            ->title($title)
                            ->body("✓ Dibuat: {$result['created']} | ⊘ Lewat: {$result['skipped']} | ✗ Gagal: {$result['failed']}")
                            ->color($color)
                            ->send();
                    }),
                Action::make('export_tickets')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (GraduationEvent $record) {
                        $fileName = 'Tiket-Wisuda-' . $record->name . '-' . now()->format('Y-m-d-His') . '.xlsx';

                        return Excel::download(
                            new GraduationTicketsExport($record),
                            $fileName
                        );
                    }),
                Tables\Actions\Action::make('set_active')
                    ->label('Set Aktif')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (GraduationEvent $record) {
                        // Deactivate all other events
                        GraduationEvent::where('id', '!=', $record->id)->update(['is_active' => false]);
                        // Activate this event
                        $record->update(['is_active' => true]);
                    })
                    ->visible(fn (GraduationEvent $record) => !$record->is_active),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListGraduationEvents::route('/'),
            'create' => Pages\CreateGraduationEvent::route('/create'),
            'edit' => Pages\EditGraduationEvent::route('/{record}/edit'),
        ];
    }
}
