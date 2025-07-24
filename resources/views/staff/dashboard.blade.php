<x-app-layout>
    <!-- Page Header with Staff Branding -->
    <div class="bg-white shadow-sm border-b">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <!-- Left Side - Title & Staff Info -->
                <div class="flex-1">
                    <div class="flex items-baseline space-x-4">
                        <h1 class="text-2xl font-bold text-gray-900">Dashboard Pegawai TU</h1>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Selamat datang di ARSIPIN - Portal Pegawai Tata Usaha</p>
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
                            <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-teal-500 rounded-full flex items-center justify-center">
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

        <!-- Welcome Section for Staff -->
        <div class="bg-gradient-to-r from-green-600 to-teal-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    @php
                        $hour = now()->hour;
                        $greeting = $hour < 12 ? 'Selamat Pagi' : ($hour < 15 ? 'Selamat Siang' : ($hour < 18 ? 'Selamat Sore' : 'Selamat Malam'));
                    @endphp
                    <h2 class="text-2xl font-bold mb-2">{{ $greeting }}, {{ Auth::user()->name }}! 👋</h2>
                    <p class="text-green-100">Anda adalah Pegawai TU dengan akses ke analytics dan manajemen arsip lengkap!</p>
                </div>
                <div class="text-right text-green-100">
                    <p class="text-lg font-semibold">{{ now()->format('H:i') }} WIB</p>
                    <p class="text-sm">{{ now()->translatedFormat('l, d F Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Staff-specific Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- My Archives -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Arsip Saya</p>
                        <p class="text-3xl font-bold text-green-600 mt-2">{{ $myArchives ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">Yang saya input</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-edit text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Arsip -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Arsip</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalArchives ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">Semua arsip sistem</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-archive text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Aktif -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Arsip Aktif</p>
                        <p class="text-3xl font-bold text-green-600 mt-2">{{ $activeArchives ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">Status aktif</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-folder-open text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Input Bulan Ini -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Semua Input Arsip Bulan Ini</p>
                        <p class="text-3xl font-bold text-indigo-600 mt-2">{{ $thisMonthArchives ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ now()->format('F Y') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-plus-circle text-white text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Staff Performance Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Kontribusi Arsip Saya</h3>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                        <span class="text-xs text-gray-600">Realtime</span>
                    </div>
                </div>

                <!-- Chart placeholder -->
                <div class="relative h-48 bg-gray-50 rounded-lg flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-chart-line text-green-400 text-4xl mb-2"></i>
                        <p class="text-gray-500 text-sm">Grafik Kontribusi</p>
                        <p class="text-gray-400 text-xs">{{ $myArchives ?? 0 }} arsip dibuat</p>

                        <!-- Simple Bar Chart -->
                        <div class="mt-4 flex items-end justify-center space-x-2 h-20">
                            @php
                                $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                                $currentMonth = now()->month;
                            @endphp
                            @foreach($months as $index => $month)
                                @php
                                    $height = $index + 1 === $currentMonth ? 16 : rand(4, 12);
                                    $color = $index + 1 === $currentMonth ? 'bg-green-500' : 'bg-green-300';
                                @endphp
                                <div class="flex flex-col items-center">
                                    <div class="w-3 {{ $color }} rounded-t" style="height: {{ $height * 4 }}px;"></div>
                                    <span class="text-xs text-gray-500 mt-1">{{ $month }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions for Staff -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Aksi Cepat Pegawai TU</h3>
                <div class="space-y-4">
                    <a href="{{ route('staff.archives.create') }}"
                       class="flex items-center justify-between p-4 bg-green-50 hover:bg-green-100 rounded-lg transition-colors group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-plus text-white"></i>
                            </div>
                            <span class="font-medium text-gray-900">Input Arsip Baru</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-green-500"></i>
                    </a>

                    <a href="{{ route('staff.archives.index') }}"
                       class="flex items-center justify-between p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-search text-white"></i>
                            </div>
                            <span class="font-medium text-gray-900">Kelola Arsip</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-blue-500"></i>
                    </a>

                    <a href="{{ route('staff.analytics.index') }}"
                       class="flex items-center justify-between p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-chart-pie text-white"></i>
                            </div>
                            <span class="font-medium text-gray-900">Dashboard Analytics</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-purple-500"></i>
                    </a>
                </div>
            </div>

            <!-- Staff Info & Statistics -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Informasi Pegawai TU</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Role</span>
                        <span class="text-sm font-medium text-green-700 bg-green-100 px-2 py-1 rounded">Pegawai TU</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Akses Analytics</span>
                        <span class="text-sm font-medium text-green-700">✓ Tersedia</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Export Excel</span>
                        <span class="text-sm font-medium text-green-700">✓ Tersedia</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Operasi Massal</span>
                        <span class="text-sm font-medium text-green-700">✓ Tersedia</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Laporan Retensi</span>
                        <span class="text-sm font-medium text-green-700">✓ Tersedia</span>
                    </div>

                    <div class="pt-4 border-t border-gray-200">
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div>
                                <p class="text-2xl font-bold text-green-600">{{ $myArchives ?? 0 }}</p>
                                <p class="text-xs text-gray-500">Total Arsip Dibuat</p>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-blue-600">{{ $thisMonthArchives ?? 0 }}</p>
                                <p class="text-xs text-gray-500">Bulan Ini</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- My Recent Archives -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Arsip Terbaru Saya</h3>
                <a href="{{ route('staff.archives.index') }}" class="text-sm text-green-600 hover:text-green-700 font-medium">Lihat Semua →</a>
            </div>

            <div class="space-y-4">
                @forelse($myRecentArchives ?? [] as $archive)
                    <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-teal-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-file-alt text-white"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $archive->uraian ?? 'Arsip Baru' }}</p>
                            <p class="text-xs text-gray-500">{{ $archive->classification->name ?? 'Tanpa Klasifikasi' }}</p>
                            <p class="text-xs text-gray-400 mt-1">Dibuat: {{ $archive->created_at ? $archive->created_at->diffForHumans() : 'Baru saja' }}</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                {{ ($archive->status ?? 'Aktif') === 'Aktif' ? 'bg-green-100 text-green-800' :
                                   (($archive->status ?? 'Aktif') === 'Inaktif' ? 'bg-yellow-100 text-yellow-800' :
                                   (($archive->status ?? 'Aktif') === 'Permanen' ? 'bg-purple-100 text-purple-800' : 'bg-red-100 text-red-800')) }}">
                                {{ ucfirst($archive->status ?? 'Aktif') }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <i class="fas fa-folder-plus text-gray-300 text-5xl mb-4"></i>
                        <p class="text-gray-500 text-lg mb-2">Belum ada arsip yang Anda buat</p>
                        <p class="text-gray-400 text-sm mb-4">Mulai input arsip untuk melihat kontribusi Anda</p>
                        <a href="{{ route('staff.archives.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Input Arsip Pertama
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Staff Status Footer -->
        <div class="bg-gradient-to-r from-green-600 to-teal-600 rounded-xl shadow-sm p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-semibold mb-2">Portal Pegawai TU - ARSIPIN</h3>
                    <p class="text-green-100">Sistem arsip pintar khusus untuk staf Tata Usaha DPMPTSP Jawa Timur</p>
                    <p class="text-green-200 text-sm mt-2">Akses: CRUD Arsip + Analytics Dashboard</p>
                </div>
                <div class="text-right">
                    <div class="flex items-center justify-end space-x-2 mb-2">
                        <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                        <span class="text-sm font-medium">Status: Online</span>
                    </div>
                    <p class="text-xs text-green-100">Pegawai TU Mode</p>
                    <p class="text-xs text-green-200 mt-1">{{ Auth::user()->name }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
