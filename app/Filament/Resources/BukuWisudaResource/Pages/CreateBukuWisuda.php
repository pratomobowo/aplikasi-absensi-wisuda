<?php

namespace App\Filament\Resources\BukuWisudaResource\Pages;

use App\Filament\Resources\BukuWisudaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateBukuWisuda extends CreateRecord
{
    protected static string $resource = BukuWisudaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Calculate file size from uploaded file
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
