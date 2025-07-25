<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <!-- Left Side - Title & Intern Info -->
                <div class="flex-1">
                    <div class="flex items-baseline space-x-4">
                        <h1 class="text-2xl font-bold text-gray-900">Dashboard Intern</h1>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Selamat datang di ARSIPIN - Sistem Arsip Digital</p>
                </div>

                <!-- Right Side - User Info -->
                <div class="flex items-center space-x-6">
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                    </div>

                    <!-- Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="w-10 h-10 bg-gradient-to-br from-orange-600 to-pink-600 rounded-full flex items-center justify-center">
                                <span class="text-white font-semibold text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            </div>
                            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open"
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50">

                            <div class="py-2">
                                <a href="{{ route('profile.edit') }}"
                                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-user-cog mr-3 text-gray-400"></i>
                                    Edit Profile
                                </a>

                                <div class="border-t border-gray-100 my-1"></div>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <i class="fas fa-sign-out-alt mr-3 text-red-400"></i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Dashboard Content -->
    <div class="p-6 space-y-8 max-w-full">

        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-orange-600 to-pink-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    @php
                        $hour = now()->hour;
                        $greeting = $hour < 12 ? 'Selamat Pagi' : ($hour < 15 ? 'Selamat Siang' : ($hour < 18 ? 'Selamat Sore' : 'Selamat Malam'));
                    @endphp
                    <h2 class="text-2xl font-bold mb-2">{{ $greeting }}, {{ Auth::user()->name }}! 👋</h2>
                    <p class="text-orange-100">Selamat bekerja di ARSIPIN! Mari bantu pengelolaan arsip digital.</p>
                </div>
                <div class="text-right text-orange-100">
                    <p class="text-lg font-semibold">{{ now()->format('H:i') }} WIB</p>
                    <p class="text-sm">{{ now()->translatedFormat('l, d F Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Work Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- My Work -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Kontribusi Saya</p>
                        <p class="text-3xl font-bold text-orange-600 mt-2">{{ $myTotalArchives ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">Arsip yang saya input</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-edit text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- This Month -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Bulan Ini</p>
                        <p class="text-3xl font-bold text-pink-600 mt-2">{{ $thisMonthArchives ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ now()->format('F Y') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- This Week -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Minggu Ini</p>
                        <p class="text-3xl font-bold text-orange-500 mt-2">{{ $weeklyContribution ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">Kontribusi mingguan</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-orange-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-chart-line text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Today -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Hari Ini</p>
                        <p class="text-3xl font-bold text-pink-500 mt-2">{{ $todayContribution ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ now()->format('d M Y') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-pink-400 to-pink-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-tasks text-white text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-bolt mr-2 text-orange-500"></i>
                    Aksi Cepat
                </h3>
                <div class="space-y-4">
                    <a href="{{ route('intern.archives.index') }}"
                       class="flex items-center justify-between p-4 bg-orange-50 hover:bg-orange-100 rounded-lg transition-colors group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-archive text-white"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 group-hover:text-orange-600">Lihat Arsip</p>
                                <p class="text-xs text-gray-500">Daftar semua arsip</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-orange-500"></i>
                    </a>

                    <a href="{{ route('intern.archives.create') }}"
                       class="flex items-center justify-between p-4 bg-pink-50 hover:bg-pink-100 rounded-lg transition-colors group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-pink-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-plus-circle text-white"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 group-hover:text-pink-600">Tambah Arsip</p>
                                <p class="text-xs text-gray-500">Input arsip baru</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-pink-500"></i>
                    </a>

                    <a href="{{ route('intern.search.index') }}"
                       class="flex items-center justify-between p-4 bg-orange-50 hover:bg-orange-100 rounded-lg transition-colors group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-orange-600 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-search text-white"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 group-hover:text-orange-600">Cari Arsip</p>
                                <p class="text-xs text-gray-500">Pencarian arsip</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-orange-500"></i>
                    </a>

                    <a href="{{ route('intern.export.index') }}"
                       class="flex items-center justify-between p-4 bg-pink-50 hover:bg-pink-100 rounded-lg transition-colors group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-pink-600 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-file-excel text-white"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 group-hover:text-pink-600">Export Data</p>
                                <p class="text-xs text-gray-500">Download arsip</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-pink-500"></i>
                    </a>
                </div>
            </div>

            <!-- My Personal Archive Progress -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-chart-pie mr-2 text-pink-500"></i>
                    Progress Input Arsip Saya
                </h3>
                <div class="space-y-6">
                    <!-- Personal Stats Summary -->
                    <div class="text-center">
                        <div class="w-20 h-20 bg-gradient-to-br from-orange-100 to-pink-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-user-edit text-orange-600 text-3xl"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900">Status Input Arsip</h4>
                        <p class="text-sm text-gray-600 mt-2">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 mt-1">Role: Intern</p>
                    </div>

                    <!-- Progress Bars -->
                    <div class="space-y-4">
                        <!-- Monthly Target -->
                        <div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-600">Target Bulan</span>
                                <span class="font-medium">{{ $thisMonthArchives ?? 0 }}/15</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-gradient-to-r from-orange-500 to-pink-500 h-3 rounded-full transition-all duration-500"
                                     style="width: {{ min(100, ($thisMonthArchives ?? 0) / 15 * 100) }}%"></div>
                            </div>
                        </div>

                        <!-- Weekly Target -->
                        <div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-600">Target Minggu</span>
                                <span class="font-medium">{{ $weeklyContribution ?? 0 }}/4</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-gradient-to-r from-pink-500 to-orange-500 h-3 rounded-full transition-all duration-500"
                                     style="width: {{ min(100, ($weeklyContribution ?? 0) / 4 * 100) }}%"></div>
                            </div>
                        </div>

                        <!-- Archive Status Distribution -->
                        <div class="pt-4">
                            <h5 class="text-sm font-medium text-gray-700 mb-3">Distribusi Status Arsip Saya</h5>
                            <div class="space-y-2">
                                @php
                                    $myArchiveStats = [
                                        'Aktif' => ['count' => $myActiveArchives ?? 0, 'color' => 'bg-green-500'],
                                        'Inaktif' => ['count' => $myInactiveArchives ?? 0, 'color' => 'bg-yellow-500'],
                                        'Permanen' => ['count' => $myPermanentArchives ?? 0, 'color' => 'bg-purple-500'],
                                        'Musnah' => ['count' => $myDestroyedArchives ?? 0, 'color' => 'bg-red-500']
                                    ];
                                @endphp
                                @foreach($myArchiveStats as $status => $data)
                                    <div class="flex items-center justify-between text-xs">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 {{ $data['color'] }} rounded-full mr-2"></div>
                                            <span class="text-gray-600">{{ $status }}</span>
                                        </div>
                                        <span class="font-medium">{{ $data['count'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Panel -->
        <div class="bg-gradient-to-r from-orange-50 to-pink-50 border border-orange-200 rounded-xl p-6">
            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-pink-500 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-info-circle text-white text-xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-orange-900 mb-3">ℹ️ Informasi Sistem</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-orange-800">
                        <div>
                            <h4 class="font-medium mb-2">📋 Akses Anda:</h4>
                            <ul class="space-y-1">
                                <li>• Lihat semua arsip dalam sistem</li>
                                <li>• Input arsip baru</li>
                                <li>• Edit arsip yang Anda buat</li>
                                <li>• Export arsip Anda sendiri</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-medium mb-2">🎯 Tips Kerja:</h4>
                            <ul class="space-y-1">
                                <li>• Pastikan data arsip lengkap dan akurat</li>
                                <li>• Gunakan kategori dan klasifikasi yang tepat</li>
                                <li>• Periksa kembali sebelum menyimpan</li>
                                <li>• Target: 15 arsip per bulan, 4 per minggu</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
