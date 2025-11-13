<?php

namespace App\Filament\Resources\MahasiswaResource\Pages;

use App\Exports\MahasiswaTemplateExport;
use App\Filament\Resources\MahasiswaResource;
use App\Imports\MahasiswaImport;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListMahasiswas extends ListRecords
{
    protected static string $resource = MahasiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('downloadTemplate')
                ->label('Download Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function () {
                    return Excel::download(new MahasiswaTemplateExport(), 'template-mahasiswa.xlsx');
                }),
            Actions\Action::make('import')
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->form([
                    FileUpload::make('file')
                        ->label('File Excel')
                        ->maxSize(10240) // 10MB
                        ->required()
                        ->helperText('Format: Excel (.xlsx, .xls) atau CSV. Kolom: NPM, Nama, Program Studi, IPK, Yudisium (opsional), Email (opsional), Phone (opsional)')
                        ->disk('local')
                        ->directory('temp-imports')
                        ->visibility('private')
                        ->acceptedFileTypes([
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'text/csv',
                            'text/comma-separated-values',
                        ])
                        ->rules([
                            'required',
                            'file',
                            'mimes:xls,xlsx,csv',
                            'max:10240',
                        ]),
                ])
                ->action(function (array $data) {
                    try {
                        $import = new MahasiswaImport();
                        
                        // Import the file directly using the disk path
                        Excel::import($import, $data['file'], 'local');
                        
                        // Get import summary
                        $summary = $import->getImportSummary();
                        
                        // Delete temporary file using Storage facade
                        \Storage::disk('local')->delete($data['file']);
                        
                        // Show notification based on results
                        if ($summary['failed'] > 0) {
                            // Build error message
                            $errorDetails = collect($summary['errors'])
                                ->take(5) // Show only first 5 errors
                                ->map(fn($error) => "Baris {$error['row']}: " . implode(', ', $error['errors']))
                                ->join("\n");
                            
                            Notification::make()
                                ->title('Import Selesai dengan Error')
                                ->body("Berhasil: {$summary['success']}, Duplikat: {$summary['duplicate']}, Gagal: {$summary['failed']}\n\nError:\n{$errorDetails}")
                                ->warning()
                                ->persistent()
                                ->send();
                        } else {
                            $message = "Data berhasil diimport: {$summary['success']} baru, {$summary['duplicate']} diperbarui";
                            if ($summary['success'] > 0) {
                                $message .= "\n\nPassword akan di-generate dari NPM. Jalankan command:\nphp artisan app:generate-mahasiswa-password --all";
                            }
                            Notification::make()
                                ->title('Import Berhasil!')
                                ->body($message)
                                ->success()
                                ->send();
                        }
                        
                        // Refresh the table
                        $this->dispatch('$refresh');
                        
                    } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                        $failures = $e->failures();
                        $errorMessage = collect($failures)
                            ->take(5)
                            ->map(fn($failure) => "Baris {$failure->row()}: " . implode(', ', $failure->errors()))
                            ->join("\n");
                        
                        Notification::make()
                            ->title('Validasi Gagal')
                            ->body("Terdapat error validasi:\n\n{$errorMessage}")
                            ->danger()
                            ->persistent()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Import Gagal')
                            ->body('Terjadi kesalahan: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->modalWidth('md')
                ->modalSubmitActionLabel('Import')
                ->modalCancelActionLabel('Batal'),
            Actions\CreateAction::make(),
        ];
    }
}
