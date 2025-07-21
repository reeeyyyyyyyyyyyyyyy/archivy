<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Archive Details
        </h2>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Main Details -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Description</h3>
                    <p class="mt-1 text-gray-600 dark:text-gray-400">{{ $archive->description }}</p>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Classification</h3>
                            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ $archive->category->classification->name ?? 'N/A' }} ({{ $archive->category->classification->code ?? 'N/A' }})</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Category</h3>
                            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ $archive->category->name ?? 'N/A' }}</p>
                        </div>
                         <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Period</h3>
                            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ $archive->kurun_waktu_start }} to {{ $archive->kurun_waktu_end ?? 'present' }}</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">File Count</h3>
                            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ $archive->jumlah_berkas }}</p>
                        </div>
                    </div>
                </div>
                <!-- Retention & Status -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Status & Retention</h3>
                     <div class="mt-4">
                        <p class="text-sm font-medium text-gray-500">Status</p>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if($archive->status == 'aktif') bg-green-100 text-green-800 @endif
                            @if($archive->status == 'inaktif') bg-yellow-100 text-yellow-800 @endif
                            @if($archive->status == 'inaktif_permanen') bg-blue-100 text-blue-800 @endif
                            @if($archive->status == 'musnah') bg-red-100 text-red-800 @endif">
                            {{ ucfirst(str_replace('_', ' ', $archive->status)) }}
                        </span>
                    </div>
                    <div class="mt-4">
                        <p class="text-sm font-medium text-gray-500">Active Retention</p>
                        <p class="mt-1 text-gray-600 dark:text-gray-400">{{ $archive->retention_active }} years</p>
                        <p class="text-xs text-gray-500">Due: {{ $archive->transition_active_due }}</p>
                    </div>
                    <div class="mt-4">
                        <p class="text-sm font-medium text-gray-500">Inactive Retention</p>
                        <p class="mt-1 text-gray-600 dark:text-gray-400">{{ $archive->retention_inactive }} years</p>
                        <p class="text-xs text-gray-500">Due: {{ $archive->transition_inactive_due }}</p>
                    </div>
                     <div class="mt-4 border-t pt-4">
                        <p class="text-sm font-medium text-gray-500">Created by</p>
                        <p class="mt-1 text-gray-600 dark:text-gray-400">{{ $archive->creator->name ?? 'N/A' }} on {{ $archive->created_at->format('d M Y') }}</p>
                    </div>
                     <div class="mt-4">
                        <p class="text-sm font-medium text-gray-500">Last updated by</p>
                        <p class="mt-1 text-gray-600 dark:text-gray-400">{{ $archive->updater->name ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <a href="{{ route('admin.archives.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to List
                </a>
            </div>
        </div>
    </div>
</x-app-layout> 