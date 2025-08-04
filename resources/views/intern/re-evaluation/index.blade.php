<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clipboard-check text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Arsip Dinilai Kembali</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Kelola arsip yang memerlukan penilaian ulang
                        </p>

                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="bg-indigo-100 text-indigo-800 text-base font-bold px-5 py-3 rounded-full">
                        {{ $archives->total() }} Arsip
                    </span>
                    <a href="{{ route('intern.re-evaluation.evaluated') }}"
                        class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                        <i class="fas fa-check-circle mr-2"></i>Sudah Dievaluasi
                    </a>
                </div>
            </div>
        </div>
    </div>
    {{-- <x-slot name="header">
        <div class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="fas fa-clipboard-check mr-2 text-indigo-600"></i>
                        Arsip Dinilai Kembali
                    </h2>
                    <div class="flex items-center space-x-3">
                        <span class="bg-indigo-100 text-indigo-800 text-base font-bold px-5 py-3 rounded-full">
                            {{ $archives->total() }} Arsip
                        </span>
                        <a href="{{ route('intern.re-evaluation.evaluated') }}"
                            class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                            <i class="fas fa-check-circle mr-2"></i>Sudah Dievaluasi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </x-slot> --}}

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-indigo-100 text-sm font-medium">Total Dinilai Kembali</p>
                            <p class="text-3xl font-bold">{{ $archives->total() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-indigo-400 bg-opacity-30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clipboard-check text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-yellow-100 text-sm font-medium">Menunggu Evaluasi</p>
                            <p class="text-3xl font-bold">{{ $archives->where('evaluation_notes', null)->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-yellow-400 bg-opacity-30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium">Sudah Dievaluasi</p>
                            <p class="text-3xl font-bold">
                                {{ $archives->where('evaluation_notes', '!=', null)->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-400 bg-opacity-30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- <!-- Filter Panel -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-filter mr-2 text-indigo-500"></i>Filter Arsip
                    </h3>
                    <button onclick="clearFilters()" class="text-sm text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times mr-1"></i>Clear Filters
                    </button>
                </div>

                <form id="filterForm" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-search mr-2 text-green-500"></i>Pencarian
                        </label>
                        <input type="text" id="search" name="search" placeholder="Cari no. arsip atau uraian..."
                            class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors py-3 px-4">
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


                    <!-- Filter Button -->
                    <div class="flex items-end">
                        <button type="button" onclick="applyFilters()"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                            <i class="fas fa-filter mr-2"></i>Terapkan Filter
                        </button>
                    </div>
                </form>
            </div> --}}

            <!-- Bulk Actions Panel -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-tasks mr-2 text-purple-500"></i>Aksi Massal
                    </h3>
                    <span id="selected-count"
                        class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                        0 dipilih
                    </span>
                </div>

                <div id="bulk-actions" class="hidden grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Status Change -->
                    <div>
                        <label for="bulkNewStatus" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-flag mr-2 text-yellow-500"></i>Status Baru
                        </label>
                        <select id="bulkNewStatus"
                            class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="">Pilih Status</option>
                            <option value="Aktif">Aktif</option>
                            <option value="Inaktif">Inaktif</option>
                            <option value="Permanen">Permanen</option>
                            <option value="Musnah">Musnah</option>
                        </select>
                    </div>

                    <!-- Evaluation Notes -->
                    <div>
                        <label for="bulkEvaluationNotes" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-sticky-note mr-2 text-blue-500"></i>Catatan Evaluasi
                        </label>
                        <textarea id="bulkEvaluationNotes" rows="3"
                            class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors py-2 px-3"
                            placeholder="Catatan evaluasi (opsional)"></textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col space-y-2">
                        <button onclick="bulkStatusChange()"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                            <i class="fas fa-flag mr-2"></i>Ubah Status
                        </button>
                        <button onclick="clearSelection()"
                            class="w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                            <i class="fas fa-times mr-2"></i>Clear Selection
                        </button>
                    </div>
                </div>
            </div>

            <!-- Archives Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Daftar Arsip Dinilai Kembali</h3>
                        <div class="flex items-center space-x-2">
                            <label class="flex items-center">
                                <input type="checkbox" id="select-all"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Pilih Semua</span>
                            </label>
                        </div>
                    </div>

                    @if ($archives->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <input type="checkbox" id="select-all-header"
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nomor Arsip</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Uraian</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Lokasi</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Evaluasi</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($archives as $archive)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="checkbox" class="archive-checkbox"
                                                    value="{{ $archive->id }}">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $archive->index_number ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                <div class="max-w-xs truncate" title="{{ $archive->description }}">
                                                    {{ $archive->description }}
                                                    <p class="text-xs text-gray-500">
                                                        {{ $archive->category->nama_kategori ?? 'N/A' }}
                                                    </p>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getStatusClass(archive.status)}">
                                                    {{ $archive->status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if ($archive->hasStorageLocation())
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                                        <i class="fas fa-check mr-1"></i>Ditempatkan
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                                        <i class="fas fa-times mr-1"></i>Belum Ditempatkan
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if ($archive->evaluation_notes)
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                                        <i class="fas fa-check mr-1"></i>Sudah Dievaluasi
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                                        <i class="fas fa-clock mr-1"></i>Menunggu Evaluasi
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('intern.re-evaluation.show', $archive) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                    <i class="fas fa-eye mr-1"></i>Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $archives->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-clipboard-check text-gray-400 text-4xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada arsip dinilai kembali</h3>
                            <p class="text-gray-500">Belum ada arsip yang masuk dalam status "Dinilai Kembali"</p>
                        </div>
                    @endif
                </div>
            </div>
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

            .select2-dropdown {
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

                // Select all functionality
                $('#select-all, #select-all-header').change(function() {
                    $('.archive-checkbox').prop('checked', this.checked);
                    updateSelectedCount();
                });

                // Individual checkbox change
                $(document).on('change', '.archive-checkbox', function() {
                    updateSelectedCount();
                    updateSelectAllState();
                });
            });

            function updateSelectedCount() {
                const selectedCount = $('.archive-checkbox:checked').length;
                $('#selected-count').text(selectedCount + ' dipilih');
                $('#bulk-actions').toggleClass('hidden', selectedCount === 0);
            }

            function updateSelectAllState() {
                const totalCheckboxes = $('.archive-checkbox').length;
                const checkedCheckboxes = $('.archive-checkbox:checked').length;
                $('#select-all, #select-all-header').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes <
                    totalCheckboxes);
                $('#select-all, #select-all-header').prop('checked', checkedCheckboxes === totalCheckboxes);
            }

            function clearSelection() {
                $('.archive-checkbox').prop('checked', false);
                $('#select-all, #select-all-header').prop('checked', false);
                updateSelectedCount();
            }

            function clearFilters() {
                $('#filterForm')[0].reset();
                $('#category_id, #classification_id').val('').trigger('change');
                // Reload page to reset filters
                location.reload();
            }

            function applyFilters() {
                const formData = new FormData($('#filterForm')[0]);

                // Add the current URL parameters to maintain pagination
                const urlParams = new URLSearchParams(window.location.search);
                for (let [key, value] of urlParams) {
                    if (!formData.has(key)) {
                        formData.append(key, value);
                    }
                }

                $.ajax({
                    url: '{{ route('intern.re-evaluation.get-archives') }}',
                    method: 'GET',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            updateArchiveTable(response.archives);
                            updatePagination(response.pagination);
                        } else {
                            console.error('Error:', response.message);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading archives:', xhr);
                        const tbody = $('tbody');
                        tbody.empty();
                        tbody.append(`
                            <tr>
                                <td colspan="7" class="text-center py-8 text-gray-500">
                                    <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                                    <p>Data tidak ada</p>
                                </td>
                            </tr>
                        `);
                    }
                });
            }

            function updateArchiveTable(archives) {
                const tbody = $('tbody');
                tbody.empty();

                if (!archives || archives.length === 0) {
                    tbody.append(`
                        <tr>
                            <td colspan="7" class="text-center py-8 text-gray-500">
                                <i class="fas fa-inbox text-2xl mb-2"></i>
                                <p>Data tidak ada</p>
                            </td>
                        </tr>
                    `);
                    return;
                }

                archives.forEach(function(archive, index) {
                    const row = `
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" class="archive-checkbox" value="${archive.id}">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${archive.index_number || 'N/A'}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="max-w-xs truncate" title="${archive.description}">
                                    ${archive.description}
                                    <p class="text-xs text-gray-500">${archive.category ? archive.category.nama_kategori : 'N/A'}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getStatusClass(archive.status)}">
                                    ${archive.status}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${archive.has_storage_location ?
                                    '<span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full"><i class="fas fa-check mr-1"></i>Ditempatkan</span>' :
                                    '<span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full"><i class="fas fa-times mr-1"></i>Belum Ditempatkan</span>'
                                }
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${archive.evaluation_notes ?
                                    '<span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full"><i class="fas fa-check mr-1"></i>Sudah Dievaluasi</span>' :
                                    '<span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full"><i class="fas fa-clock mr-1"></i>Menunggu Evaluasi</span>'
                                }
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="/intern/re-evaluation/${archive.id}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                    <i class="fas fa-eye mr-1"></i>Detail
                                </a>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            }

            function updatePagination(pagination) {
                // Implement pagination update if needed
            }

            function getStatusClass(status) {
                const classes = {
                    'Aktif': 'bg-green-100 text-green-800',
                    'Inaktif': 'bg-yellow-100 text-yellow-800',
                    'Permanen': 'bg-blue-100 text-blue-800',
                    'Musnah': 'bg-red-100 text-red-800',
                    'Dinilai Kembali': 'bg-indigo-100 text-indigo-800'
                };
                return classes[status] || 'bg-gray-100 text-gray-800';
            }

            function bulkStatusChange() {
                const selectedArchives = $('.archive-checkbox:checked');
                const newStatus = $('#bulkNewStatus').val();
                const evaluationNotes = $('#bulkEvaluationNotes').val();

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
                            url: '{{ route('intern.re-evaluation.bulk-update') }}',
                            method: 'POST',
                            data: {
                                archive_ids: archiveIds,
                                new_status: newStatus,
                                evaluation_notes: evaluationNotes,
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
        </script>
    @endpush
</x-app-layout>
