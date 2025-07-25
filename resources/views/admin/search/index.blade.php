<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-search-plus text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Pencarian Lanjutan</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-filter mr-1"></i>Cari arsip dengan filter detail dan kata kunci spesifik
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.archives.index') }}"
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
            <form id="advancedSearchForm" action="{{
                auth()->user()->isAdmin() ? route('admin.search.search') :
                (auth()->user()->isStaff() ? route('staff.search.search') : route('intern.search.search'))
            }}" method="GET">

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
                               placeholder="Cari berdasarkan nomor arsip, uraian, kategori, atau klasifikasi..."
                               class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-base">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <!-- Autocomplete dropdown will be populated here -->
                        <div id="autocomplete-results" class="absolute z-10 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 hidden max-h-60 overflow-y-auto"></div>
                    </div>
                </div>

                <!-- Advanced Filters -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">

                    <!-- Category Filter -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-folder mr-2 text-indigo-500"></i>Kategori
                        </label>
                        <select name="category_id" id="category_id" class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors select2-dropdown" data-placeholder="Pilih Kategori">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Classification Filter -->
                    <div>
                        <label for="classification_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-tags mr-2 text-cyan-500"></i>Klasifikasi
                        </label>
                        <select name="classification_id" id="classification_id" class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors select2-dropdown" data-placeholder="Pilih Klasifikasi">
                            <option value="">Semua Klasifikasi</option>
                            @foreach($classifications as $classification)
                                <option value="{{ $classification->id }}" data-category-id="{{ $classification->category_id }}" {{ request('classification_id') == $classification->id ? 'selected' : '' }}>
                                    {{ $classification->code }} - {{ $classification->nama_klasifikasi }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-flag mr-2 text-green-500"></i>Status
                        </label>
                        <select name="status" id="status" class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">Semua Status</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Range From -->
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-alt mr-2 text-orange-500"></i>Tanggal Dari
                        </label>
                        <input type="date"
                               name="date_from"
                               id="date_from"
                               value="{{ request('date_from') }}"
                               class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4">
                    </div>

                    <!-- Date Range To -->
                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-alt mr-2 text-orange-500"></i>Tanggal Sampai
                        </label>
                        <input type="date"
                               name="date_to"
                               id="date_to"
                               value="{{ request('date_to') }}"
                               class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4">
                    </div>

                    <!-- Created By Filter -->
                    <div>
                        <label for="created_by" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-2 text-purple-500"></i>Dibuat Oleh
                        </label>
                        <select name="created_by" id="created_by" class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">Semua User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('created_by') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <!-- Special Filters -->
                <div class="border-t border-gray-200 pt-6 mb-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-filter mr-2 text-blue-500"></i>
                        Filter Khusus
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Approaching Transition -->
                        <div>
                            <label for="approaching_transition" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-clock mr-2 text-yellow-500"></i>Arsip Mendekati Transisi
                            </label>
                            <select name="approaching_transition" id="approaching_transition" class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="">Tidak ada filter</option>
                                <option value="7" {{ request('approaching_transition') == '7' ? 'selected' : '' }}>7 hari ke depan</option>
                                <option value="30" {{ request('approaching_transition') == '30' ? 'selected' : '' }}>30 hari ke depan</option>
                                <option value="60" {{ request('approaching_transition') == '60' ? 'selected' : '' }}>60 hari ke depan</option>
                                <option value="90" {{ request('approaching_transition') == '90' ? 'selected' : '' }}>90 hari ke depan</option>
                            </select>
                        </div>

                        <!-- Per Page -->
                        <div>
                            <label for="per_page" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-list mr-2 text-gray-500"></i>Hasil per Halaman
                            </label>
                            <select name="per_page" id="per_page" class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="15" {{ request('per_page') == '15' ? 'selected' : '' }}>15</option>
                                <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div class="flex space-x-3">
                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-colors shadow-sm">
                            <i class="fas fa-search mr-2"></i>Cari Arsip
                        </button>

                        <button type="button" id="resetForm" class="inline-flex items-center px-4 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-xl transition-colors">
                            <i class="fas fa-undo mr-2"></i>Reset
                        </button>
                    </div>

                    {{-- <div class="flex space-x-3">
                        <button type="button" id="saveSearch" class="inline-flex items-center px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-xl transition-colors">
                            <i class="fas fa-bookmark mr-2"></i>Simpan Pencarian
                        </button>
                    </div> --}}
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
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
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
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ ($archives->currentPage() - 1) * $archives->perPage() + $loop->iteration }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $archive->index_number }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900">
                                                    @if(isset($searchTerm) && !empty($searchTerm))
                                                        {!! str_ireplace($searchTerm, '<mark class="bg-yellow-200 text-yellow-900 px-1 rounded">' . $searchTerm . '</mark>', $archive->uraian) !!}
                                                    @else
                                                        {{ Str::limit($archive->uraian, 80) }}
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
                                                    {{ $archive->status == 'Musnah' ? 'bg-red-100 text-red-800' : '' }}">
                                                    {{ $archive->status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $archive->kurun_waktu_start->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex items-center space-x-3">
                                                    <a href="{{ route('admin.archives.show', $archive) }}"
                                                       class="text-blue-600 hover:text-blue-800 hover:bg-blue-50 p-2 rounded-lg transition-colors" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.archives.edit', $archive) }}"
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
                            const autocompleteRoute = @if(auth()->user()->isAdmin())
                                '{{ route('admin.search.autocomplete') }}'
                            @elseif(auth()->user()->isStaff())
                                '{{ route('staff.search.autocomplete') }}'
                            @else
                                '{{ route('intern.search.autocomplete') }}'
                            @endif;

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
                        document.getElementById('advancedSearchForm').reset();
                        window.location.href = '{{ route('admin.search.index') }}';
                    });
                }

                // Clear search
                const clearSearchBtn = document.getElementById('clearSearch');
                if (clearSearchBtn) {
                    clearSearchBtn.addEventListener('click', function() {
                        window.location.href = '{{ route('admin.search.index') }}';
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

                        fetch(`{{ route('admin.archives.get-classifications-by-category') }}?category_id=${categoryId}`)
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
            });
        </script>
    @endpush
</x-app-layout>
