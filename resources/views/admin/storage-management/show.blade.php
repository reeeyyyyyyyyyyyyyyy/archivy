<x-app-layout>
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-archive text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Detail Rak: {{ $rack->name }}</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Informasi detail rak penyimpanan arsip
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
                    <a href="{{ route('admin.storage-management.edit', $rack->id) }}"
                        class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-edit mr-2"></i>Edit Rak
                    </a>
                    <a href="{{ route('admin.storage-management.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Rack Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Rak</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-blue-600">Nama Rak</div>
                            <div class="text-lg font-semibold text-blue-900">{{ $rack->name }}</div>
                </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-green-600">Total Baris</div>
                            <div class="text-lg font-semibold text-green-900">{{ $rack->total_rows }}</div>
                            </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-purple-600">Total Box</div>
                            <div class="text-lg font-semibold text-purple-900">{{ $rack->total_boxes }}</div>
                        </div>
                        <div class="bg-orange-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-orange-600">Kapasitas per Box</div>
                            <div class="text-lg font-semibold text-orange-900">{{ $rack->capacity_per_box }}</div>
                        </div>
                        <div class="bg-indigo-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-indigo-600">Filter Tahun</div>
                            <div class="text-lg font-semibold text-indigo-900">
                                @if($rack->year_start && $rack->year_end)
                                    {{ $rack->year_start }} - {{ $rack->year_end }}
                                @elseif($rack->year_start)
                                    {{ $rack->year_start }} - Sekarang
                                @else
                                    Semua Tahun
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($rack->description)
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                            <div class="text-sm font-medium text-gray-600">Deskripsi</div>
                            <div class="text-gray-900">{{ $rack->description }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Box Status Overview -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-boxes mr-2 text-teal-500"></i>Status Box
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-check text-white text-lg"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900">Box Tersedia</h4>
                        <p class="text-2xl font-bold text-green-600" id="available-boxes-count">Loading...</p>
                        <p class="text-sm text-gray-600">Siap digunakan</p>
                    </div>

                    <div class="text-center p-4 bg-yellow-50 rounded-lg">
                        <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-exclamation-triangle text-white text-lg"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900">Box Sebagian Penuh</h4>
                        <p class="text-2xl font-bold text-yellow-600" id="partially-full-boxes-count">Loading...</p>
                        <p class="text-sm text-gray-600">Masih bisa diisi</p>
                    </div>

                    <div class="text-center p-4 bg-red-50 rounded-lg">
                        <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-times text-white text-lg"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900">Box Penuh</h4>
                        <p class="text-2xl font-bold text-red-600" id="full-boxes-count">Loading...</p>
                        <p class="text-sm text-gray-600">Tidak bisa diisi lagi</p>
                    </div>
                </div>
            </div>

            <!-- Preview Grid -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-th mr-2 text-cyan-500"></i>Preview Grid Real-time
                </h3>
                    <div class="flex items-center space-x-3">
                        {{-- <button onclick="refreshGrid()" class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm transition-colors">
                            <i class="fas fa-sync-alt mr-1"></i>Refresh
                        </button> --}}
                        <button onclick="syncStorageBoxCounts()" class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white rounded-lg text-sm transition-colors">
                            <i class="fas fa-database mr-1"></i>Sync Counts
                        </button>
                        {{-- <span class="text-sm text-gray-500">Auto-update setiap 30 detik</span> --}}
                    </div>
                </div>
                <div id="visual_grid" class="space-y-4">
                    <!-- Will be populated by JavaScript -->
                </div>
            </div>
            <br>

            <!-- Boxes Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-list mr-2 text-teal-500"></i>Detail Box
                </h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    No. Box
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Baris
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jumlah Arsip
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($rack->boxes->sortBy('box_number') as $box)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        Box {{ $box->box_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        Baris {{ $box->row_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $box->archive_count }} arsip
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span id="box-status-{{ $box->box_number }}"
                                            class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Loading...
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="showBoxContents({{ $box->box_number }})"
                                            class="text-purple-600 hover:text-purple-800 hover:bg-purple-50 p-2 rounded-lg transition-colors"
                                            title="Lihat Isi Box">
                                            <i class="fas fa-box"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
            let refreshInterval;
            const rackId = {{ $rack->id }};
            const rackName = "{{ $rack->name }}";

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            // Show success message if exists
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 1500
                });
            @endif

                // Initialize preview grid
                updatePreviewGrid();

                // Start auto-refresh every 30 seconds and auto-sync counts every 1 second
                refreshInterval = setInterval(() => {
                    updatePreviewGrid();
                }, 30000);

                // Auto-sync counts every 1 second
                syncInterval = setInterval(() => {
                    autoSyncCounts();
                }, 1000);
            });

            function updatePreviewGrid() {
                const previewGrid = document.getElementById('visual_grid');

                // Show loading
                previewGrid.innerHTML = `
                    <div class="flex items-center justify-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                        <span class="ml-2 text-gray-600">Memuat data...</span>
                    </div>
                `;

                // Fetch latest rack data
                fetch(`/admin/storage-management/${rackId}/grid-data`)
                    .then(response => response.json())
                    .then(data => {
                        renderPreviewGrid(data);
                    })
                    .catch(error => {
                        console.error('Error fetching grid data:', error);
                        previewGrid.innerHTML = `
                            <div class="text-center py-8 text-red-500">
                                <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                                <p>Gagal memuat data grid</p>
                                <button onclick="updatePreviewGrid()" class="mt-2 px-3 py-1 bg-blue-500 text-white rounded text-sm">
                                    Coba Lagi
                                </button>
                            </div>
                        `;
                    });
            }

            function renderPreviewGrid(rackData) {
                const previewGrid = document.getElementById('visual_grid');

                let gridHTML = `
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-900 mb-3">${rackName}</h4>
                        <div class="grid grid-cols-4 gap-4">
                `;

                if (rackData.boxes && rackData.boxes.length > 0) {
                    // Group boxes by row
                    const boxesByRow = {};
                    rackData.boxes.forEach(box => {
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
                            let hoverClass = 'hover:bg-green-200';

                            if (box.status === 'full') {
                                statusClass = 'bg-red-100 border-red-200 text-red-600';
                                statusText = 'Full';
                                hoverClass = 'hover:bg-red-200';
                            } else if (box.status === 'partially_full') {
                                statusClass = 'bg-yellow-100 border-yellow-200 text-yellow-600';
                                statusText = 'Partial';
                                hoverClass = 'hover:bg-yellow-200';
                            }

                            gridHTML += `
                            <div class="${statusClass} border rounded p-3 text-center text-xs cursor-pointer transition-all duration-200 ${hoverClass} hover:shadow-md relative group"
                                 onclick="showBoxContents(${box.box_number})"
                                 title="Box ${box.box_number}: ${box.archive_count}/${box.capacity} arsip">
                                <div class="font-semibold">Box ${box.box_number}</div>
                                <div class="${statusClass.includes('text-green') ? 'text-green-600' : statusClass.includes('text-red') ? 'text-red-600' : 'text-yellow-600'}">${statusText}</div>
                                <div class="text-xs text-gray-500 mt-1">${box.archive_count}/${box.capacity}</div>

                                <!-- Manual Full Button -->
                                ${box.status !== 'full' ? `
                                <button onclick="event.stopPropagation(); setBoxToFull(${box.id}, ${box.box_number})"
                                        class="absolute top-1 right-1 bg-red-600 text-white rounded-full w-5 h-5 text-xs opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:bg-red-700"
                                        title="Ubah Status Menjadi Penuh">
                                    <i class="fas fa-check"></i>
                                </button>
                                ` : `
                                <button onclick="event.stopPropagation(); resetBoxStatus(${box.id}, ${box.box_number})"
                                        class="absolute top-1 right-1 bg-green-600 text-white rounded-full w-5 h-5 text-xs opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:bg-green-700"
                                        title="Reset Status Box">
                                    <i class="fas fa-undo"></i>
                                </button>
                                `}
                            </div>
                        `;
                        });
                    });
                } else {
                    // Fallback to grid layout if no box data
                    for (let row = 1; row <= rackData.total_rows; row++) {
                        for (let box = 1; box <= 4; box++) {
                            const boxNumber = (row - 1) * 4 + box;
                            gridHTML += `
                            <div class="bg-green-100 border border-green-200 rounded p-3 text-center text-xs cursor-pointer hover:bg-green-200 transition-all duration-200"
                                 onclick="showBoxContents(${boxNumber})"
                                 title="Box ${boxNumber}: 0/${rackData.capacity_per_box} arsip">
                                <div class="font-semibold">Box ${boxNumber}</div>
                                <div class="text-green-600">Available</div>
                                <div class="text-xs text-gray-500 mt-1">0/${rackData.capacity_per_box}</div>
                            </div>
                        `;
                        }
                    }
                }

                gridHTML += '</div></div>';
                previewGrid.innerHTML = gridHTML;

                // Update box status in table based on grid data
                updateBoxStatusInTable(rackData);
            }

            function updateBoxStatusInTable(rackData) {
                if (rackData.boxes && rackData.boxes.length > 0) {
                    let availableCount = 0;
                    let partiallyFullCount = 0;
                    let fullCount = 0;

                    rackData.boxes.forEach(box => {
                        const statusElement = document.getElementById(`box-status-${box.box_number}`);
                        if (statusElement) {
                            let statusClass = 'bg-gray-100 text-gray-800';
                            let statusText = 'Kosong';

                            if (box.status === 'full') {
                                statusClass = 'bg-red-100 text-red-800';
                                statusText = 'Penuh';
                                fullCount++;
                            } else if (box.status === 'partially_full') {
                                statusClass = 'bg-yellow-100 text-yellow-800';
                                statusText = 'Sebagian';
                                partiallyFullCount++;
                            } else if (box.archive_count > 0) {
                                statusClass = 'bg-green-100 text-green-800';
                                statusText = 'Tersedia';
                                availableCount++;
                            } else {
                                availableCount++; // Empty boxes are also available
                            }

                            statusElement.className = `px-2 py-1 text-xs font-semibold rounded-full ${statusClass}`;
                            statusElement.textContent = statusText;
                        }
                    });

                    // Update summary counts
                    document.getElementById('available-boxes-count').textContent = availableCount;
                    document.getElementById('partially-full-boxes-count').textContent = partiallyFullCount;
                    document.getElementById('full-boxes-count').textContent = fullCount;
                }
            }

            function refreshGrid() {
                updatePreviewGrid();
            }

        function showBoxContents(boxNumber) {
            // Show loading
            Swal.fire({
                title: 'Memuat isi box...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Fetch box contents
                fetch(`{{ route('admin.storage.box-contents', ['rackId' => $rack->id, 'boxNumber' => 'BOX_NUMBER']) }}`.replace('BOX_NUMBER', boxNumber))
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    let contentHtml = `
                        <div class="text-left">
                            <h3 class="font-semibold text-gray-900 mb-3">Isi Box ${boxNumber}</h3>
                            <div class="max-h-80 overflow-y-auto">
                    `;

                    if (data.length > 0) {
                            // Data sudah dikelompokkan per kategori dari backend
                            data.forEach(categoryGroup => {
                                const categoryName = categoryGroup.category;
                                const archives = categoryGroup.archives;

                                contentHtml += `
                                    <div class="mb-4">
                                        <h4 class="font-medium text-green-600 mb-2 border-b border-green-200 pb-1">
                                            📁 ${categoryName} (${archives.length} arsip)
                                        </h4>
                                `;

                                // Group archives by year within category
                                const groupedByYear = archives.reduce((acc, archive) => {
                                    const year = archive.year;
                            if (!acc[year]) acc[year] = [];
                            acc[year].push(archive);
                            return acc;
                        }, {});

                        Object.keys(groupedByYear).sort().forEach(year => {
                            const yearArchives = groupedByYear[year];
                            contentHtml += `
                                        <div class="ml-4 mb-3">
                                            <h5 class="font-medium text-blue-600 mb-2 text-sm">📅 Tahun ${year} (${yearArchives.length} arsip)</h5>
                            `;

                            yearArchives.forEach((archive, index) => {
                                let description = archive.description;
                                        if (description.length > 50) {
                                            description = description.substring(0, 50) + '...';
                                }

                                contentHtml += `
                                    <div class="border-b border-gray-100 py-2 ml-4 hover:bg-gray-50 rounded px-2">
                                        <div class="flex justify-between items-center">
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-2">
                                                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded font-medium">No ${archive.file_number}</span>
                                                            <span class="font-medium text-gray-900 text-sm">${archive.index_number}</span>
                                                        </div>
                                                        <p class="text-xs text-gray-600 mt-1">${description}</p>
                                                        <p class="text-xs text-purple-600 mt-1">
                                                            <i class="fas fa-tag mr-1"></i>${archive.classification}
                                                            ${archive.lampiran_surat ? `<i class="fas fa-paperclip ml-2 mr-1"></i>${archive.lampiran_surat}` : ''}
                                                        </p>
                                                    </div>
                                        </div>
                                    </div>
                                `;
                                    });

                                    contentHtml += `</div>`;
                            });

                            contentHtml += `</div>`;
                        });
                    } else {
                        contentHtml += `
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-inbox text-3xl mb-3 text-gray-300"></i>
                                <p class="font-medium">Box ${boxNumber} kosong</p>
                                <p class="text-sm">Belum ada arsip yang disimpan</p>
                            </div>
                        `;
                    }

                    contentHtml += `
                            </div>
                        </div>
                    `;

                    Swal.fire({
                        title: `Box ${boxNumber}`,
                        html: contentHtml,
                        width: '600px',
                        confirmButtonColor: '#4F46E5',
                        confirmButtonText: 'Tutup'
                    });
                })
                .catch(error => {
                    console.error('Error fetching box contents:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal memuat isi box: ' + error.message,
                        confirmButtonColor: '#dc2626'
                        });
                    });
            }



            function autoSyncCounts() {
                // Silent auto-sync without user notification
                fetch('{{ route('admin.storage-management.sync-counts') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Auto-sync successful:', data.message);
                    } else {
                        console.warn('Auto-sync failed:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Auto-sync error:', error);
                });
            }

            function syncStorageBoxCounts() {
                // Show loading
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sinkronisasi storage box counts',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Call artisan command via AJAX
                fetch('{{ route('admin.storage-management.sync-counts') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            updatePreviewGrid();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat sinkronisasi',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
            }

            // Cleanup interval when page is unloaded
            window.addEventListener('beforeunload', function() {
                if (refreshInterval) {
                    clearInterval(refreshInterval);
                }
            });

            // Manual Box Status Functions
            function setBoxToFull(boxId, boxNumber) {
                // Find the box data to check if it's empty
                const boxElement = document.querySelector(`[onclick*="showBoxContents(${boxNumber})"]`);
                if (!boxElement) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Data box tidak ditemukan',
                        confirmButtonColor: '#dc2626'
                    });
                    return;
                }

                // Extract archive count from the box element
                const archiveCountText = boxElement.querySelector('.text-xs.text-gray-500')?.textContent;
                if (!archiveCountText) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Tidak dapat membaca jumlah arsip box',
                        confirmButtonColor: '#dc2626'
                    });
                    return;
                }

                // Parse archive count (format: "X/Y" where X is current count, Y is capacity)
                const match = archiveCountText.match(/(\d+)\/(\d+)/);
                if (!match) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Format data box tidak valid',
                        confirmButtonColor: '#dc2626'
                    });
                    return;
                }

                const currentCount = parseInt(match[1]);
                const capacity = parseInt(match[2]);

                // Check if box is empty
                if (currentCount === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Box Kosong!',
                        text: `Box ${boxNumber} tidak memiliki arsip (0/${capacity}). Box kosong tidak bisa diubah menjadi penuh.`,
                        confirmButtonColor: '#f59e0b'
                    });
                    return;
                }

                // Check if box is already full
                if (currentCount >= capacity) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Box Sudah Penuh!',
                        text: `Box ${boxNumber} sudah penuh (${currentCount}/${capacity}).`,
                        confirmButtonColor: '#3b82f6'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Konfirmasi Ubah Status',
                    text: `Apakah Anda yakin ingin mengubah Box ${boxNumber} menjadi penuh? (${currentCount}/${capacity} arsip)`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Ubah ke Penuh',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280'
                }).then((result) => {
                    if (result.isConfirmed) {
                        updateBoxStatus(boxId, 'set_full', boxNumber);
                    }
                });
            }

            function resetBoxStatus(boxId, boxNumber) {
                Swal.fire({
                    title: 'Konfirmasi Reset Status',
                    text: `Apakah Anda yakin ingin mereset status Box ${boxNumber}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Reset Status',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#6b7280'
                }).then((result) => {
                    if (result.isConfirmed) {
                        updateBoxStatus(boxId, 'reset_status', boxNumber);
                    }
                });
            }

            function updateBoxStatus(boxId, action, boxNumber) {
                // Show loading
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Mengubah status box',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Send AJAX request
                fetch('{{ route('admin.storage-management.update-box-status') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        box_id: boxId,
                        action: action
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload(); // Refresh the entire page
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat mengubah status box',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
        }

        function showFeatureInfo() {
            const html = `
                <div class="text-left space-y-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="font-semibold text-blue-800 mb-2 flex items-center">
                            <i class="fas fa-th mr-2"></i>
                            Fitur Preview Grid Real-time
                        </h4>
                        <ul class="list-disc ml-5 text-sm text-blue-700 space-y-1">
                            <li><strong>Visualisasi Grid:</strong> Tampilan visual real-time dari seluruh rak arsip</li>
                            <li><strong>Status Box:</strong> Lihat status setiap box (Penuh, Sebagian Penuh, Kosong)</li>
                            <li><strong>Kapasitas Arsip:</strong> Lihat jumlah arsip dalam setiap box secara real-time</li>
                            <li><strong>Auto-update:</strong> Grid otomatis diperbarui setiap 30 detik</li>
                        </ul>
                    </div>

                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <h4 class="font-semibold text-green-800 mb-2 flex items-center">
                            <i class="fas fa-cogs mr-2"></i>
                            Fitur Manajemen Box
                        </h4>
                        <ul class="list-disc ml-5 text-sm text-green-700 space-y-1">
                            <li><strong>Klik Box:</strong> Klik pada box untuk melihat detail isi box</li>
                            <li><strong>Status Manual:</strong> Ubah status box secara manual (Penuh, Sebagian, Kosong)</li>
                            <li><strong>Sync Counts:</strong> Sinkronisasi jumlah arsip secara real-time</li>
                            <li><strong>Edit Rak:</strong> Ubah informasi dan filter tahun dari rak</li>
                        </ul>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <h4 class="font-semibold text-yellow-800 mb-2 flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Perhatian Khusus
                        </h4>
                        <ul class="list-disc ml-5 text-sm text-yellow-700 space-y-1">
                            <li><strong>Status Box:</strong> Status box akan mempengaruhi penempatan arsip baru</li>
                            <li><strong>Kapasitas:</strong> Box penuh tidak bisa diisi arsip baru</li>
                            <li><strong>Filter Tahun:</strong> Rak bisa dibatasi untuk arsip tahun tertentu</li>
                            <li><strong>Konfirmasi:</strong> Pastikan status box sudah benar sebelum konfirmasi</li>
                        </ul>
                    </div>

                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                        <h4 class="font-semibold text-purple-800 mb-2 flex items-center">
                            <i class="fas fa-lightbulb mr-2"></i>
                            Tips Penggunaan
                        </h4>
                        <ul class="list-disc ml-5 text-sm text-purple-700 space-y-1">
                            <li>Gunakan preview grid untuk monitoring status box secara visual</li>
                            <li>Klik box untuk melihat detail arsip yang tersimpan</li>
                            <li>Gunakan tombol sync untuk memastikan data terbaru</li>
                            <li>Atur status box sesuai dengan kondisi aktual penyimpanan</li>
                        </ul>
                    </div>
                </div>
            `;

            Swal.fire({
                title: 'Panduan Fitur: Storage Management',
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
