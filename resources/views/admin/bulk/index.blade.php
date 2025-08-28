<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-purple-500 to-blue-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-layer-group text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Operasi Massal</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-cogs mr-1"></i>Kelola multiple arsip sekaligus dengan aksi batch
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <!-- Info Fitur Button -->
                    <button type="button" onclick="showFeatureInfo()"
                        class="inline-flex items-center px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg transition-colors">
                        <i class="fas fa-question-circle mr-2"></i>
                        Info Fitur
                    </button>
                    <a href="{{ route('admin.dashboard') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Total Arsip</p>
                        <p class="text-3xl font-bold">{{ $archives->total() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-400 bg-opacity-30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-archive text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Terpilih</p>
                        <p class="text-3xl font-bold" id="selected-count">0</p>
                    </div>
                    <div class="w-12 h-12 bg-green-400 bg-opacity-30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Aktif</p>
                        <p class="text-3xl font-bold">{{ $archives->where('status', 'Aktif')->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-400 bg-opacity-30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-play-circle text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-100 text-sm font-medium">Inaktif</p>
                        <p class="text-3xl font-bold">{{ $archives->where('status', 'Inaktif')->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-400 bg-opacity-30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-pause-circle text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Panel -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-filter mr-2 text-blue-500"></i>Filter Arsip
                </h3>
                <div class="flex items-center space-x-3">
                    <button id="searchButton" onclick="loadArchives()"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-search mr-2"></i>Search
                    </button>
                    <button onclick="clearFilters()" class="text-sm text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times mr-1"></i>Clear Filters
                    </button>
                </div>
            </div>

            <form id="filterForm" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-search mr-2 text-green-500"></i>Pencarian
                    </label>
                    <input type="text" id="search" name="search" placeholder="Cari no. arsip atau uraian..."
                        class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4">
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-flag mr-2 text-yellow-500"></i>Status
                    </label>
                    <select id="status" name="status"
                        class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors select2-filter">
                        <option value="">Semua Status</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}">{{ $status }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-folder mr-2 text-indigo-500"></i>Kategori
                    </label>
                    <select id="category_id" name="category_id"
                        class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors select2-filter">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Classification -->
                <div>
                    <label for="classification_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tags mr-2 text-cyan-500"></i>Klasifikasi
                    </label>
                    <select id="classification_id" name="classification_id"
                        class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors select2-filter">
                        <option value="">Semua Klasifikasi</option>
                        @foreach ($classifications as $classification)
                            <option value="{{ $classification->id }}"
                                data-category-id="{{ $classification->category_id }}">
                                {{ $classification->code }} - {{ $classification->nama_klasifikasi }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        <!-- Bulk Actions Panel -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-tools mr-2 text-orange-500"></i>Aksi Massal
                </h3>
                <div class="flex items-center space-x-2">
                    <button onclick="selectAll()" class="text-sm text-blue-600 hover:text-blue-800">
                        <i class="fas fa-check-double mr-1"></i>Select All
                    </button>
                    <span class="text-gray-400">|</span>
                    <button onclick="deselectAll()" class="text-sm text-gray-600 hover:text-gray-800">
                        <i class="fas fa-times mr-1"></i>Deselect All
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Status Change -->
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                    <h4 class="font-semibold text-blue-900 mb-3">Ubah Status</h4>
                    <select id="bulk-status"
                        class="w-full mb-3 rounded-lg border-blue-300 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Status...</option>
                        <option value="Aktif">Aktif</option>
                        <option value="Inaktif">Inaktif</option>
                        <option value="Permanen">Permanen</option>
                        <option value="Musnah">Musnah</option>
                    </select>
                    <button onclick="bulkStatusChange()"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                        <i class="fas fa-flag mr-2"></i>Ubah Status
                    </button>
                </div>

                <!-- Delete -->
                <div class="bg-gradient-to-r from-red-50 to-red-100 rounded-lg p-4 border border-red-200">
                    <h4 class="font-semibold text-red-900 mb-3">Hapus Arsip</h4>
                    <p class="text-xs text-red-600 mb-3">Tindakan ini tidak dapat dibatalkan!</p>
                    <button onclick="bulkDelete()"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                        <i class="fas fa-trash mr-2"></i>Hapus Terpilih
                    </button>
                </div>
            </div>
        </div>

        <!-- Archives Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Daftar Arsip</h3>
                    <div class="flex items-center space-x-2 text-sm text-gray-600">
                        <span id="showing-count">Menampilkan {{ $archives->count() }} dari {{ $archives->total() }}
                            arsip</span>
                    </div>
                </div>
            </div>

            @if ($archives->count() > 0)
                <div class="overflow-x-auto">
                    <table id="archivesTable" class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" id="select-all"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    No</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nomor Arsip</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Uraian</th>
                                {{-- <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kategori</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Klasifikasi</th> --}}
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Lokasi</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($archives as $index => $archive)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" name="selected_archives[]"
                                            value="{{ $archive->id }}"
                                            class="archive-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ ($archives->currentPage() - 1) * $archives->perPage() + $loop->iteration }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $archive->index_number }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="max-w-xs truncate" title="{{ $archive->description }}">
                                            {{ $archive->description }}
                                            <p class="text-xs text-gray-500">
                                                {{ $archive->category->nama_kategori ?? 'N/A' }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ $archive->classification->code ?? 'N/A' }} -
                                                {{ $archive->classification->nama_klasifikasi ?? 'N/A' }}
                                            </p>
                                        </div>
                                    </td>
                                    {{-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $archive->category->nama_kategori ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $archive->classification->code ?? 'N/A' }} - {{ $archive->classification->nama_klasifikasi ?? 'N/A' }}
                                    </td> --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            @if ($archive->status === 'Aktif') bg-green-100 text-green-800
                                            @elseif($archive->status === 'Inaktif') bg-yellow-100 text-yellow-800
                                            @elseif($archive->status === 'Permanen') bg-purple-100 text-purple-800
                                            @elseif($archive->status === 'Musnah') bg-red-100 text-red-800
                                            @elseif($archive->status === 'Dinilai Kembali') bg-indigo-100 text-indigo-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $archive->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if ($archive->rack_number)
                                            @if ($archive->storageRack)
                                                <div class="font-medium text-blue-600">{{ $archive->storageRack->name }}</div>
                                                <div class="text-gray-600">Box: {{ $archive->box_number }}, Baris: {{ $archive->row_number }}</div>
                                            @else
                                                Rak {{ $archive->rack_number }},
                                                Box {{ $archive->box_number }},
                                                Baris {{ $archive->row_number }}
                                            @endif
                                        @else
                                            <span class="text-gray-400">Belum diset</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('admin.archives.show', $archive) }}"
                                                class="text-blue-600 hover:text-blue-900" title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.archives.edit', $archive) }}"
                                                class="text-green-600 hover:text-green-900" title="Edit">
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
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $archives->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-inbox text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada arsip</h3>
                    <p class="text-gray-500">Tidak ada arsip yang sesuai dengan filter yang dipilih.</p>
                </div>
            @endif
        </div>
    </div>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            .select2-container--default .select2-selection--single {
                height: 48px;
                border: 1px solid #d1d5db;
                border-radius: 0.75rem;
                padding: 0 12px;
                font-size: 14px;
                display: flex;
                align-items: center;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 48px;
                padding: 0;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 46px;
                right: 12px;
            }

            .select2-container--default .select2-dropdown {
                border: 1px solid #d1d5db;
                border-radius: 0.75rem;
                margin-top: 4px;
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

                $('#category_id').on('change', function() {
                    const categoryId = $(this).val();
                    const classificationSelect = $('#classification_id');

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

                $('#classification_id').on('change', function() {
                    const classificationId = $(this).val();

                    if (classificationId) {
                        // Find the selected classification and auto-select its category
                        const selectedClassification = allClassifications.find(c => c.id == classificationId);
                        if (selectedClassification && $('#category_id').val() != selectedClassification
                            .category_id) {
                            $('#category_id').val(selectedClassification.category_id).trigger(
                                'change.select2');
                        }
                    }
                });

                // Filter functionality
                let filterTimeout;
                $('#filterForm input, #filterForm select').on('change keyup', function() {
                    clearTimeout(filterTimeout);
                    filterTimeout = setTimeout(function() {
                        loadArchives();
                    }, 500);
                });

                // Search button functionality
                $('#searchButton').on('click', function(e) {
                    e.preventDefault();
                    loadArchives();
                });

                // Clear filters function
                window.clearFilters = function() {
                    $('#filterForm')[0].reset();
                    $('#category_id, #classification_id, #status').val('').trigger('change');
                    loadArchives();
                };

                // Load archives function
                window.loadArchives = function() {
                    const formData = new FormData($('#filterForm')[0]);
                    const params = new URLSearchParams();

                    // Convert FormData to URLSearchParams for GET request
                    for (let [key, value] of formData.entries()) {
                        if (value) {
                            params.append(key, value);
                        }
                    }

                    $.ajax({
                        url: '{{ route('admin.bulk.get-archives') }}?' + params.toString(),
                        method: 'GET',
                        success: function(response) {
                            console.log('API Response:', response);
                            updateArchiveTable(response.archives);
                            updatePagination(response.pagination);
                        },
                        error: function(xhr) {
                            console.error('Error loading archives:', xhr);
                            console.error('Response:', xhr.responseText);
                        }
                    });
                };

                // Update archive table
                function updateArchiveTable(archives) {
                    const tbody = $('#archivesTable tbody');
                    tbody.empty();

                    if (archives.length === 0) {
                        tbody.append(`
                            <tr>
                                <td colspan="8" class="text-center py-8 text-gray-500">
                                    <i class="fas fa-inbox text-2xl mb-2"></i>
                                    <p>Tidak ada arsip yang sesuai dengan filter</p>
                                </td>
                            </tr>
                        `);
                        return;
                    }

                    archives.forEach(function(archive) {
                        const row = `
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">
                                    <input type="checkbox" class="archive-checkbox" value="${archive.id}">
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">${archive.index_number || '-'}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">${archive.description || '-'}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusClass(archive.status)}">
                                        ${archive.status}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">${archive.category ? archive.category.nama_kategori : '-'}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">${archive.classification ? archive.classification.code + ' - ' + archive.classification.nama_klasifikasi : '-'}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">${archive.created_by_user ? archive.created_by_user.name : '-'}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">${formatDate(archive.created_at)}</td>
                            </tr>
                        `;
                        tbody.append(row);
                    });
                }

                // Update pagination
                function updatePagination(pagination) {
                    // Implement pagination update if needed
                }

                // Get status class
                function getStatusClass(status) {
                    const classes = {
                        'Aktif': 'bg-green-100 text-green-800',
                        'Inaktif': 'bg-yellow-100 text-yellow-800',
                        'Permanen': 'bg-blue-100 text-blue-800',
                        'Dinilai Kembali': 'bg-indigo-100 text-indigo-800',
                        'Musnah': 'bg-red-100 text-red-800'
                    };
                    return classes[status] || 'bg-gray-100 text-gray-800';
                }

                // Format date
                function formatDate(dateString) {
                    const date = new Date(dateString);
                    return date.toLocaleDateString('id-ID', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                }

                // Select all functionality
                $('#select-all').change(function() {
                    $('.archive-checkbox').prop('checked', this.checked);
                    updateSelectedCount();
                    updateSelectAllState();
                });

                // Individual checkbox change
                $(document).on('change', '.archive-checkbox', function() {
                    updateSelectedCount();
                    updateSelectAllState();
                });
            });

            function bulkStatusChange() {
                const selectedArchives = $('.archive-checkbox:checked');
                const newStatus = $('#bulk-status').val();

                if (selectedArchives.length === 0) {
                    Swal.fire('Peringatan', 'Pilih arsip terlebih dahulu!', 'warning');
                    return;
                }

                if (!newStatus) {
                    Swal.fire('Peringatan', 'Pilih status baru terlebih dahulu!', 'warning');
                    return;
                }

                const archiveIds = selectedArchives.map(function() {
                    return this.value;
                }).get();

                Swal.fire({
                    title: 'Konfirmasi',
                    text: `Anda yakin ingin mengubah status ${selectedArchives.length} arsip menjadi ${newStatus}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Ubah!',
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

                        // Call backend
                        $.ajax({
                            url: '{{ route('admin.bulk.status-change') }}',
                            method: 'POST',
                            data: {
                                archive_ids: archiveIds,
                                new_status: newStatus,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Berhasil!', response.message, 'success');
                                    // Reload page after 1.5 seconds
                                    setTimeout(() => {
                                        location.reload();
                                    }, 1500);
                                } else {
                                    Swal.fire('Error!', response.message, 'error');
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Error!', 'Terjadi kesalahan saat mengubah status', 'error');
                            }
                        });
                    }
                });
            }





            function bulkDelete() {
                const selectedArchives = $('.archive-checkbox:checked');

                if (selectedArchives.length === 0) {
                    Swal.fire('Peringatan', 'Pilih arsip terlebih dahulu!', 'warning');
                    return;
                }

                const archiveIds = selectedArchives.map(function() {
                    return this.value;
                }).get();

                Swal.fire({
                    title: 'Konfirmasi Penghapusan',
                    text: `Anda yakin ingin menghapus ${selectedArchives.length} arsip? Tindakan ini tidak dapat dibatalkan!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Memproses...',
                            text: 'Menghapus arsip',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Call backend
                        $.ajax({
                            url: '{{ route('admin.bulk.delete') }}',
                            method: 'POST',
                            data: {
                                archive_ids: archiveIds,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Berhasil!', response.message, 'success');
                                    // Reload page after 1.5 seconds
                                    setTimeout(() => {
                                        location.reload();
                                    }, 1500);
                                } else {
                                    Swal.fire('Error!', response.message, 'error');
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Error!', 'Terjadi kesalahan saat menghapus arsip', 'error');
                            }
                        });
                    }
                });
            }
            // Update selected count
            function updateSelectedCount() {
                const selectedCount = $('.archive-checkbox:checked').length;
                $('#selected-count').text(selectedCount);
                $('#bulk-actions').toggleClass('hidden', selectedCount === 0);
            }

            // Update select all state
            function updateSelectAllState() {
                const totalCheckboxes = $('.archive-checkbox').length;
                const checkedCheckboxes = $('.archive-checkbox:checked').length;
                $('#select-all').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes <
                    totalCheckboxes);
                $('#select-all').prop('checked', checkedCheckboxes === totalCheckboxes);
            }

            // Select All functionality
            function selectAll() {
                $('.archive-checkbox').prop('checked', true);
                updateSelectedCount();
                updateSelectAllState();
            }

            // Deselect All functionality
            function deselectAll() {
                $('.archive-checkbox').prop('checked', false);
                updateSelectedCount();
                updateSelectAllState();
            }

            // Show feature info modal
            function showFeatureInfo() {
                const html = `
                    <div class="text-left space-y-4">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="font-semibold text-blue-800 mb-2 flex items-center">
                                <i class="fas fa-layer-group mr-2"></i>
                                Fitur Operasi Massal
                            </h4>
                            <ul class="list-disc ml-5 text-sm text-blue-700 space-y-1">
                                <li><strong>Pilih Arsip:</strong> Klik checkbox pada baris arsip yang ingin dipilih</li>
                                <li><strong>Select All:</strong> Klik tombol "Select All" untuk memilih semua arsip</li>
                                <li><strong>Deselect All:</strong> Klik tombol "Deselect All" untuk menghapus pilihan</li>
                                <li><strong>Counter Terpilih:</strong> Lihat jumlah arsip yang sudah dipilih</li>
                            </ul>
                        </div>

                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <h4 class="font-semibold text-green-800 mb-2 flex items-center">
                                <i class="fas fa-tasks mr-2"></i>
                                Fitur Aksi Massal
                            </h4>
                            <ul class="list-disc ml-5 text-sm text-green-700 space-y-1">
                                <li><strong>Ubah Status:</strong> Ubah status arsip secara massal (Aktif, Inaktif, Permanen, Musnah)</li>
                                <li><strong>Hapus Arsip:</strong> Hapus arsip yang dipilih secara massal</li>
                                <li><strong>Filter Advanced:</strong> Filter berdasarkan status, kategori, klasifikasi, dan tahun</li>
                                <li><strong>Search:</strong> Cari arsip berdasarkan kata kunci</li>
                            </ul>
                        </div>

                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <h4 class="font-semibold text-yellow-800 mb-2 flex items-center">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Perhatian Khusus
                            </h4>
                            <ul class="list-disc ml-5 text-sm text-yellow-700 space-y-1">
                                <li><strong>Konfirmasi Aksi:</strong> Setiap aksi massal akan meminta konfirmasi</li>
                                <li><strong>Hapus Permanen:</strong> Arsip yang dihapus tidak dapat dikembalikan</li>
                            </ul>
                        </div>

                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                            <h4 class="font-semibold text-purple-800 mb-2 flex items-center">
                                <i class="fas fa-lightbulb mr-2"></i>
                                Tips Penggunaan
                            </h4>
                            <ul class="list-disc ml-5 text-sm text-purple-700 space-y-1">
                                <li>Gunakan filter untuk mempersempit arsip yang akan dipilih</li>
                                <li>Selalu periksa arsip yang dipilih sebelum melakukan aksi massal</li>
                                <li>Gunakan fitur search untuk menemukan arsip spesifik</li>
                                <li>Lakukan backup data sebelum melakukan operasi massal yang kritis</li>
                            </ul>
                        </div>
                    </div>
                `;

                Swal.fire({
                    title: 'Panduan Fitur: Operasi Massal',
                    html: html,
                    width: '700px',
                    confirmButtonText: 'Saya Mengerti',
                    confirmButtonColor: '#3b82f6',
                    showCloseButton: true,
                    customClass: {
                        container: 'swal2-custom-container',
                        popup: 'swal2-custom-popup'
                    }
                });
            }
        </script>
    @endpush
</x-app-layout>
