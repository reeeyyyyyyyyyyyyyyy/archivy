@props(['messages' => null])

@if ($messages || $slot->isNotEmpty())
    <div {{ $attributes->merge(['class' => 'mb-4']) }}>
        @if ($messages)
            @foreach ((array) $messages as $message)
                <div class="flex items-center p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700 font-medium">{{ $message }}</p>
                    </div>
                </div>
            @endforeach
        @else
            <div class="flex items-center p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700 font-medium">{{ $slot }}</p>
                </div>
            </div>
        @endif
    </div>
@endif
