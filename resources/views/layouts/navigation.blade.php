<!-- Sidebar Navigation -->
<div x-data="{
    sidebarOpen: false,
    archiveSubmenuOpen: localStorage.getItem('archiveSubmenuOpen') === 'true' || {{ request()->routeIs('admin.archives.*', 'archives.*') ? 'true' : 'false' }},
    masterSubmenuOpen: localStorage.getItem('masterSubmenuOpen') === 'true' || {{ request()->routeIs('admin.categories.*', 'admin.classifications.*', 'categories.*', 'classifications.*') ? 'true' : 'false' }},
    cetakExportSubmenuOpen: localStorage.getItem('cetakExportSubmenuOpen') === 'true' || {{ request()->routeIs('*.export.*', '*.archives.export-menu', '*.storage.generate-box-labels', '*.generate-labels.*') ? 'true' : 'false' }},

    // Watch for submenu changes and persist to localStorage
    init() {
        this.$watch('archiveSubmenuOpen', value => {
            localStorage.setItem('archiveSubmenuOpen', value);
        });
        this.$watch('masterSubmenuOpen', value => {
            localStorage.setItem('masterSubmenuOpen', value);
        });
        this.$watch('cetakExportSubmenuOpen', value => {
            localStorage.setItem('cetakExportSubmenuOpen', value);
        });
    },

    // Toggle functions with proper state management
    toggleArchiveSubmenu() {
        this.archiveSubmenuOpen = !this.archiveSubmenuOpen;
    },

    toggleMasterSubmenu() {
        this.masterSubmenuOpen = !this.masterSubmenuOpen;
    },

    toggleCetakExportSubmenu() {
        this.cetakExportSubmenuOpen = !this.cetakExportSubmenuOpen;
    },

    // Close sidebar on mobile navigation
    closeSidebar() {
        this.sidebarOpen = false;
    }
}" x-init="init()" class="flex min-h-screen bg-gray-50">

    <!-- Mobile sidebar overlay -->
    <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-30 lg:hidden"
        @click="closeSidebar()"></div>

    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-40 w-64 bg-white shadow-lg transform lg:translate-x-0 lg:static lg:inset-0 transition-transform duration-200 ease-out sidebar-stable"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" x-cloak>

        <!-- Sidebar Header with Role-based styling -->
        <div
            class="flex items-center justify-between h-20 px-6
                         @if (auth()->check() && auth()->user()->hasRole('admin')) bg-gradient-to-r from-blue-600 to-purple-600
             @elseif(auth()->check() && auth()->user()->hasRole('staff')) bg-gradient-to-r from-green-600 to-teal-600
             @elseif(auth()->check() && auth()->user()->hasRole('intern')) bg-gradient-to-r from-orange-600 to-pink-600
             @else bg-gradient-to-r from-blue-600 to-purple-600 @endif">
            <div class="flex items-center">
                <div class="relative mr-4">
                    <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                        <i class="fas fa-archive text-white text-xl"></i>
                    </div>
                    <div
                        class="absolute -top-1 -right-1 w-4 h-4 bg-yellow-400 rounded-full flex items-center justify-center">
                        <i class="fas fa-bolt text-blue-600 text-xs"></i>
                    </div>
                </div>
                <div class="text-white">
                    <h1 class="text-xl font-bold tracking-wide">ARSIPIN</h1>
                    <p class="text-sm text-white/80 font-medium">
                        @if (auth()->check() && auth()->user()->hasRole('admin'))
                            Admin Portal
                        @elseif(auth()->check() && auth()->user()->hasRole('staff'))
                            Portal Pegawai TU
                        @elseif(auth()->check() && auth()->user()->hasRole('intern'))
                            Portal Mahasiswa
                        @else
                            Sistem Arsip Pintar
                        @endif
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
            <a href="{{ auth()->check() ? route(auth()->user()->getDashboardRoute()) : '#' }}" @click="closeSidebar()"
                class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('admin.dashboard', 'staff.dashboard', 'intern.dashboard') ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-700' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-700 hover:translate-x-1' }}">
                <i class="fas fa-tachometer-alt mr-3 text-lg w-5 transition-colors duration-200"></i>
                Dashboard
            </a>

            <!-- Archive Menu with Submenu -->
            <div class="space-y-1 submenu-container">
                <button @click="toggleArchiveSubmenu()"
                    class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200
                               {{ request()->routeIs('admin.archives.*', 'archives.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-700 hover:translate-x-1' }}">
                    <div class="flex items-center">
                        <i class="fas fa-archive mr-3 text-lg w-5 transition-colors duration-200"></i>
                        Manajemen Arsip
                    </div>
                    <i class="fas fa-chevron-down transform transition-transform duration-200 text-xs"
                        :class="archiveSubmenuOpen ? 'rotate-180' : 'rotate-0'"></i>
                </button>

                <!-- Archive Submenu -->
                <div x-show="archiveSubmenuOpen" x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95" class="ml-8 space-y-1" x-cloak>

                    <a href="{{ auth()->check() && auth()->user()->hasRole('admin')
                        ? route('admin.archives.index')
                        : (auth()->check() && auth()->user()->hasRole('staff')
                            ? route('staff.archives.index')
                            : (auth()->check()
                                ? route('intern.archives.index')
                                : '#')) }}"
                        @click="closeSidebar()"
                        class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('*.archives.index') ? 'bg-blue-50 text-blue-700' : 'text-gray-500 hover:bg-blue-50 hover:text-blue-700 hover:translate-x-1' }}">
                        <i class="fas fa-folder mr-3 text-sm w-4 transition-colors duration-200"></i>
                        Semua Arsip
                    </a>

                    <!-- Arsip Parent - Admin and Staff -->
                    @if (auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('staff')))
                        <a href="{{ auth()->check() && auth()->user()->hasRole('admin')
                            ? route('admin.archives.parent')
                            : route('staff.archives.parent') }}"
                            @click="closeSidebar()"
                            class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('*.archives.parent') ? 'bg-purple-50 text-purple-700' : 'text-gray-500 hover:bg-purple-50 hover:text-purple-700 hover:translate-x-1' }}">
                            <i class="fas fa-folder-tree mr-3 text-sm w-4 transition-colors duration-200"></i>
                            Arsip Terkait
                        </a>
                    @endif

                    <a href="{{ auth()->check() && auth()->user()->hasRole('admin')
                        ? route('admin.archives.aktif')
                        : (auth()->check() && auth()->user()->hasRole('staff')
                            ? route('staff.archives.aktif')
                            : (auth()->check()
                                ? route('intern.archives.aktif')
                                : '#')) }}"
                        @click="closeSidebar()"
                        class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('*.archives.aktif') ? 'bg-green-50 text-green-700' : 'text-gray-500 hover:bg-green-50 hover:text-green-700 hover:translate-x-1' }}">
                        <i class="fas fa-play-circle mr-3 text-sm w-4 transition-colors duration-200"></i>
                        Arsip Aktif
                    </a>

                    <a href="{{ auth()->check() && auth()->user()->hasRole('admin')
                        ? route('admin.archives.inaktif')
                        : (auth()->check() && auth()->user()->hasRole('staff')
                            ? route('staff.archives.inaktif')
                            : route('intern.archives.inaktif')) }}"
                        @click="closeSidebar()"
                        class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('*.archives.inaktif') ? 'bg-yellow-50 text-yellow-700' : 'text-gray-500 hover:bg-yellow-50 hover:text-yellow-700 hover:translate-x-1' }}">
                        <i class="fas fa-pause-circle mr-3 text-sm w-4 transition-colors duration-200"></i>
                        Arsip Inaktif
                    </a>

                    <a href="{{ auth()->check() && auth()->user()->hasRole('admin')
                        ? route('admin.archives.permanen')
                        : (auth()->check() && auth()->user()->hasRole('staff')
                            ? route('staff.archives.permanen')
                            : route('intern.archives.permanen')) }}"
                        @click="closeSidebar()"
                        class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('*.archives.permanen') ? 'bg-purple-50 text-purple-700' : 'text-gray-500 hover:bg-purple-50 hover:text-purple-700 hover:translate-x-1' }}">
                        <i class="fas fa-shield-alt mr-3 text-sm w-4 transition-colors duration-200"></i>
                        Arsip Permanen
                    </a>

                    <a href="{{ auth()->check() && auth()->user()->hasRole('admin')
                        ? route('admin.archives.musnah')
                        : (auth()->check() && auth()->user()->hasRole('staff')
                            ? route('staff.archives.musnah')
                            : route('intern.archives.musnah')) }}"
                        @click="closeSidebar()"
                        class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('*.archives.musnah') ? 'bg-red-50 text-red-700' : 'text-gray-500 hover:bg-red-50 hover:text-red-700 hover:translate-x-1' }}">
                        <i class="fas fa-ban mr-3 text-sm w-4 transition-colors duration-200"></i>
                        Arsip Musnah
                    </a>

                    <a href="{{ auth()->check() && auth()->user()->hasRole('admin')
                        ? route('admin.re-evaluation.index')
                        : (auth()->check() && auth()->user()->hasRole('staff')
                            ? route('staff.re-evaluation.index')
                            : route('intern.re-evaluation.index')) }}"
                        @click="closeSidebar()"
                        class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('*.re-evaluation.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-500 hover:bg-indigo-50 hover:text-indigo-700 hover:translate-x-1' }}">
                        <i class="fas fa-redo mr-3 text-sm w-4 transition-colors duration-200"></i>
                        Arsip Dinilai Kembali
                    </a>

                    <!-- Create Archive - for Admin and Staff -->
                    @if (
                        (auth()->check() && auth()->user()->hasRole('admin')) ||
                            (auth()->check() && auth()->user()->hasRole('staff')) ||
                            (auth()->check() && auth()->user()->hasRole('intern')))
                        <a href="{{ auth()->check() && auth()->user()->hasRole('admin')
                            ? route('admin.archives.create')
                            : (auth()->check() && auth()->user()->hasRole('staff')
                                ? route('staff.archives.create')
                                : route('intern.archives.create')) }}"
                            @click="closeSidebar()"
                            class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('*.archives.create') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-500 hover:bg-emerald-50 hover:text-emerald-700 hover:translate-x-1' }}">
                            <i class="fas fa-plus mr-3 text-sm w-4 transition-colors duration-200"></i>
                            Tambah Arsip
                        </a>
                    @endif

                    <!-- Storage Location - for Admin, Staff, and Intern -->
                    @if (
                        (auth()->check() && auth()->user()->hasRole('admin')) ||
                            (auth()->check() && auth()->user()->hasRole('staff')) ||
                            (auth()->check() && auth()->user()->hasRole('intern')))
                        <a href="{{ auth()->check() && auth()->user()->hasRole('admin')
                            ? route('admin.storage.index')
                            : (auth()->check() && auth()->user()->hasRole('staff')
                                ? route('staff.storage.index')
                                : route('intern.storage.index')) }}"
                            @click="closeSidebar()"
                            class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('*.storage.index') ? 'bg-purple-50 text-purple-700' : 'text-gray-500 hover:bg-purple-50 hover:text-purple-700 hover:translate-x-1' }}">
                            <i class="fas fa-map-marker-alt mr-3 text-sm w-4 transition-colors duration-200"></i>
                            Lokasi Penyimpanan
                        </a>
                    @endif
                </div>
            </div>

            <!-- Bulk Operations - for Admin and Staff -->
            @if ((auth()->check() && auth()->user()->hasRole('admin')) || (auth()->check() && auth()->user()->hasRole('staff')))
                <a href="{{ auth()->check() && auth()->user()->hasRole('admin')
                    ? route('admin.bulk.index')
                    : route('staff.bulk.index') }}"
                    @click="closeSidebar()"
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('*.bulk.*') ? 'bg-red-50 text-red-700 border-r-4 border-red-700' : 'text-gray-600 hover:bg-red-50 hover:text-red-700 hover:translate-x-1' }}">
                    <i class="fas fa-tasks mr-3 text-lg w-5 transition-colors duration-200"></i>
                    Operasi Massal
                </a>
            @endif

            <!-- Storage Management - for Admin and Staff -->
            @if ((auth()->check() && auth()->user()->hasRole('admin')) || (auth()->check() && auth()->user()->hasRole('staff')))
                <a href="{{ auth()->check() && auth()->user()->hasRole('admin')
                    ? route('admin.storage-management.index')
                    : route('staff.storage-management.index') }}"
                    @click="closeSidebar()"
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('*.storage-management.*') ? 'bg-orange-50 text-orange-700 border-r-4 border-orange-700' : 'text-gray-600 hover:bg-orange-50 hover:text-orange-700 hover:translate-x-1' }}">
                    <i class="fas fa-cogs mr-3 text-lg w-5 transition-colors duration-200"></i>
                    Manajemen Storage
                </a>
            @endif

            <!-- Personal Files Management - Admin only -->
            @if (auth()->check() && auth()->user()->hasRole('admin'))
                <a href="{{ route('admin.personal-files.index') }}"
                    @click="closeSidebar()"
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('*.personal-files.*') ? 'bg-indigo-50 text-indigo-700 border-r-4 border-indigo-700' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-700 hover:translate-x-1' }}">
                    <i class="fas fa-user-friends mr-3 text-lg w-5 transition-colors duration-200"></i>
                    Berkas Perseorangan
                </a>
            @endif

            <!-- Cetak & Export Menu with Submenu -->
            <div class="space-y-1 submenu-container">
                <button @click="toggleCetakExportSubmenu()"
                    class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200
                               {{ request()->routeIs('*.export.*', '*.archives.export-menu', '*.storage.generate-box-labels', '*.generate-labels.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-600 hover:bg-teal-50 hover:text-teal-700 hover:translate-x-1' }}">
                    <div class="flex items-center">
                        <i class="fas fa-print mr-3 text-lg w-5 transition-colors duration-200"></i>
                        Cetak & Export
                    </div>
                    <i class="fas fa-chevron-down transform transition-transform duration-200 text-xs"
                        :class="cetakExportSubmenuOpen ? 'rotate-180' : 'rotate-0'"></i>
                </button>

                <!-- Cetak & Export Submenu -->
                <div x-show="cetakExportSubmenuOpen" x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95" class="ml-8 space-y-1" x-cloak>

                    <a href="{{ auth()->check() && auth()->user()->hasRole('admin')
                        ? route('admin.export.index')
                        : (auth()->check() && auth()->user()->hasRole('staff')
                            ? route('staff.export.index')
                            : route('intern.export.index')) }}"
                        @click="closeSidebar()"
                        class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('*.export.*', '*.archives.export-menu') ? 'bg-teal-50 text-teal-700' : 'text-gray-500 hover:bg-teal-50 hover:text-teal-700 hover:translate-x-1' }}">
                        <i class="fas fa-file-excel mr-3 text-sm w-4 transition-colors duration-200"></i>
                        Export Excel
                    </a>

                    <a href="{{ auth()->check() && auth()->user()->hasRole('admin')
                        ? route('admin.generate-labels.index')
                        : (auth()->check() && auth()->user()->hasRole('staff')
                            ? route('staff.generate-labels.index')
                            : route('intern.generate-labels.index')) }}"
                        @click="closeSidebar()"
                        class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('*.generate-labels.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-500 hover:bg-teal-50 hover:text-teal-700 hover:translate-x-1' }}">
                        <i class="fas fa-tags mr-3 text-sm w-4 transition-colors duration-200"></i>
                        Generate Label Box
                    </a>
                </div>
            </div>



            <!-- Master Data - Admin only -->
            @if (auth()->check() && auth()->user()->hasRole('admin'))
                <div class="space-y-1 submenu-container">
                    <button @click="toggleMasterSubmenu()"
                        class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200
                                   {{ request()->routeIs('admin.categories.*', 'admin.classifications.*') ? 'bg-cyan-50 text-cyan-700' : 'text-gray-600 hover:bg-cyan-50 hover:text-cyan-700 hover:translate-x-1' }}">
                        <div class="flex items-center">
                            <i class="fas fa-database mr-3 text-lg w-5 transition-colors duration-200"></i>
                            Master Data
                        </div>
                        <i class="fas fa-chevron-down transform transition-transform duration-200 text-xs"
                            :class="masterSubmenuOpen ? 'rotate-180' : 'rotate-0'"></i>
                    </button>

                    <!-- Master Data Submenu -->
                    <div x-show="masterSubmenuOpen" x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-95" class="ml-8 space-y-1" x-cloak>

                        <a href="{{ route('admin.categories.index') }}" @click="closeSidebar()"
                            class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('admin.categories.*') ? 'bg-cyan-50 text-cyan-700' : 'text-gray-500 hover:bg-cyan-50 hover:text-cyan-700 hover:translate-x-1' }}">
                            <i class="fas fa-layer-group mr-3 text-sm w-4 transition-colors duration-200"></i>
                            Kategori
                        </a>

                        <a href="{{ route('admin.classifications.index') }}" @click="closeSidebar()"
                            class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('admin.classifications.*') ? 'bg-cyan-50 text-cyan-700' : 'text-gray-500 hover:bg-cyan-50 hover:text-cyan-700 hover:translate-x-1' }}">
                            <i class="fas fa-tags mr-3 text-sm w-4 transition-colors duration-200"></i>
                            Klasifikasi
                        </a>
                    </div>
                </div>
            @endif

            <!-- Reports - Admin and Staff -->
            @if ((auth()->check() && auth()->user()->hasRole('admin')) || (auth()->check() && auth()->user()->hasRole('staff')))
                <a href="{{ auth()->check() && auth()->user()->hasRole('admin') ? route('admin.reports.retention-dashboard') : route('staff.reports.retention-dashboard') }}"
                    @click="closeSidebar()"
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('admin.reports.*', 'staff.reports.*') ? 'bg-orange-50 text-orange-700 border-r-4 border-orange-700' : 'text-gray-600 hover:bg-orange-50 hover:text-orange-700 hover:translate-x-1' }}">
                    <i class="fas fa-chart-bar mr-3 text-lg w-5 transition-colors duration-200"></i>
                    Laporan Retensi
                </a>


            @endif

            <!-- Role Management - Admin only -->
            @if (auth()->check() && auth()->user()->hasRole('admin'))
                <a href="{{ route('admin.roles.index') }}" @click="closeSidebar()"
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('admin.roles.*') ? 'bg-purple-50 text-purple-700 border-r-4 border-purple-700' : 'text-gray-600 hover:bg-purple-50 hover:text-purple-700 hover:translate-x-1' }}">
                    <i class="fas fa-users-cog mr-3 text-lg w-5 transition-colors duration-200"></i>
                    Manage Roles
                </a>
            @endif

            <!-- Logout - Available for all users -->
            <div class="pt-6 mt-6 border-t border-gray-200">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" @click="closeSidebar()"
                        class="w-full flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 text-red-600 hover:bg-red-50 hover:text-red-700 hover:translate-x-1">
                        <i class="fas fa-sign-out-alt mr-3 text-lg w-5 transition-colors duration-200"></i>
                        Logout
                    </button>
                </form>
            </div>
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
