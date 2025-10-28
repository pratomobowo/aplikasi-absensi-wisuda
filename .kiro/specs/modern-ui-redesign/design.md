# Design Document - Modern UI Redesign

## Overview

Redesign UI aplikasi Sistem Absensi Wisuda dengan pendekatan modern dan elegan menggunakan skema warna dominan biru. Implementasi akan menggunakan Tailwind CSS yang sudah tersedia di Laravel, dengan customisasi Filament admin panel untuk konsistensi visual di seluruh aplikasi.

## Architecture

### Technology Stack
- **Frontend Framework**: Laravel Blade Templates
- **CSS Framework**: Tailwind CSS v4
- **Admin Panel**: Filament v3
- **Component Library**: Livewire v3
- **Font**: Inter atau Poppins (modern sans-serif)

### Design System Foundation

#### Color Palette (Modern Blue Theme)
```css
Primary Colors:
- Blue-50: #EFF6FF (backgrounds, subtle highlights)
- Blue-100: #DBEAFE (hover states, light backgrounds)
- Blue-500: #3B82F6 (primary actions, links)
- Blue-600: #2563EB (primary button, main brand color)
- Blue-700: #1D4ED8 (hover states for primary)
- Blue-800: #1E40AF (dark accents)
- Blue-900: #1E3A8A (text on light backgrounds)

Neutral Colors:
- Gray-50: #F9FAFB (page backgrounds)
- Gray-100: #F3F4F6 (card backgrounds)
- Gray-200: #E5E7EB (borders)
- Gray-600: #4B5563 (secondary text)
- Gray-900: #111827 (primary text)
- White: #FFFFFF (cards, modals)

Semantic Colors:
- Success: Green-500 (#10B981)
- Error: Red-500 (#EF4444)
- Warning: Amber-500 (#F59E0B)
```

#### Typography Scale
```css
Font Family: 'Inter', sans-serif
Weights: 400 (regular), 500 (medium), 600 (semibold), 700 (bold)

Headings:
- H1: text-4xl (36px) / font-bold / leading-tight
- H2: text-3xl (30px) / font-bold / leading-tight
- H3: text-2xl (24px) / font-semibold / leading-snug
- H4: text-xl (20px) / font-semibold / leading-snug
- H5: text-lg (18px) / font-medium / leading-normal

Body:
- Large: text-lg (18px) / font-normal / leading-relaxed
- Base: text-base (16px) / font-normal / leading-normal
- Small: text-sm (14px) / font-normal / leading-normal
- XSmall: text-xs (12px) / font-normal / leading-tight
```

#### Spacing System
```css
Base unit: 4px (Tailwind default)
Scale: 4, 8, 12, 16, 20, 24, 32, 40, 48, 64, 80, 96, 128px
Usage:
- Component padding: 16px, 24px, 32px
- Section spacing: 48px, 64px, 96px
- Element gaps: 8px, 12px, 16px
```

#### Border Radius
```css
- sm: 4px (small elements, badges)
- md: 8px (buttons, inputs)
- lg: 12px (cards, modals)
- xl: 16px (large cards, hero sections)
- 2xl: 24px (special containers)
- full: 9999px (pills, avatars)
```

#### Shadow System
```css
- sm: 0 1px 2px 0 rgb(0 0 0 / 0.05) (subtle elevation)
- md: 0 4px 6px -1px rgb(0 0 0 / 0.1) (cards)
- lg: 0 10px 15px -3px rgb(0 0 0 / 0.1) (modals, dropdowns)
- xl: 0 20px 25px -5px rgb(0 0 0 / 0.1) (floating elements)
- 2xl: 0 25px 50px -12px rgb(0 0 0 / 0.25) (hero sections)
```

#### Animation & Transitions
```css
Duration:
- Fast: 150ms (hover states, small changes)
- Normal: 300ms (page transitions, modals)
- Slow: 500ms (complex animations)

Easing:
- ease-in-out: cubic-bezier(0.4, 0, 0.2, 1) (default)
- ease-out: cubic-bezier(0, 0, 0.2, 1) (entrances)
- ease-in: cubic-bezier(0.4, 0, 1, 1) (exits)
```

## Components and Interfaces

### 1. Homepage (Welcome Page)

#### Layout Structure
```
┌─────────────────────────────────────────────────────────┐
│  Logo    Beranda | Data | Alur | Buku | Help    Login  │
├─────────────────────────────────────────────────────────┤
│                                                         │
│              Hero Section                               │
│         (Gradient Blue Background)                      │
│                                                         │
├─────────────────────────────────────────────────────────┤
│                                                         │
│           Features Section                              │
│                                                         │
├─────────────────────────────────────────────────────────┤
│              Footer                                     │
└─────────────────────────────────────────────────────────┘
```

#### Component Specifications

**Navigation Bar**
- Position: Fixed top, transparent with backdrop blur
- Height: 64px (desktop), 56px (mobile)
- Background: bg-white/80 backdrop-blur-md
- Border: border-b border-gray-200
- Shadow: shadow-sm on scroll
- Layout: 
  - Logo (left)
  - Navigation Menu (center):
    1. Beranda - Link to homepage
    2. Data Wisudawan - Link to graduates data page
    3. Alur Wisuda - Link to graduation flow/process page
    4. Buku Wisuda - Link to graduation book/catalog
    5. Help Desk - Link to help/support page
  - Login button (right)
- Menu Styling:
  - Desktop: Horizontal menu with gap-8
  - Mobile: Hamburger menu (drawer from right)
  - Link style: text-gray-700 hover:text-blue-600 font-medium
  - Active state: text-blue-600 border-b-2 border-blue-600
  - Transition: transition-colors duration-200

**Hero Section**
- Background: Gradient from blue-600 to blue-800
- Height: min-h-screen (full viewport)
- Layout: Centered content with max-w-4xl
- Elements:
  - Heading (H1): text-5xl md:text-6xl, text-white, font-bold
  - Subheading: text-xl md:text-2xl, text-blue-100
  - CTA Button: bg-white text-blue-600, hover:bg-blue-50
  - Decorative elements: Subtle geometric patterns or illustrations

**Features Section**
- Background: bg-gray-50
- Padding: py-24 md:py-32
- Grid: 3 columns on desktop, 1 on mobile
- Feature Cards:
  - Background: bg-white
  - Border radius: rounded-xl
  - Shadow: shadow-md hover:shadow-lg
  - Padding: p-8
  - Icon: w-12 h-12, text-blue-600
  - Title: text-xl font-semibold text-gray-900
  - Description: text-gray-600

### 2. Login Page

#### Layout Structure
```
┌─────────────────────────────────────┐
│                                     │
│     Centered Login Card             │
│   (Blue gradient background)        │
│                                     │
└─────────────────────────────────────┘
```

#### Component Specifications

**Page Container**
- Background: Gradient from blue-50 to blue-100
- Layout: Flexbox centered (min-h-screen)
- Padding: p-4 md:p-8

**Login Card**
- Width: w-full max-w-md
- Background: bg-white
- Border radius: rounded-2xl
- Shadow: shadow-2xl
- Padding: p-8 md:p-10

**Card Header**
- Logo/Icon: mb-8, centered
- Title: text-3xl font-bold text-gray-900
- Subtitle: text-gray-600 text-center

**Form Elements**
- Input Fields:
  - Border: border-2 border-gray-200
  - Focus: focus:border-blue-500 focus:ring-4 focus:ring-blue-100
  - Border radius: rounded-lg
  - Padding: px-4 py-3
  - Font size: text-base
  - Transition: transition-all duration-200

- Labels:
  - Font: text-sm font-medium text-gray-700
  - Margin: mb-2

- Login Button:
  - Background: bg-blue-600
  - Hover: hover:bg-blue-700
  - Text: text-white font-semibold
  - Padding: px-6 py-3
  - Border radius: rounded-lg
  - Width: w-full
  - Shadow: shadow-md hover:shadow-lg
  - Transition: transition-all duration-200

- Error Messages:
  - Background: bg-red-50
  - Border: border-l-4 border-red-500
  - Text: text-red-700
  - Padding: p-4
  - Border radius: rounded-md
  - Margin: mb-4

### 3. Admin Dashboard (Filament Customization)

#### Filament Theme Configuration

**Primary Color Override**
```php
'primary' => [
    50 => '239, 246, 255',   // blue-50
    100 => '219, 234, 254',  // blue-100
    200 => '191, 219, 254',  // blue-200
    300 => '147, 197, 253',  // blue-300
    400 => '96, 165, 250',   // blue-400
    500 => '59, 130, 246',   // blue-500
    600 => '37, 99, 235',    // blue-600 (main)
    700 => '29, 78, 216',    // blue-700
    800 => '30, 64, 175',    // blue-800
    900 => '30, 58, 138',    // blue-900
    950 => '23, 37, 84',     // blue-950
]
```

#### Layout Customization

**Sidebar**
- Background: bg-blue-900
- Width: 280px (desktop), full-width drawer (mobile)
- Logo area: p-6, border-b border-blue-800
- Navigation items:
  - Default: text-blue-100 hover:bg-blue-800
  - Active: bg-blue-700 text-white
  - Icon: text-blue-300
  - Border radius: rounded-lg
  - Padding: px-4 py-3

**Top Navigation**
- Background: bg-white
- Border: border-b border-gray-200
- Height: 64px
- Shadow: shadow-sm
- User menu: Aligned right with avatar

**Content Area**
- Background: bg-gray-50
- Padding: p-6 md:p-8
- Max width: max-w-7xl mx-auto

**Widget Cards**
- Background: bg-white
- Border radius: rounded-xl
- Shadow: shadow-md
- Padding: p-6
- Border: border border-gray-200
- Hover: hover:shadow-lg transition-shadow

**Tables**
- Header: bg-gray-50 text-gray-700 font-semibold
- Rows: hover:bg-blue-50 transition-colors
- Borders: border-b border-gray-200
- Actions: text-blue-600 hover:text-blue-700

**Forms**
- Field groups: space-y-6
- Input styling: Same as login page
- Section headers: text-lg font-semibold text-gray-900 mb-4
- Help text: text-sm text-gray-500

**Buttons**
- Primary: bg-blue-600 hover:bg-blue-700 text-white
- Secondary: bg-gray-200 hover:bg-gray-300 text-gray-900
- Danger: bg-red-600 hover:bg-red-700 text-white
- All: rounded-lg px-4 py-2 font-medium transition-colors

### 4. Scanner Page (Livewire Component)

#### Layout Structure
```
┌─────────────────────────────────────┐
│         Header Bar                  │
├─────────────────────────────────────┤
│                                     │
│      Camera Preview                 │
│   (Blue border, centered)           │
│                                     │
├─────────────────────────────────────┤
│                                     │
│      Status Card                    │
│   (Result display area)             │
│                                     │
└─────────────────────────────────────┘
```

#### Component Specifications

**Page Container**
- Background: bg-gray-50
- Layout: Full height (min-h-screen)
- Padding: p-4 md:p-6

**Header Bar**
- Background: bg-white
- Border: border-b border-gray-200
- Padding: px-6 py-4
- Shadow: shadow-sm
- Content: Title (left), Logout button (right)

**Camera Container**
- Width: w-full max-w-2xl mx-auto
- Aspect ratio: aspect-video
- Border: border-4 border-blue-600
- Border radius: rounded-2xl
- Shadow: shadow-2xl
- Overflow: overflow-hidden
- Background: bg-black (when camera loading)

**Scanning Overlay**
- Position: Absolute overlay on camera
- Border: Animated scanning line
- Color: border-blue-500
- Animation: Pulse effect when scanning

**Status Card**
- Width: w-full max-w-2xl mx-auto
- Margin: mt-6
- Border radius: rounded-xl
- Padding: p-6
- Shadow: shadow-lg

**Status States**
- Ready:
  - Background: bg-blue-50
  - Border: border-l-4 border-blue-500
  - Icon: text-blue-600
  - Text: text-blue-900

- Success:
  - Background: bg-green-50
  - Border: border-l-4 border-green-500
  - Icon: text-green-600 (checkmark with animation)
  - Text: text-green-900
  - Animation: Scale in + fade in

- Error:
  - Background: bg-red-50
  - Border: border-l-4 border-red-500
  - Icon: text-red-600 (X with shake animation)
  - Text: text-red-900
  - Animation: Shake effect

**Result Display**
- Layout: Grid with 2 columns on desktop
- Labels: text-sm font-medium text-gray-600
- Values: text-base font-semibold text-gray-900
- Dividers: border-t border-gray-200 pt-4 mt-4

**Action Buttons**
- Scan Again: bg-blue-600 hover:bg-blue-700 text-white
- View Details: bg-gray-200 hover:bg-gray-300 text-gray-900
- Full width on mobile, inline on desktop
- Border radius: rounded-lg
- Padding: px-6 py-3

## Data Models

### Theme Configuration Model
```php
// config/theme.php
return [
    'colors' => [
        'primary' => '#2563EB',    // blue-600
        'secondary' => '#64748B',  // gray-500
        'success' => '#10B981',    // green-500
        'error' => '#EF4444',      // red-500
        'warning' => '#F59E0B',    // amber-500
    ],
    'fonts' => [
        'sans' => ['Inter', 'sans-serif'],
        'mono' => ['Fira Code', 'monospace'],
    ],
    'spacing' => [
        'section' => '4rem',
        'component' => '2rem',
        'element' => '1rem',
    ],
];
```

### Filament Theme Provider
```php
// app/Providers/Filament/AdminPanelProvider.php
public function panel(Panel $panel): Panel
{
    return $panel
        ->colors([
            'primary' => Color::Blue,
        ])
        ->font('Inter')
        ->brandName('Sistem Absensi Wisuda')
        ->brandLogo(asset('images/logo.svg'))
        ->darkMode(false)
        ->sidebarCollapsibleOnDesktop()
        ->navigationGroups([
            'Manajemen Data',
            'Laporan',
            'Pengaturan',
        ]);
}
```

## Error Handling

### Visual Error States

**Form Validation Errors**
- Display: Below input field
- Style: text-sm text-red-600 mt-1
- Icon: Small warning icon
- Animation: Fade in from top

**Page-Level Errors**
- Display: Toast notification (top-right)
- Background: bg-red-50 border-l-4 border-red-500
- Duration: 5 seconds auto-dismiss
- Action: Close button (X)

**Network Errors**
- Display: Modal overlay
- Background: bg-white rounded-xl shadow-2xl
- Content: Error icon, message, retry button
- Backdrop: bg-gray-900/50 backdrop-blur-sm

## Testing Strategy

### Visual Regression Testing
- Tool: Percy atau Chromatic
- Coverage: All major pages and components
- Breakpoints: Mobile (375px), Tablet (768px), Desktop (1440px)

### Responsive Testing
- Devices: iPhone SE, iPad, Desktop (1920px)
- Orientations: Portrait and landscape
- Browser: Chrome, Safari, Firefox

### Accessibility Testing
- Color contrast: WCAG AA compliance
- Keyboard navigation: All interactive elements
- Screen reader: ARIA labels and semantic HTML
- Focus indicators: Visible focus rings

### Component Testing
- Unit tests: Livewire components
- Integration tests: Form submissions, navigation
- E2E tests: Complete user flows (login, scan, view data)

### Performance Testing
- Lighthouse scores: Target 90+ for all metrics
- Page load time: < 2 seconds
- First contentful paint: < 1 second
- Time to interactive: < 3 seconds

## Implementation Notes

### Tailwind Configuration
```javascript
// tailwind.config.js (to be created)
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './app/Filament/**/*.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    50: '#eff6ff',
                    100: '#dbeafe',
                    200: '#bfdbfe',
                    300: '#93c5fd',
                    400: '#60a5fa',
                    500: '#3b82f6',
                    600: '#2563eb',
                    700: '#1d4ed8',
                    800: '#1e40af',
                    900: '#1e3a8a',
                    950: '#172554',
                },
            },
            fontFamily: {
                sans: ['Inter', 'sans-serif'],
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
}
```

### Font Integration
```html
<!-- In layout head -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
```

### CSS Custom Properties
```css
/* resources/css/app.css */
@import 'tailwindcss';

:root {
    --color-primary: #2563eb;
    --color-primary-hover: #1d4ed8;
    --transition-base: 200ms ease-in-out;
    --shadow-card: 0 4px 6px -1px rgb(0 0 0 / 0.1);
}

/* Custom animations */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-slide-in {
    animation: slideIn 300ms ease-out;
}
```

### Responsive Breakpoints
```css
/* Mobile First Approach */
/* Default: Mobile (< 640px) */
/* sm: 640px */
/* md: 768px */
/* lg: 1024px */
/* xl: 1280px */
/* 2xl: 1536px */
```

### Component Reusability
Create Blade components for common UI elements:
- `<x-button>` - Styled buttons with variants
- `<x-card>` - Card container with consistent styling
- `<x-input>` - Form inputs with validation states
- `<x-badge>` - Status badges with color variants
- `<x-alert>` - Alert messages with types
- `<x-nav-link>` - Navigation menu links with active states
- `<x-mobile-menu>` - Mobile hamburger menu drawer

### Browser Support
- Chrome: Last 2 versions
- Firefox: Last 2 versions
- Safari: Last 2 versions
- Edge: Last 2 versions
- Mobile Safari: iOS 13+
- Chrome Mobile: Android 8+

### Performance Optimizations
- Lazy load images with `loading="lazy"`
- Use CSS containment for isolated components
- Minimize JavaScript bundle size
- Use Tailwind's purge for production
- Optimize font loading with `font-display: swap`
- Implement service worker for offline support (optional)
