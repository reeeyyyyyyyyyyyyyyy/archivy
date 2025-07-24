<x-app-layout>
    <!-- Page Header with Better Layout -->
    <div class="bg-white shadow-sm border-b">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <!-- Left Side - Title & Greetings -->
                <div class="flex-1">
                    <div class="flex items-baseline space-x-4">
                        <h1 class="text-2xl font-bold text-gray-900">Dashboard Admin</h1>
                        {{-- <div class="text-base text-gray-600">
                            @php
                                $hour = now()->hour;
                                $greeting = $hour < 12 ? 'Selamat Pagi' : ($hour < 15 ? 'Selamat Siang' : ($hour < 18 ? 'Selamat Sore' : 'Selamat Malam'));
                            @endphp
                            {{ $greeting }}, {{ Auth::user()->name }}! 👋
                        </div> --}}
                    </div>
                    {{-- <p class="text-sm text-gray-500 mt-1">Selamat datang di ARSIPIN - Sistem Arsip Pintar DPMPTSP</p> --}}
                </div>

                <!-- Right Side - Time, Email, Profile -->
                <div class="flex items-center space-x-6">
                    <!-- Time -->
                    {{-- <div class="text-center">
                        <p class="text-sm font-medium text-gray-900">{{ now()->format('H:i') }} WIB</p>
                        <p class="text-xs text-gray-500">{{ now()->format('d M Y') }}</p>
                    </div> --}}

                    <!-- Email -->
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                    </div>

                    <!-- Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
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

        <!-- Welcome Greeting -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    @php
                        $hour = now()->hour;
                        $greeting = $hour < 12 ? 'Selamat Pagi' : ($hour < 15 ? 'Selamat Siang' : ($hour < 18 ? 'Selamat Sore' : 'Selamat Malam'));
                    @endphp
                    <h2 class="text-2xl font-bold mb-2">{{ $greeting }}, {{ Auth::user()->name }}! 👋</h2>
                    <p class="text-blue-100">Selamat datang di ARSIPIN. Mari kelola arsip digital dengan efisien!</p>
                </div>
                <div class="text-right text-blue-100">
                    <p class="text-lg font-semibold">{{ now()->format('H:i') }} WIB</p>
                    <p class="text-sm">{{ now()->translatedFormat('l, d F Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Archive Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Arsip -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Arsip</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalArchives ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">Semua status arsip</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-archive text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Arsip Aktif -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Arsip Aktif</p>
                        <p class="text-3xl font-bold text-green-600 mt-2">{{ $activeArchives ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $totalArchives > 0 ? round(($activeArchives ?? 0) / $totalArchives * 100, 1) : 0 }}% dari total
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-folder-open text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Arsip Inaktif -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Arsip Inaktif</p>
                        <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $inactiveArchives ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $totalArchives > 0 ? round(($inactiveArchives ?? 0) / $totalArchives * 100, 1) : 0 }}% dari total
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-folder text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Arsip Permanen -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Arsip Permanen</p>
                        <p class="text-3xl font-bold text-purple-600 mt-2">{{ $permanentArchives ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $totalArchives > 0 ? round(($permanentArchives ?? 0) / $totalArchives * 100, 1) : 0 }}% dari total
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-shield-alt text-white text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Arsip Musnah -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Arsip Musnah</p>
                        <p class="text-3xl font-bold text-red-600 mt-2">{{ $destroyedArchives ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">Sudah dimusnahkan</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-trash-alt text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Input Bulan Ini -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Input Bulan Ini</p>
                        <p class="text-3xl font-bold text-indigo-600 mt-2">{{ $thisMonthArchives ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">Arsip baru di {{ now()->format('F Y') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-plus-circle text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Retensi Mendekati -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Retensi Mendekati</p>
                        <p class="text-3xl font-bold text-orange-600 mt-2">{{ $nearRetention ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">30 hari ke depan</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clock text-white text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts & Quick Actions Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Status Distribution Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Distribusi Status Arsip</h3>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                        <span class="text-xs text-gray-600">Realtime</span>
                    </div>
                </div>

                <!-- Chart Container -->
                <div class="relative h-48">
                    <canvas id="statusChart"></canvas>
                </div>

                <!-- Legend -->
                <div class="grid grid-cols-2 gap-4 mt-6">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-green-500 rounded mr-2"></div>
                        <span class="text-sm text-gray-700">Aktif ({{ $activeArchives ?? 0 }})</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-yellow-500 rounded mr-2"></div>
                        <span class="text-sm text-gray-700">Inaktif ({{ $inactiveArchives ?? 0 }})</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-purple-500 rounded mr-2"></div>
                        <span class="text-sm text-gray-700">Permanen ({{ $permanentArchives ?? 0 }})</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-red-500 rounded mr-2"></div>
                        <span class="text-sm text-gray-700">Musnah ({{ $destroyedArchives ?? 0 }})</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions 1 -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Quick Actions</h3>
                <div class="space-y-4">
                    <a href="{{ route('admin.archives.create') }}"
                       class="flex items-center justify-between p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-plus text-white"></i>
                            </div>
                            <span class="font-medium text-gray-900">Input Arsip Baru</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-blue-500"></i>
                    </a>

                    <a href="{{ route('admin.archives.index') }}"
                       class="flex items-center justify-between p-4 bg-green-50 hover:bg-green-100 rounded-lg transition-colors group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-search text-white"></i>
                            </div>
                            <span class="font-medium text-gray-900">Cari Arsip</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-green-500"></i>
                    </a>

                    <a href="{{ route('admin.bulk.index') }}"
                       class="flex items-center justify-between p-4 bg-orange-50 hover:bg-orange-100 rounded-lg transition-colors group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-tasks text-white"></i>
                            </div>
                            <span class="font-medium text-gray-900">Operasi Massal</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-orange-500"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Actions 2 -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Fitur Lainnya</h3>
                <div class="space-y-4">
                    <a href="{{ route('admin.categories.index') }}"
                       class="flex items-center justify-between p-4 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-indigo-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-tags text-white"></i>
                            </div>
                            <span class="font-medium text-gray-900">Master Kategori</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-indigo-500"></i>
                    </a>

                    <a href="{{ route('admin.classifications.index') }}"
                       class="flex items-center justify-between p-4 bg-cyan-50 hover:bg-cyan-100 rounded-lg transition-colors group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-cyan-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-sitemap text-white"></i>
                            </div>
                            <span class="font-medium text-gray-900">Master Klasifikasi</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-cyan-500"></i>
                    </a>

                    <a href="{{ route('admin.search.index') }}"
                       class="flex items-center justify-between p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-search-plus text-white"></i>
                            </div>
                            <span class="font-medium text-gray-900">Pencarian Lanjutan</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-purple-500"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Aktivitas Terbaru</h3>
                <a href="{{ route('admin.archives.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Lihat Semua →</a>
            </div>

            <div class="space-y-4">
                @forelse($recentArchives ?? [] as $archive)
                    <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-file-alt text-white"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $archive->description ?? 'Arsip Baru' }}</p>
                            <p class="text-xs text-gray-500">{{ $archive->classification ? ($archive->classification->code . ' - ' . $archive->classification->nama_klasifikasi) : 'N/A' }}</p>
                            <p class="text-xs text-gray-400 mt-1">Oleh: {{ $archive->createdByUser->name ?? 'System' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500">{{ $archive->created_at ? $archive->created_at->diffForHumans() : 'Baru saja' }}</p>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium mt-1
                                {{ ($archive->status ?? 'Aktif') === 'Aktif' ? 'bg-green-100 text-green-800' :
                                   (($archive->status ?? 'Aktif') === 'Inaktif' ? 'bg-yellow-100 text-yellow-800' :
                                   (($archive->status ?? 'Aktif') === 'Permanen' ? 'bg-purple-100 text-purple-800' : 'bg-red-100 text-red-800')) }}">
                                {{ ucfirst($archive->status ?? 'Aktif') }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                        <p class="text-gray-500 text-lg mb-2">Belum ada aktivitas terbaru</p>
                        <p class="text-gray-400 text-sm mb-4">Mulai input arsip untuk melihat aktivitas di sini</p>
                        <a href="{{ route('admin.archives.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Input Arsip Pertama
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- System Status Footer -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl shadow-sm p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-semibold mb-2">Sistem ARSIPIN</h3>
                    <p class="text-blue-100">Sistem arsip pintar dengan automasi JRA Pergub 1 & 30 Jawa Timur</p>
                    <p class="text-blue-200 text-sm mt-2">DPMPTSP Provinsi Jawa Timur</p>
                </div>
                <div class="text-right">
                    <div class="flex items-center justify-end space-x-2 mb-2">
                        <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                        <span class="text-sm font-medium">Status: Online</span>
                    </div>
                    <p class="text-xs text-blue-100">Last Update: {{ now()->format('d M Y H:i') }} WIB</p>
                    <p class="text-xs text-blue-200 mt-1">Version 1.0.0</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Status Distribution Pie Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Aktif', 'Inaktif', 'Permanen', 'Musnah'],
                datasets: [{
                    data: [
                        {{ $activeArchives ?? 0 }},
                        {{ $inactiveArchives ?? 0 }},
                        {{ $permanentArchives ?? 0 }},
                        {{ $destroyedArchives ?? 0 }}
                    ],
                    backgroundColor: [
                        '#10B981', // Green
                        '#F59E0B', // Yellow
                        '#8B5CF6', // Purple
                        '#EF4444'  // Red
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                cutout: '60%'
            }
        });
    </script>
    @endpush
</x-app-layout>
