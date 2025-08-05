<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-teal-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-map-marker-alt text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Set Lokasi Penyimpanan</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Staff: Atur lokasi penyimpanan untuk arsip:
                            {{ $archive->index_number }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('staff.storage.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
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
                <i class="fas fa-file-alt mr-2 text-teal-500"></i>Informasi Arsip
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">No. Arsip</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $archive->index_number }}</p>
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
                <i class="fas fa-cogs mr-2 text-teal-500"></i>Pilih Lokasi Penyimpanan
            </h3>

            <form method="POST" action="{{ route('staff.storage.store', $archive->id) }}" class="space-y-6">
                @csrf

                <!-- Rack Selection -->
                <div>
                    <label for="rack_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-warehouse mr-2 text-teal-500"></i>Pilih Rak
                    </label>
                    <select name="rack_id" id="rack_id"
                        class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors py-3 px-4"
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
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="rack_number" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-sort-numeric-up mr-2 text-teal-500"></i>Nomor Rak
                        </label>
                        <input type="text" name="rack_number" id="rack_number"
                            class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors py-3 px-4"
                            required readonly>
                    </div>
                    <div>
                        <label for="row_number" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-layer-group mr-2 text-teal-500"></i>Nomor Baris
                        </label>
                        <select name="row_number" id="row_number"
                            class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors py-3 px-4"
                            required>
                            <option value="">Pilih Baris...</option>
                        </select>
                    </div>
                    <div>
                        <label for="box_number" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-box mr-2 text-teal-500"></i>Nomor Box
                        </label>
                        <input type="text" name="box_number" id="box_number"
                            class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors py-3 px-4"
                            required readonly>
                    </div>
                    <div>
                        <label for="file_number" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-file-alt mr-2 text-teal-500"></i>Nomor File
                        </label>
                        <input type="text" name="file_number" id="file_number"
                            class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors py-3 px-4"
                            required readonly>
                    </div>
                </div>

                <!-- Visual Grid Preview -->
                <div class="bg-gray-50 rounded-xl p-4">
                    <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-th mr-2 text-teal-500"></i>Preview Grid Rak
                    </h4>
                    <div id="visualGrid" class="grid grid-cols-4 gap-2">
                        <!-- Grid will be populated by JavaScript -->
                    </div>
                </div>

                <!-- File Number -->
                <div>
                    <label for="file_number" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-file mr-2 text-teal-500"></i>Nomor File
                    </label>
                    <input type="text" name="file_number" id="file_number"
                        class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors py-3 px-4"
                        required>
                    <p class="mt-1 text-xs text-gray-500">Masukkan nomor file untuk arsip ini</p>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end space-x-3">
                    <a href="{{ route('staff.storage.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-6 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-save mr-2"></i>Set Lokasi
                    </button>
                </div>
            </form>
        </div>
    </div>

        @push('scripts')
        <script>
            // Store racks data globally
            const racks = @json($racks);

            document.addEventListener('DOMContentLoaded', function() {
                const rackSelect = document.getElementById('rack_id');
                const rackNumberInput = document.getElementById('rack_number');
                const rowNumberSelect = document.getElementById('row_number');
                const boxNumberInput = document.getElementById('box_number');
                const fileNumberInput = document.getElementById('file_number');
                const visualGrid = document.getElementById('visualGrid');

                rackSelect.addEventListener('change', function() {
                    updateRackSelection();
                });

                rowNumberSelect.addEventListener('change', function() {
                    updateBoxDropdown();
                });

                function updateRackSelection() {
                    const rackId = rackSelect.value;

                    if (rackId) {
                        const rack = racks.find(r => r.id == rackId);
                        if (rack) {
                            rackNumberInput.value = rack.name;
                            updateRowDropdown(rack);
                            updateVisualGrid(rack);
                        }
                    } else {
                        rackNumberInput.value = '';
                        rowNumberSelect.innerHTML = '<option value="">Pilih Baris...</option>';
                        boxNumberSelect.innerHTML = '<option value="">Pilih Box...</option>';
                        visualGrid.innerHTML = '';
                    }
                }

                function updateRowDropdown(rack) {
                    rowNumberSelect.innerHTML = '<option value="">Pilih Baris...</option>';
                    for (let i = 1; i <= rack.total_rows; i++) {
                        rowNumberSelect.innerHTML += `<option value="${i}">Baris ${i}</option>`;
                    }
                }

                                function updateBoxDropdown() {
                    const rackId = rackSelect.value;
                    const rowNumber = rowNumberSelect.value;

                    if (rackId && rowNumber) {
                        const rack = racks.find(r => r.id == rackId);
                        if (rack && rack.boxes) {
                            // Find the next available box for this row
                            const rowBoxes = rack.boxes.filter(box => box.row_number == rowNumber);
                            const availableBox = rowBoxes.find(box => box.archive_count < box.capacity);

                            if (availableBox) {
                                boxNumberInput.value = availableBox.box_number;
                                fileNumberInput.value = availableBox.next_file_number || '1';
                            } else {
                                boxNumberInput.value = '';
                                fileNumberInput.value = '';
                            }
                        }
                    } else {
                        boxNumberInput.value = '';
                        fileNumberInput.value = '';
                    }
                }

                function updateVisualGrid(rack) {
                    if (!rack || !rack.boxes) {
                        visualGrid.innerHTML = '<div class="col-span-4 text-center text-gray-500">Tidak ada data box</div>';
                        return;
                    }

                    // Group boxes by row
                    const boxesByRow = {};
                    rack.boxes.forEach(box => {
                        if (!boxesByRow[box.row_number]) {
                            boxesByRow[box.row_number] = [];
                        }
                        boxesByRow[box.row_number].push(box);
                    });

                    let gridHTML = '';

                    // Sort rows and boxes
                    Object.keys(boxesByRow).sort((a, b) => parseInt(a) - parseInt(b)).forEach(rowNum => {
                        const boxes = boxesByRow[rowNum].sort((a, b) => a.box_number - b.box_number);

                        boxes.forEach(box => {
                            let statusClass = 'bg-green-100 border-green-200 text-green-600';
                            let statusText = 'Available';

                            if (box.archive_count >= box.capacity) {
                                statusClass = 'bg-red-100 border-red-200 text-red-600';
                                statusText = 'Full';
                            } else if (box.archive_count > box.capacity * 0.5) {
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

                    visualGrid.innerHTML = gridHTML;
                }
            });
        </script>
    @endpush
</x-app-layout>
