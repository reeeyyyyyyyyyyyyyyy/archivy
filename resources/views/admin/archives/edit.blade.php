<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-orange-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-edit text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Edit Arsip</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-pencil-alt mr-1"></i>Ubah informasi dan data arsip: {{ $archive->index_number }}
                        </p>
                        @if($archive->box_number)
                            <p class="text-xs text-blue-600 mt-1">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                Lokasi: Rak {{ $archive->rack_number }}, Baris {{ $archive->row_number }}, Box {{ $archive->box_number }}, No. Arsip {{ $archive->file_number }}
                            </p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.archives.edit-location', $archive) }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                        {{ $archive->box_number ? 'Edit Lokasi' : 'Set Lokasi' }}
                        </a>
                    <a href="{{ route('admin.archives.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
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

            <form action="{{ route('admin.archives.update', $archive) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Information Notice -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400 text-lg"></i>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-blue-800">Informasi Penting</h4>
                            <div class="mt-2 text-sm text-blue-700">
                                <p><strong>Kategori JRA:</strong> Sistem otomatis untuk nomor arsip (format: <code>KODE_KLASIFIKASI/NOMOR_URUT/KODE_KOMPONEN/TAHUN</code>), retensi aktif/inaktif, dan nasib akhir</p>
                                <p><strong>Kategori LAINNYA:</strong> Input manual untuk semua field kecuali SKKAD (tetap dropdown), perhitungan retensi tetap otomatis</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Basic Information Section -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                        Informasi Dasar
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Kategori -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-folder mr-2 text-indigo-500"></i>Kategori
                            </label>
                            <select name="category_id" id="category_id"
                                class="select2-dropdown w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4" required>
                                <option value="">Pilih Kategori...</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $archive->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Klasifikasi -->
                        <div>
                            <label for="classification_id" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-tags mr-2 text-cyan-500"></i>Klasifikasi
                            </label>
                            <select name="classification_id" id="classification_id"
                                class="select2-dropdown w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4" required>
                                <option value="">Pilih Klasifikasi...</option>
                            </select>
                            @error('classification_id')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Manual Input Indicator -->
                        <input type="hidden" name="is_manual_input" id="is_manual_input" value="{{ old('is_manual_input', $archive->is_manual_input ? '1' : '0') }}">

                        <!-- Nomor Arsip -->
                        <div id="index_number_container">
                            <label for="index_number" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-hashtag mr-2 text-green-500"></i>Nomor Arsip
                            </label>
                            <input type="text" name="index_number" id="index_number"
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                value="{{ old('index_number', $archive->index_number) }}" required placeholder="Contoh: 001/SKPD">
                            <div id="index_number_example" class="mt-1 text-xs text-gray-500">
                                <strong>Format JRA:</strong> Masukkan NOMOR_URUT/KODE_KOMPONEN (contoh: 001/SKPD)<br>
                                <small class="text-blue-600">Sistem akan auto-generate: KODE_KLASIFIKASI/001/SKPD/2024</small>
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
                                value="{{ old('kurun_waktu_start', $archive->kurun_waktu_start->format('Y-m-d')) }}" required>
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
                            required placeholder="Masukkan uraian atau deskripsi arsip">{{ old('description', $archive->description) }}</textarea>
                        @error('description')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Lampiran Surat -->
                    <div class="mt-6">
                        <label for="lampiran_surat" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-paperclip mr-2 text-teal-500"></i>Lampiran Surat (Opsional)
                        </label>
                        <textarea name="lampiran_surat" id="lampiran_surat" rows="3"
                            class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                            placeholder="Deskripsi lampiran arsip (bukan nomor arsip)">{{ old('lampiran_surat', $archive->lampiran_surat) }}</textarea>
                        @error('lampiran_surat')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Manual Input Fields (Hidden by default, shown for LAINNYA) -->
                <div id="manual_input_section" class="border-b border-gray-200 pb-6 {{ $archive->is_manual_input ? '' : 'hidden' }}">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-edit mr-2 text-red-500"></i>
                        Input Manual (Kategori di Luar JRA)
                    </h3>

                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-orange-400 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-orange-800">Khusus Kategori LAINNYA</h4>
                                <div class="mt-1 text-sm text-orange-700">
                                    <p><strong>Yang Manual:</strong> Retensi aktif/inaktif, nasib akhir, nomor arsip lengkap</p>
                                    <p><strong>Yang Otomatis:</strong> SKKAD (dropdown), perhitungan tanggal retensi</p>
                                    <p><strong>Catatan:</strong> Gunakan kategori & klasifikasi "LAINNYA" untuk arsip di luar JRA</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                        <!-- Manual Active Retention -->
                        <div>
                            <label for="manual_retention_aktif" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-clock mr-2 text-green-500"></i>Retensi Aktif (Tahun)
                            </label>
                            <input type="number" name="manual_retention_aktif" id="manual_retention_aktif" min="0"
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                value="{{ old('manual_retention_aktif', $archive->manual_retention_aktif) }}" placeholder="0">
                            @error('manual_retention_aktif')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Manual Inactive Retention -->
                        <div>
                            <label for="manual_retention_inaktif" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-pause-circle mr-2 text-yellow-500"></i>Retensi Inaktif (Tahun)
                            </label>
                            <input type="number" name="manual_retention_inaktif" id="manual_retention_inaktif" min="0"
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                value="{{ old('manual_retention_inaktif', $archive->manual_retention_inaktif) }}" placeholder="0">
                            @error('manual_retention_inaktif')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Manual Nasib Akhir -->
                        <div class="md:col-span-2">
                            <label for="manual_nasib_akhir" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-flag mr-2 text-red-500"></i>Nasib Akhir
                            </label>
                            <select name="manual_nasib_akhir" id="manual_nasib_akhir"
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4">
                                <option value="">Pilih Nasib Akhir...</option>
                                <option value="Musnah" {{ old('manual_nasib_akhir', $archive->manual_nasib_akhir) == 'Musnah' ? 'selected' : '' }}>Musnah</option>
                                <option value="Permanen" {{ old('manual_nasib_akhir', $archive->manual_nasib_akhir) == 'Permanen' ? 'selected' : '' }}>Permanen</option>
                                <option value="Dinilai Kembali" {{ old('manual_nasib_akhir', $archive->manual_nasib_akhir) == 'Dinilai Kembali' ? 'selected' : '' }}>Dinilai Kembali</option>
                            </select>
                            @error('manual_nasib_akhir')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Archive Details Section -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-cogs mr-2 text-green-500"></i>
                        Detail Arsip
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Tingkat Perkembangan (Manual Input) -->
                        <div>
                            <label for="tingkat_perkembangan" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-layer-group mr-2 text-yellow-500"></i>Tingkat Perkembangan
                            </label>
                            <input type="text" name="tingkat_perkembangan" id="tingkat_perkembangan"
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                value="{{ old('tingkat_perkembangan', $archive->tingkat_perkembangan) }}" required placeholder="Contoh: Asli, Salinan, Tembusan">
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
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4" required>
                                <option value="">Pilih SKKAD...</option>
                                <option value="SANGAT RAHASIA" {{ old('skkad', $archive->skkad) == 'SANGAT RAHASIA' ? 'selected' : '' }}>SANGAT RAHASIA</option>
                                <option value="TERBATAS" {{ old('skkad', $archive->skkad) == 'TERBATAS' ? 'selected' : '' }}>TERBATAS</option>
                                <option value="RAHASIA" {{ old('skkad', $archive->skkad) == 'RAHASIA' ? 'selected' : '' }}>RAHASIA</option>
                                <option value="BIASA/TERBUKA" {{ old('skkad', $archive->skkad) == 'BIASA/TERBUKA' ? 'selected' : '' }}>BIASA/TERBUKA</option>
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
                            <input type="number" name="jumlah_berkas" id="jumlah_berkas" min="1" step="1"
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                value="{{ old('jumlah_berkas', $archive->jumlah_berkas) }}" required placeholder="Masukkan jumlah berkas">
                            @error('jumlah_berkas')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <div class="mt-6">
                        <label for="ket" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-sticky-note mr-2 text-pink-500"></i>Keterangan (Opsional)
                        </label>
                        <textarea name="ket" id="ket" rows="3"
                            class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                            placeholder="Tambahkan keterangan tambahan jika diperlukan">{{ old('ket', $archive->ket) }}</textarea>
                        @error('ket')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Retention Information (Read-only display) -->
                <div id="retention_section">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-hourglass-half mr-2 text-amber-500"></i>
                        Informasi Retensi
                    </h3>
                    <div id="retention_info" class="bg-gray-50 p-4 rounded-xl">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div class="bg-green-100 p-3 rounded-lg">
                                <div class="font-medium text-green-800">Retensi Aktif</div>
                                <div class="text-green-600">{{ $archive->retention_aktif }} Tahun</div>
                            </div>
                            <div class="bg-yellow-100 p-3 rounded-lg">
                                <div class="font-medium text-yellow-800">Retensi Inaktif</div>
                                <div class="text-yellow-600">{{ $archive->retention_inaktif }} Tahun</div>
                            </div>
                            <div class="bg-purple-100 p-3 rounded-lg">
                                <div class="font-medium text-purple-800">Nasib Akhir</div>
                                <div class="text-purple-600">{{ $archive->classification->nasib_akhir ?? 'Manual' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                    <a href="{{ route('admin.archives.index') }}"
                        class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-6 py-3 border border-transparent rounded-xl text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Update Arsip
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            .select2-container--default .select2-selection--single {
                height: 48px;
                border: 1px solid #d1d5db;
                border-radius: 0.75rem;
                padding: 0 12px;
                font-size: 14px;
                display: flex;
                align-items: center;
            }
            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 48px;
                padding: 0;
            }
            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 46px;
                right: 12px;
            }
            .select2-dropdown {
                border: 1px solid #d1d5db;
                border-radius: 0.75rem;
                margin-top: 4px;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function() {
                // Debug: Check if jQuery and Select2 are loaded
                console.log('jQuery loaded:', typeof $ !== 'undefined');
                console.log('Select2 loaded:', typeof $.fn.select2 !== 'undefined');

                // Initialize Select2
                $('#category_id, #classification_id').select2({
                    theme: 'default',
                    width: '100%',
                    placeholder: 'Pilih...'
                });

                console.log('Select2 initialized for category and classification dropdowns');

                // Store all classifications and categories data
                const allClassifications = @json($classifications);
                const allCategories = @json($categories);

                // Find LAINNYA category
                const lainnyaCategory = allCategories.find(c => c.nama_kategori === 'LAINNYA');
                const lainnyaCategoryId = lainnyaCategory ? lainnyaCategory.id : null;

                function updateRetentionInfoFromClassification(classificationId) {
                    const retentionSection = $('#retention_section');
                    const retentionInfo = $('#retention_info');

                    if (!classificationId) {
                        return;
                    }

                    const classification = allClassifications.find(c => c.id == classificationId);
                    if (classification) {
                        const activeYears = classification.retention_aktif || 0;
                        const inactiveYears = classification.retention_inaktif || 0;
                        const nasibAkhir = classification.nasib_akhir || 'Tidak Ditentukan';

                        retentionInfo.html(`
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div class="bg-green-100 p-3 rounded-lg">
                                    <div class="font-medium text-green-800">Retensi Aktif</div>
                                    <div class="text-green-600">${activeYears} Tahun</div>
                                </div>
                                <div class="bg-yellow-100 p-3 rounded-lg">
                                    <div class="font-medium text-yellow-800">Retensi Inaktif</div>
                                    <div class="text-yellow-600">${inactiveYears} Tahun</div>
                                </div>
                                <div class="bg-purple-100 p-3 rounded-lg">
                                    <div class="font-medium text-purple-800">Nasib Akhir</div>
                                    <div class="text-purple-600">${nasibAkhir}</div>
                                </div>
                            </div>
                        `);
                    }
                }

                function toggleManualInput(isManual) {
                    const manualSection = $('#manual_input_section');
                    const isManualInput = $('#is_manual_input');
                    const retentionSection = $('#retention_section');

                    if (isManual) {
                        manualSection.removeClass('hidden');
                        isManualInput.val('1');

                        // Make manual fields required
                        $('#manual_retention_aktif, #manual_retention_inaktif, #manual_nasib_akhir').attr('required', true);

                        // Hide retention info section completely for LAINNYA
                        retentionSection.addClass('hidden');
                    } else {
                        manualSection.addClass('hidden');
                        isManualInput.val('0');

                        // Remove required from manual fields
                        $('#manual_retention_aktif, #manual_retention_inaktif, #manual_nasib_akhir').removeAttr('required');

                        // Show retention info section for JRA
                        retentionSection.removeClass('hidden');
                    }
                }

                function populateClassifications(categoryId, selectedClassificationId = null) {
                    const classificationSelect = $('#classification_id');
                    classificationSelect.empty().append('<option value="">Pilih Klasifikasi...</option>');

                    const filteredClassifications = categoryId ?
                        allClassifications.filter(c => c.category_id == categoryId) :
                        allClassifications;

                    filteredClassifications.forEach(function(classification) {
                        const isSelected = classification.id == selectedClassificationId;
                        classificationSelect.append(new Option(
                            `${classification.code} - ${classification.nama_klasifikasi}`,
                            classification.id, false, isSelected
                        ));
                    });
                    classificationSelect.trigger('change.select2');
                }

                // Event Handlers
                $('#category_id').on('change', function() {
                    const categoryId = $(this).val();

                    // Check if LAINNYA category
                    const isLainnya = categoryId == lainnyaCategoryId;
                    toggleManualInput(isLainnya);

                    // Reset classification
                    $('#classification_id').val('').trigger('change.select2');

                    populateClassifications(categoryId);
                });

                $('#classification_id').on('change', function() {
                    const classificationId = $(this).val();
                    updateRetentionInfoFromClassification(classificationId);

                    // Auto-set category if classification is selected first
                    if (classificationId) {
                        const selectedClassification = allClassifications.find(c => c.id == classificationId);
                        if (selectedClassification && $('#category_id').val() != selectedClassification.category_id) {
                            $('#category_id').val(selectedClassification.category_id).trigger('change.select2');
                        }
                    }
                });

                // Initial load logic
                const existingClassificationId = '{{ $archive->classification_id }}';
                const existingCategoryId = '{{ $archive->category_id }}';

                if (existingClassificationId) {
                    populateClassifications(existingCategoryId, existingClassificationId);
                    updateRetentionInfoFromClassification(existingClassificationId);
                } else if (existingCategoryId) {
                    populateClassifications(existingCategoryId);
                }

                // Handle manual input state from existing data
                if ('{{ $archive->is_manual_input }}' == '1') {
                    toggleManualInput(true);
                }
            });
        </script>
    @endpush
</x-app-layout>
