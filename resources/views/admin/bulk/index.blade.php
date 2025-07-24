<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b px-6 py-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Operasi Massal</h1>
                <p class="text-sm text-gray-600 mt-1">Kelola arsip secara massal dengan berbagai aksi bulk</p>
            </div>
            {{-- <div class="flex items-center space-x-3">
                <div class="text-sm text-gray-500 bg-gray-100 px-3 py-2 rounded-lg">
                    <i class="fas fa-info-circle mr-1"></i>
                    <span id="selectedCountHeader">0</span> arsip dipilih
                </div>
            </div> --}}
        </div>
    </div>

    <!-- Main Content -->
    <div class="p-6 space-y-6">

        <!-- Success/Error Messages -->
        <div id="alertContainer" class="hidden">
            <div id="alertMessage" class="px-4 py-3 rounded-xl relative" role="alert">
                <span class="block sm:inline" id="alertText"></span>
            </div>
        </div>

        <!-- Filter Panel -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
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
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
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
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" id="selectAllCheckbox" class="rounded">
                                </th>
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
                                    Kategori</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Klasifikasi</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Dibuat Oleh</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="archivesTableBody">
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6" id="paginationContainer">
                    <!-- Pagination will be loaded via AJAX -->
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
            // Global variables
            let selectedArchives = new Set();
            let currentPage = 1;

            // Global functions for onclick handlers
            function bulkStatusChange() {
                console.log('bulkStatusChange called');
                const newStatus = document.getElementById('bulkNewStatus').value;
                console.log('Selected status:', newStatus);
                console.log('Selected archives:', selectedArchives);

                if (!newStatus) {
                    window.showNotification('Pilih status baru terlebih dahulu!', 'warning');
                    return;
                }
                if (selectedArchives.size === 0) {
                    window.showNotification('Pilih arsip yang akan diubah statusnya!', 'warning');
                    return;
                }

                // Use custom confirmation modal for status change
                window.showConfirmModal(
                    `🔄 Konfirmasi Perubahan Status`,
                    `Apakah Anda yakin ingin mengubah status ${selectedArchives.size} arsip menjadi "${newStatus}"?`,
                    'Ubah Status',
                    'bg-blue-600 hover:bg-blue-700',
                    function() {
                        console.log('User confirmed, calling bulkAction');
                        bulkAction('status-change', { new_status: newStatus });
                    }
                );
            }

            function bulkExport() {
                console.log('bulkExport called');
                console.log('Selected archives:', selectedArchives);

                if (selectedArchives.size === 0) {
                    window.showNotification('Pilih arsip yang akan diexport!', 'warning');
                    return;
                }

                // Show loading animation
                window.showNotification('Memproses export data...', 'info', 1500);

                console.log('Creating export form');
                // Create form and submit for export
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("admin.bulk.export") }}';
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
                    console.log('Added archive ID to form:', id);
                });

                document.body.appendChild(form);
                console.log('Submitting export form');
                form.submit();
                document.body.removeChild(form);

                // Show success after delay
                setTimeout(() => {
                    window.showNotification(`✅ Export ${selectedArchives.size} arsip berhasil dimulai! File akan didownload secara otomatis.`, 'success', 5000);
                }, 1000);
            }

            function bulkDelete() {
                console.log('bulkDelete called');
                console.log('Selected archives:', selectedArchives);

                if (selectedArchives.size === 0) {
                    window.showNotification('Pilih arsip yang akan dihapus!', 'warning');
                    return;
                }

                // Use specific delete modal for deletion
                window.showDeleteModal(
                    `⚠️ PERINGATAN PENGHAPUSAN: Apakah Anda yakin ingin menghapus ${selectedArchives.size} arsip? Tindakan ini tidak dapat dibatalkan dan akan menghilangkan data secara permanen!`,
                    function() {
                        console.log('User confirmed delete, calling bulkAction');
                        bulkAction('delete');
                    }
                );
            }

            function clearSelection() {
                selectedArchives.clear();
                updateSelectionUI();
                updateActionButtons();
            }

            function updateActionButtons() {
                const hasSelection = selectedArchives.size > 0;
                const hasStatus = document.getElementById('bulkNewStatus').value !== '';

                console.log('updateActionButtons called');
                console.log('hasSelection:', hasSelection, 'selectedArchives.size:', selectedArchives.size);
                console.log('hasStatus:', hasStatus, 'bulkNewStatus value:', document.getElementById('bulkNewStatus').value);

                // Update button states
                const statusBtn = document.getElementById('statusChangeBtn');
                const exportBtn = document.getElementById('exportBtn');
                const deleteBtn = document.getElementById('deleteBtn');

                if (statusBtn) {
                    statusBtn.disabled = !hasSelection || !hasStatus;
                    console.log('statusChangeBtn disabled:', statusBtn.disabled);
                }
                if (exportBtn) {
                    exportBtn.disabled = !hasSelection;
                    console.log('exportBtn disabled:', exportBtn.disabled);
                }
                if (deleteBtn) {
                    deleteBtn.disabled = !hasSelection;
                    console.log('deleteBtn disabled:', deleteBtn.disabled);
                }

                // Show/hide selection info
                const selectionInfo = document.getElementById('selectionInfo');
                if (selectionInfo) {
                    if (hasSelection) {
                        selectionInfo.classList.remove('hidden');
                    } else {
                        selectionInfo.classList.add('hidden');
                    }
                }
            }

            function updateSelectionUI() {
                document.getElementById('selectedCount').textContent = selectedArchives.size;
                updateActionButtons();

                // Update checkboxes
                document.querySelectorAll('.archive-checkbox').forEach(checkbox => {
                    checkbox.checked = selectedArchives.has(String(checkbox.value)); // Convert to string
                });

                // Update select all checkbox
                const allCheckboxes = document.querySelectorAll('.archive-checkbox');
                const checkedCheckboxes = document.querySelectorAll('.archive-checkbox:checked');
                const selectAllCheckbox = document.getElementById('selectAllCheckbox');

                if (selectAllCheckbox) {
                    if (allCheckboxes.length === 0) {
                        selectAllCheckbox.checked = false;
                        selectAllCheckbox.indeterminate = false;
                    } else if (checkedCheckboxes.length === allCheckboxes.length) {
                        selectAllCheckbox.checked = true;
                        selectAllCheckbox.indeterminate = false;
                    } else if (checkedCheckboxes.length > 0) {
                        selectAllCheckbox.checked = false;
                        selectAllCheckbox.indeterminate = true;
                    } else {
                        selectAllCheckbox.checked = false;
                        selectAllCheckbox.indeterminate = false;
                    }
                }
            }

            function bulkAction(action, extraData = {}) {
                console.log('bulkAction called with:', action, extraData);

                if (selectedArchives.size === 0) {
                    alert('Tidak ada arsip yang dipilih!');
                    return;
                }

                console.log('Bulk action:', action, 'Extra data:', extraData, 'Selected archives:', Array.from(selectedArchives));

                const data = {
                    archive_ids: Array.from(selectedArchives),
                    ...extraData
                };

                const endpoints = {
                    'status-change': '{{ route('admin.bulk.status-change') }}',
                    'delete': '{{ route('admin.bulk.delete') }}'
                };

                console.log('Sending request to:', endpoints[action], 'with data:', data);

                fetch(endpoints[action], {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => {
                        console.log('Response status:', response.status);
                        console.log('Response headers:', response.headers);

                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Response data:', data);
                        if (data.success) {
                            // Show success notification based on action type
                            let message = '';
                            switch(action) {
                                case 'status-change':
                                    message = `✅ Status ${selectedArchives.size} arsip berhasil diubah menjadi "${extraData.new_status}"!`;
                                    break;
                                case 'delete':
                                    message = `✅ ${selectedArchives.size} arsip berhasil dihapus!`;
                                    break;
                                default:
                                    message = data.message;
                            }

                            window.showNotification(message, 'success');
                            selectedArchives.clear();
                            updateSelectionUI();

                            // Refresh the page to show updated data immediately
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);

                            // Reset form elements
                            document.getElementById('bulkNewStatus').value = '';
                        } else {
                            window.showNotification('❌ Gagal: ' + data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Bulk action error:', error);
                        window.showNotification('❌ Terjadi kesalahan: ' + error.message, 'error');
                    });
            }

            // Test function to manually enable buttons (for debugging)
            function enableAllButtons() {
                console.log('Manually enabling all buttons');
                document.getElementById('statusChangeBtn').disabled = false;
                document.getElementById('exportBtn').disabled = false;
                document.getElementById('deleteBtn').disabled = false;
            }

            // Make functions available globally for testing
            window.enableAllButtons = enableAllButtons;
            window.testBulkStatus = function() {
                selectedArchives.add('1');
                document.getElementById('bulkNewStatus').value = 'Aktif';
                updateActionButtons();
                console.log('Test data set - buttons should be enabled');
            };

            // DOM Ready initialization
            $(document).ready(function() {
                // Initialize Select2 for all dropdowns
                $('.select2-dropdown').select2({
                    placeholder: function() {
                        return $(this).find('option[value=""]').text();
                    },
                    allowClear: true,
                    width: '100%'
                });

                // Reinitialize after loading archives
                function reinitializeSelect2() {
                    $('.select2-dropdown').select2('destroy').select2({
                        placeholder: function() {
                            return $(this).find('option[value=""]').text();
                        },
                        allowClear: true,
                        width: '100%'
                    });
                }

                // Archive selection management
                // Select all checkbox handler
                document.getElementById('selectAllCheckbox').addEventListener('change', function() {
                    const checked = this.checked;
                    document.querySelectorAll('.archive-checkbox').forEach(checkbox => {
                        checkbox.checked = checked;
                        if (checked) {
                            selectedArchives.add(String(checkbox.value)); // Convert to string
                        } else {
                            selectedArchives.delete(String(checkbox.value)); // Convert to string
                        }
                    });
                    updateSelectionUI();
                });

                // Individual checkbox handlers
                document.addEventListener('change', function(e) {
                    if (e.target.classList.contains('archive-checkbox')) {
                        const archiveId = String(e.target.value); // Convert to string
                        if (e.target.checked) {
                            selectedArchives.add(archiveId);
                        } else {
                            selectedArchives.delete(archiveId);
                        }
                        updateSelectionUI();
                    }
                });

                // Select All button
                document.getElementById('selectAll').addEventListener('click', function() {
                    document.querySelectorAll('.archive-checkbox').forEach(checkbox => {
                        checkbox.checked = true;
                        selectedArchives.add(String(checkbox.value)); // Convert to string
                    });
                    updateSelectionUI();
                });

                // Select None button
                document.getElementById('selectNone').addEventListener('click', function() {
                    document.querySelectorAll('.archive-checkbox').forEach(checkbox => {
                        checkbox.checked = false;
                        selectedArchives.delete(String(checkbox.value)); // Convert to string
                    });
                    updateSelectionUI();
                });

                // Load initial data and set up event listeners
                console.log('DOM Content Loaded - initializing bulk operations');

                // Initialize
                loadArchives();
                updateActionButtons();

                // Test if buttons exist
                console.log('statusChangeBtn exists:', !!document.getElementById('statusChangeBtn'));
                console.log('exportBtn exists:', !!document.getElementById('exportBtn'));
                console.log('deleteBtn exists:', !!document.getElementById('deleteBtn'));

                // Add test function to window for debugging
                // window.enableAllButtons = enableAllButtons; // This line is now redundant as enableAllButtons is global
                // window.testBulkStatus = function() { // This line is now redundant as bulkStatusChange is global
                //     selectedArchives.add('1');
                //     document.getElementById('bulkNewStatus').value = 'Aktif';
                //     updateActionButtons();
                //     console.log('Test data set - buttons should be enabled');
                // };

                // Filter form submission
                document.getElementById('filterForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    currentPage = 1;
                    selectedArchives.clear();
                    updateSelectionUI();
                    loadArchives();
                });

                // Bulk Status Change
                document.getElementById('bulkNewStatus').addEventListener('change', function() {
                    updateActionButtons();
                });

                // Test function to manually enable buttons (for debugging)
                // This function is now global, so it can be called directly or via window
                // enableAllButtons();

                // Handle category-classification dependencies for bulk operations
                const allClassifications = @json($classifications);

                $('#category_id').on('change', function() {
                    const categoryId = $(this).val();
                    const classificationSelect = $('#classification_id');

                    classificationSelect.empty();
                    classificationSelect.append('<option value="">Semua Klasifikasi</option>');

                    if (categoryId) {
                        const filteredClassifications = allClassifications.filter(c => c.category_id ==
                            categoryId);
                        filteredClassifications.forEach(function(classification) {
                            classificationSelect.append(new Option(
                                `${classification.code} - ${classification.name}`,
                                classification.id));
                        });
                    } else {
                        allClassifications.forEach(function(classification) {
                            classificationSelect.append(new Option(
                                `${classification.code} - ${classification.name}`,
                                classification.id));
                        });
                    }

                    classificationSelect.select2('destroy').select2({
                        placeholder: "Semua Klasifikasi",
                        allowClear: true,
                        width: '100%'
                    });
                });

                $('#classification_id').on('change', function() {
                    const classificationId = $(this).val();

                    if (classificationId) {
                        const selectedClassification = allClassifications.find(c => c.id == classificationId);
                        if (selectedClassification && $('#category_id').val() != selectedClassification
                            .category_id) {
                            $('#category_id').val(selectedClassification.category_id).trigger('change.select2');
                        }
                    }
                });

                // Archive loading and table rendering functions
                function loadArchives() {
                    const formData = new FormData(document.getElementById('filterForm'));
                    formData.append('page', currentPage);

                    const params = new URLSearchParams(formData);

                    fetch(`{{ route('admin.bulk.get-archives') }}?${params}`)
                        .then(response => response.json())
                        .then(data => {
                            renderArchivesTable(data.archives);
                            renderPagination(data.pagination);
                            updateTotalRecords(data.pagination.total);
                            reinitializeSelect2(); // Reinitialize Select2 after loading archives
                        })
                        .catch(error => {
                            console.error('Error loading archives:', error);
                            alert('Gagal memuat data arsip');
                        });
                }

                function renderArchivesTable(archives) {
                    const tbody = document.getElementById('archivesTableBody');
                    tbody.innerHTML = '';

                    archives.forEach((archive, index) => {
                        const row = document.createElement('tr');
                        row.className = 'hover:bg-gray-50 transition-colors';

                        // Ensure consistent data type - use string for Set
                        const archiveId = String(archive.id);
                        const isChecked = selectedArchives.has(archiveId);

                        row.innerHTML = `
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox"
                                       class="archive-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500 focus:ring-2"
                                       value="${archive.id}"
                                       ${isChecked ? 'checked' : ''}>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${(currentPage - 1) * 25 + index + 1}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${archive.index_number || '-'}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="max-w-xs truncate" title="${archive.uraian || archive.description}">
                                    ${archive.uraian || archive.description || '-'}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${archive.category ? archive.category.nama_kategori : '-'}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${archive.classification ? `${archive.classification.code} - ${archive.classification.nama_klasifikasi}` : '-'}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                    ${getStatusBadgeClass(archive.status)}">
                                    ${archive.status}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${archive.created_by_user ? archive.created_by_user.name : '-'}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${new Date(archive.created_at).toLocaleDateString('id-ID')}
                            </td>
                        `;

                        tbody.appendChild(row);
                    });

                    // Add event listeners to checkboxes
                    document.querySelectorAll('.archive-checkbox').forEach(checkbox => {
                        checkbox.addEventListener('change', function() {
                            const archiveId = String(this.value); // Convert to string
                            if (this.checked) {
                                selectedArchives.add(archiveId);
                            } else {
                                selectedArchives.delete(archiveId);
                            }
                            updateSelectionUI();
                        });
                    });

                    updateSelectionUI(); // Ensure selection UI is updated after rendering
                }

                function renderPagination(pagination) {
                    const container = document.getElementById('paginationContainer');
                    container.innerHTML = '';

                    if (pagination.last_page > 1) {
                        const nav = document.createElement('nav');
                        nav.className = 'flex items-center justify-between';

                        const pageInfo = document.createElement('div');
                        pageInfo.className = 'text-sm text-gray-700';
                        pageInfo.innerHTML = `
                            Menampilkan ${pagination.from || 0} sampai ${pagination.to || 0} dari ${pagination.total} hasil
                        `;

                        const pageButtons = document.createElement('div');
                        pageButtons.className = 'flex space-x-1';

                        // Previous button
                        if (pagination.current_page > 1) {
                            const prevBtn = createPageButton('← Sebelumnya', pagination.current_page - 1);
                            pageButtons.appendChild(prevBtn);
                        }

                        // Page numbers
                        for (let i = 1; i <= pagination.last_page; i++) {
                            if (i === pagination.current_page ||
                                i === 1 ||
                                i === pagination.last_page ||
                                (i >= pagination.current_page - 1 && i <= pagination.current_page + 1)) {
                                const pageBtn = createPageButton(i, i, i === pagination.current_page);
                                pageButtons.appendChild(pageBtn);
                            } else if (i === pagination.current_page - 2 || i === pagination.current_page + 2) {
                                const dots = document.createElement('span');
                                dots.className = 'px-3 py-2 text-gray-500';
                                dots.textContent = '...';
                                pageButtons.appendChild(dots);
                            }
                        }

                        // Next button
                        if (pagination.current_page < pagination.last_page) {
                            const nextBtn = createPageButton('Selanjutnya →', pagination.current_page + 1);
                            pageButtons.appendChild(nextBtn);
                        }

                        nav.appendChild(pageInfo);
                        nav.appendChild(pageButtons);
                        container.appendChild(nav);
                    }
                }

                function createPageButton(text, page, isActive = false) {
                    const button = document.createElement('button');
                    button.className = `px-3 py-2 text-sm font-medium rounded-lg transition-colors ${
                        isActive
                            ? 'bg-blue-600 text-white'
                            : 'bg-white text-gray-500 hover:bg-gray-50 border border-gray-300'
                    }`;
                    button.textContent = text;
                    button.addEventListener('click', () => {
                        currentPage = page;
                        loadArchives();
                    });
                    return button;
                }

                function updateTotalRecords(total) {
                    document.getElementById('totalRecords').textContent = `Total: ${total} arsip`;
                }

                function getStatusBadgeClass(status) {
                    const classes = {
                        'Aktif': 'bg-green-100 text-green-800',
                        'Inaktif': 'bg-yellow-100 text-yellow-800',
                        'Permanen': 'bg-purple-100 text-purple-800',
                        'Musnah': 'bg-red-100 text-red-800'
                    };
                    return classes[status] || 'bg-gray-100 text-gray-800';
                }
            });
        </script>
    @endpush
</x-app-layout>
