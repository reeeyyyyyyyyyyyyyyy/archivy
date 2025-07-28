@props(['messages'])

@if ($messages)
    <div {{ $attributes->merge(['class' => 'mb-4']) }}>
        @foreach ((array) $messages as $message)
            <div class="flex items-center p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700 font-medium">{{ $message }}</p>
                </div>
            </div>
        @endforeach
    </div>
@endif
