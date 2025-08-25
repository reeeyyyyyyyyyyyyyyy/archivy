<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-pink-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-map-marker-alt text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Set Lokasi Penyimpanan</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Intern: Atur lokasi penyimpanan untuk arsip:
                            {{ $archive->index_number }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('intern.storage.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-orange-100 to-pink-100 hover:from-orange-200 hover:to-pink-200 text-orange-700 rounded-lg transition-all duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="p-6 space-y-6">
        <!-- Archive Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-file-alt mr-2 text-orange-500"></i>Informasi Arsip
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nomor Arsip</label>
                    <p class="mt-1 text-sm text-gray-900 font-medium">{{ $archive->index_number }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Uraian</label>
                    <p class="text-gray-900">{{ $archive->description }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <span
                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                        {{ $archive->status === 'Aktif'
                            ? 'bg-green-100 text-green-800'
                            : ($archive->status === 'Inaktif'
                                ? 'bg-yellow-100 text-yellow-800'
                                : ($archive->status === 'Permanen'
                                    ? 'bg-purple-100 text-purple-800'
                                    : ($archive->status === 'Musnah'
                                        ? 'bg-red-100 text-red-800'
                                        : 'bg-indigo-100 text-indigo-800'))) }}">
                        {{ $archive->status }}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Arsip</label>
                    <p class="text-gray-900">{{ $archive->kurun_waktu_start->format('d-m-Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Storage Location Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-cogs mr-2 text-orange-500"></i>Pilih Lokasi Penyimpanan
            </h3>

            <form method="POST" action="{{ route('intern.storage.store', $archive->id) }}" class="space-y-6">
                @csrf

                <!-- Rack Selection -->
                <div>
                    <label for="rack_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-warehouse mr-2 text-orange-500"></i>Pilih Rak
                    </label>
                    <select name="rack_id" id="rack_id"
                        class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors py-3 px-4"
                        required>
                        <option value="">Pilih Rak...</option>
                        @foreach ($racks as $rack)
                            <option value="{{ $rack->id }}" data-rows="{{ $rack->total_rows }}"
                                data-boxes="{{ $rack->total_boxes }}">
                                {{ $rack->name }}
                                ({{ $rack->available_boxes_count ?? $rack->getAvailableBoxesCount() }} box tersedia)
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
                            class="w-full bg-gray-100 border border-gray-300 rounded-xl shadow-sm py-3 px-4" readonly>
                        <p class="mt-1 text-xs text-gray-500">Auto-filled berdasarkan rak yang dipilih</p>
                    </div>

                    <div>
                        <label for="row_number" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-list-ol mr-2 text-green-500"></i>Nomor Baris
                        </label>
                        <select name="row_number" id="row_number"
                            class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors py-3 px-4"
                            required>
                            <option value="">Pilih Baris...</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Pilih baris untuk melihat box yang tersedia</p>
                    </div>

                    <div>
                        <label for="box_number" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-box mr-2 text-purple-500"></i>Nomor Box
                        </label>
                        <select name="box_number" id="box_number"
                            class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors py-3 px-4"
                            required>
                            <option value="">Pilih Box...</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Pilih box untuk melihat kapasitas</p>
                    </div>
                </div>

                <!-- File Number Display -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-file mr-2 text-orange-500"></i>Nomor File
                    </label>
                    <div class="w-full bg-blue-50 border border-blue-200 rounded-xl py-3 px-4">
                        <span id="file_number_display" class="text-lg font-semibold text-blue-800">1</span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Nomor file akan otomatis di-assign</p>
                </div>

                <!-- Box Contents Info -->
                <div id="box_contents_info" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Isi Box Saat Ini</label>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 max-h-32 overflow-y-auto">
                        <div id="box_contents_list" class="text-sm text-gray-700">
                            <!-- Will be populated by JavaScript -->
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                    <button type="button" id="next_box_btn" onclick="moveToNextBox()"
                        class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-colors">
                        <i class="fas fa-forward mr-2"></i>Box Berikutnya
                    </button>
                    <a href="{{ route('staff.storage.index') }}"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Simpan Lokasi
                    </button>
                </div>
            </form>
        </div>

        <!-- Visual Grid Preview -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-th mr-2 text-cyan-500"></i>Preview Visual Grid
            </h3>
            <div id="visual_grid" class="space-y-4">
                <!-- Will be populated by JavaScript -->
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            const racks = @json($racks ?? []);
            const archive = @json($archive);

            // Debug: Check if racks is properly loaded
            console.log('Racks loaded:', racks);
            console.log('Racks type:', typeof racks);
            console.log('Racks is array:', Array.isArray(racks));

            function updateVisualGrid() {
                const rackId = document.getElementById('rack_id').value;
                const visualGrid = document.getElementById('visual_grid');

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
                            <div class="${statusClass} border rounded p-2 text-center text-xs">
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

            function updateAutoFields() {
                const rackId = document.getElementById('rack_id').value;
                const rackNumber = document.getElementById('rack_number');
                const rowNumber = document.getElementById('row_number');
                const boxNumber = document.getElementById('box_number');
                const fileNumberDisplay = document.getElementById('file_number_display');

                console.log('updateAutoFields called with rackId:', rackId);
                console.log('racks:', racks);

                if (rackId && Array.isArray(racks)) {
                    const rack = racks.find(r => r.id == rackId);
                    console.log('Found rack:', rack);

                    if (rack) {
                        // Auto-fill rack number
                        rackNumber.value = rack.id;

                        // Populate row dropdown
                        rowNumber.innerHTML = '<option value="">Pilih Baris...</option>';
                        for (let i = 1; i <= rack.total_rows; i++) {
                            rowNumber.innerHTML += `<option value="${i}">Baris ${i}</option>`;
                        }

                        // Use pre-calculated next available box data
                        if (rack.next_available_box) {
                            rowNumber.value = rack.next_available_box.row_number;
                            updateBoxDropdown(rack, rack.next_available_box.row_number);
                            boxNumber.value = rack.next_available_box.box_number;

                            // Calculate file number based on actual archive count
                            const selectedBox = rack.boxes ? rack.boxes.find(b => b.box_number == rack.next_available_box
                                .box_number) : null;
                            if (selectedBox) {
                                if (selectedBox.archive_count >= selectedBox.capacity) {
                                    fileNumberDisplay.textContent = 'PENUH';
                                } else {
                                    fileNumberDisplay.textContent = selectedBox.archive_count + 1;
                                }

                                // Check soft limit warning (40 archives)
                                if (selectedBox.archive_count >= 40) {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Peringatan Kapasitas',
                                        text: `Box ${selectedBox.box_number} sudah berisi ${selectedBox.archive_count} arsip. Disarankan untuk menggunakan box berikutnya.`,
                                        showCancelButton: true,
                                        confirmButtonText: 'Lanjutkan',
                                        cancelButtonText: 'Pilih Box Lain',
                                        confirmButtonColor: '#4F46E5',
                                        cancelButtonColor: '#6B7280'
                                    });
                                }
                            }
                        } else {
                            // If no available box, show first row/box
                            rowNumber.value = 1;
                            updateBoxDropdown(rack, 1);
                            boxNumber.value = 1;

                            // Calculate file number for first box
                            const firstBox = rack.boxes ? rack.boxes.find(b => b.box_number == 1) : null;
                            if (firstBox) {
                                if (firstBox.archive_count >= firstBox.capacity) {
                                    fileNumberDisplay.textContent = 'PENUH';
                                } else {
                                    fileNumberDisplay.textContent = firstBox.archive_count + 1;
                                }
                            } else {
                                fileNumberDisplay.textContent = '1';
                            }
                        }
                    }
                } else {
                    rackNumber.value = '';
                    rowNumber.innerHTML = '<option value="">Pilih Baris...</option>';
                    boxNumber.innerHTML = '<option value="">Pilih Box...</option>';
                    fileNumberDisplay.textContent = 'Pilih Box terlebih dahulu';
                }

                updateVisualGrid();
            }

            function updateBoxDropdown(rack, rowNumber) {
                const boxNumber = document.getElementById('box_number');
                boxNumber.innerHTML = '<option value="">Pilih Box...</option>';

                console.log('updateBoxDropdown called with rack:', rack, 'rowNumber:', rowNumber);

                if (rack && rack.boxes && Array.isArray(rack.boxes)) {
                    const rowBoxes = rack.boxes.filter(box => box.row_number == rowNumber);
                    console.log('Found rowBoxes:', rowBoxes);

                    rowBoxes.forEach(box => {
                        let status = ' (Tersedia)';
                        if (box.archive_count >= box.capacity) {
                            status = ' (Penuh)';
                        } else if (box.archive_count >= box.capacity / 2) {
                            status = ' (Sebagian)';
                        }
                        boxNumber.innerHTML +=
                            `<option value="${box.box_number}" data-capacity="${box.capacity}" data-count="${box.archive_count}">Box ${box.box_number}${status}</option>`;
                    });
                } else {
                    console.log('No boxes found for rack or invalid data');
                }
            }

            // Auto-sync function
            function autoSyncStorage() {
                // Storage box count is automatically updated
                fetch('{{ route('intern.storage-management.sync-counts') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('Auto-sync completed');
                            updateVisualGrid();
                            // Update file number display after sync
                            const boxSelect = document.getElementById('box_number');
                            if (boxSelect && boxSelect.value) {
                                const selectedOption = boxSelect.options[boxSelect.selectedIndex];
                                const archiveCount = parseInt(selectedOption.getAttribute('data-count') || 0);
                                const capacity = parseInt(selectedOption.getAttribute('data-capacity') || 0);

                                if (archiveCount >= capacity) {
                                    document.getElementById('file_number_display').textContent = 'PENUH';
                                } else {
                                    const nextFileNumber = archiveCount + 1;
                                    document.getElementById('file_number_display').textContent = nextFileNumber;
                                }
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Auto-sync error:', error);
                    });
            }

            // Initialize
            document.addEventListener('DOMContentLoaded', function() {
                const rackSelect = document.getElementById('rack_id');
                const rowSelect = document.getElementById('row_number');
                const boxSelect = document.getElementById('box_number');

                // Initialize preview grid
                updateVisualGrid();

                rackSelect.addEventListener('change', function() {
                    updateAutoFields();
                    updateVisualGrid(); // Update grid when rack changes

                    // Update file number when rack changes
                    const boxSelect = document.getElementById('box_number');
                    if (boxSelect && boxSelect.value) {
                        const selectedOption = boxSelect.options[boxSelect.selectedIndex];
                        const archiveCount = parseInt(selectedOption.getAttribute('data-count') || 0);
                        const capacity = parseInt(selectedOption.getAttribute('data-capacity') || 0);

                        if (archiveCount >= capacity) {
                            document.getElementById('file_number_display').textContent = 'PENUH';
                        } else {
                            const nextFileNumber = archiveCount + 1;
                            document.getElementById('file_number_display').textContent = nextFileNumber;
                        }
                    }
                });

                rowSelect.addEventListener('change', function() {
                    const rackId = document.getElementById('rack_id').value;
                    const rowNumber = this.value;

                    if (rackId && rowNumber) {
                        const rack = racks.find(r => r.id == rackId);
                        if (rack) {
                            updateBoxDropdown(rack, rowNumber);
                            updateVisualGrid();
                        }
                    }
                });

                boxSelect.addEventListener('change', function() {
                    const boxNumber = this.value;
                    if (boxNumber) {
                        const rackId = document.getElementById('rack_id').value;
                        const rowNumber = document.getElementById('row_number').value;

                        // Use the correct API endpoint for getting next file number
                        fetch(`/intern/storage/box/${rackId}/${boxNumber}/next-file`)
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.json();
                            })
                            .then(data => {
                                const selectedOption = this.options[this.selectedIndex];
                                const archiveCount = parseInt(selectedOption.getAttribute('data-count') ||
                                    0);
                                const capacity = parseInt(selectedOption.getAttribute('data-capacity') ||
                                    0);

                                if (data.next_file_number) {
                                    // Check if box is full
                                    if (archiveCount >= capacity) {
                                        document.getElementById('file_number_display').textContent =
                                            'PENUH';
                                    } else {
                                        document.getElementById('file_number_display').textContent = data
                                            .next_file_number;
                                    }
                                } else {
                                    // Fallback: use archive count + 1
                                    if (archiveCount >= capacity) {
                                        document.getElementById('file_number_display').textContent =
                                            'PENUH';
                                    } else {
                                        const nextFileNumber = archiveCount + 1;
                                        document.getElementById('file_number_display').textContent =
                                            nextFileNumber;
                                    }
                                }
                            })
                            .catch(error => {
                                console.error('Error fetching file number:', error);
                                // Fallback: use archive count + 1
                                const selectedOption = this.options[this.selectedIndex];
                                const archiveCount = parseInt(selectedOption.getAttribute('data-count') ||
                                    0);
                                const capacity = parseInt(selectedOption.getAttribute('data-capacity') ||
                                    0);

                                if (archiveCount >= capacity) {
                                    document.getElementById('file_number_display').textContent = 'PENUH';
                                } else {
                                    const nextFileNumber = archiveCount + 1;
                                    document.getElementById('file_number_display').textContent =
                                        nextFileNumber;
                                }
                            });
                    }
                    updateVisualGrid();
                });



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

                // Start auto-sync every 1 second
                setInterval(autoSyncStorage, 1000);
            });

            function moveToNextBox() {
                const rackId = document.getElementById('rack_id').value;
                const currentBoxNumber = document.getElementById('box_number').value;

                console.log('moveToNextBox called with rackId:', rackId);
                console.log('racks:', racks);

                if (!rackId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Pilih rak terlebih dahulu!'
                    });
                    return;
                }

                if (!Array.isArray(racks)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Data rak tidak tersedia!'
                    });
                    return;
                }

                const rack = racks.find(r => r.id == rackId);
                console.log('Found rack:', rack);

                if (!rack || !rack.boxes) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Data rak tidak ditemukan!'
                    });
                    return;
                }

                // Find current box
                const currentBox = rack.boxes.find(b => b.box_number == currentBoxNumber);
                if (!currentBox) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Box saat ini tidak ditemukan!'
                    });
                    return;
                }

                // Find next available box with minimum 40 archives threshold
                const nextBox = rack.boxes.find(b =>
                    b.box_number > currentBoxNumber &&
                    b.status !== 'full' &&
                    b.archive_count < b.capacity &&
                    b.archive_count < 40
                );

                if (!nextBox) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Informasi',
                        text: 'Tidak ada box berikutnya yang tersedia!'
                    });
                    return;
                }

                // Update form fields
                document.getElementById('row_number').value = nextBox.row_number;
                document.getElementById('box_number').value = nextBox.box_number;

                // Update dropdowns to reflect the new selection
                const rowSelect = document.getElementById('row_number');
                const boxSelect = document.getElementById('box_number');

                // Update row dropdown
                if (rowSelect) {
                    rowSelect.value = nextBox.row_number;
                    // Trigger change event to update box dropdown
                    $(rowSelect).trigger('change');
                }

                // Update box dropdown after a longer delay to ensure row change is processed and box dropdown is populated
                setTimeout(() => {
                    if (boxSelect) {
                        // First, populate the box dropdown for the selected row
                        const rack = racks.find(r => r.id == rackId);
                        if (rack && rack.boxes) {
                            const rowBoxes = rack.boxes.filter(box => box.row_number == nextBox.row_number);
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

                        // Then set the selected box
                        boxSelect.value = nextBox.box_number;
                        $(boxSelect).trigger('change');
                    }
                }, 200);

                // Get real-time file number for the next box
                const nextRackId = document.getElementById('rack_id').value;
                fetch(`/intern/storage/box/${nextRackId}/${nextBox.box_number}/next-file`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        const fileNumberDisplay = document.getElementById('file_number_display');

                        if (data.next_file_number && fileNumberDisplay) {
                            // Check if box is full
                            if (nextBox.archive_count >= nextBox.capacity) {
                                fileNumberDisplay.textContent = 'PENUH';
                            } else {
                                fileNumberDisplay.textContent = data.next_file_number;
                            }
                        } else {
                            // Fallback calculation
                            if (nextBox.archive_count >= nextBox.capacity) {
                                if (fileNumberDisplay) fileNumberDisplay.textContent = 'PENUH';
                            } else {
                                const nextFileNumber = nextBox.archive_count > 0 ? nextBox.archive_count + 1 : 1;
                                if (fileNumberDisplay) fileNumberDisplay.textContent = nextFileNumber;
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching file number:', error);
                        // Fallback calculation
                        const fileNumberDisplay = document.getElementById('file_number_display');

                        if (fileNumberDisplay) {
                            if (nextBox.archive_count >= nextBox.capacity) {
                                fileNumberDisplay.textContent = 'PENUH';
                            } else {
                                const nextFileNumber = nextBox.archive_count > 0 ? nextBox.archive_count + 1 : 1;
                                fileNumberDisplay.textContent = nextFileNumber;
                            }
                        }
                    });

                // Show confirmation
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: `Pindah ke Box ${nextBox.box_number} (Baris ${nextBox.row_number})`,
                    showConfirmButton: false,
                    timer: 1500
                });

                // Update visual grid
                updateVisualGrid();
            }
        </script>
    @endpush
</x-app-layout>
