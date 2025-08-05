<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    @php
                        $headerConfig = match ($title) {
                            'Semua Arsip' => [
                                'icon' => 'fas fa-archive',
                                'bg' => 'bg-teal-600',
                                'subtitle' => 'Staff: Manajemen lengkap semua arsip digital sistem',
                            ],
                            'Arsip Aktif' => [
                                'icon' => 'fas fa-play-circle',
                                'bg' => 'bg-emerald-600',
                                'subtitle' => 'Staff: Arsip dalam periode aktif dan dapat diakses',
                            ],
                            'Arsip Inaktif' => [
                                'icon' => 'fas fa-pause-circle',
                                'bg' => 'bg-amber-600',
                                'subtitle' => 'Staff: Arsip yang telah melewati masa aktif',
                            ],
                            'Arsip Permanen' => [
                                'icon' => 'fas fa-shield-alt',
                                'bg' => 'bg-indigo-600',
                                'subtitle' => 'Staff: Arsip dengan nilai guna berkelanjutan',
                            ],
                            'Arsip Musnah' => [
                                'icon' => 'fas fa-ban',
                                'bg' => 'bg-rose-600',
                                'subtitle' => 'Staff: Arsip yang telah dimusnahkan sesuai retensi',
                            ],
                            default => [
                                'icon' => 'fas fa-archive',
                                'bg' => 'bg-teal-600',
                                'subtitle' => 'Staff: Kelola dan pantau arsip digital',
                            ],
                        };
                    @endphp

                    <div class="w-12 h-12 {{ $headerConfig['bg'] }} rounded-xl flex items-center justify-center">
                        <i class="{{ $headerConfig['icon'] }} text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">{{ $title }}</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-user-tie mr-1"></i>{{ $headerConfig['subtitle'] }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    @if (isset($showAddButton) && $showAddButton)
                        <a href="{{ route('staff.archives.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Tambah Arsip
                        </a>
                    @endif
                    <a href="{{ route('staff.search.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-search mr-2"></i>
                        Pencarian
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="p-6 space-y-6">

        <!-- Filter Button -->
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <button onclick="showFilterModal()"
                    class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-filter mr-2"></i>
                    Filter & Pencarian
                </button>
                @if (request()->hasAny([
                        'search',
                        'category_filter',
                        'classification_filter',
                        'date_from',
                        'date_to',
                        'created_by_filter',
                    ]))
                    <span class="text-sm text-gray-600">
                        <i class="fas fa-info-circle mr-1"></i>
                        Filter aktif: {{ request()->get('search', '') ? 'Pencarian, ' : '' }}
                        {{ request()->get('category_filter') ? 'Kategori, ' : '' }}
                        {{ request()->get('classification_filter') ? 'Klasifikasi, ' : '' }}
                        {{ request()->get('date_from') ? 'Tanggal, ' : '' }}
                        {{ request()->get('created_by_filter') ? 'User' : '' }}
                        {{ request()->get('per_page') ? 'Per Halaman: ' . request()->get('per_page') : '' }}
                    </span>
                @endif
            </div>
            {{-- <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-600">
                    Total: {{ $archives->total() }} arsip
                </span>
            </div> --}}
        </div>

        <!-- Filter Modal -->
        <div id="filterModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-xl shadow-2xl max-w-5xl w-full max-h-[90vh] overflow-y-auto">

                    <!-- Modal Header -->
                    <div class="sticky top-0 bg-white border-b border-gray-200 p-6 rounded-t-xl z-10">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                <i class="fas fa-filter mr-3 text-teal-600"></i>
                                Filter & Pencarian Data Arsip
                            </h3>
                            <button onclick="hideFilterModal()"
                                class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-lg transition-all">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                        <p class="text-sm text-gray-600 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            Staff: Gunakan filter di bawah untuk mempersempit pencarian arsip
                        </p>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6">
                        <form method="GET" action="{{ request()->url() }}" id="filterForm" class="space-y-6">

                            <!-- Section 1: Pencarian Utama -->
                            <div
                                class="bg-gradient-to-r from-green-50 to-blue-50 p-5 rounded-xl border border-green-200">
                                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-search mr-2 text-green-600"></i>
                                    Pencarian Utama
                                </h4>
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-search mr-2 text-green-500"></i>
                                            Kata Kunci
                                        </label>
                                        <input type="text" name="search" id="search"
                                            value="{{ request('search') }}"
                                            placeholder="Cari berdasarkan nomor arsip, uraian, atau kata kunci lainnya..."
                                            class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all py-3 px-4">
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: Klasifikasi & Kategori -->
                            <div
                                class="bg-gradient-to-r from-indigo-50 to-purple-50 p-5 rounded-xl border border-indigo-200">
                                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-tags mr-2 text-indigo-600"></i>
                                    Klasifikasi & Kategori
                                </h4>
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label for="category_filter"
                                            class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-folder mr-2 text-indigo-500"></i>
                                            Kategori
                                        </label>
                                        <select name="category_filter" id="category_filter"
                                            class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all py-3 px-4 select2-filter">
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
                                            class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-tags mr-2 text-cyan-500"></i>
                                            Klasifikasi
                                        </label>
                                        <select name="classification_filter" id="classification_filter"
                                            class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all py-3 px-4 select2-filter">
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
                                class="bg-gradient-to-r from-orange-50 to-yellow-50 p-5 rounded-xl border border-orange-200">
                                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-calendar-alt mr-2 text-orange-600"></i>
                                    Tanggal Arsip
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-calendar mr-2 text-orange-500"></i>
                                            Dari Tanggal
                                        </label>
                                        <input type="date" name="date_from" id="date_from"
                                            value="{{ request('date_from') }}"
                                            class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all py-3 px-4">
                                    </div>
                                    <div>
                                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-calendar mr-2 text-orange-500"></i>
                                            Sampai Tanggal
                                        </label>
                                        <input type="date" name="date_to" id="date_to"
                                            value="{{ request('date_to') }}"
                                            class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all py-3 px-4">
                                    </div>
                                </div>
                            </div>

                            <!-- Section 4: User & Pagination -->
                            <div
                                class="bg-gradient-to-r from-purple-50 to-pink-50 p-5 rounded-xl border border-purple-200">
                                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-users mr-2 text-purple-600"></i>
                                    User & Pagination
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="created_by_filter"
                                            class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-user mr-2 text-purple-500"></i>
                                            Dibuat Oleh
                                        </label>
                                        <select name="created_by_filter" id="created_by_filter"
                                            class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all py-3 px-4">
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
                                            <i class="fas fa-list mr-2 text-purple-500"></i>
                                            Per Halaman
                                        </label>
                                        <select name="per_page" id="per_page"
                                            class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all py-3 px-4">
                                            <option value="50"
                                                {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                                            <option value="100"
                                                {{ request('per_page') == '100' ? 'selected' : '' }}>100</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Filter Actions -->
                            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                                <a href="{{ request()->url() }}"
                                    class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-undo mr-2"></i>
                                    Reset Filter
                                </a>
                                <button type="submit"
                                    class="inline-flex items-center px-6 py-3 border border-transparent rounded-xl text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition-colors">
                                    <i class="fas fa-filter mr-2"></i>
                                    Terapkan Filter
                                </button>
                            </div>
                        </form>
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
                        @php
                            $exportStatus = match ($title) {
                                'Arsip Aktif' => 'aktif',
                                'Arsip Inaktif' => 'inaktif',
                                'Arsip Permanen' => 'permanen',
                                'Arsip Musnah' => 'musnah',
                                default => 'all',
                            };
                        @endphp

                    </div>
                </div>

                @if ($archives->isEmpty())
                    <div class="text-center py-16">
                        <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak ada arsip ditemukan</h3>
                        <p class="text-gray-500 mb-6">{{ $title }} saat ini kosong atau tidak sesuai dengan
                            filter yang diterapkan.</p>
                        @if (isset($showAddButton) && $showAddButton)
                            {{-- <a href="{{ route('staff.archives.create') }}"
                                class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl transition-colors">
                                <i class="fas fa-plus mr-2"></i>Tambah Arsip Pertama
                            </a> --}}
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
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ ($archives->currentPage() - 1) * $archives->perPage() + $loop->iteration }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $archive->index_number }}
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
                                        <td
                                            class="px-6 py-4 max-w-xs truncate whitespace-nowrap text-sm text-gray-900">
                                            @if ($archive->box_number)
                                                <div class="text-xs max-w-xs truncate whitespace">
                                                    {{ $archive->storage_location }}
                                                </div>
                                            @else
                                                <a href="{{ route('staff.storage.create', $archive->id) }}"
                                                    class="inline-flex items-center px-2 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-xs rounded transition-colors">
                                                    <i class="fas fa-map-marker-alt mr-1"></i>Set Lokasi
                                                </a>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ route('staff.archives.show', $archive) }}"
                                                    class="text-blue-600 hover:text-blue-800 hover:bg-blue-50 p-2 rounded-lg transition-colors"
                                                    title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('staff.archives.edit', $archive) }}"
                                                    class="text-green-600 hover:text-green-800 hover:bg-green-50 p-2 rounded-lg transition-colors"
                                                    title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
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
        @push('scripts')
            <script>
                function changeStatus(archiveId, newStatus) {
                    // Use custom confirmation modal for status change (different from delete)
                    window.showConfirmModal(
                        `🔄 Konfirmasi Perubahan Status`,
                        `Apakah Anda yakin ingin mengubah status arsip menjadi "${newStatus}"?`,
                        'Ubah Status',
                        'bg-blue-600 hover:bg-blue-700',
                        function() {
                            // Show loading notification
                            window.showNotification('⏳ Mengubah status arsip...', 'info', 5000);

                            fetch('{{ route('staff.archives.change-status') }}', {
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
                                        window.showNotification(`✅ Status arsip berhasil diubah menjadi "${newStatus}"!`,
                                            'success');
                                        setTimeout(() => {
                                            location.reload();
                                        }, 1500);
                                    } else {
                                        window.showNotification('❌ Gagal mengubah status: ' + data.message, 'error');
                                    }
                                })
                                .catch(error => {
                                    window.showNotification('❌ Terjadi kesalahan: ' + error.message, 'error');
                                });
                        }
                    );
                }
            </script>
        @endpush
    @endif

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            .select2-container--default .select2-selection--single {
                height: 56px !important;
                border: 1px solid #d1d5db;
                border-radius: 0.75rem;
                padding: 0 1rem;
                /* hilangkan padding vertikal */
                display: flex;
                align-items: center;
                /* vertikal tengah */
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: normal !important;
                padding-left: 0;
                color: #374151;
                text-align: left;
                flex: 1;
                /* biar teks memenuhi sisa ruang */
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 56px !important;
                right: 12px;
                display: flex;
                align-items: center;
                /* agar panah juga vertikal tengah */
            }

            .select2-container--default .select2-selection--single:focus {
                outline: none;
                border-color: #3b82f6;
                box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
            }

            .select2-dropdown {
                border-radius: 0.75rem;
                border: 1px solid #d1d5db;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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

            // Close modal when clicking outside
            document.getElementById('filterModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    hideFilterModal();
                }
            });

            // Show success message with location options if new archive was created
            @if (session('show_location_options') && session('new_archive_id'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    showCancelButton: true,
                    confirmButtonText: 'Set Lokasi',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#14B8A6',
                    cancelButtonColor: '#6B7280'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '{{ route('staff.storage.create', session('new_archive_id')) }}';
                    }
                });
            @elseif (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 1500
                });
            @endif
        </script>
    @endpush
</x-app-layout>
