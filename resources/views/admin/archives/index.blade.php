<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    @php
                        $headerConfig = match ($title) {
                            'Arsip' => [
                                'icon' => 'fas fa-archive',
                                'bg' => 'bg-blue-600',
                                'subtitle' => 'Manajemen lengkap semua arsip digital sistem',
                            ],
                            'Arsip Aktif' => [
                                'icon' => 'fas fa-play-circle',
                                'bg' => 'bg-green-600',
                                'subtitle' => 'Arsip dalam periode aktif dan dapat diakses',
                            ],
                            'Arsip Inaktif' => [
                                'icon' => 'fas fa-pause-circle',
                                'bg' => 'bg-yellow-600',
                                'subtitle' => 'Arsip yang telah melewati masa aktif',
                            ],
                            'Arsip Permanen' => [
                                'icon' => 'fas fa-shield-alt',
                                'bg' => 'bg-purple-600',
                                'subtitle' => 'Arsip dengan nilai guna berkelanjutan',
                            ],
                            'Arsip Musnah' => [
                                'icon' => 'fas fa-ban',
                                'bg' => 'bg-red-600',
                                'subtitle' => 'Arsip yang telah dimusnahkan sesuai retensi',
                            ],
                            default => [
                                'icon' => 'fas fa-archive',
                                'bg' => 'bg-gray-600',
                                'subtitle' => 'Kelola dan pantau arsip digital',
                            ],
                        };
                    @endphp

                    <div class="w-12 h-12 {{ $headerConfig['bg'] }} rounded-xl flex items-center justify-center">
                        <i class="{{ $headerConfig['icon'] }} text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">{{ $title }}</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>{{ $headerConfig['subtitle'] }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    @if (isset($showAddButton) && $showAddButton)
                        <a href="{{ route('admin.archives.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Tambah Arsip
                        </a>
                    @endif
                    <a href="{{ route('admin.search.index') }}"
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
        {{-- <!-- Filter Button -->
        <div class="flex justify-end">
            <button type="button" onclick="showFilterModal()"
                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                <i class="fas fa-filter mr-2"></i>
                Filter Arsip
            </button>
        </div> --}}

        <!-- Filter Modal -->
        <div id="filterModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <i class="fas fa-filter mr-2 text-blue-500"></i>Filter & Pencarian
                            </h3>
                            <button onclick="hideFilterModal()" class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>

                        <form method="GET" action="{{ request()->url() }}" id="filterForm"
                            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <!-- Search -->
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-search mr-2 text-green-500"></i>Pencarian
                                </label>
                                <input type="text" name="search" id="search" value="{{ request('search') }}"
                                    placeholder="Cari no. arsip atau uraian..."
                                    class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4">
                            </div>

                            <!-- Category Filter -->
                            <div>
                                <label for="category_filter" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-folder mr-2 text-indigo-500"></i>Kategori
                                </label>
                                <select name="category_filter" id="category_filter"
                                    class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4 select2-filter">
                                    <option value="">Semua Kategori</option>
                                    @foreach ($categories ?? [] as $category)
                                        <option value="{{ $category->id }}"
                                            {{ request('category_filter') == $category->id ? 'selected' : '' }}>
                                            {{ $category->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Classification Filter -->
                            <div>
                                <label for="classification_filter" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-tags mr-2 text-cyan-500"></i>Klasifikasi
                                </label>
                                <select name="classification_filter" id="classification_filter"
                                    class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4 select2-filter">
                                    <option value="">Semua Klasifikasi</option>
                                    @foreach ($classifications ?? [] as $classification)
                                        <option value="{{ $classification->id }}"
                                            {{ request('classification_filter') == $classification->id ? 'selected' : '' }}>
                                            {{ $classification->code }} - {{ $classification->nama_klasifikasi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Date Range -->
                            <div>
                                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-calendar-alt mr-2 text-orange-500"></i>Tanggal Arsip
                                </label>
                                <input type="date" name="date_from" id="date_from"
                                    value="{{ request('date_from') }}"
                                    class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4">
                            </div>

                            <!-- Location Filter (Cascading) -->
                            <div>
                                <label for="location_filter" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-map-marker-alt mr-2 text-purple-500"></i>Lokasi Penyimpanan
                                </label>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                    <!-- Rack Filter -->
                                    <select name="rack_filter" id="rack_filter"
                                        class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4">
                                        <option value="">Pilih Rak</option>
                                        @foreach (\App\Models\StorageRack::where('status', 'active')->get() as $rack)
                                            <option value="{{ $rack->id }}"
                                                {{ request('rack_filter') == $rack->id ? 'selected' : '' }}>
                                                {{ $rack->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <!-- Row Filter -->
                                    <select name="row_filter" id="row_filter"
                                        class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                        disabled>
                                        <option value="">Pilih Baris</option>
                                    </select>

                                    <!-- Box Filter -->
                                    <select name="box_filter" id="box_filter"
                                        class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                        disabled>
                                        <option value="">Pilih Box</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Created By -->
                            <div>
                                <label for="created_by_filter" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-user mr-2 text-purple-500"></i>Dibuat Oleh
                                </label>
                                <select name="created_by_filter" id="created_by_filter"
                                    class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4">
                                    <option value="">Semua User</option>
                                    @foreach ($users ?? [] as $user)
                                        <option value="{{ $user->id }}"
                                            {{ request('created_by_filter') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Per Page -->
                            <div>
                                <label for="per_page" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-list mr-2 text-gray-500"></i>Per Halaman
                                </label>
                                <select name="per_page" id="per_page"
                                    class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4">
                                    <option value="1000" {{ request('per_page') == '1000' ? 'selected' : '' }}>Semua
                                        Data</option>
                                    <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25
                                    </option>
                                    <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50
                                    </option>
                                    <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100
                                    </option>
                                </select>
                            </div>

                            <!-- Filter Buttons -->
                            <div class="flex items-end space-x-2 col-span-full">
                                <button type="submit"
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-xl transition-colors shadow-sm">
                                    <i class="fas fa-filter mr-2"></i>Terapkan Filter
                                </button>
                                <button type="button" onclick="resetFilters()"
                                    class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-3 px-4 rounded-xl transition-colors">
                                    <i class="fas fa-undo mr-2"></i>Reset
                                </button>
                                <button type="button" onclick="hideFilterModal()"
                                    class="bg-red-500 hover:bg-red-600 text-white font-medium py-3 px-4 rounded-xl transition-colors">
                                    <i class="fas fa-times mr-2"></i>Tutup
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
                        <button type="button" onclick="showFilterModal()"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
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
                            <a href="{{ route('admin.archives.create') }}"
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
                                        <td class="px-6 py-4 max-w-xs truncate whitespace-nowrap text-sm text-gray-900">
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
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if ($archive->box_number)
                                                <div class="text-xs">
                                                    {{ $archive->storage_location }}
                                                </div>
                                            @else
                                                <a href="{{ route('admin.storage.create', $archive->id) }}"
                                                    class="inline-flex items-center px-2 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-xs rounded transition-colors">
                                                    <i class="fas fa-map-marker-alt mr-1"></i>Set Lokasi
                                                </a>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ route('admin.archives.show', $archive) }}"
                                                    class="text-blue-600 hover:text-blue-800 hover:bg-blue-50 p-2 rounded-lg transition-colors"
                                                    title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.archives.edit', $archive) }}"
                                                    class="text-green-600 hover:text-green-800 hover:bg-green-50 p-2 rounded-lg transition-colors"
                                                    title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if (Auth::user()->hasRole('admin'))
                                                    <button
                                                        onclick="confirmDeleteArchive({{ $archive->id }}, '{{ $archive->index_number }}', '{{ $archive->description }}')"
                                                        class="text-red-600 hover:text-red-800 hover:bg-red-50 p-2 rounded-lg transition-colors"
                                                        title="Hapus Arsip">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                                @if (isset($showStatusActions) && $showStatusActions && $archive->status === 'Musnah')
                                                    <button onclick="changeStatus({{ $archive->id }}, 'Aktif')"
                                                        class="text-green-600 hover:text-green-800 hover:bg-green-50 p-2 rounded-lg transition-colors"
                                                        title="Aktifkan">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                    <button onclick="changeStatus({{ $archive->id }}, 'Inaktif')"
                                                        class="text-yellow-600 hover:text-yellow-800 hover:bg-yellow-50 p-2 rounded-lg transition-colors"
                                                        title="Inaktifkan">
                                                        <i class="fas fa-pause"></i>
                                                    </button>
                                                    <button onclick="changeStatus({{ $archive->id }}, 'Permanen')"
                                                        class="text-purple-600 hover:text-purple-800 hover:bg-purple-50 p-2 rounded-lg transition-colors"
                                                        title="Permanenkan">
                                                        <i class="fas fa-shield-alt"></i>
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

                            fetch('{{ route('admin.archives.change-status') }}', {
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
            /* Select2 Custom Styling */
            .select2-container--default .select2-selection--single {
                height: 56px !important;
                padding: 0.75rem 1rem !important;
                line-height: 1.25rem;
                border-radius: 0.75rem;
                display: flex;
                align-items: center;
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
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Filter modal functions
            function showFilterModal() {
                document.getElementById('filterModal').classList.remove('hidden');
                document.body.style.overflowY = 'hidden'; // Disable scroll
                document.body.style.overflowX = 'hidden'; // Disable scroll
                // Initialize Select2 for the modal
                $('.select2-filter').select2({
                    placeholder: 'Pilih opsi...',
                    allowClear: true
                });
            }

            function hideFilterModal() {
                document.getElementById('filterModal').classList.add('hidden');
                document.body.style.overflow = 'auto'; // Re-enable scroll
            }

            function resetFilters() {
                document.getElementById('filterForm').reset();
                // Reset Select2 dropdowns
                $('#category_filter, #classification_filter, #rack_filter, #row_filter, #box_filter, #created_by_filter').val(
                    '').trigger('change');
                // Reset other fields
                document.getElementById('search').value = '';
                document.getElementById('date_from').value = '';
                document.getElementById('per_page').value = '1000';
                // Hide modal and re-enable scroll
                hideFilterModal();
                // Redirect to clean URL
                window.location.href = window.location.pathname;
            }

            function showFilterModal() {
                document.getElementById('filterModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden'; // Disable scroll
                // Initialize Select2 for the modal
                $('.select2-filter').select2({
                    placeholder: 'Pilih opsi...',
                    allowClear: true
                });
            }

            function hideFilterModal() {
                document.getElementById('filterModal').classList.add('hidden');
                document.body.style.overflow = 'auto'; // Re-enable scroll
            }

            function resetFilters() {
                document.getElementById('filterForm').reset();
                // Reset Select2 dropdowns
                $('#category_filter, #classification_filter, #rack_filter, #row_filter, #box_filter, #created_by_filter').val('').trigger('change');
                // Reset other fields
                document.getElementById('search').value = '';
                document.getElementById('date_from').value = '';
                document.getElementById('per_page').value = '1000';
                // Hide modal and re-enable scroll
                hideFilterModal();
                // Redirect to clean URL
                window.location.href = window.location.pathname;
            }
        </script>

        <script>
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
                        form.action = `/admin/archives/${archiveId}`;

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
                        $.get(`/admin/archives/api/rack-rows/${rackId}`, function(rows) {
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
                        $.get(`/admin/archives/api/rack-row-boxes/${rackId}/${rowNumber}`, function(boxes) {
                            boxes.forEach(function(box) {
                                boxSelect.append(new Option(`Box ${box.box_number}`, box
                                    .box_number));
                            });
                        });
                    }
                });
            });

            // Show create success message with Set Lokasi option
            @if (session('create_success'))
                setTimeout(function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: '{{ session('create_success') }}',
                        showCancelButton: true,
                        confirmButtonText: 'Set Lokasi',
                        cancelButtonText: 'Tutup',
                        confirmButtonColor: '#10b981', // Changed to green
                        cancelButtonColor: '#6b7280',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Redirect to specific archive for set location
                            window.location.href =
                                '{{ route('admin.storage.create', session('new_archive_id')) }}';
                        }
                    });
                }, 500);
            @endif
        </script>
    @endpush
</x-app-layout>
