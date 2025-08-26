<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    @php
                        $internHeaderConfig = match ($title) {
                            'Semua Arsip' => [
                                'icon' => 'fas fa-graduation-cap',
                                'bg' => 'bg-gradient-to-r from-orange-500 to-pink-500',
                                'subtitle' => 'Pembelajaran pengelolaan arsip digital untuk magang',
                            ],
                            'Arsip Aktif' => [
                                'icon' => 'fas fa-book-reader',
                                'bg' => 'bg-gradient-to-r from-orange-400 to-pink-400',
                                'subtitle' => 'Pelajari arsip dalam masa aktif dan aksesibilitas',
                            ],
                            'Arsip Inaktif' => [
                                'icon' => 'fas fa-hourglass-half',
                                'bg' => 'bg-gradient-to-r from-orange-500 to-pink-500',
                                'subtitle' => 'Observasi arsip dalam transisi periode',
                            ],
                            'Arsip Permanen' => [
                                'icon' => 'fas fa-star',
                                'bg' => 'bg-gradient-to-r from-orange-400 to-pink-400',
                                'subtitle' => 'Studi arsip bernilai historis permanen',
                            ],
                            'Arsip Musnah' => [
                                'icon' => 'fas fa-exclamation-triangle',
                                'bg' => 'bg-gradient-to-r from-orange-500 to-pink-500',
                                'subtitle' => 'Referensi arsip yang telah dimusnahkan',
                            ],
                            default => [
                                'icon' => 'fas fa-graduation-cap',
                                'bg' => 'bg-gradient-to-r from-orange-500 to-pink-500',
                                'subtitle' => 'Platform pembelajaran arsip digital',
                            ],
                        };
                    @endphp

                    <div class="w-12 h-12 {{ $internHeaderConfig['bg'] }} rounded-xl flex items-center justify-center">
                        <i class="{{ $internHeaderConfig['icon'] }} text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">{{ $title }}</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>{{ $internHeaderConfig['subtitle'] }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    @if (isset($showAddButton) && $showAddButton)
                        <a href="{{ route('intern.archives.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-orange-500 to-pink-500 hover:from-orange-600 hover:to-pink-600 text-white rounded-lg transition-all duration-200 shadow-md hover:shadow-lg">
                            <i class="fas fa-plus mr-2"></i>
                            Tambah Arsip
                        </a>
                    @endif
                    <a href="{{ route('intern.search.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-pink-500 to-orange-500 hover:from-pink-600 hover:to-orange-600 text-white rounded-lg transition-all duration-200 shadow-md hover:shadow-lg">
                        <i class="fas fa-search mr-2"></i>
                        Pencarian
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="p-6 space-y-6">

        <!-- Filter Modal -->
        <div id="filterModal" class="fixed inset-0 bg-black/50 z-50 hidden">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full max-h-[70vh] overflow-y-auto">

                    <!-- Modal Header -->
                    <div class="sticky top-0 bg-white border-b border-gray-200 p-3 rounded-t-xl z-10">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-bold text-gray-900 flex items-center">
                                <i class="fas fa-filter mr-2 text-blue-600"></i>
                                Filter & Pencarian Data Arsip
                            </h3>
                            <button onclick="hideFilterModal()"
                                class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-1 rounded transition-all">
                                <i class="fas fa-times text-base"></i>
                            </button>
                        </div>
                        <p class="text-xs text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            Gunakan filter di bawah untuk mempersempit pencarian arsip
                        </p>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-3">
                        <form method="GET" action="{{ request()->url() }}" id="filterForm" class="space-y-3">

                            <!-- Section 1: Pencarian Utama -->
                            <div
                                class="bg-gradient-to-r from-green-50 to-blue-50 p-4 rounded-xl border border-green-200">
                                <h4 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
                                    <i class="fas fa-search mr-2 text-green-600"></i>
                                    Pencarian Utama
                                </h4>
                                <div class="grid grid-cols-1 gap-3">
                                    <div>
                                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                                            <i class="fas fa-search mr-2 text-green-500"></i>
                                            Kata Kunci
                                        </label>
                                        <input type="text" name="search" id="search"
                                            value="{{ request('search') }}"
                                            placeholder="Cari berdasarkan nomor arsip, uraian, atau kata kunci lainnya..."
                                            class="w-full bg-white border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all py-2 px-3 text-sm">
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: Klasifikasi & Kategori -->
                            <div
                                class="bg-gradient-to-r from-indigo-50 to-purple-50 p-4 rounded-xl border border-indigo-200">
                                <h4 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
                                    <i class="fas fa-tags mr-2 text-indigo-600"></i>
                                    Klasifikasi & Kategori
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <label for="category_filter"
                                            class="block text-sm font-medium text-gray-700 mb-1">
                                            <i class="fas fa-folder mr-2 text-indigo-500"></i>
                                            Kategori
                                        </label>
                                        <select name="category_filter" id="category_filter"
                                            class="w-full bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all py-2 px-3 text-sm select2-filter">
                                            <option value="">-- Semua Kategori --</option>
                                            @foreach ($categories ?? [] as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ request('category_filter') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->nama_kategori }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="classification_filter"
                                            class="block text-sm font-medium text-gray-700 mb-1">
                                            <i class="fas fa-tags mr-2 text-cyan-500"></i>
                                            Klasifikasi
                                        </label>
                                        <select name="classification_filter" id="classification_filter"
                                            class="w-full bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all py-2 px-3 text-sm select2-filter">
                                            <option value="">-- Semua Klasifikasi --</option>
                                            @foreach ($classifications ?? [] as $classification)
                                                <option value="{{ $classification->id }}"
                                                    {{ request('classification_filter') == $classification->id ? 'selected' : '' }}>
                                                    {{ $classification->code }} -
                                                    {{ $classification->nama_klasifikasi }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 3: Tanggal Arsip -->
                            <div
                                class="bg-gradient-to-r from-orange-50 to-yellow-50 p-4 rounded-xl border border-orange-200">
                                <h4 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
                                    <i class="fas fa-calendar-alt mr-2 text-orange-600"></i>
                                    Tanggal Arsip
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">
                                            <i class="fas fa-calendar-check mr-2 text-orange-500"></i>
                                            Dari Tanggal
                                        </label>
                                        <input type="date" name="date_from" id="date_from"
                                            value="{{ request('date_from') }}"
                                            class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all py-3 px-4">
                                    </div>
                                    <div>
                                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-calendar-check mr-2 text-yellow-500"></i>
                                            Sampai Tanggal
                                        </label>
                                        <input type="date" name="date_to" id="date_to"
                                            value="{{ request('date_to') }}"
                                            class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all py-3 px-4">
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <p class="text-sm text-gray-500 italic">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Filter berdasarkan tanggal pembuatan arsip (kosongkan untuk semua tanggal)
                                    </p>
                                </div>
                            </div>

                            <!-- Section 4: Lokasi Penyimpanan -->
                            <div
                                class="bg-gradient-to-r from-purple-50 to-pink-50 p-5 rounded-xl border border-purple-200">
                                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-map-marker-alt mr-2 text-purple-600"></i>
                                    Lokasi Penyimpanan
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label for="rack_filter" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-warehouse mr-2 text-purple-500"></i>
                                            Rak
                                        </label>
                                        <select name="rack_filter" id="rack_filter"
                                            class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all py-3 px-4">
                                            <option value="">-- Pilih Rak --</option>
                                            @foreach (\App\Models\StorageRack::where('status', 'active')->get() as $rack)
                                                <option value="{{ $rack->id }}"
                                                    {{ request('rack_filter') == $rack->id ? 'selected' : '' }}>
                                                    {{ $rack->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="row_filter" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-list mr-2 text-pink-500"></i>
                                            Baris
                                        </label>
                                        <select name="row_filter" id="row_filter"
                                            class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all py-3 px-4"
                                            disabled>
                                            <option value="">-- Pilih Baris --</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="box_filter" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-box mr-2 text-teal-500"></i>
                                            Box
                                        </label>
                                        <select name="box_filter" id="box_filter"
                                            class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all py-3 px-4"
                                            disabled>
                                            <option value="">-- Pilih Box --</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 5: Pengaturan Tampilan -->
                            <div
                                class="bg-gradient-to-r from-gray-50 to-blue-50 p-5 rounded-xl border border-gray-200">
                                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-cog mr-2 text-gray-600"></i>
                                    Pengaturan Tampilan
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="created_by_filter"
                                            class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-user mr-2 text-blue-500"></i>
                                            Dibuat Oleh
                                        </label>
                                        <select name="created_by_filter" id="created_by_filter"
                                            class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all py-3 px-4">
                                            <option value="">-- Semua User --</option>
                                            @foreach ($users ?? [] as $user)
                                                <option value="{{ $user->id }}"
                                                    {{ request('created_by_filter') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="per_page" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-list-ol mr-2 text-gray-500"></i>
                                            Data Per Halaman
                                        </label>
                                        <select name="per_page" id="per_page"
                                            class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-all py-3 px-4">
                                            <option value="25"
                                                {{ request('per_page') == '25' ? 'selected' : '' }}>25 Data</option>
                                            <option value="50"
                                                {{ request('per_page') == '50' ? 'selected' : '' }}>50 Data</option>
                                            <option value="100"
                                                {{ request('per_page') == '100' ? 'selected' : '' }}>100 Data</option>
                                            <option value="1000"
                                                {{ request('per_page') == '1000' ? 'selected' : '' }}>Semua Data
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>

                    <!-- Modal Footer -->
                    <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 p-6 rounded-b-xl">
                        <div class="flex flex-col sm:flex-row gap-3 justify-end">
                            <button type="button" onclick="resetFilters()"
                                class="order-3 sm:order-1 flex items-center justify-center px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-xl transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-undo mr-2"></i>
                                Reset Filter
                            </button>
                            <button type="button" onclick="hideFilterModal()"
                                class="order-2 sm:order-2 flex items-center justify-center px-6 py-3 bg-red-500 hover:bg-red-600 text-white font-medium rounded-xl transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-times mr-2"></i>
                                Tutup
                            </button>
                            <button type="submit" form="filterForm"
                                class="order-1 sm:order-3 flex items-center justify-center px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-filter mr-2"></i>
                                Terapkan Filter
                            </button>
                        </div>
                        <div class="mt-3 text-center">
                            <p class="text-xs text-gray-500">
                                <i class="fas fa-lightbulb mr-1"></i>
                                Tips: Gunakan kombinasi filter untuk hasil pencarian yang lebih spesifik
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Archive Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6">
                <!-- Table Header -->
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-archive mr-2 text-green-500"></i>{{ $title }}
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Total: {{ $archives->total() }} arsip
                            @if (request()->hasAny([
                                    'search',
                                    'category_filter',
                                    'classification_filter',
                                    'date_from',
                                    'date_to',
                                    'created_by_filter',
                                ]))
                                <span class="text-blue-600">(terfilter)</span>
                            @endif
                        </p>
                    </div>

                    <div class="flex space-x-3">
                        <button type="button" onclick="showFilterModal()"
                            class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-pink-700 text-white rounded-lg transition-colors">
                            <i class="fas fa-filter mr-2"></i>Filter Arsip
                        </button>
                    </div>
                </div>

                @if ($archives->isEmpty())
                    <div class="text-center py-16">
                        <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak ada arsip ditemukan</h3>
                        <p class="text-gray-500 mb-6">{{ $title }} saat ini kosong atau tidak sesuai dengan
                            filter yang diterapkan.</p>
                        @if (isset($showAddButton) && $showAddButton)
                            <a href="{{ route('intern.archives.create') }}"
                                class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-colors">
                                <i class="fas fa-plus mr-2"></i>Tambah Arsip Pertama
                            </a>
                        @endif
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        No</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        No. Arsip</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Uraian</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    {{-- <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kategori</th> --}}
                                    {{-- <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tipe</th> --}}
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Lokasi</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($archives as $archive)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td
                                            class="px-6 py-4 max-w-xs truncate whitespace-nowrap text-sm text-gray-900">
                                            {{ ($archives->currentPage() - 1) * $archives->perPage() + $loop->iteration }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <div class="max-w-xs truncate" title="{{ $archive->index_number }}"
                                                style="max-width: 200px;">
                                                {{ $archive->index_number }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <div class="max-w-xs truncate" title="{{ $archive->description }}"
                                                style="max-width: 200px;">
                                                {{ $archive->description }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusClasses = [
                                                    'Aktif' => 'bg-green-100 text-green-800',
                                                    'Inaktif' => 'bg-yellow-100 text-yellow-800',
                                                    'Permanen' => 'bg-purple-100 text-purple-800',
                                                    'Musnah' => 'bg-red-100 text-red-800',
                                                    'Dinilai Kembali' => 'bg-indigo-100 text-indigo-800',
                                                ];
                                            @endphp
                                            <span
                                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses[$archive->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ $archive->status }}
                                            </span>
                                        </td>
                                        {{-- <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($archive->category)
                                                @php
                                                    $categoryColors = [
                                                        'UMUM' => 'bg-rose-100 text-rose-800 border-rose-200',
                                                        'PEMERINTAHAN' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                                        'POLITIK' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                                        'NON KEUANGAN' => 'bg-cyan-100 text-cyan-800 border-cyan-200',
                                                        'KEAMANAN DAN KETERTIBAN' => 'bg-amber-100 text-amber-800 border-amber-200',
                                                        'KESEJAHTERAAN RAKYAT' => 'bg-lime-100 text-lime-800 border-lime-200',
                                                        'PEREKONOMIAN' => 'bg-fuchsia-100 text-fuchsia-800 border-fuchsia-200',
                                                        'PEKERJAAN UMUM DAN KETENAGAAN' => 'bg-sky-100 text-sky-800 border-sky-200',
                                                        'PENGAWASAN' => 'bg-teal-100 text-teal-800 border-teal-200',
                                                        'KEPEGAWAIAN' => 'bg-purple-100 text-purple-800 border-purple-200',
                                                        'KEUANGAN' => 'bg-pink-100 text-pink-800 border-pink-200',
                                                        'LAINNYA' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                        'NON KEPEGAWAIAN & NON KEUANGAN' => 'bg-orange-100 text-orange-800 border-orange-200',
                                                        'NON KEPEGAWAIAN' => 'bg-violet-100 text-violet-800 border-violet-200',
                                                    ];
                                                    $categoryColor = $categoryColors[$archive->category->nama_kategori] ?? 'bg-blue-100 text-blue-800 border-blue-200';
                                                @endphp
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border {{ $categoryColor }}">
                                                    {{ $archive->category->nama_kategori }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 text-xs">-</span>
                                            @endif
                                        </td> --}}
                                        {{-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if ($archive->is_parent)
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                    <i class="fas fa-folder-tree mr-1"></i>Parent
                                                </span>
                                            @elseif($archive->parent_archive_id)
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    <i class="fas fa-link mr-1"></i>Child
                                                </span>
                                            @else
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    <i class="fas fa-file mr-1"></i>Standalone
                                                </span>
                                            @endif
                                        </td> --}}
                                        <td
                                            class="px-6 py-4 max-w-xs truncate whitespace-nowrap text-sm text-gray-900">
                                                @if ($archive->box_number)
                                                    <div class="text-xs max-w-xs truncate whitespace">
                                                        {{ $archive->storage_location }}
                                                    </div>
                                                @else
                                                    @if (Auth::user()->id === $archive->created_by)
                                                        <a href="{{ route('intern.storage.create', $archive->id) }}"
                                                            class="inline-flex items-center px-2 py-1 bg-pink-600 hover:bg-orange-700 text-white text-xs rounded transition-colors">
                                                            <i class="fas fa-map-marker-alt mr-1"></i>Set Lokasi
                                                        </a>
                                                    @else
                                                        <button
                                                            onclick="showSetLocationWarning('{{ $archive->index_number }}', '{{ $archive->description }}', '{{ $archive->createdByUser->name ?? 'Unknown User' }}')"
                                                            class="inline-flex items-center px-2 py-1 bg-orange-600 hover:bg-orange-700 text-white text-xs rounded transition-colors">
                                                            <i class="fas fa-exclamation-triangle mr-1"></i>Set Lokasi
                                                        </button>
                                                    @endif
                                                @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-1">
                                                <!-- Show button for all pages -->
                                                <a href="{{ route('intern.archives.show', $archive) }}"
                                                    class="text-blue-600 hover:text-blue-800 hover:bg-blue-50 p-1.5 rounded transition-colors"
                                                    title="Lihat Detail">
                                                    <i class="fas fa-eye text-sm"></i>
                                                </a>

                                                @if (isset($showActionButtons) && $showActionButtons)
                                                    <!-- Edit and Delete buttons for status-specific pages -->
                                                    <a href="{{ route('intern.archives.edit', $archive) }}"
                                                        class="text-green-600 hover:text-green-800 hover:bg-green-50 p-1.5 rounded transition-colors"
                                                        title="Edit">
                                                        <i class="fas fa-edit text-sm"></i>
                                                    </a>
                                                    @if (Auth::user()->role_type === 'admin')
                                                        <button
                                                            onclick="confirmDeleteArchive({{ $archive->id }}, '{{ $archive->index_number }}', '{{ $archive->description }}')"
                                                            class="text-red-600 hover:text-red-800 hover:bg-red-50 p-1.5 rounded transition-colors"
                                                            title="Hapus Arsip">
                                                            <i class="fas fa-trash text-sm"></i>
                                                        </button>
                                                    @endif
                                                @else
                                                    <!-- Related archive buttons for main index page -->
                                                    <a href="{{ route('intern.archives.related', $archive) }}"
                                                        class="text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 p-1.5 rounded transition-colors"
                                                        title="Arsip Terkait">
                                                        <i class="fas fa-link text-sm"></i>
                                                    </a>
                                                    <a href="{{ route('intern.archives.create-related', $archive) }}"
                                                        class="text-purple-600 hover:text-purple-800 hover:bg-purple-50 p-1.5 rounded transition-colors"
                                                        title="Tambah Berkas Arsip yang Sama">
                                                        <i class="fas fa-plus-circle text-sm"></i>
                                                    </a>
                                                @endif
                                                @if (isset($showStatusActions) && $showStatusActions)
                                                    <button onclick="changeStatus({{ $archive->id }}, 'Aktif')"
                                                        class="text-green-600 hover:text-green-800 hover:bg-green-50 p-1.5 rounded transition-colors"
                                                        title="Aktifkan">
                                                        <i class="fas fa-play text-sm"></i>
                                                    </button>
                                                    <button onclick="changeStatus({{ $archive->id }}, 'Inaktif')"
                                                        class="text-yellow-600 hover:text-yellow-800 hover:bg-yellow-50 p-1.5 rounded transition-colors"
                                                        title="Inaktifkan">
                                                        <i class="fas fa-pause text-sm"></i>
                                                    </button>
                                                    <button onclick="changeStatus({{ $archive->id }}, 'Permanen')"
                                                        class="text-purple-600 hover:text-purple-800 hover:bg-purple-50 p-1.5 rounded transition-colors"
                                                        title="Permanenkan">
                                                        <i class="fas fa-shield-alt text-sm"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6 flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Menampilkan {{ $archives->firstItem() }} sampai {{ $archives->lastItem() }} dari
                            {{ $archives->total() }} hasil
                        </div>
                        <div>
                            {{ $archives->appends(request()->query())->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if (isset($showStatusActions) && $showStatusActions)
        <script>
            function changeStatus(archiveId, newStatus) {
                // Use SweetAlert2 for status change confirmation
                Swal.fire({
                    title: '🔄 Konfirmasi Perubahan Status',
                    text: `Apakah Anda yakin ingin mengubah status arsip menjadi "${newStatus}"?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ubah Status',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Memproses...',
                            text: 'Mengubah status arsip',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        fetch('{{ route('intern.archives.change-status') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    archive_id: archiveId,
                                    status: newStatus
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: `Status arsip berhasil diubah menjadi "${newStatus}"!`,
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'Gagal mengubah status: ' + data.message,
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Terjadi kesalahan: ' + error.message,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            });
                    }
                });
            }
        </script>
    @endif

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            /* Additional CSS untuk styling yang lebih baik */
            .select2-container--default .select2-selection--single {
                height: 56px !important;
                padding: 0.75rem 1rem !important;
                line-height: 1.25rem;
                border-radius: 0.75rem !important;
                display: flex;
                align-items: center;
                border: 1px solid #d1d5db !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: normal !important;
                padding-left: 0 !important;
                color: #374151;
                text-align: left;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 56px !important;
                right: 12px !important;
                display: flex;
                align-items: center;
            }

            .select2-container--default.select2-container--focus .select2-selection--single {
                border-color: #3b82f6 !important;
                box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2) !important;
            }

            .select2-dropdown {
                border-radius: 0.75rem !important;
                border: 1px solid #d1d5db !important;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
            }

            .select2-results__option {
                padding: 0.75rem 1rem !important;
            }

            .select2-results__option--highlighted {
                background-color: #3b82f6 !important;
                color: white !important;
            }

            /* Custom scroll bar */
            .overflow-y-auto::-webkit-scrollbar {
                width: 6px;
            }

            .overflow-y-auto::-webkit-scrollbar-track {
                background: #f1f5f9;
                border-radius: 10px;
            }

            .overflow-y-auto::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 10px;
            }

            .overflow-y-auto::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }

            /* Animation untuk modal */
            @keyframes fadeIn {
                from {
                    opacity: 0;
                }

                to {
                    opacity: 1;
                }
            }

            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateY(-20px) scale(0.95);
                }

                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }

            #filterModal {
                animation: fadeIn 0.3s ease-out;
            }

            #filterModal>div>div {
                animation: slideIn 0.3s ease-out;
            }

            /* Custom SweetAlert2 Button Styles */
            .swal2-confirm-custom {
                border-radius: 0.75rem !important;
                font-weight: 600 !important;
                padding: 12px 24px !important;
                transition: all 0.3s ease !important;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
            }

            .swal2-deny-custom {
                border-radius: 0.75rem !important;
                font-weight: 600 !important;
                padding: 12px 24px !important;
                transition: all 0.3s ease !important;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
            }

            .swal2-cancel-custom {
                border-radius: 0.75rem !important;
                font-weight: 600 !important;
                padding: 12px 24px !important;
                transition: all 0.3s ease !important;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
            }

            .swal2-confirm-custom:hover {
                transform: translateY(-2px) !important;
                box-shadow: 0 8px 15px -3px rgba(0, 0, 0, 0.2) !important;
            }

            .swal2-deny-custom:hover {
                transform: translateY(-2px) !important;
                box-shadow: 0 8px 15px -3px rgba(0, 0, 0, 0.2) !important;
            }

            .swal2-cancel-custom:hover {
                transform: translateY(-2px) !important;
                box-shadow: 0 8px 15px -3px rgba(0, 0, 0, 0.2) !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            $(document).ready(function() {
                // Initialize Select2 for filter dropdowns
                $('.select2-filter').select2({
                    placeholder: function() {
                        return $(this).find('option[value=""]').text();
                    },
                    allowClear: true,
                    width: '100%'
                });

                // Handle category-classification filter dependencies
                const allClassifications = @json($classifications ?? []);

                $('#category_filter').on('change', function() {
                    const categoryId = $(this).val();
                    const classificationSelect = $('#classification_filter');

                    // Reset classification select
                    classificationSelect.empty();
                    classificationSelect.append('<option value="">Semua Klasifikasi</option>');

                    if (categoryId) {
                        // Filter classifications by selected category
                        const filteredClassifications = allClassifications.filter(c => c.category_id ==
                            categoryId);
                        filteredClassifications.forEach(function(classification) {
                            classificationSelect.append(new Option(
                                `${classification.code} - ${classification.nama_klasifikasi}`,
                                classification.id));
                        });
                    } else {
                        // Show all classifications
                        allClassifications.forEach(function(classification) {
                            classificationSelect.append(new Option(
                                `${classification.code} - ${classification.nama_klasifikasi}`,
                                classification.id));
                        });
                    }

                    // Reinitialize Select2
                    classificationSelect.select2({
                        placeholder: "Semua Klasifikasi",
                        allowClear: true,
                        width: '100%'
                    });
                });

                $('#classification_filter').on('change', function() {
                    const classificationId = $(this).val();

                    if (classificationId) {
                        // Find the selected classification and auto-select its category
                        const selectedClassification = allClassifications.find(c => c.id == classificationId);
                        if (selectedClassification && $('#category_filter').val() != selectedClassification
                            .category_id) {
                            $('#category_filter').val(selectedClassification.category_id).trigger(
                                'change.select2');
                        }
                    }
                });

                // Handle cascading location filters
                $('#rack_filter').on('change', function() {
                    const rackId = $(this).val();
                    const rowSelect = $('#row_filter');
                    const boxSelect = $('#box_filter');

                    // Reset row and box selects
                    rowSelect.empty().append('<option value="">Pilih Baris</option>').prop('disabled', true);
                    boxSelect.empty().append('<option value="">Pilih Box</option>').prop('disabled', true);

                    if (rackId) {
                        // Enable row select and populate with available rows
                        rowSelect.prop('disabled', false);

                        // Get rows for selected rack via AJAX
                        $.get(`/intern/archives/api/rack-rows/${rackId}`, function(rows) {
                            rows.forEach(function(row) {
                                rowSelect.append(new Option(`Baris ${row.row_number}`, row
                                    .row_number));
                            });
                        });
                    }
                });

                $('#row_filter').on('change', function() {
                    const rackId = $('#rack_filter').val();
                    const rowNumber = $(this).val();
                    const boxSelect = $('#box_filter');

                    // Reset box select
                    boxSelect.empty().append('<option value="">Pilih Box</option>').prop('disabled', true);

                    if (rackId && rowNumber) {
                        // Enable box select and populate with available boxes
                        boxSelect.prop('disabled', false);

                        // Get boxes for selected rack and row via AJAX
                        $.get(`/intern/archives/api/rack-row-boxes/${rackId}/${rowNumber}`, function(boxes) {
                            boxes.forEach(function(box) {
                                const status = box.archive_count >= box.capacity ? ' (Penuh)' :
                                    box.archive_count >= box.capacity * 0.8 ?
                                    ' (Hampir Penuh)' : ' (Tersedia)';
                                boxSelect.append(new Option(`Box ${box.box_number}${status}`,
                                    box.box_number));
                            });
                        });
                    }
                });

                // Handle per page change
                $('#per_page').on('change', function() {
                    const currentUrl = new URL(window.location);
                    currentUrl.searchParams.set('per_page', $(this).val());
                    window.location.href = currentUrl.toString();
                });

                // Handle search input with debounce
                let searchTimeout;
                $('#search').on('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        const currentUrl = new URL(window.location);
                        const searchValue = $(this).val().trim();

                        if (searchValue) {
                            currentUrl.searchParams.set('search', searchValue);
                        } else {
                            currentUrl.searchParams.delete('search');
                        }

                        window.location.href = currentUrl.toString();
                    }, 500);
                });

                // Show create success message with options
                @if (session('create_success'))
                    setTimeout(function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: '{{ session('create_success') }}',
                            showDenyButton: true,
                            showCancelButton: true,
                            confirmButtonText: 'Set Lokasi Arsip',
                            denyButtonText: 'Buat Arsip Terkait',
                            cancelButtonText: 'Tutup',
                            confirmButtonColor: '#10b981',
                            denyButtonColor: '#3b82f6',
                            cancelButtonColor: '#6b7280',
                            reverseButtons: true,
                            customClass: {
                                confirmButton: 'swal2-confirm-custom',
                                denyButton: 'swal2-deny-custom',
                                cancelButton: 'swal2-cancel-custom'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Redirect to specific archive for set location
                                window.location.href =
                                    '{{ route('intern.storage.create', session('new_archive_id')) }}';
                            } else if (result.isDenied) {
                                // Redirect to create related archive
                                window.location.href =
                                    '{{ route('intern.archives.create-related', session('new_archive_id')) }}';
                            }
                        });
                    }, 500);
                @endif
            });

            // Filter Modal Functions
            function showFilterModal() {
                document.getElementById('filterModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function hideFilterModal() {
                document.getElementById('filterModal').classList.add('hidden');
                document.body.style.overflow = 'auto';
            }

            function resetFilters() {
                // Reset all filter inputs
                $('#category_filter').val('').trigger('change.select2');
                $('#classification_filter').val('').trigger('change.select2');
                $('#created_by_filter').val('');
                $('#date_from').val('');
                $('#date_to').val('');
                $('#rack_filter').val('').trigger('change.select2');
                $('#row_filter').val('').trigger('change.select2');
                $('#box_filter').val('').trigger('change.select2');
                $('#search').val('');
                $('#per_page').val('25');

                // Trigger category change to reset classification dropdown
                $('#category_filter').trigger('change');

                // Hide modal and redirect to clean URL
                hideFilterModal();
                window.location.href = window.location.pathname;
            }

            function applyFilters() {
                const form = document.getElementById('filterForm');
                const formData = new FormData(form);

                // Build query string
                const params = new URLSearchParams();
                for (let [key, value] of formData.entries()) {
                    if (value) {
                        params.append(key, value);
                    }
                }

                // Redirect with filters
                const url = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                window.location.href = url;
            }

            // Show Set Location Warning
            function showSetLocationWarning(indexNumber, description, createdBy) {
                Swal.fire({
                    title: '⚠️ Set Lokasi Penyimpanan',
                    html: `
                        <div class="text-left">
                            <p class="mb-3">Fitur Set Lokasi Penyimpanan hanya dapat dilakukan oleh user yang menginput arsip tersebut.</p>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <p class="font-semibold text-gray-800">Nomor Arsip: ${indexNumber}</p>
                                <p class="text-gray-600 text-sm">${description}</p>
                            </div>
                            <p class="text-orange-600 text-sm mt-3">
                                <i class="fas fa-info-circle mr-1"></i>
                                Silakan hubungi user (${createdBy}) yang menginput arsip ini untuk mengatur lokasi penyimpanan.
                            </p>
                        </div>
                    `,
                    icon: 'warning',
                    confirmButtonColor: '#f59e0b',
                    confirmButtonText: 'Mengerti',
                    showCancelButton: false
                });
            }

            // Delete confirmation with SweetAlert
            function confirmDeleteArchive(archiveId, indexNumber, description) {
                Swal.fire({
                    title: 'Konfirmasi Hapus Arsip',
                    html: `
                        <div class="text-left">
                            <p class="mb-3">Apakah Anda yakin ingin menghapus arsip ini?</p>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <p class="font-semibold text-gray-800">Nomor Arsip: ${indexNumber}</p>
                                <p class="text-gray-600 text-sm">${description}</p>
                            </div>
                            <p class="text-red-600 text-sm mt-3">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Data akan hilang secara permanen dan tidak dapat dikembalikan!
                            </p>
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '<i class="fas fa-trash mr-2"></i>Hapus Arsip',
                    cancelButtonText: '<i class="fas fa-times mr-2"></i>Batal',
                    reverseButtons: true,
                    customClass: {
                        confirmButton: 'swal2-confirm',
                        cancelButton: 'swal2-cancel'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Menghapus Arsip...',
                            text: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Create form and submit
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/intern/archives/${archiveId}`;

                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';

                        const methodField = document.createElement('input');
                        methodField.type = 'hidden';
                        methodField.name = '_method';
                        methodField.value = 'DELETE';

                        form.appendChild(csrfToken);
                        form.appendChild(methodField);
                        document.body.appendChild(form);

                        form.submit();
                    }
                });
            }
        </script>
    @endpush

</x-app-layout>
