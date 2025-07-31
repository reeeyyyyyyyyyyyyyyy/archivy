<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-warehouse text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Tambah Rak Baru</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Buat rak penyimpanan baru dengan konfigurasi yang diinginkan
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.storage-management.index') }}"
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
                <form action="{{ route('admin.storage-management.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Rack Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-warehouse mr-2 text-purple-500"></i>Nama Rak
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors py-3 px-4"
                                placeholder="Contoh: Rak 1, Rak A, Rak Utara" required>
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Total Rows -->
                        <div>
                            <label for="total_rows" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-layer-group mr-2 text-green-500"></i>Jumlah Baris
                            </label>
                            <input type="number" name="total_rows" id="total_rows" value="{{ old('total_rows', 4) }}"
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors py-3 px-4"
                                min="1" max="20" required>
                            @error('total_rows')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Capacity per Box -->
                        <div>
                            <label for="capacity_per_box" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-box mr-2 text-blue-500"></i>Kapasitas per Box
                            </label>
                            <input type="number" name="capacity_per_box" id="capacity_per_box" value="{{ old('capacity_per_box', 50) }}"
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors py-3 px-4"
                                min="10" max="100" required>
                            @error('capacity_per_box')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-toggle-on mr-2 text-orange-500"></i>Status
                            </label>
                            <select name="status" id="status"
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors py-3 px-4">
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                                <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            </select>
                            @error('status')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mt-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-align-left mr-2 text-gray-500"></i>Deskripsi (Opsional)
                        </label>
                        <textarea name="description" id="description" rows="3"
                            class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors py-3 px-4"
                            placeholder="Deskripsi tambahan tentang rak ini...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Preview -->
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-3">
                            <i class="fas fa-eye mr-2 text-purple-500"></i>Preview Konfigurasi
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium">Total Baris:</span>
                                <span id="preview_rows">4</span>
                            </div>
                            <div>
                                <span class="font-medium">Total Box:</span>
                                <span id="preview_boxes">16</span>
                            </div>
                            <div>
                                <span class="font-medium">Kapasitas per Box:</span>
                                <span id="preview_capacity">50</span>
                            </div>
                            <div>
                                <span class="font-medium">Total Kapasitas:</span>
                                <span id="preview_total_capacity">800</span>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.storage-management.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                            <i class="fas fa-times mr-2"></i>Batal
                        </a>
                        <button type="submit"
                            class="inline-flex items-center px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                            <i class="fas fa-save mr-2"></i>Simpan Rak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Update preview when form fields change
        document.getElementById('total_rows').addEventListener('input', updatePreview);
        document.getElementById('capacity_per_box').addEventListener('input', updatePreview);

        function updatePreview() {
            const rows = parseInt(document.getElementById('total_rows').value) || 0;
            const capacity = parseInt(document.getElementById('capacity_per_box').value) || 0;
            const boxes = rows * 4; // 4 boxes per row
            const totalCapacity = boxes * capacity;

            document.getElementById('preview_rows').textContent = rows;
            document.getElementById('preview_boxes').textContent = boxes;
            document.getElementById('preview_capacity').textContent = capacity;
            document.getElementById('preview_total_capacity').textContent = totalCapacity;
        }

        // Initialize preview
        updatePreview();
    </script>
    @endpush
</x-app-layout>
