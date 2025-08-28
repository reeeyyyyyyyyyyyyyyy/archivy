<x-app-layout>
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-map-marker-alt text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Edit Lokasi Penyimpanan</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Ubah lokasi penyimpanan arsip
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.archives.show', $archive) }}"
                        class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Archive Information -->
                    <div class="mb-8 p-6 bg-gray-50 rounded-lg border">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Arsip</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nomor Arsip</label>
                                <p class="mt-1 text-sm text-gray-900 font-medium">{{ $archive->index_number }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal Arsip</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $archive->kurun_waktu_start ? $archive->kurun_waktu_start->format('d/m/Y') : 'N/A' }}
                                </p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Uraian</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $archive->description }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kategori</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $archive->category->nama_kategori ?? 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Klasifikasi</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $archive->classification->nama_klasifikasi ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Current Location -->
                    @if ($currentRack)
                        <div class="mb-8 p-6 bg-blue-50 rounded-lg border">
                            <h3 class="text-lg font-semibold text-blue-900 mb-4">Lokasi Saat Ini</h3>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-blue-700">Rak</label>
                                    <p class="mt-1 text-sm text-blue-900 font-medium">{{ $currentRack->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-blue-700">Baris</label>
                                    <p class="mt-1 text-sm text-blue-900">{{ $currentRow }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-blue-700">Box</label>
                                    <p class="mt-1 text-sm text-blue-900">{{ $currentBox }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-blue-700">File</label>
                                    <p class="mt-1 text-sm text-blue-900">{{ $currentFile }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Storage Location Form -->
                    <form method="POST" action="{{ route('admin.archives.update-location', $archive) }}"
                        id="locationForm" class="space-y-6">
                        @csrf

                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Edit Lokasi Penyimpanan</h3>
                            <p class="text-sm text-gray-600 mb-6">
                                Pilih lokasi penyimpanan baru untuk arsip ini. Nomor file akan ditentukan otomatis
                                berdasarkan nomor box yang dipilih.
                            </p>
                        </div>

                        <!-- Rack Selection -->
                        <div>
                            <label for="rack_id" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-warehouse mr-2 text-indigo-500"></i>Pilih Rak
                            </label>
                            <select name="rack_id" id="rack_id"
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                required>
                                <option value="">Pilih Rak...</option>
                                @foreach ($racks as $rack)
                                    <option value="{{ $rack->id }}" data-rows="{{ $rack->total_rows }}"
                                        data-boxes="{{ $rack->total_boxes }}">
                                        {{ $rack->name }}
                                        ({{ $rack->available_boxes_count ?? $rack->getAvailableBoxesCount() }} box
                                        tersedia)
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Pilih rak untuk auto-fill baris dan box</p>
                        </div>

                        <!-- Auto-filled Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="rack_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-sort-numeric-up mr-2 text-blue-500"></i>Nomor Rak
                                </label>
                                <input type="number" name="rack_number" id="rack_number"
                                    class="w-full bg-gray-100 border border-gray-300 rounded-xl shadow-sm py-3 px-4"
                                    readonly>
                                <p class="mt-1 text-xs text-gray-500">Auto-filled berdasarkan rak yang dipilih</p>
                            </div>

                            <div>
                                <label for="row_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-list-ol mr-2 text-green-500"></i>Nomor Baris
                                </label>
                                <select name="row_number" id="row_number"
                                    class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                    required>
                                    <option value="">Pilih Baris...</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Auto-filled berdasarkan rak yang dipilih</p>
                            </div>

                            <div>
                                <label for="box_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-box mr-2 text-purple-500"></i>Nomor Box
                                </label>
                                <select name="box_number" id="box_number"
                                    class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                    required>
                                    <option value="">Pilih Box...</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Auto-filled berdasarkan baris yang dipilih</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- File Number Display -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-file mr-2 text-orange-500"></i>Nomor File (Otomatis)
                                </label>
                                <div id="file_number_display"
                                    class="w-full px-3 py-3 border border-gray-300 bg-gray-100 rounded-xl shadow-sm text-gray-700">
                                    Pilih Box terlebih dahulu
                                </div>
                                <input type="hidden" name="file_number" id="file_number" value="">
                                <p class="mt-1 text-xs text-gray-500">
                                    Nomor file akan ditentukan otomatis berdasarkan box yang dipilih
                                </p>
                            </div>


                        </div>

                        <!-- Preview Grid -->
                        <div class="mt-8">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Preview Grid</h4>
                            <div id="preview_grid" class="bg-white border border-gray-200 rounded-lg p-6">
                                <div class="text-center text-gray-500 py-8">
                                    <i class="fas fa-info-circle text-2xl mb-2"></i>
                                    <p>Pilih rak untuk melihat preview grid</p>
                                </div>
                            </div>
                        </div>
                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end space-x-3 mt-8">
                            <a href="{{ route('admin.archives.show', $archive) }}"
                                class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                                <i class="fas fa-times mr-2"></i>Batal
                            </a>
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                                <i class="fas fa-save mr-2"></i>Simpan Lokasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Data dari PHP
            const racks = @json($racksArray ?? []);
            const archive = @json($archive);

            console.log('Racks data loaded:', racks);
            console.log('Archive data loaded:', archive);
            console.log('Racks type:', typeof racks);
            console.log('Racks is array:', Array.isArray(racks));

            function updateVisualGrid() {
                const rackId = document.getElementById('rack_id').value;
                const visualGrid = document.getElementById('preview_grid');

                if (!rackId) {
                    visualGrid.innerHTML = '<p class="text-gray-500 text-center">Pilih rak untuk melihat preview</p>';
                    return;
                }

                // Only show loading on first load, not on auto-sync
                const isAutoSync = arguments[0] === 'auto-sync';
                if (!isAutoSync) {
                    visualGrid.innerHTML = `
                        <div class="flex items-center justify-center py-8">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                            <span class="ml-2 text-gray-600">Memuat data real-time...</span>
                        </div>
                    `;
                }

                // Fetch real-time data from database like management storage
                fetch(`/admin/storage-management/${rackId}/grid-data`)
                    .then(response => response.json())
                    .then(data => {
                        renderRealTimeGrid(data);
                    })
                    .catch(error => {
                        console.error('Error fetching real-time grid data:', error);
                        // Fallback to static data if API fails
                        renderStaticGrid(rackId);
                    });
            }

            function renderRealTimeGrid(rackData) {
                const visualGrid = document.getElementById('preview_grid');

                let gridHTML = `
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h4 class="font-semibold text-gray-900 mb-4 text-lg">${rackData.name}</h4>
                        <div class="grid grid-cols-4 gap-6">
                `;

                if (rackData.boxes && rackData.boxes.length > 0) {
                    rackData.boxes.forEach(box => {
                        let statusClass = 'bg-green-100 border-green-200 text-green-600';
                        let statusText = 'Available';

                        // Use real-time status from API
                        if (box.status === 'full') {
                            statusClass = 'bg-red-100 border-red-200 text-red-600';
                            statusText = 'Full';
                        } else if (box.status === 'partially_full') {
                            statusClass = 'bg-yellow-100 border-yellow-200 text-yellow-600';
                            statusText = 'Partial';
                        }

                        gridHTML += `
                            <div class="${statusClass} border rounded p-6 text-center text-lg cursor-pointer hover:shadow-lg transition-all duration-200 hover:scale-105" onclick="selectBox(${rackData.id}, ${box.box_number})">
                                <div class="font-bold text-xl mb-2">Box ${box.box_number}</div>
                                <div class="${statusClass.includes('text-green') ? 'text-green-600' : statusClass.includes('text-red') ? 'text-red-600' : 'text-yellow-600'} font-semibold text-base mb-1">${statusText}</div>
                                <div class="text-sm text-gray-600 font-medium">${box.archive_count}/${box.capacity}</div>
                            </div>
                        `;
                    });
                } else {
                    // Fallback for empty racks
                    for (let box = 1; box <= 8; box++) {
                        gridHTML += `
                            <div class="bg-green-100 border border-green-200 rounded p-3 text-center text-sm">
                                <div class="font-semibold">Box ${box}</div>
                                <div class="text-green-600">Available</div>
                                <div class="text-xs text-gray-500">0/50</div>
                            </div>
                        `;
                    }
                }

                gridHTML += '</div></div>';
                visualGrid.innerHTML = gridHTML;
            }

            function renderStaticGrid(rackId) {
                // Fallback to static data if real-time API fails
                const rack = racks.find(r => r.id == rackId);
                if (!rack) return;

                const visualGrid = document.getElementById('preview_grid');
                let gridHTML = `
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h4 class="font-semibold text-gray-900 mb-4 text-lg">${rack.name}</h4>
                        <div class="grid grid-cols-4 gap-6">
                `;

                if (rack.boxes && rack.boxes.length > 0) {
                    const boxesByRow = {};
                    rack.boxes.forEach(box => {
                        if (!boxesByRow[box.row_number]) {
                            boxesByRow[box.row_number] = [];
                        }
                        boxesByRow[box.row_number].push(box);
                    });

                    Object.keys(boxesByRow)
                        .sort((a, b) => parseInt(a) - parseInt(b))
                        .forEach(rowNum => {
                            const boxes = boxesByRow[rowNum].sort((a, b) => a.box_number - b.box_number);
                            boxes.forEach(box => {
                                let statusClass = 'bg-green-100 border-green-200 text-green-600';
                                let statusText = 'Available';

                                if (box.archive_count >= box.capacity) {
                                    statusClass = 'bg-red-100 border-red-200 text-red-600';
                                    statusText = 'Full';
                                } else if (box.archive_count >= box.capacity / 2) {
                                    statusClass = 'bg-yellow-100 border-yellow-200 text-yellow-600';
                                    statusText = 'Partial';
                                }

                                gridHTML += `
                                    <div class="${statusClass} border rounded p-6 text-center text-lg cursor-pointer hover:shadow-lg transition-all duration-200 hover:scale-105" onclick="selectBox(${rack.id}, ${box.box_number})">
                                        <div class="font-bold text-xl mb-2">Box ${box.box_number}</div>
                                        <div class="${statusClass.includes('text-green') ? 'text-green-600' : statusClass.includes('text-red') ? 'text-red-600' : 'text-yellow-600'} font-semibold text-base mb-1">${statusText}</div>
                                        <div class="text-sm text-gray-600 font-medium">${box.archive_count}/${box.capacity}</div>
                                    </div>
                                `;
                            });
                        });
                } else {
                    for (let row = 1; row <= rack.total_rows; row++) {
                        for (let box = 1; box <= 4; box++) {
                            const boxNumber = (row - 1) * 4 + box;
                            gridHTML += `
                                <div class="bg-green-100 border border-green-200 rounded p-3 text-center text-sm">
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

            function updateAutoFields() {
                const rackId = document.getElementById('rack_id').value;
                const rackNumber = document.getElementById('rack_number');
                const rowNumber = document.getElementById('row_number');
                const boxNumber = document.getElementById('box_number');
                const fileNumberDisplay = document.getElementById('file_number_display');

                if (!rackNumber || !rowNumber || !boxNumber || !fileNumberDisplay) return;

                if (rackId && racks.length > 0) {
                    const rack = racks.find(r => r.id == rackId);
                    if (rack) {
                        rackNumber.value = rack.id;
                        rowNumber.innerHTML = '<option value="">Pilih Baris...</option>';
                        for (let i = 1; i <= rack.total_rows; i++) {
                            rowNumber.innerHTML += `<option value="${i}">Baris ${i}</option>`;
                        }
                        boxNumber.innerHTML = '<option value="">Pilih Box...</option>';
                        fileNumberDisplay.textContent = 'Pilih Box terlebih dahulu';
                    }
                } else {
                    rackNumber.value = '';
                    rowNumber.innerHTML = '<option value="">Pilih Baris...</option>';
                    boxNumber.innerHTML = '<option value="">Pilih Box...</option>';
                    fileNumberDisplay.textContent = '1';
                }

                updateVisualGrid();
            }

            function updateBoxDropdown(rack, rowNumber) {
                const boxNumber = document.getElementById('box_number');
                const fileNumberDisplay = document.getElementById('file_number_display');

                boxNumber.innerHTML = '<option value="">Pilih Box...</option>';
                fileNumberDisplay.textContent = 'Pilih Box terlebih dahulu';

                // Get real-time data for this rack to update dropdown
                const rackId = document.getElementById('rack_id').value;
                if (rackId) {
                    fetch(`/admin/storage-management/${rackId}/grid-data`)
                        .then(response => response.json())
                        .then(data => {
                            // Clear existing options
                            boxNumber.innerHTML = '<option value="">Pilih Box...</option>';

                            // Filter boxes by row and add with real-time data
                            const rowBoxes = data.boxes.filter(box => {
                                // Find the box's row number from the rack data
                                const boxData = rack.boxes.find(b => b.box_number == box.box_number);
                                return boxData && boxData.row_number == rowNumber;
                            });

                            rowBoxes.forEach(box => {
                                const capacity = box.capacity;
                                const archiveCount = box.archive_count;

                                let status = ' (Kosong)';
                                if (archiveCount >= capacity) status = ' (Penuh)';
                                else if (archiveCount >= capacity / 2) status = ' (Sebagian)';
                                else if (archiveCount > 0) status = ' (Tersedia)';

                                boxNumber.innerHTML +=
                                    `<option value="${box.box_number}" data-capacity="${box.capacity}" data-count="${box.archive_count}">Box ${box.box_number}${status}</option>`;
                            });

                            // Update file number if a box is already selected
                            if (boxNumber.value) {
                                updateFileNumberFromSelectedBox();
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching real-time box data for dropdown:', error);
                            // Fallback to static data
                            if (rack && rack.boxes && Array.isArray(rack.boxes)) {
                                const rowBoxes = rack.boxes.filter(box => box.row_number == rowNumber);
                                rowBoxes.forEach(box => {
                                    const capacity = box.capacity;
                                    const halfN = capacity / 2;
                                    const archiveCount = box.archive_count;

                                    let status = ' (Kosong)';
                                    if (archiveCount >= capacity) status = ' (Penuh)';
                                    else if (archiveCount >= halfN) status = ' (Sebagian)';
                                    else if (archiveCount > 0) status = ' (Tersedia)';

                                    boxNumber.innerHTML +=
                                        `<option value="${box.box_number}" data-capacity="${box.capacity}" data-count="${box.archive_count}">Box ${box.box_number}${status}</option>`;
                                });
                            }
                        });
                }
            }

            // Function to update file number based on selected box
            function updateFileNumberFromSelectedBox() {
                const boxSelect = document.getElementById('box_number');
                const fileNumberDisplay = document.getElementById('file_number_display');
                const fileNumberInput = document.getElementById('file_number');

                if (boxSelect && boxSelect.value) {
                    const selectedOption = boxSelect.options[boxSelect.selectedIndex];
                    const archiveCount = parseInt(selectedOption.getAttribute('data-count') || 0);
                    const capacity = parseInt(selectedOption.getAttribute('data-capacity') || 0);

                    if (archiveCount >= capacity) {
                        fileNumberDisplay.textContent = 'PENUH';
                        if (fileNumberInput) fileNumberInput.value = '';
                    } else {
                        const nextFileNumber = archiveCount + 1;
                        fileNumberDisplay.textContent = nextFileNumber;
                        if (fileNumberInput) fileNumberInput.value = nextFileNumber;
                    }
                }
            }

            function selectBox(rackId, boxNumber) {
                const currentRack = document.getElementById('rack_id').value;
                const currentRow = document.getElementById('row_number').value;
                const currentBox = document.getElementById('box_number').value;

                // Find the rack and box to get row number
                const rack = racks.find(r => r.id == rackId);
                if (rack && rack.boxes) {
                    const box = rack.boxes.find(b => b.box_number == boxNumber);
                    if (box) {
                        // Check if same location (prioritized)
                        if (currentRack == rackId && currentRow == box.row_number && currentBox == boxNumber) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Lokasi Sama',
                                text: 'Anda memilih lokasi yang sama dengan lokasi saat ini.',
                                confirmButtonText: 'OK'
                            });
                            return;
                        }

                        // Check if box is full
                        if (box.archive_count >= box.capacity) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Box Penuh',
                                text: `Box ${boxNumber} sudah penuh (${box.archive_count}/${box.capacity}). Pilih box lain yang tersedia.`,
                                confirmButtonText: 'OK'
                            });
                            return;
                        }

                        // Set rack and row first
                        document.getElementById('rack_id').value = rackId;
                        document.getElementById('row_number').value = box.row_number;

                        // Update box dropdown for the selected row
                        updateBoxDropdown(rack, box.row_number);

                        // Set box number with longer delay to ensure dropdown is populated
                        setTimeout(() => {
                            document.getElementById('box_number').value = boxNumber;

                            // Trigger change events
                            $('#rack_id').trigger('change');
                            $('#row_number').trigger('change');
                            $('#box_number').trigger('change');

                            // Auto-generate file number after dropdown is fully updated
                            setTimeout(() => {
                                // Get real-time data for the selected box
                                fetch(`/admin/storage-management/${rackId}/grid-data`)
                                    .then(response => response.json())
                                    .then(data => {
                                        const selectedBox = data.boxes.find(b => b.box_number == boxNumber);
                                        if (selectedBox) {
                                            const archiveCount = selectedBox.archive_count;
                                            const capacity = selectedBox.capacity;

                                            if (archiveCount >= capacity) {
                                                document.getElementById('file_number_display').textContent = 'PENUH';
                                                document.getElementById('file_number').value = '';
                                            } else {
                                                const nextFileNumber = archiveCount + 1;
                                                document.getElementById('file_number_display').textContent = nextFileNumber;
                                                document.getElementById('file_number').value = nextFileNumber;
                                            }
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error fetching real-time data for file number:', error);
                                        // Fallback to dropdown data
                                        const boxSelect = document.getElementById('box_number');
                                        if (boxSelect.value) {
                                            const selectedOption = boxSelect.options[boxSelect.selectedIndex];
                                            const archiveCount = parseInt(selectedOption.getAttribute('data-count') || 0);
                                            const capacity = parseInt(selectedOption.getAttribute('data-capacity') || 0);

                                            if (archiveCount >= capacity) {
                                                document.getElementById('file_number_display').textContent = 'PENUH';
                                                document.getElementById('file_number').value = '';
                                            } else {
                                                const nextFileNumber = archiveCount + 1;
                                                document.getElementById('file_number_display').textContent = nextFileNumber;
                                                document.getElementById('file_number').value = nextFileNumber;
                                            }
                                        }
                                    });
                            }, 200); // Wait for dropdown to be fully populated
                        }, 300); // Wait for dropdown update to complete
                    }
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Box Dipilih',
                    text: `Box ${boxNumber} telah dipilih`,
                    showConfirmButton: false,
                    timer: 1500
                });
            }

            // Auto-sync function for real-time storage box counts (less aggressive)
            function autoSyncStorage() {
                // Only sync if user has selected a rack
                const rackId = document.getElementById('rack_id').value;
                if (!rackId) return;

                // Storage box count is automatically updated
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
                        console.log('Auto-sync completed for edit-location');
                        // Only update grid if user is still on the same rack
                        const currentRackId = document.getElementById('rack_id').value;
                        if (currentRackId == rackId) {
                            updateVisualGrid('auto-sync');
                        }
                    }
                })
                .catch(error => {
                    console.error('Auto-sync error:', error);
                });
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
                        autoBoxDetails.textContent =
                            `Akan mengisi Box ${startBox} untuk ${archiveCount} arsip dengan file numbering otomatis mulai dari nomor ${startNumber}`;
                    }
                } else {
                    autoBoxInfo.classList.add('hidden');
                    definitiveNumberInput.value = '';
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                const rackSelect = document.getElementById('rack_id');
                const rowSelect = document.getElementById('row_number');
                const boxSelect = document.getElementById('box_number');

                updateVisualGrid();
                rackSelect.addEventListener('change', function() {
                    updateAutoFields();
                    // Reset file number when rack changes
                    document.getElementById('file_number_display').textContent = 'Pilih Box terlebih dahulu';
                    document.getElementById('file_number').value = '';
                });

                rowSelect.addEventListener('change', function() {
                    const rackId = document.getElementById('rack_id').value;
                    const rowNumber = this.value;
                    if (rackId && rowNumber) {
                        const rack = racks.find(r => r.id == rackId);
                        if (rack) {
                            updateBoxDropdown(rack, rowNumber);
                            updateVisualGrid();
                            // Reset file number when row changes
                            document.getElementById('file_number_display').textContent = 'Pilih Box terlebih dahulu';
                            document.getElementById('file_number').value = '';
                        }
                    }
                });

                boxSelect.addEventListener('change', function() {
                    const boxNumber = this.value;
                    if (boxNumber) {
                        // Update file number using the new function
                        updateFileNumberFromSelectedBox();
                    } else {
                        // Reset file number display
                        document.getElementById('file_number_display').textContent = 'Pilih Box terlebih dahulu';
                        document.getElementById('file_number').value = '';
                    }
                    updateVisualGrid();
                });

                $('#locationForm').on('submit', function(e) {
                    e.preventDefault();
                    const form = this;
                    const rackId = document.getElementById('rack_id').value;
                    const rowNumber = document.getElementById('row_number').value;
                    const boxNumber = document.getElementById('box_number').value;

                    // Check if same location
                    const currentRack = @json($currentRack ? $currentRack->id : null);
                    const currentRow = @json($currentRow);
                    const currentBox = @json($currentBox);

                    if (currentRack && currentRow && currentBox) {
                        if (rackId == currentRack && rowNumber == currentRow && boxNumber == currentBox) {
                            Swal.fire({
                                title: 'Lokasi Sama',
                                text: 'Anda memilih lokasi yang sama dengan lokasi saat ini. Tidak ada perubahan yang akan dilakukan.',
                                icon: 'warning',
                                confirmButtonText: 'OK'
                            });
                            return;
                        }
                    }

                    Swal.fire({
                        title: 'Konfirmasi Perubahan Lokasi',
                        text: 'Anda yakin ingin mengubah lokasi penyimpanan arsip ini?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Ubah Lokasi!',
                        cancelButtonText: 'Batal'
                    }).then(result => {
                        if (result.isConfirmed) {
                            // Show loading
                            Swal.fire({
                                title: 'Memproses...',
                                text: 'Menyimpan perubahan lokasi',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            form.submit();
                        }
                    });
                });

                @if (session('success'))
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: '{{ session('success') }}',
                        showConfirmButton: false,
                        timer: 1500
                    });
                @endif

                // Start auto-sync every 5 seconds for real-time data (less aggressive)
                setInterval(autoSyncStorage, 5000);
            });
        </script>
    @endpush
</x-app-layout>
