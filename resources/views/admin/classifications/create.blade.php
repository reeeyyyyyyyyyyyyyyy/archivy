<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b px-6 py-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tambah Klasifikasi Baru</h1>
                <p class="text-sm text-gray-600 mt-1">Buat klasifikasi arsip dalam kategori yang sudah ada</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.classifications.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali ke Klasifikasi
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

            <form action="{{ route('admin.classifications.store') }}" method="POST" class="space-y-6" id="classificationForm">
                @csrf

                <!-- Basic Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                        Informasi Klasifikasi
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Category -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-folder mr-2 text-indigo-500"></i>Kategori
                            </label>
                            <select name="category_id" id="category_id"
                                    class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4" required>
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')<span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                        </div>

                        <!-- Code -->
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-hashtag mr-2 text-green-500"></i>Kode Klasifikasi
                            </label>
                            <input type="text" name="code" id="code"
                                   class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                   value="{{ old('code') }}" required
                                   placeholder="Contoh: 01.02.03">
                            @error('code')<span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                        </div>
                    </div>

                                        <!-- Nama Klasifikasi -->
                    <div class="mt-6">
                        <label for="nama_klasifikasi" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-tags mr-2 text-cyan-500"></i>Nama Klasifikasi
                        </label>
                        <input type="text" name="nama_klasifikasi" id="nama_klasifikasi"
                               class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                               value="{{ old('nama_klasifikasi') }}" required
                               placeholder="Masukkan nama klasifikasi">
                        @error('nama_klasifikasi')<span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                    </div>
                </div>

                <!-- Retention Settings -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-clock mr-2 text-green-500"></i>
                        Pengaturan Retensi
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Retention Aktif -->
                        <div>
                            <label for="retention_aktif" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-check mr-2 text-green-500"></i>Retensi Aktif (Tahun)
                            </label>
                            <input type="number" name="retention_aktif" id="retention_aktif"
                                   class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                   value="{{ old('retention_aktif', 0) }}" required min="0"
                                   placeholder="Masukkan jumlah tahun">
                            @error('retention_aktif')<span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                        </div>

                        <!-- Retention Inaktif -->
                        <div>
                            <label for="retention_inaktif" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-times mr-2 text-yellow-500"></i>Retensi Inaktif (Tahun)
                            </label>
                            <input type="number" name="retention_inaktif" id="retention_inaktif"
                                   class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                   value="{{ old('retention_inaktif', 0) }}" required min="0"
                                   placeholder="Masukkan jumlah tahun">
                            @error('retention_inaktif')<span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                <!-- Final Disposition -->
                <div class="border-b border-gray-200 pb-6">
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
                            <option value="Musnah" {{ old('nasib_akhir') == 'Musnah' ? 'selected' : '' }}>Musnah</option>
                            <option value="Permanen" {{ old('nasib_akhir') == 'Permanen' ? 'selected' : '' }}>Permanen</option>
                        </select>
                        @error('nasib_akhir')<span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror

                        <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-sm text-blue-700">
                                <i class="fas fa-info-circle mr-1"></i>
                                <strong>Catatan:</strong> Nasib akhir menentukan apa yang terjadi dengan arsip setelah masa retensi berakhir.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="pt-6 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <button type="submit"
                                    class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-colors shadow-sm" id="submitBtn">
                                <i class="fas fa-save mr-2"></i>
                                Simpan Klasifikasi
                            </button>
                            <a href="{{ route('admin.classifications.index') }}"
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

@push('scripts')
<script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('classificationForm');
            const submitBtn = document.getElementById('submitBtn');

            if (form && submitBtn) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Field validation
                    const requiredFields = ['category_id', 'code', 'nama_klasifikasi', 'retention_aktif', 'retention_inaktif', 'nasib_akhir'];

                    if (!window.validateRequiredFields(requiredFields)) {
                        return;
                    }

                    // Confirm create with SWAL
                    window.showCreateConfirm('Apakah Anda yakin ingin menyimpan klasifikasi baru ini?')
                        .then((result) => {
                            if (result.isConfirmed) {
                                // Show loading state
                                submitBtn.disabled = true;
                                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';

                                // Submit form
                                form.submit();
                            }
                        });
                });
            }
        });
</script>
@endpush
