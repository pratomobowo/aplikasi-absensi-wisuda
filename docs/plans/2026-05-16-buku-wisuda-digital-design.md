# Design: Buku Wisuda Digital - Dual Mode Viewer

## Overview
Halaman publik untuk menampilkan buku wisuda digital dengan 2 mode tampilan:
1. **Flipbook Mode** - Tampilan seperti buku nyata dengan page flip animation
2. **Grid Mode** - Tampilan galeri/card grid untuk browsing cepat

## Features
- Dual mode viewer (Flipbook/Grid)
- Search by nama/NPM/program studi
- Filter by periode wisuda
- Download PDF
- Mobile responsive
- Modern, elegant UI

## Data Display
Per mahasiswa menampilkan:
- Nama Lengkap
- NPM
- Foto Wisuda
- Program Studi
- IPK
- Yudisium (Predikat)

## Design Specs

### Layout Structure
```
[Hero Section]
  - Title: "Buku Wisuda Digital"
  - Subtitle + decorative elements
  - Gradient background (blue to indigo)

[Controls Bar]
  - Search input (debounced)
  - Filter periode (dropdown)
  - Mode toggle: [Flipbook | Grid]
  - Download PDF button

[Content Area - Flipbook Mode]
  - Book container with 3D effect
  - Page flip animation (CSS/JS)
  - Navigation: prev/next, page number input
  - Spread view (2 pages side by side on desktop, 1 on mobile)
  - Cover page + content pages
  - Page content: 
    - Left: Mahasiswa data card
    - Right: Foto + details

[Content Area - Grid Mode]
  - Responsive grid: 1 col (mobile) / 2 col (tablet) / 3-4 col (desktop)
  - Card design:
    - Photo top (aspect ratio 3:4)
    - Name + NPM
    - Prodi + IPK + Yudisium badge
  - Hover effect: lift + shadow
  - Infinite scroll or pagination

[Footer]
  - Navigation help
  - Back to top
```

### Color Scheme
- Primary: Blue gradient (#3B82F6 to #4F46E5)
- Background: Light gray (#F8FAFC)
- Card: White with subtle shadow
- Text: Dark gray (#1E293B)
- Accent: Gold/amber for yudisium badges

### Interactions
- Mode switch: Smooth fade transition
- Flipbook: CSS 3D transform with page curl effect
- Grid: Hover lift animation
- Search: Real-time filter with debounce 300ms
- Download: Progress indicator

### Responsive Breakpoints
- Mobile: < 640px - Single page flipbook, 1 col grid
- Tablet: 640-1024px - Single page flipbook, 2 col grid
- Desktop: > 1024px - Dual page flipbook, 3-4 col grid

### Technical Notes
- Use CSS Grid/Flexbox for layout
- CSS 3D transforms for flipbook (no heavy library needed)
- Lazy load images for performance
- Cache filter results client-side
- PDF generation: reuse existing BukuWisudaService

## Implementation Plan
1. Create Livewire component: `BukuWisudaDigital`
2. Create views: 
   - `livewire/buku-wisuda-digital.blade.php` (main)
   - `livewire/buku-wisuda-flipbook.blade.php` (flipbook mode)
   - `livewire/buku-wisuda-grid.blade.php` (grid mode)
3. Add routes
4. Update navigation
5. Styling with Tailwind CSS
