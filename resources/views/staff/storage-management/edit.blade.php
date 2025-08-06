<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-teal-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-warehouse text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Edit Rak: {{ $rack->name }}</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Ubah konfigurasi rak penyimpanan
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('staff.storage-management.index') }}"
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
                <form action="{{ route('staff.storage-management.update', $rack->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Rack Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-warehouse mr-2 text-teal-500"></i>Nama Rak
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $rack->name) }}"
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors py-3 px-4"
                                placeholder="Contoh: Rak 1, Rak A, Rak Utara" required>
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-toggle-on mr-2 text-amber-500"></i>Status
                            </label>
                            <select name="status" id="status"
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors py-3 px-4">
                                <option value="active" {{ old('status', $rack->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ old('status', $rack->status) == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                                <option value="maintenance" {{ old('status', $rack->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            </select>
                            @error('status')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Capacity per Box -->
                        <div>
                            <label for="capacity_per_box" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-box mr-2 text-blue-500"></i>Kapasitas per Box
                            </label>
                            <input type="number" name="capacity_per_box" id="capacity_per_box" value="{{ old('capacity_per_box', $rack->capacity_per_box) }}"
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors py-3 px-4"
                                min="10" max="100" required>
                            @error('capacity_per_box')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Total Rows (Read-only) -->
                        <div>
                            <label for="total_rows" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-layer-group mr-2 text-teal-500"></i>Jumlah Baris
                            </label>
                            <input type="number" name="total_rows" id="total_rows" value="{{ $rack->total_rows }}"
                                class="w-full bg-gray-100 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors py-3 px-4"
                                readonly>
                            <p class="mt-1 text-xs text-gray-500">Tidak dapat diubah setelah rak dibuat</p>
                        </div>
                    </div>

                    <!-- Year Filter -->
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-calendar mr-2 text-teal-500"></i>Filter Tahun Arsip
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="year_start" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-calendar mr-2 text-blue-500"></i>Tahun Mulai
                                </label>
                                <input type="number" name="year_start" id="year_start" value="{{ old('year_start', $rack->year_start) }}"
                                    class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors py-3 px-4"
                                    placeholder="Contoh: 2019" min="1900" max="2100">
                                <p class="text-xs text-gray-500 mt-1">Tahun mulai untuk arsip yang akan disimpan di rak ini</p>
                                @error('year_start')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="year_end" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-calendar mr-2 text-red-500"></i>Tahun Akhir
                                </label>
                                <input type="number" name="year_end" id="year_end" value="{{ old('year_end', $rack->year_end) }}"
                                    class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors py-3 px-4"
                                    placeholder="Contoh: 2023" min="1900" max="2100">
                                <p class="text-xs text-gray-500 mt-1">Tahun akhir untuk arsip yang akan disimpan di rak ini</p>
                                @error('year_end')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mt-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-align-left mr-2 text-gray-500"></i>Deskripsi (Opsional)
                        </label>
                        <textarea name="description" id="description" rows="3"
                            class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors py-3 px-4"
                            placeholder="Deskripsi tambahan tentang rak ini...">{{ old('description', $rack->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Current Statistics -->
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-3">
                            <i class="fas fa-chart-bar mr-2 text-teal-500"></i>Statistik Saat Ini
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="font-medium">Total Baris:</span>
                                <span>{{ $rack->total_rows }}</span>
                            </div>
                            <div>
                                <span class="font-medium">Total Box:</span>
                                <span>{{ $rack->total_boxes }}</span>
                            </div>
                            <div>
                                <span class="font-medium">Box Tersedia:</span>
                                <span class="text-green-600">{{ $rack->getAvailableBoxesCount() }}</span>
                            </div>
                            <div>
                                <span class="font-medium">Utilisasi:</span>
                                <span>{{ $rack->getUtilizationPercentage() }}%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ route('staff.storage-management.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                            <i class="fas fa-times mr-2"></i>Batal
                        </a>
                        <button type="submit"
                            class="inline-flex items-center px-6 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition-colors">
                            <i class="fas fa-save mr-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Update preview when form fields change
        document.getElementById('capacity_per_box').addEventListener('input', updatePreview);

        function updatePreview() {
            const capacity = parseInt(document.getElementById('capacity_per_box').value) || 0;
            const rows = {{ $rack->total_rows }};
            const boxes = rows * 4; // 4 boxes per row
            const totalCapacity = boxes * capacity;

            console.log('Preview updated:', { capacity, rows, boxes, totalCapacity });
        }

        // Initialize preview
        updatePreview();
    </script>
    @endpush
</x-app-layout>
