@props([
    'variant' => 'info',
    'color' => null,
    'size' => 'md',
])

@php
    // Support both 'variant' and 'color' props for backwards compatibility
    $colorValue = $color ?? $variant;
    
    $baseClasses = 'inline-flex items-center font-medium';
    
    $variantClasses = [
        'success' => 'bg-green-100 text-green-800',
        'error' => 'bg-red-100 text-red-800',
        'warning' => 'bg-amber-100 text-amber-800',
        'info' => 'bg-blue-100 text-blue-800',
        'blue' => 'bg-blue-100 text-blue-800',
        'green' => 'bg-green-100 text-green-800',
        'purple' => 'bg-purple-100 text-purple-800',
        'amber' => 'bg-amber-100 text-amber-800',
        'red' => 'bg-red-100 text-red-800',
        'indigo' => 'bg-indigo-100 text-indigo-800',
    ];
    
    $sizeClasses = [
        'sm' => 'px-2 py-0.5 text-xs rounded',
        'md' => 'px-2.5 py-1 text-sm rounded-md',
        'lg' => 'px-3 py-1.5 text-base rounded-lg',
    ];
    
    $classes = $baseClasses . ' ' . ($variantClasses[$colorValue] ?? $variantClasses['info']) . ' ' . ($sizeClasses[$size] ?? $sizeClasses['md']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
