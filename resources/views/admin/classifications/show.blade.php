<!-- resources/views/admin/categories/show.blade.php -->

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Category Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ __('Category Details') }}
                    </h2>
                    <div class="mt-4">
                        <p class="text-gray-600 dark:text-gray-400">
                            <strong>{{ __('Code') }}:</strong> {{ $category->code }}
                        </p>
                        <p class="text-gray-600 dark:text-gray-400">
                            <strong>{{ __('Name') }}:</strong> {{ $category->name }}
                        </p>
                        <p class="text-gray-600 dark:text-gray-400">
                            <strong>{{ __('Description') }}:</strong> {{ $category->description }}
                        </p>
                        <p class="text-gray-600 dark:text-gray-400">
                            <strong>{{ __('Retention Active') }}:</strong> {{ $classification->retention_aktif }}
                        </p>
                        <p class="text-gray-600 dark:text-gray-400">
                            <strong>{{ __('Retention Inactive') }}:</strong> {{ $category->retention_inactive }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
