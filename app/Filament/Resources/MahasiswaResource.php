<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MahasiswaResource\Pages;
use App\Filament\Resources\MahasiswaResource\RelationManagers;
use App\Models\Mahasiswa;
use App\Imports\MahasiswaImport;
use App\Exports\MahasiswaTemplateExport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Maatwebsite\Excel\Facades\Excel;

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
                Forms\Components\TextInput::make('fakultas')
                    ->label('Fakultas')
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
                        'Cum Laude' => 'Cum Laude',
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
                Tables\Columns\TextColumn::make('fakultas')
                    ->label('Fakultas')
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
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('program_studi')
                    ->label('Program Studi')
                    ->options(fn () => \App\Models\Mahasiswa::distinct()->pluck('program_studi', 'program_studi')->toArray()),
                Tables\Filters\SelectFilter::make('fakultas')
                    ->label('Fakultas')
                    ->options(fn () => \App\Models\Mahasiswa::distinct()->pluck('fakultas', 'fakultas')->toArray()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('import')
                    ->label('Import Excel')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('success')
                    ->form([
                        Forms\Components\FileUpload::make('file')
                            ->label('File Excel')
                            ->acceptedFileTypes([
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            ])
                            ->required()
                            ->helperText('Upload file Excel (.xls atau .xlsx) dengan format sesuai template'),
                    ])
                    ->action(function (array $data) {
                        try {
                            // Get the uploaded file path
                            $filePath = storage_path('app/public/' . $data['file']);
                            
                            // Create import instance
                            $import = new MahasiswaImport();
                            
                            // Process the import
                            Excel::import($import, $filePath);
                            
                            // Get import summary
                            $summary = $import->getImportSummary();
                            
                            // Build notification message
                            $message = "Import selesai! ";
                            $message .= "Berhasil: {$summary['success']}, ";
                            $message .= "Duplikat (diupdate): {$summary['duplicate']}, ";
                            $message .= "Gagal: {$summary['failed']}";
                            
                            // Show notification based on results
                            if ($summary['failed'] > 0) {
                                // Build error details
                                $errorDetails = '';
                                foreach ($summary['errors'] as $error) {
                                    $errorDetails .= "Baris {$error['row']}: " . implode(', ', $error['errors']) . "\n";
                                }
                                
                                Notification::make()
                                    ->warning()
                                    ->title('Import selesai dengan error')
                                    ->body($message . "\n\nDetail error:\n" . $errorDetails)
                                    ->persistent()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->success()
                                    ->title('Import berhasil!')
                                    ->body($message)
                                    ->send();
                            }
                            
                            // Clean up uploaded file
                            if (file_exists($filePath)) {
                                unlink($filePath);
                            }
                            
                        } catch (\Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Import gagal')
                                ->body('Terjadi kesalahan: ' . $e->getMessage())
                                ->persistent()
                                ->send();
                        }
                    })
                    ->modalWidth('md')
                    ->modalSubmitActionLabel('Import')
                    ->modalCancelActionLabel('Batal'),
                    
                Tables\Actions\Action::make('downloadTemplate')
                    ->label('Download Template')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->action(function () {
                        return Excel::download(new MahasiswaTemplateExport(), 'template-mahasiswa.xlsx');
                    }),
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
