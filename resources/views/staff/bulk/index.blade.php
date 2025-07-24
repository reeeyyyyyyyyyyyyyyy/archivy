<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b px-6 py-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Operasi Massal</h1>
                <p class="text-sm text-gray-600 mt-1">Kelola arsip secara massal dengan berbagai aksi bulk</p>
            </div>
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
                                            <input type="text" id="search" name="search" placeholder="Cari no. arsip atau description..."
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
                                {{ $classification->code }} - {{ $classification->name }}
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
                        <option value="">Semua User (TU & Intern)</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Buttons -->
                <div class="flex items-end space-x-2">
                    <button type="submit" id="filterBtn"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-xl transition-colors shadow-sm">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                    <button type="button" id="resetBtn"
                        class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-3 px-4 rounded-xl transition-colors">
                        <i class="fas fa-undo"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Bulk Actions Panel -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-tools mr-2 text-orange-500"></i>Aksi Massal
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Status Change -->
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                    <h4 class="font-semibold text-blue-900 mb-2">Ubah Status</h4>
                    <div class="space-y-2">
                        <select id="newStatus" class="w-full text-sm border border-blue-300 rounded px-2 py-1">
                            <option value="">Pilih Status</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}">{{ $status }}</option>
                            @endforeach
                        </select>
                        <button onclick="bulkStatusChange()"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm py-2 px-3 rounded transition-colors">
                            <i class="fas fa-exchange-alt mr-1"></i>Ubah Status
                        </button>
                    </div>
                </div>

                <!-- Delete Selected -->
                <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                    <h4 class="font-semibold text-red-900 mb-2">Hapus Terpilih</h4>
                    <div class="space-y-2">
                        <button onclick="bulkDelete()"
                                class="w-full bg-red-600 hover:bg-red-700 text-white text-sm py-2 px-3 rounded transition-colors">
                            <i class="fas fa-trash mr-1"></i>Hapus Terpilih
                        </button>
                        <p class="text-xs text-red-600">Hapus arsip yang dipilih secara permanen</p>
                    </div>
                </div>

                <!-- Assign Classification -->
                <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                    <h4 class="font-semibold text-purple-900 mb-2">Assign Klasifikasi</h4>
                    <div class="space-y-2">
                        <select id="newClassification" class="w-full text-sm border border-purple-300 rounded px-2 py-1">
                            <option value="">Pilih Klasifikasi</option>
                            @foreach ($classifications as $classification)
                                <option value="{{ $classification->id }}">
                                    {{ $classification->code }} - {{ $classification->nama_klasifikasi }}
                                </option>
                            @endforeach
                        </select>
                        <button onclick="bulkAssignClassification()"
                                class="w-full bg-purple-600 hover:bg-purple-700 text-white text-sm py-2 px-3 rounded transition-colors">
                            <i class="fas fa-tags mr-1"></i>Assign Klasifikasi
                        </button>
                    </div>
                </div>

                <!-- Export Selected -->
                <div class="bg-orange-50 rounded-lg p-4 border border-orange-200">
                    <h4 class="font-semibold text-orange-900 mb-2">Export Terpilih</h4>
                    <div class="space-y-2">
                        <button onclick="bulkExport()"
                                class="w-full bg-orange-600 hover:bg-orange-700 text-white text-sm py-2 px-3 rounded transition-colors">
                            <i class="fas fa-file-excel mr-1"></i>Export Excel
                        </button>
                        <p class="text-xs text-orange-600">Export arsip yang dipilih</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Archives Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6">
                <!-- Table Header -->
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-archive mr-2 text-green-500"></i>Daftar Arsip
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Total: <span id="totalCount">{{ $archives->total() }}</span> arsip
                            <span class="text-blue-600">(TU & Intern)</span>
                        </p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="text-sm text-gray-500 bg-gray-100 px-3 py-2 rounded-lg">
                            <i class="fas fa-check-square mr-1"></i>
                            <span id="selectedCount">0</span> dipilih
                        </div>
                        <button onclick="selectAll()" class="text-sm text-blue-600 hover:text-blue-700">
                            <i class="fas fa-check-double mr-1"></i>Pilih Semua
                        </button>
                        <button onclick="deselectAll()" class="text-sm text-gray-600 hover:text-gray-700">
                            <i class="fas fa-times mr-1"></i>Batal Pilih
                        </button>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" id="selectAllCheckbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    No. Arsip
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Description
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kategori
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Dibuat Oleh
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal
                                </th>
                            </tr>
                        </thead>
                        <tbody id="archivesTableBody" class="bg-white divide-y divide-gray-200">
                            @foreach($archives as $archive)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" class="archive-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" value="{{ $archive->id }}">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $archive->index_number ?? 'N/A' }}
                                    </td>
                                                                <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 font-medium">{{ Str::limit($archive->description, 50) }}</div>
                                <div class="text-sm text-gray-500 truncate max-w-xs">{{ Str::limit($archive->description, 50) }}</div>
                            </td>
                                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $archive->category->nama_kategori ?? 'N/A' }}
                                </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusClasses = [
                                                'Aktif' => 'bg-green-100 text-green-800',
                                                'Inaktif' => 'bg-yellow-100 text-yellow-800',
                                                'Permanen' => 'bg-purple-100 text-purple-800',
                                                'Musnah' => 'bg-red-100 text-red-800',
                                            ];
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses[$archive->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $archive->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $archive->createdByUser->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $archive->created_at->diffForHumans() }}
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
            $(document).ready(function() {
                // Initialize Select2
                $('.select2-dropdown').select2({
                    placeholder: function() {
                        return $(this).find('option[value=""]').text();
                    },
                    allowClear: true,
                    width: '100%'
                });

                // Handle category-classification filter dependencies
                const allClassifications = @json($classifications);

                $('#category_id').on('change', function() {
                    const categoryId = $(this).val();
                    const classificationSelect = $('#classification_id');

                    // Clear current options
                    classificationSelect.empty().append('<option value="">Semua Klasifikasi</option>');

                    if (categoryId) {
                        // Filter classifications by category
                        const filteredClassifications = allClassifications.filter(c => c.category_id == categoryId);
                        filteredClassifications.forEach(c => {
                            classificationSelect.append(`<option value="${c.id}">${c.code} - ${c.name}</option>`);
                        });
                    } else {
                        // Show all classifications
                        allClassifications.forEach(c => {
                            classificationSelect.append(`<option value="${c.id}">${c.code} - ${c.name}</option>`);
                        });
                    }

                    classificationSelect.trigger('change');
                });

                // Handle checkbox selection
                $('#selectAllCheckbox').on('change', function() {
                    $('.archive-checkbox').prop('checked', this.checked);
                    updateSelectedCount();
                });

                $('.archive-checkbox').on('change', function() {
                    updateSelectedCount();
                    updateSelectAllCheckbox();
                });

                // Handle form submission
                $('#filterForm').on('submit', function(e) {
                    e.preventDefault();
                    loadArchives();
                });

                // Handle reset button
                $('#resetBtn').on('click', function() {
                    $('#filterForm')[0].reset();
                    $('.select2-dropdown').val('').trigger('change');
                    loadArchives();
                });
            });

            function updateSelectedCount() {
                const selectedCount = $('.archive-checkbox:checked').length;
                $('#selectedCount').text(selectedCount);
            }

            function updateSelectAllCheckbox() {
                const totalCheckboxes = $('.archive-checkbox').length;
                const checkedCheckboxes = $('.archive-checkbox:checked').length;
                $('#selectAllCheckbox').prop('checked', totalCheckboxes === checkedCheckboxes);
            }

            function selectAll() {
                $('.archive-checkbox').prop('checked', true);
                updateSelectedCount();
                updateSelectAllCheckbox();
            }

            function deselectAll() {
                $('.archive-checkbox').prop('checked', false);
                updateSelectedCount();
                updateSelectAllCheckbox();
            }

            function getSelectedArchiveIds() {
                return $('.archive-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();
            }

            function showAlert(message, type = 'success') {
                const alertContainer = $('#alertContainer');
                const alertMessage = $('#alertMessage');
                const alertText = $('#alertText');

                alertText.text(message);
                alertMessage.removeClass().addClass(`px-4 py-3 rounded-xl relative ${type === 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200'}`);
                alertContainer.removeClass('hidden').show();

                setTimeout(() => {
                    alertContainer.fadeOut();
                }, 5000);
            }

            function loadArchives() {
                const formData = new FormData($('#filterForm')[0]);

                $.ajax({
                    url: '{{ route("staff.bulk.get-archives") }}',
                    method: 'GET',
                    data: Object.fromEntries(formData),
                    success: function(response) {
                        if (response.archives && response.pagination) {
                            let html = '';
                            response.archives.forEach(function(archive) {
                                const statusClasses = {
                                    'Aktif': 'bg-green-100 text-green-800',
                                    'Inaktif': 'bg-yellow-100 text-yellow-800',
                                    'Permanen': 'bg-purple-100 text-purple-800',
                                    'Musnah': 'bg-red-100 text-red-800'
                                };

                                html += `
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox" class="archive-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" value="${archive.id}">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            ${archive.index_number || 'N/A'}
                                        </td>
                                                                        <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 font-medium">${archive.description ? archive.description.substring(0, 50) + (archive.description.length > 50 ? '...' : '') : 'N/A'}</div>
                                    <div class="text-sm text-gray-500 truncate max-w-xs">${archive.description || 'N/A'}</div>
                                </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            ${archive.category ? archive.category.name : 'N/A'}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClasses[archive.status] || 'bg-gray-100 text-gray-800'}">
                                                ${archive.status}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            ${archive.created_by_user ? archive.created_by_user.name : 'N/A'}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            ${new Date(archive.created_at).toLocaleDateString('id-ID')}
                                        </td>
                                    </tr>
                                `;
                            });

                            $('#archivesTableBody').html(html);
                            $('#totalCount').text(response.pagination.total);

                            // Re-bind checkbox events
                            $('.archive-checkbox').on('change', function() {
                                updateSelectedCount();
                                updateSelectAllCheckbox();
                            });

                            updateSelectedCount();
                            updateSelectAllCheckbox();
                        }
                    },
                    error: function() {
                        showAlert('Gagal memuat data arsip', 'error');
                    }
                });
            }

            function bulkStatusChange() {
                const archiveIds = getSelectedArchiveIds();
                const newStatus = $('#newStatus').val();

                if (archiveIds.length === 0) {
                    showAlert('Pilih arsip terlebih dahulu', 'error');
                    return;
                }

                if (!newStatus) {
                    showAlert('Pilih status baru', 'error');
                    return;
                }

                if (!confirm(`Yakin ingin mengubah status ${archiveIds.length} arsip menjadi "${newStatus}"?`)) {
                    return;
                }

                $.ajax({
                    url: '{{ route("staff.bulk.status-change") }}',
                    method: 'POST',
                    data: {
                        archive_ids: archiveIds,
                        new_status: newStatus,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            showAlert(response.message, 'success');
                            loadArchives();
                        } else {
                            showAlert(response.message, 'error');
                        }
                    },
                    error: function() {
                        showAlert('Terjadi kesalahan saat mengubah status', 'error');
                    }
                });
            }

            function bulkDelete() {
                const archiveIds = getSelectedArchiveIds();

                if (archiveIds.length === 0) {
                    showAlert('Pilih arsip terlebih dahulu', 'error');
                    return;
                }

                if (!confirm(`Yakin ingin menghapus ${archiveIds.length} arsip secara permanen? Tindakan ini tidak dapat dibatalkan.`)) {
                    return;
                }

                $.ajax({
                    url: '{{ route("staff.bulk.delete") }}',
                    method: 'POST',
                    data: {
                        archive_ids: archiveIds,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            showAlert(response.message, 'success');
                            loadArchives();
                        } else {
                            showAlert(response.message, 'error');
                        }
                    },
                    error: function() {
                        showAlert('Terjadi kesalahan saat menghapus arsip', 'error');
                    }
                });
            }

            function bulkAssignClassification() {
                const archiveIds = getSelectedArchiveIds();
                const newClassification = $('#newClassification').val();

                if (archiveIds.length === 0) {
                    showAlert('Pilih arsip terlebih dahulu', 'error');
                    return;
                }

                if (!newClassification) {
                    showAlert('Pilih klasifikasi baru', 'error');
                    return;
                }

                if (!confirm(`Yakin ingin mengubah klasifikasi ${archiveIds.length} arsip?`)) {
                    return;
                }

                $.ajax({
                    url: '{{ route("staff.bulk.assign-classification") }}',
                    method: 'POST',
                    data: {
                        archive_ids: archiveIds,
                        classification_id: newClassification,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            showAlert(response.message, 'success');
                            loadArchives();
                        } else {
                            showAlert(response.message, 'error');
                        }
                    },
                    error: function() {
                        showAlert('Terjadi kesalahan saat mengubah klasifikasi', 'error');
                    }
                });
            }

            function bulkExport() {
                const archiveIds = getSelectedArchiveIds();

                if (archiveIds.length === 0) {
                    showAlert('Pilih arsip terlebih dahulu', 'error');
                    return;
                }

                // Create a form and submit it to download the file
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("staff.bulk.export") }}';

                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = '{{ csrf_token() }}';
                form.appendChild(tokenInput);

                const idsInput = document.createElement('input');
                idsInput.type = 'hidden';
                idsInput.name = 'archive_ids';
                idsInput.value = JSON.stringify(archiveIds);
                form.appendChild(idsInput);

                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);
            }
        </script>
    @endpush
</x-app-layout>
