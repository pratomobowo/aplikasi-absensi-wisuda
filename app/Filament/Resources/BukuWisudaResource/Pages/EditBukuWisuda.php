<?php

namespace App\Filament\Resources\BukuWisudaResource\Pages;

use App\Filament\Resources\BukuWisudaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditBukuWisuda extends EditRecord
{
    protected static string $resource = BukuWisudaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Recalculate file size if file was updated
        if (isset($data['file_path'])) {
            $filePath = $data['file_path'];
            $fullPath = Storage::disk('buku_wisuda')->path($filePath);

            if (file_exists($fullPath)) {
                $data['file_size'] = filesize($fullPath);
            }
        }

        return $data;
    }
}
