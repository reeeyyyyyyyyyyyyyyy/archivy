<!-- Sidebar Navigation -->
<div x-data="{
    sidebarOpen: false,
    archiveSubmenuOpen: localStorage.getItem('archiveSubmenuOpen') === 'true' || {{ request()->routeIs('admin.archives.*', 'archives.*') ? 'true' : 'false' }},
    masterSubmenuOpen: localStorage.getItem('masterSubmenuOpen') === 'true' || {{ request()->routeIs('admin.categories.*', 'admin.classifications.*', 'categories.*', 'classifications.*') ? 'true' : 'false' }},
    
    // Watch for submenu changes and persist to localStorage
    init() {
        this.$watch('archiveSubmenuOpen', value => {
            localStorage.setItem('archiveSubmenuOpen', value);
        });
        this.$watch('masterSubmenuOpen', value => {
            localStorage.setItem('masterSubmenuOpen', value);
        });
    },
    
    // Toggle functions with proper state management
    toggleArchiveSubmenu() {
        this.archiveSubmenuOpen = !this.archiveSubmenuOpen;
    },
    
    toggleMasterSubmenu() {
        this.masterSubmenuOpen = !this.masterSubmenuOpen;
    },
    
    // Close sidebar on mobile navigation
    closeSidebar() {
        this.sidebarOpen = false;
    }
}" 
x-init="init()"
class="flex min-h-screen bg-gray-50">
    
    <!-- Mobile sidebar overlay -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-600 bg-opacity-75 z-30 lg:hidden"
         @click="closeSidebar()"></div>

    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-40 w-64 bg-white shadow-lg transform lg:translate-x-0 lg:static lg:inset-0 transition-transform duration-200 ease-out sidebar-stable"
         :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
         x-cloak>
        
        <!-- Sidebar Header with Role-based styling -->
        <div class="flex items-center justify-between h-20 px-6 
             @if(auth()->user()->isAdmin()) bg-gradient-to-r from-blue-600 to-purple-600 
             @elseif(auth()->user()->isStaff()) bg-gradient-to-r from-green-600 to-teal-600
             @elseif(auth()->user()->isIntern()) bg-gradient-to-r from-orange-600 to-pink-600
             @else bg-gradient-to-r from-blue-600 to-purple-600 @endif">
            <div class="flex items-center">
                <div class="relative mr-4">
                    <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                        <i class="fas fa-archive text-white text-xl"></i>
                    </div>
                    <div class="absolute -top-1 -right-1 w-4 h-4 bg-yellow-400 rounded-full flex items-center justify-center">
                        <i class="fas fa-bolt text-blue-600 text-xs"></i>
                    </div>
                </div>
                <div class="text-white">
                    <h1 class="text-xl font-bold tracking-wide">ARSIPIN</h1>
                    <p class="text-sm text-white/80 font-medium">
                        @if(auth()->user()->isAdmin()) Admin Portal
                        @elseif(auth()->user()->isStaff()) Portal Pegawai TU
                        @elseif(auth()->user()->isIntern()) Portal Mahasiswa
                        @else Sistem Arsip Pintar @endif
                    </p>
                </div>
            </div>
            <button @click="closeSidebar()" class="lg:hidden text-white hover:text-gray-200 transition-colors">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto nav-transition">
            <!-- Dashboard -->
            <a href="{{ route(auth()->user()->getDashboardRoute()) }}" 
               @click="closeSidebar()"
               class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.dashboard', 'staff.dashboard', 'intern.dashboard') ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <i class="fas fa-tachometer-alt mr-3 text-lg w-5"></i>
                Dashboard
            </a>

            <!-- Archive Menu with Submenu -->
            <div class="space-y-1 submenu-container">
                <button @click="toggleArchiveSubmenu()"
                        class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg transition-colors
                               {{ request()->routeIs('admin.archives.*', 'archives.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="flex items-center">
                        <i class="fas fa-archive mr-3 text-lg w-5"></i>
                        Manajemen Arsip
                    </div>
                    <i class="fas fa-chevron-down transform transition-transform duration-200 text-xs" 
                       :class="archiveSubmenuOpen ? 'rotate-180' : 'rotate-0'"></i>
                </button>
                
                <!-- Archive Submenu -->
                <div x-show="archiveSubmenuOpen" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95"
                     class="ml-8 space-y-1"
                     x-cloak>
                    
                    <a href="{{ route('archives.index') }}" 
                       @click="closeSidebar()"
                       class="flex items-center px-4 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('archives.index') ? 'bg-blue-50 text-blue-700' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }}">
                        <i class="fas fa-folder mr-3 text-sm w-4"></i>
                        Semua Arsip
                    </a>
                    
                    <a href="{{ route('archives.aktif') }}" 
                       @click="closeSidebar()"
                       class="flex items-center px-4 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('archives.aktif') ? 'bg-green-50 text-green-700' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }}">
                        <i class="fas fa-folder-open mr-3 text-sm w-4 text-green-500"></i>
                        Arsip Aktif
                    </a>
                    
                    <a href="{{ route('archives.inaktif') }}" 
                       @click="closeSidebar()"
                       class="flex items-center px-4 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('archives.inaktif') ? 'bg-yellow-50 text-yellow-700' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }}">
                        <i class="fas fa-folder mr-3 text-sm w-4 text-yellow-500"></i>
                        Arsip Inaktif
                    </a>
                    
                    <a href="{{ route('archives.permanen') }}" 
                       @click="closeSidebar()"
                       class="flex items-center px-4 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('archives.permanen') ? 'bg-purple-50 text-purple-700' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }}">
                        <i class="fas fa-shield-alt mr-3 text-sm w-4 text-purple-500"></i>
                        Arsip Permanen
                    </a>
                    
                    <a href="{{ route('archives.musnah') }}" 
                       @click="closeSidebar()"
                       class="flex items-center px-4 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('archives.musnah') ? 'bg-red-50 text-red-700' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }}">
                        <i class="fas fa-trash-alt mr-3 text-sm w-4 text-red-500"></i>
                        Arsip Musnah
                    </a>
                </div>
            </div>

            {{-- <!-- Search Menu - All roles -->
            <a href="{{ 
                auth()->user()->isAdmin() ? route('admin.search.index') : 
                (auth()->user()->isStaff() ? route('staff.search.index') : route('intern.search.index'))
            }}" 
               @click="closeSidebar()"
               class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.search.*', 'staff.search.*', 'intern.search.*') ? 'bg-emerald-50 text-emerald-700 border-r-4 border-emerald-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <i class="fas fa-search mr-3 text-lg w-5"></i>
                Pencarian Lanjutan
            </a> --}}

            <!-- Export Excel Menu - All roles -->
            <a href="{{ 
                auth()->user()->isAdmin() ? route('admin.export.index') : 
                (auth()->user()->isStaff() ? route('staff.export.index') : route('intern.export.index'))
            }}" 
               @click="closeSidebar()"
               class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.export.*', 'staff.export.*', 'intern.export.*') ? 'bg-green-50 text-green-700 border-r-4 border-green-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <i class="fas fa-file-excel mr-3 text-lg w-5"></i>
                Export Excel
            </a>

            <!-- Master Data Menu - Only for Admin, read-only for Staff/Intern -->
            <div class="space-y-1 submenu-container">
                <button @click="toggleMasterSubmenu()"
                        class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg transition-colors
                               {{ request()->routeIs('admin.categories.*', 'admin.classifications.*', 'categories.*', 'classifications.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="flex items-center">
                        <i class="fas fa-database mr-3 text-lg w-5"></i>
                        Master Data
                        @if(!auth()->user()->isAdmin())
                            <span class="ml-2 text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">Lihat</span>
                        @endif
                    </div>
                    <i class="fas fa-chevron-down transform transition-transform duration-200 text-xs" 
                       :class="masterSubmenuOpen ? 'rotate-180' : 'rotate-0'"></i>
                </button>
                
                <!-- Master Data Submenu -->
                <div x-show="masterSubmenuOpen" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95"
                     class="ml-8 space-y-1"
                     x-cloak>
                    
                    <a href="{{ route('admin.categories.index') }}" 
                       @click="closeSidebar()"
                       class="flex items-center px-4 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.categories.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }}">
                        <i class="fas fa-tags mr-3 text-sm w-4 text-indigo-500"></i>
                        Kategori
                    </a>
                    
                    <a href="{{ route('admin.classifications.index') }}" 
                       @click="closeSidebar()"
                       class="flex items-center px-4 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.classifications.*') ? 'bg-cyan-50 text-cyan-700' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }}">
                        <i class="fas fa-sitemap mr-3 text-sm w-4 text-cyan-500"></i>
                        Klasifikasi
                    </a>
                </div>
            </div>

            <!-- Analytics - Admin and Staff only -->
            @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
                <a href="{{ auth()->user()->isAdmin() ? route('admin.analytics.index') : route('staff.analytics.index') }}" 
                   @click="closeSidebar()"
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.analytics.*', 'staff.analytics.*') ? 'bg-purple-50 text-purple-700 border-r-4 border-purple-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-chart-pie mr-3 text-lg w-5"></i>
                    Advanced Analytics
                </a>
            @endif

            <!-- Reports - Admin only -->
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.reports.retention-dashboard') }}" 
                   @click="closeSidebar()"
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.reports.*') ? 'bg-orange-50 text-orange-700 border-r-4 border-orange-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-chart-bar mr-3 text-lg w-5"></i>
                    Laporan Retensi
                </a>

                <!-- Role Management - Admin only -->
                <a href="{{ route('admin.roles.index') }}" 
                   @click="closeSidebar()"
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.roles.*') ? 'bg-purple-50 text-purple-700 border-r-4 border-purple-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-users-cog mr-3 text-lg w-5"></i>
                    Manage Roles
                </a>

                <!-- Bulk Operations - Admin only -->
                <a href="{{ route('admin.bulk.index') }}" 
                   @click="closeSidebar()"
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.bulk.*') ? 'bg-pink-50 text-pink-700 border-r-4 border-pink-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-tasks mr-3 text-lg w-5"></i>
                    Operasi Massal
                </a>
            @endif
        </nav>
    </div>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col lg:ml-0 main-content">
        <!-- Mobile Header -->
        <div class="lg:hidden bg-white shadow-sm border-b px-4 py-3 flex items-center justify-between">
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-600 hover:text-gray-900 transition-colors">
                <i class="fas fa-bars text-lg"></i>
            </button>
            <h1 class="text-lg font-semibold text-gray-900">ARSIPIN</h1>
            <div class="w-6"></div> <!-- Spacer -->
        </div>

        <!-- Page Content -->
        <main class="flex-1 bg-gray-50">
            {{ $slot }}
        </main>
    </div>
</div>
