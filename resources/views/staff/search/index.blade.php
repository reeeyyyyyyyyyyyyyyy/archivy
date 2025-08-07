<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-search text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Pencarian Arsip</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-book mr-1"></i>Cari dan analisis arsip digital dengan filter yang tepat
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

    <!-- Main Content -->
    <div class="p-6 space-y-6">

        <!-- Search Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form id="staffSearchForm" action="{{ route('staff.search.search') }}" method="GET">

                <!-- Main Search Input -->
                <div class="mb-6">
                    <label for="search_term" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-search mr-2 text-blue-500"></i>Kata Kunci Pencarian
                    </label>
                    <div class="relative">
                        <input type="text"
                               name="term"
                               id="search_term"
                               value="{{ request('term') }}"
                               placeholder="Cari berdasarkan nomor arsip atau uraian..."
                               class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-base">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Basic Filters -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Status Filter -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-info-circle mr-2 text-green-500"></i>Status Arsip
                        </label>
                        <select name="status" id="status" class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="">Semua Status</option>
                            @foreach(['Aktif', 'Inaktif', 'Permanen', 'Musnah', 'Dinilai Kembali'] as $statusOption)
                                <option value="{{ $statusOption }}" {{ request('status') == $statusOption ? 'selected' : '' }}>
                                    {{ $statusOption }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Created By Filter (Only Staff and Intern Users) -->
                    <div>
                        <label for="created_by" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user-tie mr-2 text-purple-500"></i>Dibuat Oleh (TU & Intern)
                        </label>
                        <select name="created_by" id="created_by" class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="">Semua TU & Intern</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('created_by') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div class="flex space-x-3">
                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl transition-colors shadow-sm">
                            <i class="fas fa-search mr-2"></i>Cari Arsip
                        </button>

                        <button type="button" id="resetForm" class="inline-flex items-center px-4 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-xl transition-colors">
                            <i class="fas fa-undo mr-2"></i>Reset
                        </button>
                    </div>
                </div>

            </form>
        </div>

        <!-- Search Results -->
        @if(isset($archives))
            <!-- Search Statistics -->
            @if(isset($searchStats))
                <div class="bg-gradient-to-r from-blue-50 to-indigo-100 rounded-xl p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <i class="fas fa-chart-bar mr-2 text-blue-500"></i>Hasil Pencarian
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                Ditemukan <strong>{{ number_format($searchStats['total_found']) }}</strong> arsip
                                @if(!empty($searchStats['search_term']))
                                    dengan kata kunci "<strong>{{ $searchStats['search_term'] }}</strong>"
                                @endif
                            </p>
                        </div>

                        <div class="flex space-x-6 text-sm">
                            <div class="text-center">
                                <div class="font-semibold text-blue-600 text-lg">{{ $searchStats['filters_applied'] }}</div>
                                <div class="text-gray-500">Filter Aktif</div>
                            </div>
                            <div class="text-center">
                                <div class="font-semibold text-green-600 text-lg">{{ $archives->currentPage() }}</div>
                                <div class="text-gray-500">Halaman</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Results Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6">
                    @if($archives->isEmpty())
                        <div class="text-center py-16">
                            <i class="fas fa-search text-gray-300 text-6xl mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak Ada Hasil</h3>
                            <p class="text-gray-500 mb-6">Tidak ditemukan arsip yang sesuai dengan kriteria pencarian Anda.</p>
                            {{-- <button type="button" id="clearSearch" class="inline-flex items-center px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-xl transition-colors">
                                <i class="fas fa-times mr-2"></i>Hapus Filter
                            </button> --}}
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <!-- Bulk Actions -->
                            <div class="mb-4 flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <label class="flex items-center">
                                        <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">Pilih Semua</span>
                                    </label>
                                    <span id="selectedCount" class="text-sm text-gray-500">0 arsip dipilih</span>
                                </div>
                                <div class="flex space-x-2">
                                    <button type="button" id="bulkExport" class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition-colors" disabled>
                                        <i class="fas fa-file-excel mr-2"></i>Export
                                    </button>
                                    <button type="button" id="bulkStatusChange" class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition-colors" disabled>
                                        <i class="fas fa-exchange-alt mr-2"></i>Ubah Status
                                    </button>
                                </div>
                            </div>

                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <input type="checkbox" id="selectAllHeader" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        </th>
                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Arsip</th>
                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uraian</th>
                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($archives as $archive)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="checkbox" name="selected_archives[]" value="{{ $archive->id }}" class="archive-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ ($archives->currentPage() - 1) * $archives->perPage() + $loop->iteration }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $archive->index_number }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900">
                                                    @if(isset($searchTerm) && !empty($searchTerm))
                                                        {!! str_ireplace($searchTerm, '<mark class="bg-yellow-200 text-yellow-900 px-1 rounded">' . $searchTerm . '</mark>', $archive->description) !!}
                                                    @else
                                                        {{ Str::limit($archive->description, 80) }}
                                                    @endif
                                                </div>
                                                <div class="text-sm text-gray-500">{{ $archive->classification->code }} - {{ $archive->classification->nama_klasifikasi }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $archive->category->nama_kategori }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    {{ $archive->status == 'Aktif' ? 'bg-green-100 text-green-800' : '' }}
                                                    {{ $archive->status == 'Inaktif' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                    {{ $archive->status == 'Permanen' ? 'bg-purple-100 text-purple-800' : '' }}
                                                    {{ $archive->status == 'Musnah' ? 'bg-red-100 text-red-800' : '' }}
                                                    {{ $archive->status == 'Dinilai Kembali' ? 'bg-blue-100 text-blue-800' : '' }}">
                                                    {{ $archive->status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $archive->kurun_waktu_start->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex items-center space-x-3">
                                                    <a href="{{ route('staff.archives.show', $archive) }}"
                                                       class="text-blue-600 hover:text-blue-800 hover:bg-blue-50 p-2 rounded-lg transition-colors" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('staff.archives.edit', $archive) }}"
                                                       class="text-green-600 hover:text-green-800 hover:bg-green-50 p-2 rounded-lg transition-colors" title="Edit">
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
                                Menampilkan {{ $archives->firstItem() }} sampai {{ $archives->lastItem() }} dari {{ $archives->total() }} hasil
                            </div>
                            <div>
                                {{ $archives->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

    </div>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            .select2-container--default .select2-selection--single {
                height: 48px;
                border: 1px solid #d1d5db;
                border-radius: 0.75rem;
                padding: 0.75rem 1rem;
            }
            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 32px;
                padding-left: 0;
                color: #374151;
            }
            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 46px;
                right: 12px;
            }
            .select2-container--default .select2-selection--single:focus {
                outline: none;
                border-color: #3b82f6;
                box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize Select2 for dropdowns with better configuration
                $('.select2-dropdown').each(function() {
                    $(this).select2({
                        placeholder: $(this).data('placeholder') || 'Pilih...',
                        allowClear: true,
                        width: '100%',
                        minimumResultsForSearch: 0 // Always show search box
                    });
                });

                // Auto-complete functionality
                const searchInput = document.getElementById('search_term');
                const autocompleteResults = document.getElementById('autocomplete-results');
                let debounceTimer;

                if (searchInput && autocompleteResults) {
                    searchInput.addEventListener('input', function() {
                        clearTimeout(debounceTimer);
                        const term = this.value;

                        if (term.length < 2) {
                            autocompleteResults.classList.add('hidden');
                            return;
                        }

                        debounceTimer = setTimeout(() => {
                            const autocompleteRoute = '{{ route('staff.search.autocomplete') }}';

                            fetch(`${autocompleteRoute}?term=${encodeURIComponent(term)}`)
                                .then(response => response.json())
                                .then(data => {
                                    if (data.length > 0) {
                                        autocompleteResults.innerHTML = data.map(item =>
                                            `<div class="px-4 py-3 hover:bg-gray-100 cursor-pointer autocomplete-item border-b border-gray-100 last:border-0">${item}</div>`
                                        ).join('');
                                        autocompleteResults.classList.remove('hidden');
                                    } else {
                                        autocompleteResults.classList.add('hidden');
                                    }
                                })
                                .catch(error => console.error('Autocomplete error:', error));
                        }, 300);
                    });

                    // Handle autocomplete item click
                    document.addEventListener('click', function(e) {
                        if (e.target.classList.contains('autocomplete-item')) {
                            searchInput.value = e.target.textContent;
                            autocompleteResults.classList.add('hidden');
                        } else if (!searchInput.contains(e.target)) {
                            autocompleteResults.classList.add('hidden');
                        }
                    });
                }

                // Reset form
                const resetFormBtn = document.getElementById('resetForm');
                if (resetFormBtn) {
                    resetFormBtn.addEventListener('click', function() {
                        // Reset Select2 dropdowns
                        $('.select2-dropdown').val(null).trigger('change');
                        document.getElementById('staffSearchForm').reset();
                        window.location.href = '{{ route('staff.search.index') }}';
                    });
                }

                // Clear search
                const clearSearchBtn = document.getElementById('clearSearch');
                if (clearSearchBtn) {
                    clearSearchBtn.addEventListener('click', function() {
                        window.location.href = '{{ route('staff.search.index') }}';
                    });
                }

                // Save search functionality (placeholder)
                const saveSearchBtn = document.getElementById('saveSearch');
                if (saveSearchBtn) {
                    saveSearchBtn.addEventListener('click', function() {
                        alert('Fitur simpan pencarian akan segera hadir!');
                    });
                }

                // Define category change handler as named function
                function handleCategoryChange() {
                    const categoryId = $(this).val();
                    const classificationSelect = $('#classification_id');
                    const currentClassificationValue = classificationSelect.val();

                    if (categoryId) {
                        // Show loading
                        classificationSelect.append('<option value="">Loading...</option>').trigger('change');

                        fetch(`{{ route('staff.archives.get-classifications-by-category') }}?category_id=${categoryId}`)
                            .then(response => response.json())
                            .then(data => {
                                // Clear current options
                                classificationSelect.empty().append('<option value="">Semua Klasifikasi</option>');

                                // Add filtered classifications
                                data.forEach(classification => {
                                    const option = new Option(
                                        `${classification.code} - ${classification.nama_klasifikasi}`,
                                        classification.id
                                    );
                                    option.setAttribute('data-category-id', classification.category_id);
                                    classificationSelect.append(option);
                                });

                                // Restore previous selection if it exists in filtered list
                                if (currentClassificationValue && classificationSelect.find(`option[value="${currentClassificationValue}"]`).length > 0) {
                                    classificationSelect.val(currentClassificationValue);
                                }

                                // Trigger Select2 to refresh
                                classificationSelect.trigger('change');
                            })
                            .catch(error => {
                                console.error('Error loading classifications:', error);
                                classificationSelect.empty().append('<option value="">Error loading data</option>').trigger('change');
                            });
                    } else {
                        // Restore all classifications
                        classificationSelect.empty().append('<option value="">Semua Klasifikasi</option>');
                        @foreach($classifications as $classification)
                            const option{{ $classification->id }} = new Option(
                                '{{ $classification->code }} - {{ $classification->nama_klasifikasi }}',
                                '{{ $classification->id }}'
                            );
                            option{{ $classification->id }}.setAttribute('data-category-id', '{{ $classification->category_id }}');
                            classificationSelect.append(option{{ $classification->id }});
                        @endforeach

                        // Restore previous selection
                        if (currentClassificationValue) {
                            classificationSelect.val(currentClassificationValue);
                        }

                        classificationSelect.trigger('change');
                    }
                }

                // Category/Classification dependency - When Category is selected
                $('#category_id').on('change.autoFill', handleCategoryChange);

                // Classification/Category dependency - When Classification is selected
                $('#classification_id').on('change', function() {
                    const classificationId = $(this).val();
                    const categorySelect = $('#category_id');

                    if (classificationId) {
                        // Find the category for this classification
                        const selectedOption = $(this).find('option:selected');
                        const categoryId = selectedOption.data('category-id');

                        if (categoryId && categorySelect.val() != categoryId) {
                            // Temporarily disable category change handler to prevent reset
                            categorySelect.off('change.autoFill');

                            // Auto-select the category without triggering classification reset
                            categorySelect.val(categoryId).trigger('change');

                            // Re-enable category change handler after a brief delay
                            setTimeout(() => {
                                categorySelect.on('change.autoFill', handleCategoryChange);
                            }, 100);
                        }
                    }
                });

                // Bulk Operations JavaScript
                const selectAllCheckbox = document.getElementById('selectAll');
                const selectAllHeaderCheckbox = document.getElementById('selectAllHeader');
                const archiveCheckboxes = document.querySelectorAll('.archive-checkbox');
                const selectedCountSpan = document.getElementById('selectedCount');
                const bulkExportBtn = document.getElementById('bulkExport');
                const bulkStatusChangeBtn = document.getElementById('bulkStatusChange');

                // Function to update selected count and button states
                function updateBulkActions() {
                    const selectedCheckboxes = document.querySelectorAll('.archive-checkbox:checked');
                    const selectedCount = selectedCheckboxes.length;

                    selectedCountSpan.textContent = `${selectedCount} arsip dipilih`;

                    // Enable/disable bulk action buttons
                    bulkExportBtn.disabled = selectedCount === 0;
                    bulkStatusChangeBtn.disabled = selectedCount === 0;

                    // Update select all checkbox state
                    const totalCheckboxes = archiveCheckboxes.length;
                    const allChecked = selectedCount === totalCheckboxes;
                    const someChecked = selectedCount > 0 && selectedCount < totalCheckboxes;

                    if (selectAllCheckbox) {
                        selectAllCheckbox.checked = allChecked;
                        selectAllCheckbox.indeterminate = someChecked;
                    }

                    if (selectAllHeaderCheckbox) {
                        selectAllHeaderCheckbox.checked = allChecked;
                        selectAllHeaderCheckbox.indeterminate = someChecked;
                    }
                }

                // Select all functionality
                function selectAll(checked) {
                    archiveCheckboxes.forEach(checkbox => {
                        checkbox.checked = checked;
                    });
                    updateBulkActions();
                }

                // Event listeners for select all checkboxes
                if (selectAllCheckbox) {
                    selectAllCheckbox.addEventListener('change', function() {
                        selectAll(this.checked);
                    });
                }

                if (selectAllHeaderCheckbox) {
                    selectAllHeaderCheckbox.addEventListener('change', function() {
                        selectAll(this.checked);
                    });
                }

                // Event listeners for individual checkboxes
                archiveCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', updateBulkActions);
                });

                // Bulk Export functionality
                if (bulkExportBtn) {
                    bulkExportBtn.addEventListener('click', function() {
                        const selectedIds = Array.from(document.querySelectorAll('.archive-checkbox:checked'))
                            .map(checkbox => checkbox.value);

                        if (selectedIds.length === 0) {
                            alert('Pilih arsip yang akan di-export!');
                            return;
                        }

                        // Create form and submit for export
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route('staff.bulk.export') }}';

                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';

                        const archiveIdsInput = document.createElement('input');
                        archiveIdsInput.type = 'hidden';
                        archiveIdsInput.name = 'archive_ids';
                        archiveIdsInput.value = JSON.stringify(selectedIds);

                        form.appendChild(csrfToken);
                        form.appendChild(archiveIdsInput);
                        document.body.appendChild(form);
                        form.submit();
                    });
                }

                // Bulk Status Change functionality
                if (bulkStatusChangeBtn) {
                    bulkStatusChangeBtn.addEventListener('click', function() {
                        const selectedIds = Array.from(document.querySelectorAll('.archive-checkbox:checked'))
                            .map(checkbox => checkbox.value);

                        if (selectedIds.length === 0) {
                            alert('Pilih arsip yang akan diubah statusnya!');
                            return;
                        }

                        // Show status selection modal
                        const newStatus = prompt('Masukkan status baru (Aktif/Inaktif/Permanen/Musnah):');
                        if (!newStatus) return;

                        const validStatuses = ['Aktif', 'Inaktif', 'Permanen', 'Musnah'];
                        if (!validStatuses.includes(newStatus)) {
                            alert('Status tidak valid! Gunakan: Aktif, Inaktif, Permanen, atau Musnah');
                            return;
                        }

                        // Confirm action
                        if (!confirm(`Ubah status ${selectedIds.length} arsip menjadi "${newStatus}"?`)) {
                            return;
                        }

                        // Submit status change request
                        fetch('{{ route('staff.bulk.status-change') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                archive_ids: selectedIds,
                                new_status: newStatus
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(`Berhasil mengubah status ${data.updated_count} arsip!`);
                                location.reload();
                            } else {
                                alert('Gagal mengubah status: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Terjadi kesalahan saat mengubah status');
                        });
                    });
                }

                // Initialize bulk actions
                updateBulkActions();
            });
        </script>
    @endpush
</x-app-layout>
