<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-pink-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-plus text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Tambah Berkas Arsip yang Sama</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-link mr-1"></i>Tambahkan arsip terkait dengan kategori dan klasifikasi yang
                            sama
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="javascript:window.history.back()"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-orange-100 to-pink-100 hover:from-orange-200 hover:to-pink-200 text-orange-700 rounded-lg transition-all duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="p-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            {{-- Display validation errors if any --}}
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

            <form method="POST" action="{{ route('intern.archives.store-related', $parentArchive) }}" class="space-y-6"
                id="createRelatedForm">
                @csrf

                <!-- Parent Archive Information -->
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-orange-400 text-lg"></i>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-orange-800">Informasi Arsip Induk</h4>
                            <div class="mt-2 text-sm text-orange-700">
                                <p><strong>Kategori:</strong>
                                    {{ $parentArchive->category ? $parentArchive->category->nama_kategori : 'Tidak ada data' }}
                                </p>
                                <p><strong>Klasifikasi:</strong>
                                    {{ $parentArchive->classification ? $parentArchive->classification->nama_klasifikasi : 'Tidak ada data' }}
                                </p>
                                <p><strong>Lampiran Surat:</strong> {{ $parentArchive->lampiran_surat }}</p>
                                {{-- <p><strong>Retensi Aktif:</strong> {{ $parentArchive->retention_aktif ?? 0 }} tahun</p>
                                <p><strong>Retensi Inaktif:</strong> {{ $parentArchive->retention_inaktif ?? 0 }} tahun
                                </p>
                                <p><strong>Nasib Akhir:</strong>
                                    @if ($parentArchive->classification && $parentArchive->classification->nasib_akhir)
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full
                                            @if ($parentArchive->classification->nasib_akhir === 'Musnah') bg-red-100 text-red-800
                                            @elseif($parentArchive->classification->nasib_akhir === 'Permanen') bg-purple-100 text-purple-800
                                            @else bg-blue-100 text-blue-800 @endif">
                                            {{ $parentArchive->classification->nasib_akhir }}
                                        </span>
                                    @else
                                        Tidak ada data
                                    @endif --}}
                                {{-- </p> --}}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Auto-filled fields (hidden) -->
                <input type="hidden" name="category_id" value="{{ $parentArchive->category_id }}">
                <input type="hidden" name="classification_id" value="{{ $parentArchive->classification_id }}">
                <input type="hidden" name="lampiran_surat" value="{{ $parentArchive->lampiran_surat }}">
                <input type="hidden" name="parent_archive_id" value="{{ $parentArchive->id }}">
                <input type="hidden" name="is_manual_input" value="1">

                <!-- Basic Information Section -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-orange-500"></i>
                        Informasi Dasar
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nomor Arsip -->
                        <div>
                            <label for="index_number" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-hashtag mr-2 text-green-500"></i>Nomor Arsip
                            </label>
                            <input type="text" name="index_number" id="index_number"
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                value="{{ old('index_number') }}" required
                                placeholder="Masukkan nomor arsip sesuai format">
                            <div class="mt-1 text-xs text-gray-500">
                                <strong>Format:</strong> Masukkan nomor arsip sesuai format<br>
                                <small class="text-gray-600">Input manual sesuai format</small>
                            </div>
                            @error('index_number')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Tanggal Arsip -->
                        <div>
                            <label for="kurun_waktu_start" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-alt mr-2 text-orange-500"></i>Tanggal Arsip
                            </label>
                            <input type="date" name="kurun_waktu_start" id="kurun_waktu_start"
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                value="{{ old('kurun_waktu_start', date('Y-m-d')) }}" required>
                            @error('kurun_waktu_start')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Uraian Arsip -->
                    <div class="mt-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-file-alt mr-2 text-purple-500"></i>Uraian Arsip
                        </label>
                        <textarea name="description" id="description" rows="4"
                            class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                            required placeholder="Masukkan uraian atau deskripsi arsip">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Archive Details Section -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-cogs mr-2 text-green-500"></i>
                        Detail Arsip
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Tingkat Perkembangan -->
                        <div>
                            <label for="tingkat_perkembangan" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-layer-group mr-2 text-yellow-500"></i>Tingkat Perkembangan
                            </label>
                            <input type="text" name="tingkat_perkembangan" id="tingkat_perkembangan"
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                value="{{ old('tingkat_perkembangan') }}" required
                                placeholder="Contoh: Asli, Salinan, Tembusan">
                            @error('tingkat_perkembangan')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- SKKAD -->
                        <div>
                            <label for="skkad" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-shield-alt mr-2 text-red-500"></i>SKKAD (Sifat Keamanan)
                            </label>
                            <select name="skkad" id="skkad"
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                required>
                                <option value="">Pilih SKKAD...</option>
                                <option value="SANGAT RAHASIA"
                                    {{ old('skkad') == 'SANGAT RAHASIA' ? 'selected' : '' }}>SANGAT RAHASIA</option>
                                <option value="TERBATAS" {{ old('skkad') == 'TERBATAS' ? 'selected' : '' }}>TERBATAS
                                </option>
                                <option value="RAHASIA" {{ old('skkad') == 'RAHASIA' ? 'selected' : '' }}>RAHASIA
                                </option>
                                <option value="BIASA/TERBUKA" {{ old('skkad') == 'BIASA/TERBUKA' ? 'selected' : '' }}>
                                    BIASA/TERBUKA</option>
                            </select>
                            @error('skkad')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Jumlah Berkas -->
                        <div>
                            <label for="jumlah_berkas" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-sort-numeric-up mr-2 text-blue-500"></i>Jumlah Berkas
                            </label>
                            <input type="number" name="jumlah_berkas" id="jumlah_berkas" min="1"
                                step="1"
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                value="{{ old('jumlah_berkas', 1) }}" required placeholder="Masukkan jumlah berkas">
                            @error('jumlah_berkas')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Keterangan -->
                        <div class="md:col-span-2">
                            <label for="ket" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-sticky-note mr-2 text-purple-500"></i>Keterangan (Opsional)
                            </label>
                            <textarea name="ket" id="ket" rows="3"
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                placeholder="Masukkan keterangan tambahan untuk arsip ini...">{{ old('ket') }}</textarea>
                            @error('ket')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Retention Information Section -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-hourglass-half mr-2 text-amber-500"></i>
                                Informasi Retensi
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Retensi Aktif -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-clock mr-2 text-green-500"></i>Retensi Aktif
                                    </label>
                                    <div class="bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 text-gray-900">
                                        {{ $parentArchive->retention_aktif ?? 0 }} tahun
                                    </div>
                                </div>

                                <!-- Retensi Inaktif -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-pause-circle mr-2 text-yellow-500"></i>Retensi Inaktif
                                    </label>
                                    <div class="bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 text-gray-900">
                                        {{ $parentArchive->retention_inaktif ?? 0 }} tahun
                                    </div>
                                </div>

                                <!-- Nasib Akhir -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-flag mr-2 text-purple-500"></i>Nasib Akhir
                                    </label>
                                    <div class="bg-gray-50 border border-gray-300 rounded-xl px-4 py-3">
                                        @if ($parentArchive->manual_nasib_akhir)
                                            {{-- Untuk kategori LAINNYA, gunakan manual_nasib_akhir --}}
                                            <span
                                                class="px-3 py-1 text-sm font-semibold rounded-full
                                                @if ($parentArchive->manual_nasib_akhir === 'Musnah') bg-red-100 text-red-800
                                                @elseif($parentArchive->manual_nasib_akhir === 'Permanen') bg-purple-100 text-purple-800
                                                @else bg-blue-100 text-blue-800 @endif">
                                                {{ $parentArchive->manual_nasib_akhir }}
                                            </span>
                                        @elseif ($parentArchive->classification && $parentArchive->classification->nasib_akhir)
                                            {{-- Untuk kategori JRA, gunakan classification->nasib_akhir --}}
                                            <span
                                                class="px-3 py-1 text-sm font-semibold rounded-full
                                                @if ($parentArchive->classification->nasib_akhir === 'Musnah') bg-red-100 text-red-800
                                                @elseif($parentArchive->classification->nasib_akhir === 'Permanen') bg-purple-100 text-purple-800
                                                @else bg-blue-100 text-blue-800 @endif">
                                                {{ $parentArchive->classification->nasib_akhir }}
                                            </span>
                                        @else
                                            <span class="text-gray-500">Tidak ada data</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                    <a href="{{ route('intern.archives.index') }}"
                        class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-6 py-3 border border-transparent rounded-xl text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-colors"
                        onclick="return confirmCreateRelated()">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Arsip Terkait
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function confirmCreateRelated() {
            Swal.fire({
                title: 'Konfirmasi Simpan Arsip Terkait',
                html: `
                    <div class="text-left">
                        <p class="mb-2">Apakah Anda yakin ingin menyimpan arsip terkait ini?</p>
                        <p class="text-sm text-gray-600 mb-3">
                            Arsip ini akan ditambahkan ke grup arsip yang sama dengan arsip induk.
                        </p>
                        <div class="bg-blue-50 p-3 rounded-lg">
                            <p class="text-sm"><strong>Kategori:</strong> {{ $parentArchive->category ? $parentArchive->category->nama_kategori : 'Tidak ada data' }}</p>
                            <p class="text-sm"><strong>Klasifikasi:</strong> {{ $parentArchive->classification ? $parentArchive->classification->nama_klasifikasi : 'Tidak ada data' }}</p>
                            <p class="text-sm"><strong>Lampiran:</strong> {{ $parentArchive->lampiran_surat }}</p>
                        </div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('createRelatedForm').submit();
                }
            });
            return false;
        }
    </script>
</x-app-layout>
