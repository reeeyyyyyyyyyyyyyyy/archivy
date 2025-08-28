<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-600 to-pink-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-excel text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Export Data Arsip</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-download mr-1"></i>Ekspor data arsip Anda ke format Excel dengan filter periode
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('intern.archives.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Arsip
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <!-- Welcome Section -->
            <div class="bg-gradient-to-r from-orange-50 to-pink-100 rounded-xl shadow-sm border border-orange-200 p-6 mb-8">
                <div class="flex items-center mb-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-orange-600 to-pink-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-download text-white text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Preview Export Data Arsip</h3>
                        <p class="text-gray-600 mt-1">Preview fitur export arsip untuk pembelajaran (mode intern)</p>
                    </div>
                </div>

                <!-- User Role Info -->
                <div class="flex items-center space-x-4 text-sm">
                    <div class="flex items-center">
                        <i class="fas fa-user-circle mr-2 text-orange-600"></i>
                        <span class="font-medium text-gray-700">{{ auth()->user()->name }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-graduation-cap mr-2 text-pink-600"></i>
                        <span class="font-medium text-gray-700">Intern</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-calendar mr-2 text-orange-600"></i>
                        <span class="font-medium text-gray-700">{{ now()->format('d F Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Export Options Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($statuses as $key => $label)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-all duration-300 hover:border-orange-300">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                @php
                                    $config = match($key) {
                                        'all' => ['icon' => 'fas fa-archive', 'color' => 'text-gray-600', 'bg' => 'bg-gray-100'],
                                        'aktif' => ['icon' => 'fas fa-play-circle', 'color' => 'text-green-600', 'bg' => 'bg-green-100'],
                                        'inaktif' => ['icon' => 'fas fa-pause-circle', 'color' => 'text-yellow-600', 'bg' => 'bg-yellow-100'],
                                        'permanen' => ['icon' => 'fas fa-shield-alt', 'color' => 'text-purple-600', 'bg' => 'bg-purple-100'],
                                        'musnah' => ['icon' => 'fas fa-ban', 'color' => 'text-red-600', 'bg' => 'bg-red-100'],
                                        default => ['icon' => 'fas fa-file', 'color' => 'text-gray-600', 'bg' => 'bg-gray-100']
                                    };
                                @endphp
                                <div class="w-12 h-12 {{ $config['bg'] }} rounded-lg flex items-center justify-center mr-3">
                                    <i class="{{ $config['icon'] }} {{ $config['color'] }} text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900">{{ $label }}</h4>
                                    <p class="text-sm text-gray-500">{{ $archiveCounts[$key] ?? 0 }} Arsip</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <!-- Advanced Export Button Only -->
                            <a href="{{ route('intern.archives.export-form', $key) }}"
                               class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-orange-600 to-pink-600 hover:from-orange-700 hover:to-pink-700 text-white font-medium rounded-lg transition-all shadow-lg hover:shadow-xl">
                                <i class="fas fa-file-excel mr-2"></i>
                                Export Excel
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
