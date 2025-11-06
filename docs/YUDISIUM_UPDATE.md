# Update Pilihan Yudisium

## Perubahan
Mengubah pilihan yudisium dari "Cum Laude" menjadi "Dengan Pujian" sesuai dengan standar akademik Indonesia.

## Pilihan Yudisium

### Sebelum
1. Cum Laude
2. Sangat Memuaskan
3. Memuaskan

### Sesudah
1. **Dengan Pujian** (sebelumnya: Cum Laude)
2. Sangat Memuaskan
3. Memuaskan

## File yang Dimodifikasi

### 1. Filament Resource
**File**: `app/Filament/Resources/MahasiswaResource.php`
- Mengubah options select yudisium
- "Cum Laude" → "Dengan Pujian"

### 2. Import Validation
**File**: `app/Imports/MahasiswaImport.php`
- Mengubah validation rule: `in:Dengan Pujian,Sangat Memuaskan,Memuaskan`
- Mengubah pesan error validasi

### 3. Export Template
**File**: `app/Exports/MahasiswaTemplateExport.php`
- Mengubah contoh data dari "Cum Laude" menjadi "Dengan Pujian"

### 4. CSV Template
**File**: `public/templates/mahasiswa-import-template.csv`
- Mengubah contoh data yudisium

### 5. Frontend Display
**File**: `resources/views/livewire/data-wisudawan.blade.php`
- Mengubah kondisi badge: `$mhs->yudisium === 'Dengan Pujian'`
- Badge tetap berwarna kuning (bg-yellow-100 text-yellow-800)

## Migrasi Data

Data mahasiswa yang sudah ada dengan yudisium "Cum Laude" telah diupdate otomatis menjadi "Dengan Pujian":

```php
App\Models\Mahasiswa::where('yudisium', 'Cum Laude')
    ->update(['yudisium' => 'Dengan Pujian']);
```

## Styling Badge (Tidak Berubah)

Badge yudisium tetap menggunakan warna yang sama:

| Yudisium | Warna Badge | Class |
|----------|-------------|-------|
| Dengan Pujian | Kuning | `bg-yellow-100 text-yellow-800` |
| Sangat Memuaskan | Hijau | `bg-green-100 text-green-800` |
| Memuaskan | Biru | `bg-blue-100 text-blue-800` |

## Validasi Import

Template import Excel/CSV sekarang hanya menerima nilai:
- Dengan Pujian
- Sangat Memuaskan
- Memuaskan
- (kosong/null)

Nilai lain akan ditolak dengan pesan error:
> "Yudisium harus salah satu dari: Dengan Pujian, Sangat Memuaskan, Memuaskan"

## Testing

### Verifikasi Data
```bash
php artisan tinker --execute="
\$yudisiumCounts = App\Models\Mahasiswa::selectRaw('yudisium, COUNT(*) as count')
    ->whereNotNull('yudisium')
    ->groupBy('yudisium')
    ->get();
foreach (\$yudisiumCounts as \$item) {
    echo \$item->yudisium . ': ' . \$item->count . PHP_EOL;
}
"
```

### Expected Output
```
Dengan Pujian: X mahasiswa
Sangat Memuaskan: Y mahasiswa
Memuaskan: Z mahasiswa
```

## Catatan Penting

1. ✅ Data lama sudah dimigrasi otomatis
2. ✅ Tidak ada breaking changes
3. ✅ Template import/export sudah diupdate
4. ✅ Validasi sudah disesuaikan
5. ✅ Frontend display sudah diupdate
6. ⚠️ User perlu download ulang template import terbaru

## Backward Compatibility

- Data dengan yudisium "Cum Laude" sudah dikonversi ke "Dengan Pujian"
- Import file lama dengan "Cum Laude" akan ditolak (perlu update manual)
- Sistem tidak lagi menerima nilai "Cum Laude"

## Rekomendasi

Untuk user yang memiliki file import lama:
1. Download template import terbaru
2. Ganti semua "Cum Laude" dengan "Dengan Pujian"
3. Upload file yang sudah diupdate
