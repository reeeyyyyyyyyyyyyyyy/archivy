<x-app-layout>
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-edit text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Edit Rak: {{ $rack->name }}</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Ubah informasi rak penyimpanan arsip
                        </p>
                    </div>
                </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.storage-management.show', $rack->id) }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
                <a href="{{ route('admin.storage-management.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-list mr-2"></i>Daftar Rak
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="p-6">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <form method="POST" action="{{ route('admin.storage-management.update', $rack->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Rack Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-tag mr-2 text-blue-500"></i>Nama Rak
                                </label>
                                <input type="text" name="name" id="name" required
                                    class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                    value="{{ old('name', $rack->name) }}" placeholder="Masukkan nama rak">
                                @error('name')
                                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Capacity per Box -->
                            <div>
                                <label for="capacity_per_box" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-boxes mr-2 text-green-500"></i>Kapasitas per Box
                                </label>
                                <input type="number" name="capacity_per_box" id="capacity_per_box" min="10" max="100" required
                                    class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                    value="{{ old('capacity_per_box', $rack->capacity_per_box) }}" placeholder="40">
                                @error('capacity_per_box')
                                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-toggle-on mr-2 text-purple-500"></i>Status
                                </label>
                                <select name="status" id="status" required
                                    class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4">
                                    <option value="active" {{ old('status', $rack->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                                    <option value="inactive" {{ old('status', $rack->status) == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                                    <option value="maintenance" {{ old('status', $rack->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                </select>
                                @error('status')
                                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Year Start -->
                            <div>
                                <label for="year_start" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-calendar mr-2 text-blue-500"></i>Tahun Mulai
                                </label>
                                <input type="number" name="year_start" id="year_start" min="1900" max="2100"
                                    class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                    value="{{ old('year_start', $rack->year_start) }}" placeholder="Contoh: 2019">
                                @error('year_start')
                                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Year End -->
                            <div>
                                <label for="year_end" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-calendar mr-2 text-red-500"></i>Tahun Akhir
                                </label>
                                <input type="number" name="year_end" id="year_end" min="1900" max="2100"
                                    class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                    value="{{ old('year_end', $rack->year_end) }}" placeholder="Contoh: 2023">
                                @error('year_end')
                                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-align-left mr-2 text-gray-500"></i>Deskripsi
                                </label>
                                <textarea name="description" id="description" rows="3"
                                    class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                    placeholder="Masukkan deskripsi rak (opsional)">{{ old('description', $rack->description) }}</textarea>
                                @error('description')
                                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Current Rack Information -->
                        <div class="mt-8 p-4 bg-gray-50 rounded-xl">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Rak Saat Ini</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-blue-50 p-3 rounded-lg">
                                    <div class="text-sm font-medium text-blue-600">Total Baris</div>
                                    <div class="text-lg font-semibold text-blue-900">{{ $rack->total_rows }}</div>
                                </div>
                                <div class="bg-green-50 p-3 rounded-lg">
                                    <div class="text-sm font-medium text-green-600">Total Box</div>
                                    <div class="text-lg font-semibold text-green-900">{{ $rack->total_boxes }}</div>
                                </div>
                                <div class="bg-purple-50 p-3 rounded-lg">
                                    <div class="text-sm font-medium text-purple-600">Arsip dalam Rak</div>
                                    <div class="text-lg font-semibold text-purple-900">{{ \App\Models\Archive::where('rack_number', $rack->id)->count() }}</div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 mt-3">
                                <i class="fas fa-info-circle mr-1"></i>
                                Total baris dan box tidak dapat diubah karena sudah ada struktur yang dibuat.
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end space-x-4 mt-8">
                            <a href="{{ route('admin.storage-management.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-700 text-white rounded-lg transition-colors">
                                <i class="fas fa-times mr-2"></i>Batal
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                <i class="fas fa-save mr-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
