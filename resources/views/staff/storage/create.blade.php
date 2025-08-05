<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-orange-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-map-marker-alt text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Set Lokasi Penyimpanan Arsip</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Tentukan lokasi penyimpanan untuk arsip ini
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('staff.storage.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="p-6">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">

                <!-- Archive Information -->
                <div class="mb-8 p-6 bg-gradient-to-r from-orange-50 to-teal-50 rounded-lg border border-orange-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-archive mr-2 text-orange-600"></i>
                        Informasi Arsip
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nomor Arsip</label>
                            <p class="mt-1 text-sm text-gray-900 font-medium">{{ $archive->index_number }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Arsip</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $archive->kurun_waktu_start->format('d/m/Y') }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Uraian</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $archive->description }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kategori</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $archive->category->nama_kategori }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Klasifikasi</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $archive->classification->nama_klasifikasi }}</p>
                        </div>
                    </div>
                </div>

                <!-- Storage Location Form -->
                <form method="POST" action="{{ route('staff.storage.store', $archive->id) }}" class="space-y-6">
                    @csrf

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-map-marker-alt mr-2 text-teal-600"></i>
                            Set Lokasi Penyimpanan
                        </h3>
                        <p class="text-sm text-gray-600 mb-6">
                            Isi informasi lokasi penyimpanan untuk arsip ini. Nomor file akan ditentukan otomatis berdasarkan nomor box yang dipilih.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Rack Selection -->
                        <div>
                            <label for="rack_id" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-warehouse mr-2 text-orange-500"></i>Pilih Rak
                            </label>
                            <select id="rack_id" name="rack_id"
                                    class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors py-3 px-4"
                                    onchange="updateRackInfo()">
                                <option value="">-- Pilih Rak --</option>
                                @foreach($racks as $rack)
                                    <option value="{{ $rack->id }}" data-rack-number="{{ $rack->rack_number }}">
                                        Rak {{ $rack->rack_number }} - {{ $rack->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('rack_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">
                                Pilih rak untuk mengisi nomor rak otomatis
                            </p>
                        </div>

                        <!-- Box Number -->
                        <div>
                            <label for="box_number" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-box mr-2 text-teal-500"></i>Nomor Box
                            </label>
                            <input type="number" id="box_number" name="box_number"
                                   class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors py-3 px-4"
                                   value="{{ old('box_number', $nextBoxNumber) }}"
                                   required min="1" oninput="updateFileNumber()" />
                            @error('box_number')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">
                                Disarankan: Box {{ $nextBoxNumber }} (Box baru)
                            </p>
                        </div>

                        <!-- File Number Display -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-file mr-2 text-blue-500"></i>Nomor File (Otomatis)
                            </label>
                            <div id="file_number_display"
                                 class="w-full px-3 py-3 border border-gray-300 bg-gray-100 rounded-xl shadow-sm text-gray-700 font-medium">
                                Loading...
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                Nomor file akan ditentukan otomatis berdasarkan box yang dipilih
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Rack Number (Auto-filled) -->
                        <div>
                            <label for="rack_number" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-hashtag mr-2 text-orange-500"></i>Nomor Rak
                            </label>
                            <input type="number" id="rack_number" name="rack_number"
                                   class="w-full bg-gray-100 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors py-3 px-4"
                                   value="{{ old('rack_number') }}" required min="1" readonly />
                            @error('rack_number')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">
                                Akan terisi otomatis saat memilih rak
                            </p>
                        </div>

                        <!-- Row Number -->
                        <div>
                            <label for="row_number" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-layer-group mr-2 text-teal-500"></i>Nomor Baris
                            </label>
                            <input type="number" id="row_number" name="row_number"
                                   class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors py-3 px-4"
                                   value="{{ old('row_number') }}" required min="1" />
                            @error('row_number')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">
                                Contoh: 1, 2, 3, dst. (dari atas ke bawah)
                            </p>
                        </div>
                    </div>

                    <!-- Box Contents Info -->
                    <div id="box_contents_info" class="hidden">
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                            <h4 class="text-sm font-semibold text-orange-900 mb-3 flex items-center">
                                <i class="fas fa-info-circle mr-2"></i>
                                Isi Box Saat Ini
                            </h4>
                            <div class="max-h-32 overflow-y-auto">
                                <div id="box_contents_list" class="text-sm text-gray-700 space-y-1">
                                    <!-- Will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                        <a href="{{ route('staff.archives.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Batal
                        </a>
                        <button type="submit"
                                class="inline-flex items-center px-6 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition-colors">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Lokasi Penyimpanan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript for dynamic functionality -->
    <script>
        let currentBoxNumber = {{ $nextBoxNumber }};

        function updateRackInfo() {
            const rackSelect = document.getElementById('rack_id');
            const rackNumberInput = document.getElementById('rack_number');
            const selectedOption = rackSelect.options[rackSelect.selectedIndex];

            if (selectedOption && selectedOption.value) {
                const rackNumber = selectedOption.getAttribute('data-rack-number');
                rackNumberInput.value = rackNumber;
            } else {
                rackNumberInput.value = '';
            }
        }

        function updateFileNumber() {
            const boxNumber = document.getElementById('box_number').value;
            const fileNumberDisplay = document.getElementById('file_number_display');
            const boxContentsInfo = document.getElementById('box_contents_info');
            const boxContentsList = document.getElementById('box_contents_list');

            if (boxNumber && boxNumber > 0) {
                // Get suggested file number
                fetch(`{{ route('staff.storage.box.next-file', '') }}/${boxNumber}`)
                    .then(response => response.json())
                    .then(data => {
                        fileNumberDisplay.textContent = data.next_file_number;
                    })
                    .catch(error => {
                        fileNumberDisplay.textContent = '1';
                        console.error('Error:', error);
                    });

                // Get box contents if box exists
                fetch(`{{ route('staff.storage.box.contents', '') }}/${boxNumber}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            boxContentsInfo.classList.remove('hidden');
                            boxContentsList.innerHTML = data.map(archive =>
                                `<div class="flex items-center py-1 border-b border-orange-100">
                                    <span class="font-medium text-orange-600">File ${archive.file_number}:</span>
                                    <span class="ml-2">${archive.index_number} - ${archive.description.substring(0, 40)}${archive.description.length > 40 ? '...' : ''}</span>
                                </div>`
                            ).join('');
                        } else {
                            boxContentsInfo.classList.add('hidden');
                        }
                    })
                    .catch(error => {
                        boxContentsInfo.classList.add('hidden');
                        console.error('Error:', error);
                    });
            } else {
                fileNumberDisplay.textContent = '1';
                boxContentsInfo.classList.add('hidden');
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateFileNumber();
        });
    </script>
</x-app-layout>
