@props(['messages'])

@if ($messages)
    <div {{ $attributes->merge(['class' => 'mt-2']) }}>
        @foreach ((array) $messages as $message)
            <div class="flex items-center p-3 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700 font-medium">{{ $message }}</p>
                </div>
            </div>
        @endforeach
    </div>
@endif
