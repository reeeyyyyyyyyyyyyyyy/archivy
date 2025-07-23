<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b px-6 py-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Kategori</h1>
                <p class="text-sm text-gray-600 mt-1">Perbarui informasi kategori: {{ $category->name }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.categories.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali ke Kategori
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="p-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <h4 class="font-medium">Terdapat kesalahan:</h4>
                    </div>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="text-sm">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <!-- Basic Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                        Informasi Kategori
                    </h3>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-folder mr-2 text-indigo-500"></i>Nama Kategori
                            </label>
                            <input type="text" name="name" id="name" 
                                   class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4" 
                                   value="{{ old('name', $category->name) }}" required 
                                   placeholder="Masukkan nama kategori">
                            @error('name')<span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                <!-- Retention Settings -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-clock mr-2 text-green-500"></i>
                        Pengaturan Retensi
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Retention Active -->
                        <div>
                            <label for="retention_active" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-check mr-2 text-green-500"></i>Retensi Aktif (Tahun)
                            </label>
                            <input type="number" name="retention_active" id="retention_active" 
                                   class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4" 
                                   value="{{ old('retention_active', $category->retention_active) }}" required min="0" 
                                   placeholder="Masukkan jumlah tahun">
                            @error('retention_active')<span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                        </div>

                        <!-- Retention Inactive -->
                        <div>
                            <label for="retention_inactive" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-times mr-2 text-yellow-500"></i>Retensi Inaktif (Tahun)
                            </label>
                            <input type="number" name="retention_inactive" id="retention_inactive" 
                                   class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4" 
                                   value="{{ old('retention_inactive', $category->retention_inactive) }}" required min="0" 
                                   placeholder="Masukkan jumlah tahun">
                            @error('retention_inactive')<span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                <!-- Final Disposition -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-gavel mr-2 text-purple-500"></i>
                        Nasib Akhir Arsip
                    </h3>
                    
                    <div>
                        <label for="nasib_akhir" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-exclamation-triangle mr-2 text-red-500"></i>Nasib Akhir
                        </label>
                        <select name="nasib_akhir" id="nasib_akhir" 
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4" required>
                            <option value="">Pilih Nasib Akhir</option>
                            <option value="Musnah" {{ old('nasib_akhir', $category->nasib_akhir) == 'Musnah' ? 'selected' : '' }}>Musnah</option>
                            <option value="Permanen" {{ old('nasib_akhir', $category->nasib_akhir) == 'Permanen' ? 'selected' : '' }}>Permanen</option>
                            <option value="Dinilai Kembali" {{ old('nasib_akhir', $category->nasib_akhir) == 'Dinilai Kembali' ? 'selected' : '' }}>Dinilai Kembali</option>
                        </select>
                        @error('nasib_akhir')<span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                        
                        <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-sm text-blue-700">
                                <i class="fas fa-info-circle mr-1"></i>
                                <strong>Catatan:</strong> Perubahan nasib akhir akan mempengaruhi semua arsip dalam kategori ini.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="pt-6 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <button type="submit" 
                                    class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-colors shadow-sm">
                                <i class="fas fa-save mr-2"></i>
                                Update Kategori
                            </button>
                            <a href="{{ route('admin.categories.index') }}" 
                               class="inline-flex items-center px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-xl transition-colors">
                                <i class="fas fa-times mr-2"></i>
                                Batal
                            </a>
                        </div>
                        <div class="text-sm text-gray-500">
                            <i class="fas fa-asterisk text-red-400 mr-1"></i>
                            Field yang wajib diisi
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>