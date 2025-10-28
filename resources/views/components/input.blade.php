@props([
    'label' => null,
    'error' => null,
    'helpText' => null,
    'id' => null,
    'name' => null,
    'type' => 'text',
])

@php
    $inputId = $id ?? $name ?? 'input-' . uniqid();
    $baseClasses = 'w-full px-4 py-3 text-base border-2 rounded-lg transition-all duration-200 focus:outline-none';
    $normalClasses = 'border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100';
    $errorClasses = 'border-red-500 focus:border-red-500 focus:ring-4 focus:ring-red-100';
    
    $classes = $baseClasses . ' ' . ($error ? $errorClasses : $normalClasses);
@endphp

<div {{ $attributes->only('class') }}>
    @if($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-700 mb-2">
            {{ $label }}
        </label>
    @endif

    <input 
        type="{{ $type }}"
        id="{{ $inputId }}"
        @if($name) name="{{ $name }}" @endif
        {{ $attributes->except(['class', 'label', 'error', 'helpText'])->merge(['class' => $classes]) }}
    >

    @if($error)
        <p class="mt-1 text-sm text-red-600">
            {{ $error }}
        </p>
    @endif

    @if($helpText && !$error)
        <p class="mt-1 text-sm text-gray-500">
            {{ $helpText }}
        </p>
    @endif
</div>
