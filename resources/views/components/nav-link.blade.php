@props([
    'href' => '#',
    'active' => false,
])

@php
    $baseClasses = 'inline-flex items-center px-4 py-2 text-base font-semibold transition-all duration-200 rounded-lg relative';
    $activeClasses = 'text-blue-600 bg-blue-50';
    $inactiveClasses = 'text-gray-700 hover:text-blue-600 hover:bg-blue-50/50';
    
    $classes = $baseClasses . ' ' . ($active ? $activeClasses : $inactiveClasses);
    
    // Auto-detect active state based on current route if not explicitly set
    if (!$active && $href !== '#') {
        $active = request()->is(trim($href, '/')) || request()->is(trim($href, '/').'/*');
        if ($active) {
            $classes = $baseClasses . ' ' . $activeClasses;
        }
    }
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
