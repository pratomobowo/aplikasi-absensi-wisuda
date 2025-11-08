<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BukuWisudaResource\Pages;
use App\Filament\Resources\BukuWisudaResource\RelationManagers;
use App\Models\BukuWisuda;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BukuWisudaResource extends Resource
{
    protected static ?string $model = BukuWisuda::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Buku Wisuda';

    protected static ?int $navigationSort = 3;  // Setelah Acara Wisuda

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Buku Wisuda')
                    ->description('Unggah buku wisuda untuk acara tertentu')
                    ->schema([
                        Forms\Components\Select::make('graduation_event_id')
                            ->label('Acara Wisuda')
                            ->relationship('graduationEvent', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpanFull()
                            ->helperText('Pilih acara wisuda yang akan menerima buku ini'),

                        Forms\Components\FileUpload::make('file_path')
                            ->label('File Buku Wisuda (PDF)')
                            ->disk('buku_wisuda')
                            ->directory('uploads')
                            ->visibility('private')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(512000) // 500MB
                            ->required()
                            ->columnSpanFull()
                            ->storeFileNamesIn('filename')
                            ->helperText('Format: PDF. Max: 500MB'),

                        Forms\Components\Hidden::make('uploaded_at')
                            ->default(now()),

                        Forms\Components\TextInput::make('mime_type')
                            ->default('application/pdf')
                            ->hidden(),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('graduationEvent.name')
                    ->label('Acara Wisuda')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('filename')
                    ->label('Nama File')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('file_size')
                    ->label('Ukuran')
                    ->formatStateUsing(fn($record) => $record->getHumanFileSize())
                    ->sortable(),

                Tables\Columns\TextColumn::make('download_count')
                    ->label('Downloads')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('uploaded_at')
                    ->label('Tanggal Upload')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('graduation_event_id')
                    ->label('Filter Acara')
                    ->relationship('graduationEvent', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Lihat Buku')
                    ->icon('heroicon-o-eye')
                    ->url(fn (BukuWisuda $record): string => route('buku-wisuda.viewer', $record->id))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('uploaded_at', 'desc');
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
            'index' => Pages\ListBukuWisudas::route('/'),
            'create' => Pages\CreateBukuWisuda::route('/create'),
            'edit' => Pages\EditBukuWisuda::route('/{record}/edit'),
        ];
    }
}
