@props(['type' => 'info', 'messages'])

@php
    $alertClasses = [
        'success' => 'bg-green-50 border-green-200 text-green-700',
        'error' => 'bg-red-50 border-red-200 text-red-700',
        'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-700',
        'info' => 'bg-blue-50 border-blue-200 text-blue-700',
    ];

    $iconClasses = [
        'success' => 'fas fa-check-circle text-green-500',
        'error' => 'fas fa-exclamation-triangle text-red-500',
        'warning' => 'fas fa-exclamation-triangle text-yellow-500',
        'info' => 'fas fa-info-circle text-blue-500',
    ];

    $alertClass = $alertClasses[$type] ?? $alertClasses['info'];
    $iconClass = $iconClasses[$type] ?? $iconClasses['info'];
@endphp

@if ($messages)
    <div {{ $attributes->merge(['class' => 'mb-4']) }}>
        @foreach ((array) $messages as $message)
            <div class="flex items-center p-4 border rounded-lg {{ $alertClass }}">
                <div class="flex-shrink-0">
                    <i class="{{ $iconClass }}"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">{{ $message }}</p>
                </div>
            </div>
        @endforeach
    </div>
@endif
