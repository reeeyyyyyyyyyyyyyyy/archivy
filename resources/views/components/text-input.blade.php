@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-white border-gray-300 text-gray-900 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm']) }}>
