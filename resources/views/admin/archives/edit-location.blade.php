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
                                <p class="mt-1 text-sm text-gray-900">{{ $archive->kurun_waktu_start ? $archive->kurun_waktu_start->format('d/m/Y') : 'N/A' }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Uraian</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $archive->description }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kategori</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $archive->category->nama_kategori ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Klasifikasi</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $archive->classification->nama_klasifikasi ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Current Location -->
                    @if($currentRack)
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
                    <form method="POST" action="{{ route('admin.archives.update-location', $archive) }}" id="locationForm" class="space-y-6">
                        @csrf

                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Edit Lokasi Penyimpanan</h3>
                            <p class="text-sm text-gray-600 mb-6">
                                Pilih lokasi penyimpanan baru untuk arsip ini. Nomor file akan ditentukan otomatis berdasarkan nomor box yang dipilih.
                            </p>
                        </div>

                        <!-- Rack Selection -->
                        <div>
                            <label for="rack_id" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-warehouse mr-2 text-indigo-500"></i>Pilih Rak
                            </label>
                            <select name="rack_id" id="rack_id" class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4" required>
                                <option value="">Pilih Rak...</option>
                                @foreach($racks as $rack)
                                    <option value="{{ $rack->id }}" data-rows="{{ $rack->total_rows }}" data-boxes="{{ $rack->total_boxes }}">
                                        {{ $rack->name }} ({{ $rack->available_boxes_count ?? $rack->getAvailableBoxesCount() }} box tersedia)
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
                                <input type="number" name="rack_number" id="rack_number" class="w-full bg-gray-100 border border-gray-300 rounded-xl shadow-sm py-3 px-4" readonly>
                                <p class="mt-1 text-xs text-gray-500">Auto-filled berdasarkan rak yang dipilih</p>
                            </div>

                            <div>
                                <label for="row_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-list-ol mr-2 text-green-500"></i>Nomor Baris
                                </label>
                                <select name="row_number" id="row_number" class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4" required>
                                    <option value="">Pilih Baris...</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Auto-filled berdasarkan rak yang dipilih</p>
                            </div>

                            <div>
                                <label for="box_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-box mr-2 text-purple-500"></i>Nomor Box
                                </label>
                                <select name="box_number" id="box_number" class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4" required>
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
            // Rack data from PHP
            const racks = @json($racksArray ?? []);
            const archive = @json($archive);

            // Debug racks data
            console.log('Racks data loaded:', racks);
            console.log('Archive data loaded:', archive);
            console.log('Racks type:', typeof racks);
            console.log('Racks is array:', Array.isArray(racks));

            // Initialize racks data

            function updateVisualGrid() {
                const rackId = document.getElementById('rack_id').value;
                const visualGrid = document.getElementById('preview_grid');

                if (!rackId) {
                    visualGrid.innerHTML = '<p class="text-gray-500 text-center">Pilih rak untuk melihat preview</p>';
                    return;
                }

                const rack = racks.find(r => r.id == rackId);
                if (!rack) return;

                let gridHTML = `
                <div class="bg-gray-50 rounded-lg p-6">
                    <h4 class="font-semibold text-gray-900 mb-4 text-lg">${rack.name}</h4>
                    <div class="grid grid-cols-4 gap-6">
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

                            if (box.status === 'full') {
                                statusClass = 'bg-red-100 border-red-200 text-red-600';
                                statusText = 'Full';
                            } else if (box.status === 'partially_full') {
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
                    // Fallback to grid layout if no box data
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

                console.log('updateAutoFields called with rackId:', rackId);

                // Check if elements exist
                if (!rackNumber || !rowNumber || !boxNumber || !fileNumberDisplay) {
                    console.error('Required elements not found');
                    return;
                }

                if (rackId && racks && racks.length > 0) {
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

                        // Clear previous selections
                        boxNumber.innerHTML = '<option value="">Pilih Box...</option>';
                        fileNumberDisplay.textContent = 'Pilih Box terlebih dahulu';

                        // Don't auto-select for edit location - let user choose manually
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

                console.log('updateBoxDropdown called with rack:', rack, 'rowNumber:', rowNumber);

                // Use rack data directly instead of API call
                if (rack && rack.boxes && Array.isArray(rack.boxes)) {
                    const rowBoxes = rack.boxes.filter(box => box.row_number == rowNumber);
                    console.log('Using rack data, found rowBoxes:', rowBoxes);

                    rowBoxes.forEach(box => {
                        const capacity = box.capacity;
                        const halfN = capacity / 2;
                        const archiveCount = box.archive_count;

                        let status = ' (Kosong)';
                        if (archiveCount >= capacity) {
                            status = ' (Penuh)';
                        } else if (archiveCount >= halfN) {
                            status = ' (Sebagian)';
                        } else if (archiveCount > 0) {
                            status = ' (Tersedia)';
                        }

                        boxNumber.innerHTML +=
                            `<option value="${box.box_number}" data-capacity="${box.capacity}" data-count="${box.archive_count}">Box ${box.box_number}${status}</option>`;
                    });
                } else {
                    // Fallback to API if no rack data
                    fetch('{{ route('admin.storage.get-boxes') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            rack_id: rack.id,
                            row_number: rowNumber
                        })
                    })
                .then(response => response.json())
                .then(data => {
                    console.log('API response:', data);
                    if (data.success && data.boxes) {
                        data.boxes.forEach(box => {
                            const capacity = box.capacity;
                            const halfN = capacity / 2;
                            const archiveCount = box.archive_count;

                            let status = ' (Kosong)';
                            if (archiveCount >= capacity) {
                                status = ' (Penuh)';
                            } else if (archiveCount >= halfN) {
                                status = ' (Sebagian)';
                            } else if (archiveCount > 0) {
                                status = ' (Tersedia)';
                            }

                            boxNumber.innerHTML +=
                                `<option value="${box.box_number}" data-capacity="${box.capacity}" data-count="${box.archive_count}">Box ${box.box_number}${status}</option>`;
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching boxes:', error);
                    // Fallback to rack data if available
                    if (rack && rack.boxes && Array.isArray(rack.boxes)) {
                        const rowBoxes = rack.boxes.filter(box => box.row_number == rowNumber);
                        console.log('Using fallback data, found rowBoxes:', rowBoxes);

                        rowBoxes.forEach(box => {
                            const capacity = box.capacity;
                            const halfN = capacity / 2;
                            const archiveCount = box.archive_count;

                            let status = ' (Kosong)';
                            if (archiveCount >= capacity) {
                                status = ' (Penuh)';
                            } else if (archiveCount >= halfN) {
                                status = ' (Sebagian)';
                            } else if (archiveCount > 0) {
                                status = ' (Tersedia)';
                            }

                            boxNumber.innerHTML +=
                                `<option value="${box.box_number}" data-capacity="${box.capacity}" data-count="${box.archive_count}">Box ${box.box_number}${status}</option>`;
                        });
                    }
                });
            }

            // Initialize
            document.addEventListener('DOMContentLoaded', function() {
                const rackSelect = document.getElementById('rack_id');
                const rowSelect = document.getElementById('row_number');
                const boxSelect = document.getElementById('box_number');

                // Initialize preview grid
                updateVisualGrid();

                rackSelect.addEventListener('change', updateAutoFields);

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
                        // Fetch suggested file number from API
                        const rackId = document.getElementById('rack_id').value;
                        fetch(`{{ route('admin.storage.box.next-file', ['rackId' => 'RACK_ID', 'boxNumber' => 'BOX_NUMBER']) }}`
                            .replace('RACK_ID', rackId)
                            .replace('BOX_NUMBER', boxNumber))
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.next_file_number) {
                                    document.getElementById('file_number_display').textContent = data.next_file_number;
                                    document.getElementById('file_number').value = data.next_file_number;
                                } else {
                                    throw new Error('No file number data received');
                                }
                            })
                            .catch(error => {
                                console.error('Error fetching file number:', error);
                                console.log('Using fallback calculation...');

                                // Find box data from racks array for more accurate calculation
                                let selectedBox = null;
                                for (const rack of racks) {
                                    if (rack.boxes) {
                                        selectedBox = rack.boxes.find(box => box.box_number == boxNumber);
                                        if (selectedBox) break;
                                    }
                                }

                                if (selectedBox) {
                                    // Use archive_count from box data
                                    const nextFileNumber = selectedBox.archive_count > 0 ? selectedBox.archive_count + 1 : 1;
                                    document.getElementById('file_number_display').textContent = nextFileNumber;
                                    document.getElementById('file_number').value = nextFileNumber;
                                    console.log(`Box ${boxNumber}: ${selectedBox.archive_count} archives -> next file: ${nextFileNumber}`);
                                } else {
                                    // Ultimate fallback
                                    document.getElementById('file_number_display').textContent = '1';
                                    document.getElementById('file_number').value = '1';
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
            });

            // Function to select box from grid
            function selectBox(rackId, boxNumber) {
                const currentRack = document.getElementById('rack_id').value;
                const currentBox = document.getElementById('box_number').value;

                // Check if selecting the same location
                if (currentRack == rackId && currentBox == boxNumber) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Lokasi Sama',
                        text: 'Anda memilih lokasi yang sama dengan lokasi saat ini.',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                // Set the values
                document.getElementById('rack_id').value = rackId;
                document.getElementById('box_number').value = boxNumber;

                // Trigger change events
                $('#rack_id').trigger('change');
                $('#box_number').trigger('change');

                Swal.fire({
                    icon: 'success',
                    title: 'Box Dipilih',
                    text: `Box ${boxNumber} telah dipilih`,
                    showConfirmButton: false,
                    timer: 1500
                });
            }

            // Form submission with confirmation
            $('#locationForm').on('submit', function(e) {
                e.preventDefault();

                const form = this; // Store form reference

                Swal.fire({
                    title: 'Konfirmasi Perubahan Lokasi',
                    text: 'Anda yakin ingin mengubah lokasi penyimpanan arsip ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Ubah Lokasi!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
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

                        // Submit the form
                        form.submit();
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
