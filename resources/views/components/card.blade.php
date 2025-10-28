@props([
    'shadow' => 'md',
    'padding' => 'default',
])

@php
    $baseClasses = 'bg-white border border-gray-200 transition-shadow duration-200';
    
    $shadowClasses = [
        'sm' => 'shadow-sm hover:shadow-md rounded-lg',
        'md' => 'shadow-md hover:shadow-lg rounded-xl',
        'lg' => 'shadow-lg hover:shadow-xl rounded-xl',
        'xl' => 'shadow-xl hover:shadow-2xl rounded-2xl',
    ];
    
    $paddingClasses = [
        'none' => '',
        'sm' => 'p-4',
        'default' => 'p-6',
        'lg' => 'p-8',
    ];
    
    $classes = $baseClasses . ' ' . ($shadowClasses[$shadow] ?? $shadowClasses['md']) . ' ' . ($paddingClasses[$padding] ?? $paddingClasses['default']);
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    @isset($header)
        <div class="border-b border-gray-200 pb-4 mb-4">
            {{ $header }}
        </div>
    @endisset

    {{ $slot }}

    @isset($footer)
        <div class="border-t border-gray-200 pt-4 mt-4">
            {{ $footer }}
        </div>
    @endisset
</div>
