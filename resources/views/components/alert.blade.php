@props([
    'type' => 'info',
    'dismissible' => false,
])

@php
    $baseClasses = 'p-4 rounded-md border-l-4 flex items-start gap-3';
    
    $typeClasses = [
        'success' => 'bg-green-50 border-green-500 text-green-900',
        'error' => 'bg-red-50 border-red-500 text-red-900',
        'warning' => 'bg-amber-50 border-amber-500 text-amber-900',
        'info' => 'bg-blue-50 border-blue-500 text-blue-900',
    ];
    
    $iconPaths = [
        'success' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'error' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
        'warning' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        'info' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    ];
    
    $iconColors = [
        'success' => 'text-green-600',
        'error' => 'text-red-600',
        'warning' => 'text-amber-600',
        'info' => 'text-blue-600',
    ];
    
    $classes = $baseClasses . ' ' . ($typeClasses[$type] ?? $typeClasses['info']);
    $iconColor = $iconColors[$type] ?? $iconColors['info'];
    $iconPath = $iconPaths[$type] ?? $iconPaths['info'];
@endphp

<div {{ $attributes->merge(['class' => $classes]) }} x-data="{ show: true }" x-show="show" x-transition>
    <div class="flex-shrink-0">
        <svg class="w-5 h-5 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}"></path>
        </svg>
    </div>
    
    <div class="flex-1">
        {{ $slot }}
    </div>
    
    @if($dismissible)
        <button 
            type="button" 
            @click="show = false"
            class="flex-shrink-0 ml-auto -mx-1.5 -my-1.5 rounded-lg p-1.5 inline-flex h-8 w-8 hover:bg-black/5 transition-colors duration-150"
        >
            <span class="sr-only">Dismiss</span>
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
        </button>
    @endif
</div>
