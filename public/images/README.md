# Images Directory

Folder ini digunakan untuk menyimpan gambar-gambar yang digunakan di website.

## Struktur Folder
- `/public/images/` - Gambar yang dapat diakses langsung dari browser
- `/public/images/hero/` - Gambar untuk hero section
- `/public/images/icons/` - Icon dan logo
- `/public/images/gallery/` - Galeri foto wisuda

## Penggunaan
Gambar di folder ini dapat diakses melalui URL:
```
{{ asset('images/nama-file.jpg') }}
```

## Catatan
- Gunakan format WebP untuk performa lebih baik
- Kompres gambar sebelum upload
- Gunakan nama file yang deskriptif (contoh: `wisuda-2024-hero.jpg`)
