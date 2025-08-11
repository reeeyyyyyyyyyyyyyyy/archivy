<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-plus text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Input Arsip Baru</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-folder-plus mr-1"></i>Tambahkan arsip baru ke dalam sistem ARSIPIN
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.archives.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Arsip
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

            <form action="{{ route('admin.archives.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Information Notice -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400 text-lg"></i>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-blue-800">Informasi Penting</h4>
                            <div class="mt-2 text-sm text-blue-700">
                                <p><strong>Kategori JRA:</strong> Sistem otomatis untuk retensi
                                    aktif/inaktif, dan nasib akhir</p>
                                <p><strong>Kategori LAINNYA:</strong> Input manual untuk semua field, perhitungan
                                    retensi
                                    tetap otomatis</p>
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
                                class="select2-dropdown w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                required>
                                <option value="">Pilih Kategori...</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                class="select2-dropdown w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                required>
                                <option value="">Pilih Klasifikasi...</option>
                            </select>
                            @error('classification_id')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Manual Input Indicator -->
                        <input type="hidden" name="is_manual_input" id="is_manual_input"
                            value="{{ old('is_manual_input', '0') }}">

                        <!-- Nomor Arsip -->
                        <div id="index_number_container">
                            <label for="index_number" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-hashtag mr-2 text-green-500"></i>Nomor Arsip
                            </label>
                            <input type="text" name="index_number" id="index_number"
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                value="{{ old('index_number') }}" required
                                placeholder="Masukkan nomor arsip sesuai format">
                            <div id="index_number_example" class="mt-1 text-xs text-gray-500">
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

                    <!-- Lampiran Surat -->
                    <div class="mt-6">
                        <label for="lampiran_surat" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-paperclip mr-2 text-teal-500"></i>Lampiran Surat
                        </label>
                        <textarea name="lampiran_surat" id="lampiran_surat" rows="3"
                            class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                            placeholder="Deskripsi lampiran arsip">{{ old('lampiran_surat') }}</textarea>
                        @error('lampiran_surat')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
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

                <!-- Manual Input Fields (Hidden by default, shown for LAINNYA) -->
                <div id="manual_input_section" class="border-b border-gray-200 pb-6 hidden">
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
                                    <p><strong>Isilah Secara Manual:</strong> Retensi aktif/inaktif, nasib akhir, nomor
                                        arsip lengkap</p>
                                    {{-- <p><strong>Yang Otomatis:</strong> SKKAD (dropdown), perhitungan tanggal retensi</p> --}}
                                    <p><strong>Catatan:</strong> Gunakan kategori & klasifikasi "LAINNYA" untuk arsip di
                                        luar JRA</p>
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
                            <input type="number" name="manual_retention_aktif" id="manual_retention_aktif"
                                min="0"
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                value="{{ old('manual_retention_aktif') }}" placeholder="0">
                            @error('manual_retention_aktif')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Manual Inactive Retention -->
                        <div>
                            <label for="manual_retention_inaktif"
                                class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-pause-circle mr-2 text-yellow-500"></i>Retensi Inaktif (Tahun)
                            </label>
                            <input type="number" name="manual_retention_inaktif" id="manual_retention_inaktif"
                                min="0"
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                value="{{ old('manual_retention_inaktif') }}" placeholder="0">
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
                                <option value="Musnah" {{ old('manual_nasib_akhir') == 'Musnah' ? 'selected' : '' }}>
                                    Musnah</option>
                                <option value="Permanen"
                                    {{ old('manual_nasib_akhir') == 'Permanen' ? 'selected' : '' }}>Permanen</option>
                                <option value="Dinilai Kembali"
                                    {{ old('manual_nasib_akhir') == 'Dinilai Kembali' ? 'selected' : '' }}>Dinilai
                                    Kembali</option>
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
                    </div>


                </div>

                <!-- Retention Information (Read-only display) -->
                <div id="retention_section">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-hourglass-half mr-2 text-amber-500"></i>
                        Informasi Retensi
                    </h3>
                    <div id="retention_info" class="bg-gray-50 p-4 rounded-xl">
                        <p class="text-sm text-gray-600">Pilih klasifikasi untuk melihat informasi retensi</p>
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
                        class="inline-flex items-center px-6 py-3 border border-transparent rounded-xl text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Arsip
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
                    placeholder: 'Pilih...',
                    allowClear: true,
                    // dropdownParent: $('#category_id, #classification_id')
                });

                console.log('Select2 initialized for category and classification dropdowns');

                // Store all classifications and categories data
                const allClassifications = @json($classifications);
                const allCategories = @json($categories);

                // Find LAINNYA category
                const lainnyaCategory = allCategories.find(c => c.nama_kategori === 'LAINNYA');
                const lainnyaCategoryId = lainnyaCategory ? lainnyaCategory.id : null;

                // Auto-numbering counter (you might want to get this from server)
                let nextAutoNumber = 1;

                function updateRetentionInfoFromClassification(classificationId) {
                    const retentionSection = $('#retention_section');
                    const retentionInfo = $('#retention_info');

                    if (!classificationId) {
                        retentionInfo.html(
                            '<p class="text-sm text-gray-600">Pilih klasifikasi untuk melihat informasi retensi</p>'
                        );
                        return;
                    }

                    const classification = allClassifications.find(c => c.id == classificationId);
                    if (classification) {
                        const category = allCategories.find(c => c.id == classification.category_id);
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
                    const indexNumberInput = $('#index_number');
                    const exampleDiv = $('#index_number_example');
                    const retentionSection = $('#retention_section');

                    if (isManual) {
                        // KATEGORI LAINNYA - Manual Input Mode
                        manualSection.removeClass('hidden');
                        isManualInput.val('1');
                        indexNumberInput.prop('readonly', false);
                        indexNumberInput.attr('placeholder', 'Masukkan nomor arsip manual lengkap');
                        exampleDiv.html(`
                            <strong>Manual Input (LAINNYA):</strong> Isi nomor arsip lengkap manual<br>
                            <small class="text-orange-600">Contoh: DOK/001/SKPD/2024 | SURAT/005/BKPSDM/2024</small><br>
                        `);

                        // Make manual fields required
                        $('#manual_retention_aktif, #manual_retention_inaktif, #manual_nasib_akhir').attr('required',
                            true);

                        // Hide retention info section completely for LAINNYA
                        retentionSection.addClass('hidden');
                    } else {
                        // KATEGORI JRA - Semi-Automatic Mode
                        manualSection.addClass('hidden');
                        isManualInput.val('0');

                        // Remove required from manual fields
                        $('#manual_retention_aktif, #manual_retention_inaktif, #manual_nasib_akhir').removeAttr(
                            'required');

                        // Show retention info section for JRA
                        retentionSection.removeClass('hidden');

                        // Update example based on classification
                        const classificationId = $('#classification_id').val();
                        if (classificationId) {
                            updateNumberingExample(classificationId);
                            updateRetentionInfoFromClassification(classificationId);
                        } else {
                            indexNumberInput.prop('readonly', false);
                            indexNumberInput.attr('placeholder', 'Contoh: XXX/XXX/XXX/XXX');
                            exampleDiv.html(`
                                <strong>Format JRA:</strong> Masukkan NOMOR_URUT/KODE_KOMPONEN (contoh: 001/SKPD)<br>
                                <small class="text-blue-600">Sistem akan auto-generate: KODE_KLASIFIKASI/001/SKPD/2024</small>
                            `);
                        }
                    }
                }

                function updateNumberingExample(classificationId) {
                    const exampleDiv = $('#index_number_example');
                    const indexNumberInput = $('#index_number');

                    if (!classificationId) {
                        exampleDiv.html(`
                            <strong>Format JRA:</strong> Masukkan NOMOR_URUT/KODE_KOMPONEN (contoh: 001/SKPD)<br>
                            <small class="text-blue-600">Sistem akan auto-generate: KODE_KLASIFIKASI/001/SKPD/2024</small>
                        `);
                        indexNumberInput.attr('placeholder', 'Contoh: 001/SKPD');
                        return;
                    }

                    const classification = allClassifications.find(c => c.id == classificationId);
                    if (classification) {
                        const currentYear = new Date().getFullYear();
                        const kodeKlasifikasi = classification.code;

                        exampleDiv.html(`
                            <strong>Format JRA:</strong> Masukkan NOMOR_URUT/KODE_KOMPONEN (contoh: 001/SKPD)<br>
                            <small class="text-blue-600">Sistem akan auto-generate: <strong>${kodeKlasifikasi}</strong>/001/SKPD/${currentYear}</small><br>
                            <small class="text-green-600">✓ User input: NOMOR_URUT/KODE_KOMPONEN | ✓ Auto: Kode Klasifikasi & Tahun</small>
                        `);
                        indexNumberInput.attr('placeholder', 'Contoh: 001/SKPD');
                        indexNumberInput.prop('readonly', false); // Allow user to input NOMOR_URUT/KODE_KOMPONEN
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
                    updateRetentionInfoFromClassification(null);

                    populateClassifications(categoryId);
                });

                $('#classification_id').on('change', function() {
                    const classificationId = $(this).val();

                    if (classificationId) {
                        const selectedClassification = allClassifications.find(c => c.id == classificationId);

                        // Auto-set category if classification is selected first
                        if (selectedClassification && $('#category_id').val() != selectedClassification
                            .category_id) {
                            $('#category_id').val(selectedClassification.category_id).trigger('change.select2');
                        }

                        // Check if this is LAINNYA classification and auto-set manual mode
                        if (selectedClassification && selectedClassification.code === 'LAINNYA') {
                            toggleManualInput(true);
                        } else {
                            // Only update retention and numbering if NOT in manual mode
                            if ($('#is_manual_input').val() !== '1') {
                                updateRetentionInfoFromClassification(classificationId);
                                updateNumberingExample(classificationId);
                            }
                        }
                    }
                });

                // Initial load logic
                const oldClassificationId = '{{ old('classification_id') }}';
                const oldCategoryId = '{{ old('category_id') }}';

                if (oldClassificationId) {
                    const selectedClassification = allClassifications.find(c => c.id == oldClassificationId);
                    if (selectedClassification) {
                        $('#category_id').val(selectedClassification.category_id).trigger('change.select2');
                        populateClassifications(selectedClassification.category_id, oldClassificationId);
                        updateRetentionInfoFromClassification(oldClassificationId);
                    }
                } else if (oldCategoryId) {
                    $('#category_id').val(oldCategoryId).trigger('change.select2');
                    populateClassifications(oldCategoryId);
                } else {
                    populateClassifications(null);
                }

                // Handle manual input state from old values
                if ('{{ old('is_manual_input') }}' === '1') {
                    toggleManualInput(true);
                }

                // Handle form submission with timeout for SweetAlert
                $('form').on('submit', function() {
                    // Show loading state
                    const submitBtn = $(this).find('button[type="submit"]');
                    const originalText = submitBtn.html();
                    submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...');
                    submitBtn.prop('disabled', true);

                    // Set timeout for SweetAlert to prevent overlap
                    setTimeout(function() {
                    // This will be handled by the controller response
                    }, 500);
                });
            });
            // Duplicate warning modal
            @if (session('duplicate_warning'))
                $(document).ready(function() {
                    Swal.fire({
                        title: 'Arsip Serupa Ditemukan!',
                        html: `
                            <div class="text-left">
                                <p class="mb-3">Arsip dengan kategori/klasifikasi/lampiran yang sama sudah ada:</p>
                                <div class="bg-yellow-50 p-3 rounded-lg mb-3">
                                    <p class="font-semibold text-gray-800">{{ session('duplicate_archive_description') }}</p>
                                    <p class="text-sm text-gray-600">Tahun: {{ session('duplicate_archive_year') }}</p>
                                </div>
                                <p class="text-sm text-gray-600 mb-3">Apakah Anda ingin:</p>
                                <ul class="text-sm text-gray-600 list-disc list-inside mb-3">
                                    <li>Membatalkan dan kembali ke form</li>
                                    <li>Atau menambahkan sebagai arsip terkait?</li>
                                </ul>
                            </div>
                        `,
                        icon: 'warning',
                        showCancelButton: true,
                        // showDenyButton: true,
                        confirmButtonText: 'Masukkan ke Terkait',
                        // denyButtonText: 'Batal',
                        cancelButtonText: 'Tutup',
                        confirmButtonColor: '#10b981',
                        // denyButtonColor: '#6b7280',
                        cancelButtonColor: '#dc2626',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Redirect to create related archive
                            window.location.href =
                                '{{ route('admin.archives.create-related', session('duplicate_archive_id')) }}';
                        } else {
                            // Stay on current form (do nothing)
                        }
                    });
                });
            @endif
        </script>
    @endpush
</x-app-layout>
