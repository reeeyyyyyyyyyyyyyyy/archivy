<x-app-layout>
    <!-- Page Header with Intern Branding -->
    <div class="bg-white shadow-sm border-b">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <!-- Left Side - Title & Intern Info -->
                <div class="flex-1">
                    <div class="flex items-baseline space-x-4">
                        <h1 class="text-2xl font-bold text-gray-900">Dashboard Mahasiswa Magang</h1>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Selamat datang di ARSIPIN - Portal Mahasiswa Magang</p>
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
                            <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-pink-500 rounded-full flex items-center justify-center">
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
        
        <!-- Welcome Section for Intern -->
        <div class="bg-gradient-to-r from-orange-600 to-pink-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    @php
                        $hour = now()->hour;
                        $greeting = $hour < 12 ? 'Selamat Pagi' : ($hour < 15 ? 'Selamat Siang' : ($hour < 18 ? 'Selamat Sore' : 'Selamat Malam'));
                    @endphp
                    <h2 class="text-2xl font-bold mb-2">{{ $greeting }}, {{ Auth::user()->name }}! 👋</h2>
                    <p class="text-orange-100">Selamat belajar di ARSIPIN! Mari berkontribusi dalam pengelolaan arsip digital.</p>
                </div>
                <div class="text-right text-orange-100">
                    <p class="text-lg font-semibold">{{ now()->format('H:i') }} WIB</p>
                    <p class="text-sm">{{ now()->translatedFormat('l, d F Y') }}</p>
                </div>
            </div>
        </div>
        
        <!-- Intern Performance Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- My Total Archives -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Kontribusi</p>
                        <p class="text-3xl font-bold text-orange-600 mt-2">{{ $myTotalArchives ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">Arsip yang saya buat</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-graduate text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- This Month -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Bulan Ini</p>
                        <p class="text-3xl font-bold text-green-600 mt-2">{{ $thisMonthArchives ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ now()->format('F Y') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-calendar-check text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Weekly -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Minggu Ini</p>
                        <p class="text-3xl font-bold text-blue-600 mt-2">{{ $weeklyContribution ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">Kontribusi mingguan</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-chart-line text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Today -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Hari Ini</p>
                        <p class="text-3xl font-bold text-purple-600 mt-2">{{ $todayContribution ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">Kontribusi harian</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-tasks text-white text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Learning Overview for Intern -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- System Overview -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Arsip Sistem</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalArchives ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">Seluruh sistem</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-database text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Active Archives -->
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

            <!-- Categories -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Kategori</p>
                        <p class="text-3xl font-bold text-indigo-600 mt-2">{{ $categoryCount ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">Untuk dipelajari</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-tags text-white text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Quick Actions for Intern -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Aktivitas Mahasiswa</h3>
                <div class="space-y-4">
                    <a href="{{ route('admin.archives.create') }}" 
                       class="flex items-center justify-between p-4 bg-orange-50 hover:bg-orange-100 rounded-lg transition-colors group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-plus text-white"></i>
                            </div>
                            <span class="font-medium text-gray-900">Input Arsip Baru</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-orange-500"></i>
                    </a>
                    
                    <a href="{{ route('admin.archives.index') }}" 
                       class="flex items-center justify-between p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-search text-white"></i>
                            </div>
                            <span class="font-medium text-gray-900">Lihat Arsip</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-blue-500"></i>
                    </a>
                    
                    <a href="{{ route('admin.categories.index') }}" 
                       class="flex items-center justify-between p-4 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-indigo-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-book text-white"></i>
                            </div>
                            <span class="font-medium text-gray-900">Pelajari Kategori</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-indigo-500"></i>
                    </a>
                </div>
            </div>

            <!-- Learning Progress -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Progress Belajar</h3>
                <div class="space-y-4">
                    <div class="text-center">
                        <div class="w-20 h-20 bg-gradient-to-br from-orange-100 to-pink-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-graduation-cap text-orange-600 text-2xl"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900">Status Magang</h4>
                        <p class="text-sm text-gray-600 mt-2">Mahasiswa Magang Aktif</p>
                        <p class="text-xs text-gray-500 mt-1">Level: Pemula</p>
                        
                        <div class="mt-6 space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Kontribusi</span>
                                <span class="font-medium">{{ $myTotalArchives ?? 0 }} arsip</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Target Bulan</span>
                                <span class="font-medium">20 arsip</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-orange-500 h-2 rounded-full" 
                                     style="width: {{ min(100, ($thisMonthArchives ?? 0) / 20 * 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Intern Info & Permissions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Hak Akses Magang</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Role</span>
                        <span class="text-sm font-medium text-orange-700 bg-orange-100 px-2 py-1 rounded">Mahasiswa</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Input Arsip</span>
                        <span class="text-sm font-medium text-green-700">✓ Dapat</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Edit Arsip</span>
                        <span class="text-sm font-medium text-green-700">✓ Dapat</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Export Excel</span>
                        <span class="text-sm font-medium text-green-700">✓ Dapat</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Analytics</span>
                        <span class="text-sm font-medium text-gray-500">✗ Tidak</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Master Data</span>
                        <span class="text-sm font-medium text-gray-500">✗ Hanya Lihat</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- My Recent Work -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Pekerjaan Terbaru Saya</h3>
                <a href="{{ route('admin.archives.index') }}" class="text-sm text-orange-600 hover:text-orange-700 font-medium">Lihat Semua →</a>
            </div>
            
            <div class="space-y-4">
                @forelse($myRecentArchives ?? [] as $archive)
                    <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-pink-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-file-alt text-white"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $archive->uraian ?? 'Arsip Baru' }}</p>
                            <p class="text-xs text-gray-500">{{ $archive->classification->name ?? 'Tanpa Klasifikasi' }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $archive->created_at ? $archive->created_at->diffForHumans() : 'Baru saja' }}</p>
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
                        <i class="fas fa-graduation-cap text-gray-300 text-5xl mb-4"></i>
                        <p class="text-gray-500 text-lg mb-2">Selamat datang mahasiswa magang!</p>
                        <p class="text-gray-400 text-sm mb-4">Mulai kontribusi dengan input arsip pertama Anda</p>
                        <a href="{{ route('admin.archives.create') }}" class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Input Arsip Pertama
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Intern Status Footer -->
        <div class="bg-gradient-to-r from-orange-600 to-pink-600 rounded-xl shadow-sm p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-semibold mb-2">Portal Mahasiswa Magang - ARSIPIN</h3>
                    <p class="text-orange-100">Platform pembelajaran arsip digital untuk mahasiswa magang DPMPTSP Jawa Timur</p>
                    <p class="text-orange-200 text-sm mt-2">Akses: Input & Edit Arsip + Export Excel</p>
                </div>
                <div class="text-right">
                    <div class="flex items-center justify-end space-x-2 mb-2">
                        <div class="w-3 h-3 bg-orange-400 rounded-full animate-pulse"></div>
                        <span class="text-sm font-medium">Status: Belajar</span>
                    </div>
                    <p class="text-xs text-orange-100">Mahasiswa Magang</p>
                    <p class="text-xs text-orange-200 mt-1">{{ Auth::user()->name }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 