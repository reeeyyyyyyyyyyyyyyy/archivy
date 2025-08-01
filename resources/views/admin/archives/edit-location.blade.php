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

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Rack Selection -->
                            <div>
                                <label for="rack_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-archive mr-2 text-blue-500"></i>Rak
                                </label>
                                <select name="rack_number" id="rack_number" required
                                    class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                    onchange="updateRowOptions()">
                                    <option value="">Pilih Rak</option>
                                    @foreach($racks as $rack)
                                        <option value="{{ $rack->id }}" {{ $currentRack && $currentRack->id == $rack->id ? 'selected' : '' }}>
                                            {{ $rack->name }} (Kapasitas: {{ $rack->capacity_per_box }}/box)
                                        </option>
                                    @endforeach
                                </select>
                                @error('rack_number')
                                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Row Number -->
                            <div>
                                <label for="row_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-layer-group mr-2 text-green-500"></i>Baris
                                </label>
                                <select name="row_number" id="row_number" required
                                    class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                    onchange="updateBoxOptions()">
                                    <option value="">Pilih Baris</option>
                                </select>
                                @error('row_number')
                                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Box Number -->
                            <div>
                                <label for="box_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-box mr-2 text-purple-500"></i>Box
                                </label>
                                <select name="box_number" id="box_number" required
                                    class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                    onchange="updateFileNumber()">
                                    <option value="">Pilih Box</option>
                                </select>
                                @error('box_number')
                                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
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
                                <p class="mt-1 text-xs text-gray-500">
                                    Nomor file akan ditentukan otomatis berdasarkan box yang dipilih
                                </p>
                            </div>

                            <!-- Hidden File Number Input -->
                            <div class="hidden">
                                <input type="number" name="file_number" id="file_number" value="{{ $currentFile }}">
                            </div>
                        </div>

                        <!-- Preview Grid -->
                        <div class="mt-8">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Preview Grid</h4>
                            <div id="preview_grid" class="grid grid-cols-4 gap-2">
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
            // Initialize on page load
            $(document).ready(function() {
                if ($('#rack_number').val()) {
                    updateRowOptions();
                }
            });

            // Update row options when rack changes
            function updateRowOptions() {
                const rackId = $('#rack_number').val();
                const rowSelect = $('#row_number');
                const boxSelect = $('#box_number');

                rowSelect.empty().append('<option value="">Pilih Baris</option>');
                boxSelect.empty().append('<option value="">Pilih Box</option>');

                if (rackId) {
                    $.get(`/admin/archives/api/rack-rows/${rackId}`, function(rows) {
                        rows.forEach(function(row) {
                            rowSelect.append(new Option(`Baris ${row.row_number}`, row.row_number));
                        });
                        updatePreviewGrid();
                    });
                }
            }

            // Update box options when row changes
            function updateBoxOptions() {
                const rackId = $('#rack_number').val();
                const rowNumber = $('#row_number').val();
                const boxSelect = $('#box_number');

                boxSelect.empty().append('<option value="">Pilih Box</option>');

                if (rackId && rowNumber) {
                    $.get(`/admin/archives/api/rack-row-boxes/${rackId}/${rowNumber}`, function(boxes) {
                        boxes.forEach(function(box) {
                            boxSelect.append(new Option(`Box ${box.box_number}`, box.box_number));
                        });
                    });
                }
            }

            // Update file number when box changes
            function updateFileNumber() {
                const rackId = $('#rack_number').val();
                const boxNumber = $('#box_number').val();

                if (rackId && boxNumber) {
                    $.get(`/admin/storage/box/${boxNumber}/next-file`, function(response) {
                        $('#file_number_display').text(response.next_file_number);
                        $('#file_number').val(response.next_file_number);
                    });
                } else {
                    $('#file_number_display').text('Pilih Box terlebih dahulu');
                    $('#file_number').val('');
                }
            }

            // Update preview grid
            function updatePreviewGrid() {
                const rackId = $('#rack_number').val();

                if (rackId) {
                    $.get(`/admin/storage-management/${rackId}/preview`, function(response) {
                        $('#preview_grid').html(response.html);
                    });
                }
            }

            // Form submission with confirmation
            $('#locationForm').on('submit', function(e) {
                e.preventDefault();

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
                        this.submit();
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
