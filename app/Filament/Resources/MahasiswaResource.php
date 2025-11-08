<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MahasiswaResource\Pages;
use App\Filament\Resources\MahasiswaResource\RelationManagers;
use App\Models\Mahasiswa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MahasiswaResource extends Resource
{
    protected static ?string $model = Mahasiswa::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Mahasiswa';

    protected static ?string $pluralLabel = 'Mahasiswa';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('npm')
                    ->label('NPM')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(20),
                Forms\Components\TextInput::make('nama')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('program_studi')
                    ->label('Program Studi')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('ipk')
                    ->label('IPK')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(4)
                    ->step(0.01),
                Forms\Components\Select::make('yudisium')
                    ->label('Yudisium')
                    ->options([
                        'Dengan Pujian' => 'Dengan Pujian',
                        'Sangat Memuaskan' => 'Sangat Memuaskan',
                        'Memuaskan' => 'Memuaskan',
                    ])
                    ->nullable(),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255)
                    ->nullable(),
                Forms\Components\TextInput::make('phone')
                    ->label('Telepon')
                    ->tel()
                    ->maxLength(20)
                    ->nullable(),
                Forms\Components\TextInput::make('nomor_kursi')
                    ->label('Nomor Kursi')
                    ->maxLength(20)
                    ->nullable()
                    ->helperText('Nomor kursi saat wisuda (opsional)'),
                Forms\Components\Textarea::make('judul_skripsi')
                    ->label('Judul Skripsi')
                    ->rows(3)
                    ->maxLength(500)
                    ->nullable()
                    ->helperText('Judul skripsi/tugas akhir (opsional)'),
                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->maxLength(255)
                    ->helperText('Kosongkan jika tidak ingin mengubah password'),
                Forms\Components\FileUpload::make('foto_wisuda')
                    ->label('Foto Wisuda')
                    ->image()
                    ->disk('graduation_photos')
                    ->directory('')
                    ->visibility('public')
                    ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png'])
                    ->maxSize(2048)
                    ->imagePreviewHeight('250')
                    ->nullable()
                    ->helperText('Format: JPG, PNG. Max: 2MB'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['graduationTickets']))
            ->columns([
                Tables\Columns\TextColumn::make('npm')
                    ->label('NPM')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('program_studi')
                    ->label('Program Studi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ipk')
                    ->label('IPK')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state, 2)),
                Tables\Columns\TextColumn::make('yudisium')
                    ->label('Yudisium'),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telepon')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('nomor_kursi')
                    ->label('Nomor Kursi')
                    ->default('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('judul_skripsi')
                    ->label('Judul Skripsi')
                    ->limit(50)
                    ->default('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ImageColumn::make('foto_wisuda')
                    ->label('Foto Wisuda')
                    ->disk('graduation_photos')
                    ->defaultImageUrl(url('/storage/graduation-photos/no-photo.jpg'))
                    ->circular()
                    ->size(40)
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('program_studi')
                    ->label('Program Studi')
                    ->options(fn () => \App\Models\Mahasiswa::distinct()->pluck('program_studi', 'program_studi')->toArray()),
            ])
            ->actions([
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
            'index' => Pages\ListMahasiswas::route('/'),
            'create' => Pages\CreateMahasiswa::route('/create'),
            'edit' => Pages\EditMahasiswa::route('/{record}/edit'),
        ];
    }
}
