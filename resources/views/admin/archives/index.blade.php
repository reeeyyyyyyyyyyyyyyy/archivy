<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    @php
                        $headerConfig = match($title) {
                            'Arsip' => [
                                'icon' => 'fas fa-archive',
                                'bg' => 'bg-blue-600',
                                'subtitle' => 'Manajemen lengkap semua arsip digital sistem'
                            ],
                            'Arsip Aktif' => [
                                'icon' => 'fas fa-play-circle',
                                'bg' => 'bg-green-600',
                                'subtitle' => 'Arsip dalam periode aktif dan dapat diakses'
                            ],
                            'Arsip Inaktif' => [
                                'icon' => 'fas fa-pause-circle',
                                'bg' => 'bg-yellow-600',
                                'subtitle' => 'Arsip yang telah melewati masa aktif'
                            ],
                            'Arsip Permanen' => [
                                'icon' => 'fas fa-shield-alt',
                                'bg' => 'bg-purple-600',
                                'subtitle' => 'Arsip dengan nilai guna berkelanjutan'
                            ],
                            'Arsip Musnah' => [
                                'icon' => 'fas fa-ban',
                                'bg' => 'bg-red-600',
                                'subtitle' => 'Arsip yang telah dimusnahkan sesuai retensi'
                            ],
                            default => [
                                'icon' => 'fas fa-archive',
                                'bg' => 'bg-gray-600',
                                'subtitle' => 'Kelola dan pantau arsip digital'
                            ]
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

        <!-- Search & Filter Panel -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-filter mr-2 text-blue-500"></i>Filter & Pencarian
            </h3>

            <form method="GET" action="{{ request()->url() }}"
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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
                        <i class="fas fa-calendar-alt mr-2 text-orange-500"></i>Tanggal Dari
                    </label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                        class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4">
                </div>

                <!-- Date To -->
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt mr-2 text-orange-500"></i>Tanggal Sampai
                    </label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                        class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4">
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
                        <option value="15" {{ request('per_page') == '15' ? 'selected' : '' }}>15</option>
                        <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100</option>
                    </select>
                </div>

                <!-- Filter Buttons -->
                <div class="flex items-end space-x-2">
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-xl transition-colors shadow-sm">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                    <a href="{{ request()->url() }}"
                        class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-3 px-4 rounded-xl transition-colors">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
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
                        <a href="{{ route('admin.export.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                            <i class="fas fa-file-excel mr-2"></i>Export Excel
                        </a>
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
                                        Kategori</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Klasifikasi</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tgl Arsip</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
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
                                            <div class="max-w-xs truncate" title="{{ $archive->description }}">
                                                {{ $archive->description }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $archive->category->nama_kategori ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $archive->classification ? ($archive->classification->code . ' - ' . $archive->classification->nama_klasifikasi) : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $archive->kurun_waktu_start->format('d-m-Y') }}
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
                                            <span
                                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses[$archive->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ $archive->status }}
                                            </span>
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
                                                    <button onclick="confirmDeleteArchive({{ $archive->id }}, '{{ $archive->index_number }}', '{{ $archive->description }}')"
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
                                                        title="Permanen">
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
                border: 1px solid #d1d5db !important;
                border-radius: 0.75rem !important;
                display: flex !important;
                align-items: center !important;
                background-color: white !important;
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
                                `${classification.code} - ${classification.name}`,
                                classification.id));
                        });
                    } else {
                        // Show all classifications
                        allClassifications.forEach(function(classification) {
                            classificationSelect.append(new Option(
                                `${classification.code} - ${classification.name}`,
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
        </script>
    @endpush
</x-app-layout>
