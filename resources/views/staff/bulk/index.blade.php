<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-orange-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-list-check text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Operasi Massal</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-cogs mr-1"></i>Kelola multiple arsip sekaligus dengan aksi batch
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('staff.archives.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Arsip
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Panel -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-filter mr-2 text-blue-500"></i>Filter Arsip
            </h3>

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
                        class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors select2-dropdown">
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
                        class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors select2-dropdown">
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
                        class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors select2-dropdown">
                        <option value="">Semua Klasifikasi</option>
                        @foreach ($classifications as $classification)
                            <option value="{{ $classification->id }}"
                                data-category-id="{{ $classification->category_id }}">
                                {{ $classification->code }} - {{ $classification->nama_klasifikasi }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Created By -->
                <div>
                    <label for="created_by" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2 text-purple-500"></i>Dibuat Oleh
                    </label>
                    <select id="created_by" name="created_by"
                        class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors select2-dropdown">
                        <option value="">Semua User</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date From -->
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt mr-2 text-orange-500"></i>Tanggal Dari
                    </label>
                    <input type="date" id="date_from" name="date_from"
                        class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4">
                </div>

                <!-- Date To -->
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt mr-2 text-orange-500"></i>Tanggal Sampai
                    </label>
                    <input type="date" id="date_to" name="date_to"
                        class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4">
                </div>

                <!-- Filter Button -->
                <div class="flex items-end">
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-xl transition-colors shadow-sm">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Bulk Actions Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-tasks mr-2 text-purple-500"></i>
                Aksi Massal
            </h3>

            <!-- Selection Info -->
            <div id="selectionInfo" class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg hidden">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        <span class="text-blue-800 font-medium">Terpilih: <span id="selectedCount">0</span> arsip</span>
                    </div>
                    <button onclick="clearSelection()" class="text-blue-600 hover:text-blue-800 text-sm">
                        <i class="fas fa-times mr-1"></i>Batal Pilih
                    </button>
                </div>
            </div>

            <!-- Quick Selection -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <button type="button" id="selectAll"
                        class="flex items-center justify-center px-4 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-check-square mr-2"></i>Pilih Semua di Halaman
                </button>
                <button type="button" id="selectNone"
                        class="flex items-center justify-center px-4 py-3 bg-gray-400 hover:bg-gray-500 text-white rounded-lg transition-colors">
                    <i class="fas fa-square mr-2"></i>Batal Pilih Semua
                </button>
            </div>

            <!-- Available Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                <!-- Status Change Action -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                    <h4 class="text-sm font-semibold text-yellow-800 mb-3 flex items-center">
                        <i class="fas fa-exchange-alt mr-2"></i>Ubah Status
                    </h4>
                    <div class="space-y-3">
                        <select id="bulkNewStatus" class="w-full bg-white border border-yellow-300 rounded-lg py-2 px-3 text-sm focus:ring-2 focus:ring-yellow-500">
                            <option value="">Pilih Status Baru...</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}">{{ $status }}</option>
                            @endforeach
                        </select>
                        <button onclick="bulkStatusChange()" disabled id="statusChangeBtn"
                                class="w-full px-4 py-2 bg-yellow-600 hover:bg-yellow-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-lg text-sm transition-colors">
                            <i class="fas fa-exchange-alt mr-2"></i>Ubah Status
                        </button>
                    </div>
                </div>

                <!-- Export Action -->
                <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                    <h4 class="text-sm font-semibold text-green-800 mb-3 flex items-center">
                        <i class="fas fa-file-excel mr-2"></i>Export Data
                    </h4>
                    <div class="space-y-3">
                        <p class="text-xs text-green-700">Export arsip terpilih ke Excel</p>
                        <button onclick="bulkExport()" disabled id="exportBtn"
                                class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-lg text-sm transition-colors">
                            <i class="fas fa-download mr-2"></i>Download Excel
                        </button>
                    </div>
                </div>

                <!-- Delete Action -->
                <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                    <h4 class="text-sm font-semibold text-red-800 mb-3 flex items-center">
                        <i class="fas fa-trash mr-2"></i>Hapus Arsip
                    </h4>
                    <div class="space-y-3">
                        <p class="text-xs text-red-700">⚠️ Tindakan ini tidak dapat dibatalkan</p>
                        <button onclick="bulkDelete()" disabled id="deleteBtn"
                                class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-lg text-sm transition-colors">
                            <i class="fas fa-trash mr-2"></i>Hapus Terpilih
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Archives Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-archive mr-2 text-green-500"></i>Daftar Arsip
                    </h3>
                    <div class="text-sm text-gray-500 bg-gray-100 px-3 py-2 rounded-lg" id="totalRecords">
                        Loading...
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="archivesTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" id="selectAllCheckbox" class="rounded">
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    No. Arsip
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Uraian
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kategori
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Klasifikasi
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Dibuat Oleh
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal
                                </th>
                            </tr>
                        </thead>
                        <tbody id="archivesTableBody" class="bg-white divide-y divide-gray-200">
                            <!-- Archive rows will be loaded here via JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div id="paginationContainer" class="mt-6 flex items-center justify-between">
                    <!-- Pagination will be loaded here via JavaScript -->
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            .select2-container--default .select2-selection--single {
                height: 56px !important;
                border: 1px solid #d1d5db;
                border-radius: 0.75rem;
                padding: 0 1rem;
                display: flex;
                align-items: center;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: normal !important;
                padding-left: 0;
                color: #374151;
                text-align: left;
                flex: 1;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 56px !important;
                right: 12px;
                display: flex;
                align-items: center;
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
            // Global variables
            let selectedArchives = new Set();
            let currentPage = 1;
            let isLoading = false;

            $(document).ready(function() {
                // Initialize Select2
                $('.select2-dropdown').select2({
                    placeholder: 'Pilih opsi...',
                    allowClear: true
                });

                // Category-Classification dependency
                $('#category_id').on('change', function() {
                    const categoryId = $(this).val();
                    const classificationSelect = $('#classification_id');

                    // Clear current classification
                    classificationSelect.val('').trigger('change');

                    if (categoryId) {
                        // Show only classifications for selected category
                        classificationSelect.find('option').each(function() {
                            const option = $(this);
                            const optionCategoryId = option.data('category-id');

                            if (optionCategoryId == categoryId || optionCategoryId === undefined) {
                                option.show();
                            } else {
                                option.hide();
                            }
                        });
                    } else {
                        // Show all classifications when no category is selected
                        classificationSelect.find('option').show();
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

                // Clear filters function
                window.clearFilters = function() {
                    $('#filterForm')[0].reset();
                    $('#category_id, #classification_id, #status').val('').trigger('change');
                    loadArchives();
                };
            });

            // Global functions for onclick handlers
            function bulkStatusChange() {
                const newStatus = document.getElementById('bulkNewStatus').value;

                if (!newStatus) {
                    window.showMessage.warning('Pilih status baru terlebih dahulu!');
                    return;
                }
                if (selectedArchives.size === 0) {
                    window.showMessage.warning('Pilih arsip yang akan diubah statusnya!');
                    return;
                }

                // Use SweetAlert2 confirmation
                window.showConfirm(
                    '🔄 Konfirmasi Perubahan Status',
                    `Apakah Anda yakin ingin mengubah status ${selectedArchives.size} arsip menjadi "${newStatus}"?`,
                    'Ubah Status',
                    'warning'
                ).then((result) => {
                    if (result.isConfirmed) {
                        bulkAction('status-change', { new_status: newStatus });
                    }
                });
            }

            function bulkExport() {
                if (selectedArchives.size === 0) {
                    window.showMessage.warning('Pilih arsip yang akan diexport!');
                    return;
                }

                // Show loading
                window.showMessage.info('Memproses export data...');

                // Create form and submit for export
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("staff.bulk.export") }}';
                form.style.display = 'none';

                // CSRF token
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);

                // Archive IDs
                selectedArchives.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'archive_ids[]';
                    input.value = id;
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);

                // Show success after delay
                setTimeout(() => {
                    window.showMessage.success(`Export ${selectedArchives.size} arsip berhasil dimulai! File akan didownload secara otomatis.`);
                }, 1000);
            }

            function bulkDelete() {
                if (selectedArchives.size === 0) {
                    window.showMessage.warning('Pilih arsip yang akan dihapus!');
                    return;
                }

                // Use SweetAlert2 confirmation
                window.showConfirm(
                    '🗑️ Konfirmasi Penghapusan',
                    `Apakah Anda yakin ingin menghapus ${selectedArchives.size} arsip? Tindakan ini tidak dapat dibatalkan!`,
                    'Hapus Arsip',
                    'warning'
                ).then((result) => {
                    if (result.isConfirmed) {
                        bulkAction('delete');
                    }
                });
            }

            function clearSelection() {
                selectedArchives.clear();
                updateSelectionUI();
                updateActionButtons();
            }

            function updateActionButtons() {
                const hasSelection = selectedArchives.size > 0;
                const hasStatus = document.getElementById('bulkNewStatus').value !== '';

                // Update button states
                const statusBtn = document.getElementById('statusChangeBtn');
                const exportBtn = document.getElementById('exportBtn');
                const deleteBtn = document.getElementById('deleteBtn');

                if (statusBtn) {
                    statusBtn.disabled = !hasSelection || !hasStatus;
                }
                if (exportBtn) {
                    exportBtn.disabled = !hasSelection;
                }
                if (deleteBtn) {
                    deleteBtn.disabled = !hasSelection;
                }
            }

            function updateSelectionUI() {
                const count = selectedArchives.size;
                const selectionInfo = document.getElementById('selectionInfo');
                const selectedCountElement = document.getElementById('selectedCount');

                if (count > 0) {
                    selectionInfo.classList.remove('hidden');
                    selectedCountElement.textContent = count;
                } else {
                    selectionInfo.classList.add('hidden');
                }

                // Update master checkbox state
                const checkboxes = document.querySelectorAll('.archive-checkbox');
                const selectAllCheckbox = document.getElementById('selectAllCheckbox');
                const checkedCount = document.querySelectorAll('.archive-checkbox:checked').length;

                if (checkedCount === 0) {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = false;
                } else if (checkedCount === checkboxes.length) {
                    selectAllCheckbox.checked = true;
                    selectAllCheckbox.indeterminate = false;
                } else {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = true;
                }

                updateActionButtons();
            }

            function bulkAction(action, extraData = {}) {
                if (selectedArchives.size === 0) {
                    window.showMessage.warning('Tidak ada arsip yang dipilih!');
                    return;
                }

                const data = {
                    archive_ids: Array.from(selectedArchives),
                    ...extraData
                };

                const endpoints = {
                    'status-change': '{{ route('staff.bulk.status-change') }}',
                    'delete': '{{ route('staff.bulk.delete') }}'
                };

                // Show loading
                window.showMessage.info('Memproses...');

                fetch(endpoints[action], {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.showMessage.success(data.message);

                        // Clear selection and reload
                        selectedArchives.clear();
                        updateSelectionUI();
                        loadArchives();
                    } else {
                        window.showMessage.error(data.message || 'Terjadi kesalahan');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    window.showMessage.error('Terjadi kesalahan saat melakukan operasi');
                });
            }

            // Load archives function
            function loadArchives() {
                if (isLoading) return;

                isLoading = true;
                const formData = new FormData(document.getElementById('filterForm'));
                formData.append('page', currentPage);

                const params = new URLSearchParams();
                for (let [key, value] of formData.entries()) {
                    if (value) params.append(key, value);
                }

                fetch('{{ route('staff.bulk.get-archives') }}?' + params.toString(), {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    renderArchivesTable(data.archives);
                    renderPagination(data.pagination);
                    updateTotalRecords(data.pagination.total);
                    isLoading = false;
                })
                .catch(error => {
                    console.error('Error loading archives:', error);
                    window.showMessage.error('Terjadi kesalahan saat memuat data arsip');
                    isLoading = false;
                });
            }

            function renderArchivesTable(archives) {
                const tbody = document.getElementById('archivesTableBody');

                if (archives.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-2 block text-gray-300"></i>
                                Tidak ada data arsip yang sesuai dengan filter
                            </td>
                        </tr>
                    `;
                    return;
                }

                tbody.innerHTML = archives.map(archive => `
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" value="${archive.id}" class="archive-checkbox rounded">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            ${archive.index_number || '-'}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div class="max-w-xs truncate">${archive.description}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ${archive.category ? archive.category.nama_kategori : '-'}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ${archive.classification ? archive.classification.code + ' - ' + archive.classification.nama_klasifikasi : '-'}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                ${archive.status === 'Aktif' ? 'bg-green-100 text-green-800' :
                                  archive.status === 'Inaktif' ? 'bg-yellow-100 text-yellow-800' :
                                  archive.status === 'Permanen' ? 'bg-blue-100 text-blue-800' :
                                  'bg-red-100 text-red-800'}">
                                ${archive.status}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ${archive.created_by_user ? archive.created_by_user.name : '-'}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ${new Date(archive.created_at).toLocaleDateString('id-ID')}
                        </td>
                    </tr>
                `).join('');

                // Re-attach event listeners for checkboxes
                attachCheckboxListeners();
            }

            function renderPagination(pagination) {
                const container = document.getElementById('paginationContainer');

                if (pagination.last_page <= 1) {
                    container.innerHTML = '';
                    return;
                }

                let paginationHTML = `
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Menampilkan ${pagination.from} sampai ${pagination.to} dari ${pagination.total} data
                        </div>
                        <div class="flex space-x-1">
                `;

                // Previous button
                if (pagination.current_page > 1) {
                    paginationHTML += `
                        <button onclick="changePage(${pagination.current_page - 1})"
                                class="px-3 py-2 text-sm border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 rounded-md">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                    `;
                }

                // Page numbers
                for (let i = Math.max(1, pagination.current_page - 2); i <= Math.min(pagination.last_page, pagination.current_page + 2); i++) {
                    paginationHTML += `
                        <button onclick="changePage(${i})"
                                class="px-3 py-2 text-sm border ${i === pagination.current_page ? 'border-blue-500 bg-blue-500 text-white' : 'border-gray-300 bg-white text-gray-500 hover:bg-gray-50'} rounded-md">
                            ${i}
                        </button>
                    `;
                }

                // Next button
                if (pagination.current_page < pagination.last_page) {
                    paginationHTML += `
                        <button onclick="changePage(${pagination.current_page + 1})"
                                class="px-3 py-2 text-sm border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 rounded-md">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    `;
                }

                paginationHTML += `
                        </div>
                    </div>
                `;

                container.innerHTML = paginationHTML;
            }

            function changePage(page) {
                currentPage = page;
                selectedArchives.clear();
                updateSelectionUI();
                loadArchives();
            }

            function updateTotalRecords(total) {
                document.getElementById('totalRecords').textContent = `Total: ${total} arsip`;
            }

            function attachCheckboxListeners() {
                // Individual checkbox handlers
                document.querySelectorAll('.archive-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const archiveId = String(this.value);
                        if (this.checked) {
                            selectedArchives.add(archiveId);
                        } else {
                            selectedArchives.delete(archiveId);
                        }
                        updateSelectionUI();
                    });
                });
            }

            $(document).ready(function() {
                // Initialize Select2 for all dropdowns
                $('.select2-dropdown').select2({
                    placeholder: function() {
                        return $(this).find('option[value=""]').text();
                    },
                    allowClear: true,
                    width: '100%'
                });

                // Archive selection management
                // Select all checkbox handler
                document.getElementById('selectAllCheckbox').addEventListener('change', function() {
                    const checked = this.checked;
                    document.querySelectorAll('.archive-checkbox').forEach(checkbox => {
                        checkbox.checked = checked;
                        if (checked) {
                            selectedArchives.add(String(checkbox.value));
                        } else {
                            selectedArchives.delete(String(checkbox.value));
                        }
                    });
                    updateSelectionUI();
                });

                // Select All button
                document.getElementById('selectAll').addEventListener('click', function() {
                    document.querySelectorAll('.archive-checkbox').forEach(checkbox => {
                        checkbox.checked = true;
                        selectedArchives.add(String(checkbox.value));
                    });
                    updateSelectionUI();
                });

                // Select None button
                document.getElementById('selectNone').addEventListener('click', function() {
                    document.querySelectorAll('.archive-checkbox').forEach(checkbox => {
                        checkbox.checked = false;
                        selectedArchives.delete(String(checkbox.value));
                    });
                    updateSelectionUI();
                });

                // Load initial data
                loadArchives();
                updateActionButtons();

                // Filter form submission
                document.getElementById('filterForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    currentPage = 1;
                    selectedArchives.clear();
                    updateSelectionUI();
                    loadArchives();
                });

                // Bulk Status Change dropdown
                document.getElementById('bulkNewStatus').addEventListener('change', function() {
                    updateActionButtons();
                });

                // Handle category-classification dependencies for bulk operations
                const allClassifications = @json($classifications);

                $('#category_id').on('change', function() {
                    const categoryId = $(this).val();
                    const classificationSelect = $('#classification_id');

                    classificationSelect.empty();
                    classificationSelect.append('<option value="">Semua Klasifikasi</option>');

                    if (categoryId) {
                        const filteredClassifications = allClassifications.filter(c => c.category_id == categoryId);
                        filteredClassifications.forEach(classification => {
                            classificationSelect.append(new Option(
                                `${classification.code} - ${classification.nama_klasifikasi}`,
                                classification.id
                            ));
                        });
                    } else {
                        allClassifications.forEach(classification => {
                            classificationSelect.append(new Option(
                                `${classification.code} - ${classification.nama_klasifikasi}`,
                                classification.id
                            ));
                        });
                    }

                    classificationSelect.trigger('change');
                });
            });
        </script>
    @endpush
</x-app-layout>
