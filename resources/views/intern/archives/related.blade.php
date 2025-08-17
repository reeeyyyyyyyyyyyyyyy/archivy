<x-app-layout>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endpush

    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-orange-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-link text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Arsip Terkait</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Arsip dengan kategori/klasifikasi/lampiran yang sama
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('intern.archives.create-related', $archive) }}"
                        class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Tambah Berkas Arsip yang Sama
                    </a>
                    <a href="javascript:history.back()"
                        class="inline-flex items-center px-4 py-2 bg-orange-100 hover:bg-orange-200 text-orange-700 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Arsip Induk
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="p-6 space-y-6">

        <!-- Archive Info Card -->
        <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold mb-2">
                        Lampiran Surat: {{ $archive->lampiran_surat }}
                    </h2>
                    <p class="text-emerald-100 text-lg">
                        Kategori: {{ $archive->category->nama_kategori }} |
                        Klasifikasi: {{ $archive->classification->nama_klasifikasi }}
                    </p>
                </div>
                <div class="text-right">
                    <div
                        class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 rounded-full text-white font-semibold">
                        <i class="fas fa-archive mr-2"></i>{{ $relatedArchives->count() }} Arsip Terkait
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter and Bulk Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-filter mr-2 text-indigo-500"></i>
                    Filter & Bulk Actions
                </h3>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Year Filter -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label for="yearFilter" class="block text-sm font-medium text-gray-700 mb-3">
                            <i class="fas fa-calendar mr-2 text-orange-500"></i>Filter Tahun
                        </label>
                        <select id="yearFilter"
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Tahun</option>
                            @php
                                $years = $relatedArchives
                                    ->pluck('kurun_waktu_start')
                                    ->map(function ($date) {
                                        return $date->format('Y');
                                    })
                                    ->unique()
                                    ->sort()
                                    ->reverse();
                            @endphp
                            @foreach ($years as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Musnah Toggle -->
                    @if ($archive->classification && $archive->classification->nasib_akhir === 'Musnah')
                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                <i class="fas fa-filter mr-2 text-red-500"></i>Filter Arsip Musnah
                            </label>
                            <label class="flex items-center cursor-pointer group">
                                <input type="checkbox" id="filterMusnah" class="sr-only">
                                <div class="relative">
                                    <div
                                        class="block bg-gray-600 w-14 h-8 rounded-full transition-all duration-300 ease-in-out">
                                    </div>
                                    <div
                                        class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition-all duration-300 ease-in-out transform">
                                    </div>
                                </div>
                                <span class="ml-3 text-sm text-gray-600 group-hover:text-blue-600 transition-colors">
                                    Sembunyikan arsip musnah
                                </span>
                            </label>
                        </div>
                    @endif

                    <!-- Bulk Location Button -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            <i class="fas fa-map-marker-alt mr-2 text-green-500"></i>Set Lokasi Bulk
                        </label>
                        <button id="bulkLocationBtn"
                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            Set Lokasi Bulk
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Archives Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-list mr-2 text-indigo-500"></i>
                    Daftar Arsip Terkait
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="selectAll"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tahun
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nomor Arsip
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Deskripsi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Lokasi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($relatedArchives as $relatedArchive)
                            <tr class="hover:bg-gray-50 transition-colors archive-row"
                                data-status="{{ $relatedArchive->status }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <input type="checkbox"
                                        class="archive-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        value="{{ $relatedArchive->id }}" data-archive-id="{{ $relatedArchive->id }}">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $relatedArchive->kurun_waktu_start->format('Y') }}
                                </td>
                                <td class="px-6 py-4 truncate whitespace-nowrap text-sm text-gray-900"
                                    style="max-width: 100px;">
                                    {{ $relatedArchive->index_number }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="max-w-xs truncate" title="{{ $relatedArchive->description }}">
                                        {{ $relatedArchive->description }}
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
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses[$relatedArchive->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $relatedArchive->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if ($relatedArchive->status === 'Musnah')
                                        <span class="text-red-600 text-xs font-medium">
                                            <i class="fas fa-fire mr-1"></i>Dibakar/Dibuang
                                        </span>
                                    @elseif($relatedArchive->rack_number)
                                        <div class="text-xs">
                                            <i class="fas fa-map-marker-alt mr-1 text-blue-500"></i>
                                            Rak {{ $relatedArchive->rack_number }},
                                            Box {{ $relatedArchive->box_number }},
                                            Baris {{ $relatedArchive->row_number }}
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs">
                                            <i class="fas fa-question-circle mr-1"></i>Belum ditentukan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('staff.archives.show', $relatedArchive) }}"
                                            class="text-teal-600 hover:text-teal-800 hover:bg-teal-50 p-2 rounded-lg transition-colors"
                                            title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('staff.archives.edit', $relatedArchive) }}"
                                            class="text-emerald-600 hover:text-emerald-800 hover:bg-emerald-50 p-2 rounded-lg transition-colors"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                    <div class="flex flex-col items-center py-8">
                                        <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                                        <p class="text-gray-500">Tidak ada arsip terkait ditemukan.</p>
                                        <p class="text-sm text-gray-400 mt-1">Arsip ini belum memiliki berkas terkait.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($relatedArchives->count() > 0)
            <!-- Summary Card -->
            <div class="bg-blue-50 rounded-xl p-6">
                <h3 class="text-lg font-medium text-blue-900 mb-4 flex items-center">
                    <i class="fas fa-chart-bar mr-2"></i>
                    Ringkasan Arsip Terkait
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $relatedArchives->count() }}</div>
                        <div class="text-sm text-gray-600">Total Arsip Terkait</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-green-600">
                            {{ $relatedArchives->min('kurun_waktu_start')->format('Y') }}</div>
                        <div class="text-sm text-gray-600">Tahun Terlama</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-purple-600">
                            {{ $relatedArchives->max('kurun_waktu_start')->format('Y') }}</div>
                        <div class="text-sm text-gray-600">Tahun Terbaru</div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Bulk Location Modal -->
    <div id="bulkLocationModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-2xl max-w-6xl w-full max-h-[90vh] overflow-y-auto">
                <!-- Modal Header -->
                <div class="sticky top-0 bg-white border-b border-gray-200 p-6 rounded-t-xl z-10">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-map-marker-alt mr-3 text-indigo-600"></i>
                            Set Lokasi Bulk untuk Arsip Terpilih
                        </h3>
                        <button onclick="closeBulkLocationModal()"
                            class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-full transition-all">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <p class="text-sm text-gray-600 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Pilih lokasi penyimpanan untuk arsip yang dipilih dengan preview visual
                    </p>
                </div>

                <div class="p-6">
                    <form id="bulkLocationForm">
                        <!-- Rack Selection -->
                        <div class="mb-6">
                            <label for="bulkRackNumber" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-warehouse mr-2 text-indigo-500"></i>Pilih Rak
                            </label>
                            <select id="bulkRackNumber"
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4">
                                <option value="">Pilih Rak...</option>
                                @foreach (\App\Models\StorageRack::where('status', 'active')->orderBy('name')->get() as $rack)
                                    @php
                                        // Calculate real-time available boxes
                                        $availableBoxes = $rack->boxes->filter(function ($box) use ($rack) {
                                            $realTimeArchiveCount = \App\Models\Archive::where('rack_number', $rack->id)
                                                ->where('box_number', $box->box_number)
                                                ->count();
                                            return $realTimeArchiveCount < $box->capacity;
                                        });
                                        $availableCount = $availableBoxes->count();
                                    @endphp
                                    <option value="{{ $rack->id }}">{{ $rack->name }}
                                        ({{ $availableCount }} box tersedia)
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Pilih rak untuk melihat preview visual</p>
                        </div>

                        <!-- Auto-filled Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div>
                                <label for="bulkRowNumber" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-list-ol mr-2 text-green-500"></i>Nomor Baris
                                </label>
                                <select id="bulkRowNumber"
                                    class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4">
                                    <option value="">Pilih Baris...</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Pilih baris untuk melihat box yang tersedia</p>
                            </div>

                            <div>
                                <label for="bulkBoxNumber" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-box mr-2 text-purple-500"></i>Nomor Box
                                </label>
                                <select id="bulkBoxNumber"
                                    class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4">
                                    <option value="">Pilih Box...</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Pilih box untuk melihat kapasitas</p>
                            </div>

                            <div>
                                <label for="bulkDefinitiveNumber"
                                    class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-hashtag mr-2 text-orange-500"></i>Nomor Definitif Mulai
                                </label>
                                <input type="number" id="bulkDefinitiveNumber"
                                    class="w-full bg-blue-50 border border-blue-200 rounded-xl shadow-sm py-3 px-4"
                                    readonly>
                                <p class="mt-1 text-xs text-gray-500">Auto-filled berdasarkan jumlah arsip yang dipilih
                                </p>
                            </div>
                        </div>

                        <!-- Auto Box Generation Info -->
                        <div id="autoBoxInfo" class="hidden mb-6 p-4 bg-blue-50 border border-blue-200 rounded-xl">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle text-blue-500 mr-3 text-lg"></i>
                                <div>
                                    <p class="text-sm font-medium text-blue-800">Auto Box Filling</p>
                                    <p class="text-xs text-blue-600" id="autoBoxDetails">
                                        Arsip akan otomatis diisi ke box yang tersedia dengan file numbering otomatis
                                    </p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Visual Grid Preview -->
                <div class="px-6 pb-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-th mr-2 text-cyan-500"></i>Preview Visual Grid
                    </h4>
                    <div id="visualGrid" class="space-y-4">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>

                <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                    <button onclick="closeBulkLocationModal()"
                        class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button onclick="saveBulkLocation()"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Simpan Lokasi
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Fixed JavaScript code for related archives page
            const racks = @json($racks ?? []);

            // Debug: Check if racks is properly loaded
            console.log('Racks loaded:', racks);
            console.log('Racks type:', typeof racks);
            console.log('Racks is array:', Array.isArray(racks));

            // Year Filter
            const yearFilter = document.getElementById('yearFilter');

            function applyFilters() {
                const selectedYear = yearFilter.value;
                const filterMusnahChecked = document.getElementById('filterMusnah') ? document.getElementById('filterMusnah')
                    .checked : false;
                const archiveRows = document.querySelectorAll('.archive-row');

                archiveRows.forEach(row => {
                    const year = row.querySelector('td:nth-child(2)').textContent.trim();
                    const status = row.getAttribute('data-status');
                    let showRow = true;

                    // Apply year filter
                    if (selectedYear && year !== selectedYear) {
                        showRow = false;
                    }

                    // Apply musnah filter
                    if (filterMusnahChecked && status === 'Musnah') {
                        showRow = false;
                    }

                    row.style.display = showRow ? '' : 'none';
                });

                // Update select all checkbox
                updateSelectAllCheckbox();
            }

            // Year filter event listener
            if (yearFilter) {
                yearFilter.addEventListener('change', applyFilters);
            }

            // Filter Musnah Toggle with enhanced animation
            const filterMusnah = document.getElementById('filterMusnah');
            if (filterMusnah) {
                filterMusnah.addEventListener('change', function() {
                    const isChecked = this.checked;
                    const toggle = this.parentElement.querySelector('.block');
                    const dot = this.parentElement.querySelector('.dot');

                    // Enhanced toggle animation
                    if (isChecked) {
                        toggle.classList.remove('bg-gray-600');
                        toggle.classList.add('bg-blue-600');
                        dot.classList.remove('translate-x-0');
                        dot.classList.add('translate-x-6');
                    } else {
                        toggle.classList.remove('bg-blue-600');
                        toggle.classList.add('bg-gray-600');
                        dot.classList.remove('translate-x-6');
                        dot.classList.add('translate-x-0');
                    }

                    // Apply all filters (year + musnah)
                    applyFilters();
                });
            }

            function updateSelectAllCheckbox() {
                const visibleCheckboxes = document.querySelectorAll('.archive-checkbox:not([style*="display: none"])');
                const checkedVisibleBoxes = document.querySelectorAll(
                    '.archive-checkbox:checked:not([style*="display: none"])');
                const selectAllCheckbox = document.getElementById('selectAll');

                if (visibleCheckboxes.length > 0) {
                    selectAllCheckbox.checked = visibleCheckboxes.length === checkedVisibleBoxes.length;
                    selectAllCheckbox.indeterminate = checkedVisibleBoxes.length > 0 && checkedVisibleBoxes.length <
                        visibleCheckboxes.length;
                } else {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = false;
                }
            }

            // Select All Checkbox
            document.getElementById('selectAll').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.archive-checkbox');
                checkboxes.forEach(checkbox => {
                    if (checkbox.closest('tr').style.display !== 'none') {
                        checkbox.checked = this.checked;
                    }
                });
            });

            // Individual Checkbox
            document.querySelectorAll('.archive-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateSelectAllCheckbox();
                });
            });

            // Bulk Location Modal
            document.getElementById('bulkLocationBtn').addEventListener('click', function() {
                const checkedBoxes = document.querySelectorAll('.archive-checkbox:checked');
                if (checkedBoxes.length === 0) {
                    Swal.fire({
                        title: 'Peringatan!',
                        text: 'Pilih arsip terlebih dahulu untuk set lokasi bulk',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                // Check if any selected archives have "Musnah" status
                let hasMusnahArchives = false;
                let musnahArchives = [];

                checkedBoxes.forEach(checkbox => {
                    const row = checkbox.closest('tr');
                    const status = row.getAttribute('data-status');
                    const description = row.querySelector('td:nth-child(4)').textContent.trim();

                    if (status === 'Musnah') {
                        hasMusnahArchives = true;
                        musnahArchives.push(description);
                    }
                });

                if (hasMusnahArchives) {
                    Swal.fire({
                        title: 'Peringatan Arsip Musnah!',
                        html: `
            <div class="text-left">
                <p class="mb-3">Beberapa arsip yang dipilih memiliki status <strong>Musnah</strong>:</p>
                <div class="bg-red-50 p-3 rounded-lg mb-3 max-h-32 overflow-y-auto">
                    ${musnahArchives.map(desc => `<p class="text-sm text-red-700">• ${desc}</p>`).join('')}
                </div>
                <p class="text-sm text-gray-600">Arsip dengan status Musnah tidak seharusnya disimpan di lokasi fisik.</p>
                <p class="text-sm text-gray-600 font-semibold">Apakah Anda yakin ingin melanjutkan?</p>
            </div>
        `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Lanjutkan',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // show error
                            Swal.fire({
                                title: 'Peringatan!',
                                text: 'Arsip dengan status Musnah tidak seharusnya disimpan di lokasi fisik.',
                                icon: 'warning',
                                confirmButtonText: 'OK'
                            });
                            return;
                        }
                    });
                } else {
                    document.getElementById('bulkLocationModal').classList.remove('hidden');
                    updateVisualGrid(); // Initialize visual grid
                }
            });

            function closeBulkLocationModal() {
                document.getElementById('bulkLocationModal').classList.add('hidden');
            }

            // Visual Grid Preview
            function updateVisualGrid() {
                const rackId = document.getElementById('bulkRackNumber').value;
                const visualGrid = document.getElementById('visualGrid');

                if (!rackId) {
                    visualGrid.innerHTML = '<p class="text-gray-500 text-center">Pilih rak untuk melihat preview</p>';
                    return;
                }

                const rack = racks.find(r => r.id == rackId);
                if (!rack) return;

                let gridHTML = `
        <div class="bg-gray-50 rounded-lg p-4">
            <h4 class="font-semibold text-gray-900 mb-3">${rack.name}</h4>
            <div class="grid grid-cols-4 gap-4">
    `;

                // Use actual box data from the rack
                if (rack.boxes && rack.boxes.length > 0) {
                    // Group boxes by row
                    const boxesByRow = {};
                    rack.boxes.forEach(box => {
                        if (!boxesByRow[box.row_number]) {
                            boxesByRow[box.row_number] = [];
                        }
                        boxesByRow[box.row_number].push(box);
                    });

                    // Sort rows and boxes
                    Object.keys(boxesByRow).sort((a, b) => parseInt(a) - parseInt(b)).forEach(rowNum => {
                        const boxes = boxesByRow[rowNum].sort((a, b) => a.box_number - b.box_number);

                        boxes.forEach(box => {
                            let statusClass = 'bg-green-100 border-green-200 text-green-600';
                            let statusText = 'Available';

                            // Use real-time status calculation
                            if (box.archive_count >= box.capacity) {
                                statusClass = 'bg-red-100 border-red-200 text-red-600';
                                statusText = 'Full';
                            } else if (box.archive_count >= box.capacity / 2) {
                                statusClass = 'bg-yellow-100 border-yellow-200 text-yellow-600';
                                statusText = 'Partial';
                            }

                            gridHTML += `
                <div class="${statusClass} border rounded p-2 text-center text-xs" data-box-number="${box.box_number}" data-archive-count="${box.archive_count}" data-capacity="${box.capacity}">
                    <div class="font-semibold">Box ${box.box_number}</div>
                    <div class="${statusClass.includes('text-green') ? 'text-green-600' : statusClass.includes('text-red') ? 'text-red-600' : 'text-yellow-600'}">${statusText}</div>
                    <div class="text-xs text-gray-500">${box.archive_count}/${box.capacity}</div>
                </div>
            `;
                        });
                    });
                } else {
                    // Fallback to grid layout if no box data
                    for (let row = 1; row <= rack.total_rows; row++) {
                        for (let box = 1; box <= 4; box++) {
                            const boxNumber = (row - 1) * 4 + box;
                            gridHTML += `
                <div class="bg-green-100 border border-green-200 rounded p-2 text-center text-xs">
                    <div class="font-semibold">Box ${boxNumber}</div>
                    <div class="text-green-600">Available</div>
                    <div class="text-xs text-gray-500">0/${rack.capacity_per_box}</div>
                </div>
            `;
                        }
                    }
                }

                gridHTML += '</div></div>';
                visualGrid.innerHTML = gridHTML;
            }

            // Preview Grid and Auto Box Generation
            async function updatePreviewGrid() {
                const rackNumber = document.getElementById('bulkRackNumber').value;
                const rowNumber = document.getElementById('bulkRowNumber').value;
                const boxNumber = document.getElementById('bulkBoxNumber').value;
                const checkedBoxes = document.querySelectorAll('.archive-checkbox:checked');
                const archiveCount = checkedBoxes.length;

                const autoBoxInfo = document.getElementById('autoBoxInfo');
                const autoBoxDetails = document.getElementById('autoBoxDetails');
                const definitiveNumberInput = document.getElementById('bulkDefinitiveNumber');

                if (rackNumber && rowNumber && archiveCount > 0) {
                    autoBoxInfo.classList.remove('hidden');

                    // Calculate boxes needed
                    const boxesNeeded = Math.ceil(archiveCount / 50);
                    const startBox = boxNumber || 1;

                    // Get existing archives count in the selected box (real-time)
                    const existingCount = await getExistingArchivesCount(rackNumber, rowNumber, startBox);
                    const startNumber = existingCount + 1;

                    // Auto-fill definitive number based on existing count
                    if (archiveCount === 1) {
                        definitiveNumberInput.value = startNumber.toString();
                    } else {
                        // Only show the starting number, not a range
                        definitiveNumberInput.value = startNumber.toString();
                    }

                    // Update auto box info
                    if (boxesNeeded > 1) {
                        autoBoxDetails.textContent =
                            `Akan mengisi ${boxesNeeded} box (Box ${startBox}-${startBox + boxesNeeded - 1}) untuk ${archiveCount} arsip dengan file numbering otomatis mulai dari nomor ${startNumber}`;
                    } else {
                        // Check if selected box is full using real-time data
                        const rackId = document.getElementById('bulkRackNumber').value;
                        if (rackId && Array.isArray(racks)) {
                            const rack = racks.find(r => r.id == rackId);
                            if (rack && rack.boxes) {
                                const selectedBox = rack.boxes.find(b => b.box_number == startBox);
                                if (selectedBox && selectedBox.archive_count >= selectedBox.capacity) {
                                    autoBoxDetails.textContent = `Box ${startBox} sudah penuh (${selectedBox.archive_count}/${selectedBox.capacity}). Pilih box lain.`;
                                    definitiveNumberInput.value = '';
                                    definitiveNumberInput.placeholder = 'PENUH';
                                } else {
                                    autoBoxDetails.textContent =
                                        `Akan mengisi Box ${startBox} untuk ${archiveCount} arsip dengan file numbering otomatis mulai dari nomor ${startNumber}`;
                                }
                            } else {
                                autoBoxDetails.textContent =
                                    `Akan mengisi Box ${startBox} untuk ${archiveCount} arsip dengan file numbering otomatis mulai dari nomor ${startNumber}`;
                            }
                        } else {
                            autoBoxDetails.textContent =
                                `Akan mengisi Box ${startBox} untuk ${archiveCount} arsip dengan file numbering otomatis mulai dari nomor ${startNumber}`;
                        }
                    }
                } else {
                    autoBoxInfo.classList.add('hidden');
                    definitiveNumberInput.value = '';
                }
            }

            // Function to get existing archives count in a specific box (real-time)
            async function getExistingArchivesCount(rackNumber, rowNumber, boxNumber) {
                try {
                    // Fetch real-time data from API
                    const response = await fetch(
                        `{{ route('staff.archives.storage-management.grid-data', ['rack' => 'RACK_ID']) }}`.replace(
                            'RACK_ID', rackNumber));
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const data = await response.json();

                    if (data.boxes) {
                        const box = data.boxes.find(b => b.box_number == boxNumber);
                        if (box) {
                            return box.archive_count || 0;
                        }
                    }
                } catch (error) {
                    console.error('Error fetching real-time data:', error);
                }

                // Fallback: try to get from dropdown if available
                const boxSelect = document.getElementById('bulkBoxNumber');
                if (boxSelect) {
                    const selectedBoxOption = boxSelect.querySelector(`option[value="${boxNumber}"]`);
                    if (selectedBoxOption) {
                        const text = selectedBoxOption.textContent;
                        const match = text.match(/\((\d+)\/(\d+)\)/);
                        if (match) {
                            return parseInt(match[1]) || 0;
                        }
                    }
                }

                return 0;
            }

            // Add event listeners for preview
            document.getElementById('bulkRackNumber').addEventListener('change', function() {
                const rackId = this.value;
                if (rackId && Array.isArray(racks)) {
                    const rack = racks.find(r => r.id == rackId);
                    if (rack) {
                        // Populate row dropdown
                        const rowSelect = document.getElementById('bulkRowNumber');
                        rowSelect.innerHTML = '<option value="">Pilih Baris...</option>';
                        for (let i = 1; i <= rack.total_rows; i++) {
                            rowSelect.innerHTML += `<option value="${i}">Baris ${i}</option>`;
                        }
                    }
                }
                updatePreviewGrid();
                updateVisualGrid();
            });

            document.getElementById('bulkRowNumber').addEventListener('change', function() {
                const rackId = document.getElementById('bulkRackNumber').value;
                const rowNumber = this.value;

                if (rackId && rowNumber) {
                    // Use real-time API to get box data
                    fetch(`{{ route('staff.archives.storage-management.grid-data', ['rack' => 'RACK_ID']) }}`.replace(
                            'RACK_ID', rackId))
                        .then(response => response.json())
                        .then(data => {
                            if (data.boxes) {
                                const rowBoxes = data.boxes.filter(box => box.row_number == rowNumber);
                                const boxSelect = document.getElementById('bulkBoxNumber');
                                boxSelect.innerHTML = '<option value="">Pilih Box...</option>';

                                rowBoxes.forEach(box => {
                                    let status = 'Tersedia';
                                    if (box.archive_count >= box.capacity) {
                                        status = 'Penuh';
                                    } else if (box.archive_count >= box.capacity / 2) {
                                        status = 'Sebagian';
                                    }
                                    boxSelect.innerHTML +=
                                        `<option value="${box.box_number}" data-capacity="${box.capacity}" data-count="${box.archive_count}">Box ${box.box_number} (${box.archive_count}/${box.capacity}) - ${status}</option>`;
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching real-time box data:', error);
                            // Fallback to static data
                            if (Array.isArray(racks)) {
                                const rack = racks.find(r => r.id == rackId);
                                if (rack && rack.boxes) {
                                    const rowBoxes = rack.boxes.filter(box => box.row_number == rowNumber);
                                    const boxSelect = document.getElementById('bulkBoxNumber');
                                    boxSelect.innerHTML = '<option value="">Pilih Box...</option>';

                                    rowBoxes.forEach(box => {
                                        let status = 'Tersedia';
                                        if (box.archive_count >= box.capacity) {
                                            status = 'Penuh';
                                        } else if (box.archive_count >= box.capacity / 2) {
                                            status = 'Sebagian';
                                        }
                                        boxSelect.innerHTML +=
                                            `<option value="${box.box_number}" data-capacity="${box.capacity}" data-count="${box.archive_count}">Box ${box.box_number} (${box.archive_count}/${box.capacity}) - ${status}</option>`;
                                    });
                                }
                            }
                        });
                }
                updatePreviewGrid();
            });

            document.getElementById('bulkBoxNumber').addEventListener('change', updatePreviewGrid);

            // Fixed saveBulkLocation function
            function saveBulkLocation() {
                const rackId = document.getElementById('bulkRackNumber').value;
                const rowNumber = document.getElementById('bulkRowNumber').value;
                const boxNumber = document.getElementById('bulkBoxNumber').value;

                if (!rackId) {
                    Swal.fire('Peringatan', 'Pilih Rak terlebih dahulu!', 'warning');
                    return;
                }

                if (!rowNumber) {
                    Swal.fire('Peringatan', 'Pilih Baris terlebih dahulu!', 'warning');
                    return;
                }

                if (!boxNumber) {
                    Swal.fire('Peringatan', 'Pilih Box terlebih dahulu!', 'warning');
                    return;
                }

                const checkedBoxes = document.querySelectorAll('.archive-checkbox:checked');
                const archiveIds = Array.from(checkedBoxes).map(cb => cb.value);

                if (archiveIds.length === 0) {
                    Swal.fire('Peringatan', 'Pilih arsip terlebih dahulu!', 'warning');
                    return;
                }

                // Check if selected box is full
                const boxSelect = document.getElementById('bulkBoxNumber');
                const selectedOption = boxSelect.options[boxSelect.selectedIndex];
                if (selectedOption) {
                    const archiveCount = parseInt(selectedOption.getAttribute('data-count') || 0);
                    const capacity = parseInt(selectedOption.getAttribute('data-capacity') || 0);

                    if (archiveCount >= capacity) {
                        Swal.fire({
                            title: 'Box Penuh!',
                            text: `Box ${boxNumber} sudah penuh (${archiveCount}/${capacity}). Pilih box lain yang tersedia.`,
                            icon: 'error',
                            confirmButtonText: 'Pilih Box Lain'
                        });
                        return;
                    }
                }

                // Check if any selected archives already have location
                let hasExistingLocation = false;
                let existingLocationArchives = [];

                checkedBoxes.forEach(checkbox => {
                    const row = checkbox.closest('tr');
                    const locationCell = row.querySelector('td:nth-child(6)'); // Location column
                    const locationText = locationCell.textContent.trim();

                    if (locationText !== 'Belum ditentukan' && locationText !== 'Dibakar/Dibuang') {
                        hasExistingLocation = true;
                        const description = row.querySelector('td:nth-child(4)').textContent.trim();
                        const currentLocation = locationCell.textContent.trim();
                        existingLocationArchives.push({
                            description: description,
                            location: currentLocation
                        });
                    }
                });

                // Get rack info for confirmation
                const rackSelect = document.getElementById('bulkRackNumber');
                const selectedRackOption = rackSelect.options[rackSelect.selectedIndex];
                const rackName = selectedRackOption.text.split('(')[0].trim();

                // Confirmation dialog
                let confirmationHTML = `
        <div class="text-left">
            <p class="mb-3"><strong>Lokasi yang dipilih:</strong></p>
            <div class="bg-blue-50 p-3 rounded-lg mb-3">
                <p class="text-sm font-medium text-blue-800">${rackName}, Box ${boxNumber}, Baris ${rowNumber}</p>
            </div>
            <p class="mb-3"><strong>Jumlah arsip yang akan dipindahkan:</strong> ${archiveIds.length} arsip</p>
    `;

                if (hasExistingLocation) {
                    confirmationHTML += `
            <div class="bg-orange-50 p-3 rounded-lg mb-3">
                <p class="text-sm font-medium text-orange-800 mb-2">⚠️ Peringatan: Beberapa arsip sudah memiliki lokasi:</p>
                <div class="max-h-24 overflow-y-auto">
                    ${existingLocationArchives.map(item =>
                        `<p class="text-xs text-orange-700">• ${item.description} - ${item.location}</p>`
                    ).join('')}
                </div>
            </div>
        `;
                }

                confirmationHTML += '<p class="text-sm text-gray-600">Lanjutkan proses set lokasi bulk?</p></div>';

                Swal.fire({
                    title: 'Konfirmasi Set Lokasi Bulk',
                    html: confirmationHTML,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Set Lokasi',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#3b82f6',
                    cancelButtonColor: '#6b7280'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Memproses...',
                            text: 'Sedang menyimpan lokasi untuk ' + archiveIds.length + ' arsip',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Send AJAX request to update locations
                        fetch('{{ route('staff.archives.bulk-update-location') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    archive_ids: archiveIds,
                                    rack_number: rackId,
                                    row_number: rowNumber,
                                    box_number: boxNumber,
                                    auto_generate_boxes: true
                                })
                            })
                            .then(response => {
                                if (!response.ok) {
                                    return response.json().then(data => {
                                        throw new Error(data.message || 'Network response was not ok');
                                    });
                                }
                                return response.json();
                            })
                            .then(data => {
                                Swal.close();
                                if (data.success) {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: data.message ||
                                            'Lokasi berhasil disimpan untuk semua arsip yang dipilih',
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        closeBulkLocationModal();
                                        location.reload(); // Refresh page to show updated locations
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Gagal!',
                                        text: data.message || 'Terjadi kesalahan saat menyimpan lokasi',
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.close();

                                // Check if it's a box full error
                                if (error.message && error.message.includes('sudah penuh')) {
                                    Swal.fire({
                                        title: 'Box Penuh!',
                                        text: error.message,
                                        icon: 'error',
                                        confirmButtonText: 'Pilih Box Lain',
                                        showCancelButton: true,
                                        cancelButtonText: 'Batal'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            // Focus on box selection
                                            document.getElementById('bulkBoxNumber').focus();
                                        }
                                    });
                                } else if (error.message && error.message.includes('sudah berada di lokasi yang sama')) {
                                    Swal.fire({
                                        title: 'Lokasi Sama!',
                                        text: error.message,
                                        icon: 'warning',
                                        confirmButtonText: 'OK'
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Gagal!',
                                        text: 'Terjadi kesalahan saat mengirim data',
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            });
                        }
                    });
                }

            // Success notifications
            @if (session('delete_success'))
                Swal.fire({
                    title: 'Berhasil!',
                    text: '{{ session('delete_success') }}',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            @endif

            @if (session('success') && session('show_add_related_button'))
                Swal.fire({
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    showConfirmButton: true,
                    showDenyButton: true,
                    showCancelButton: false,
                    confirmButtonText: 'Tambah Lagi',
                    denyButtonText: 'Tutup',
                    confirmButtonColor: '#10b981',
                    denyButtonColor: '#6b7280',
                    reverseButtons: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '{{ route('staff.archives.create-related', session('parent_archive_id')) }}';
                    }
                });
            @elseif (session('success'))
                Swal.fire({
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    showConfirmButton: true,
                    confirmButtonText: 'OK',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
            @endif
        </script>
    @endpush
</x-app-layout>
