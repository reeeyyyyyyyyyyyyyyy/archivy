<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b px-6 py-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tambah Kategori Baru</h1>
                <p class="text-sm text-gray-600 mt-1">Buat kategori arsip sesuai dengan JRA Pergub 1 & 30</p>
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

            <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Basic Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                        Informasi Kategori
                    </h3>

                    <div class="grid grid-cols-1 gap-6">
                        <!-- Nama Kategori -->
                        <div>
                            <label for="nama_kategori" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-folder mr-2 text-indigo-500"></i>Nama Kategori
                            </label>
                            <input type="text" name="nama_kategori" id="nama_kategori"
                                   class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                   value="{{ old('nama_kategori') }}" required
                                   placeholder="Masukkan nama kategori">
                            @error('nama_kategori')<span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                                <!-- Information -->
                <div>
                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                            <p class="text-sm text-blue-700">
                                <strong>Informasi:</strong> Kategori adalah kelompok besar urusan pemerintahan. Pengaturan retensi dan nasib akhir akan diatur pada level klasifikasi.
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
                                Simpan Kategori
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
