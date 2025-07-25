<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-600 to-pink-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-plus-circle text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Tambah Arsip Baru</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-archive mr-1"></i>Input data arsip digital ke dalam sistem
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('intern.archives.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('intern.archives.store') }}" method="POST" class="space-y-8" id="archiveForm">
                @csrf

                <!-- Classification Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-sitemap mr-2 text-orange-500"></i>
                        Klasifikasi Arsip
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Kategori -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-folder mr-2 text-blue-500"></i>Kategori
                            </label>
                            <select name="category_id" id="category_id"
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors py-3 px-4"
                                data-placeholder="Pilih Kategori..." required>
                                <option value="">Pilih Kategori...</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors py-3 px-4"
                                required>
                                <option value="">Pilih Klasifikasi...</option>
                                {{-- Options will be populated by JavaScript --}}
                            </select>
                            @error('classification_id')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Nomor Berkas -->
                        <div>
                            <label for="index_number" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-hashtag mr-2 text-green-500"></i>Nomor Berkas/Lampiran Arsip
                            </label>
                            <input type="text" name="index_number" id="index_number"
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors py-3 px-4"
                                value="{{ old('index_number') }}" required placeholder="Masukkan nomor berkas">
                            @error('index_number')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Retention Information Display -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-info-circle mr-2 text-purple-500"></i>Informasi Retensi
                            </label>
                            <div class="grid grid-cols-3 gap-2">
                                <input type="text" id="retention_active_info" readonly
                                    class="text-xs bg-gray-50 border border-gray-200 rounded px-2 py-1"
                                    placeholder="Aktif">
                                <input type="text" id="retention_inactive_info" readonly
                                    class="text-xs bg-gray-50 border border-gray-200 rounded px-2 py-1"
                                    placeholder="Inaktif">
                                <input type="text" id="nasib_akhir_info" readonly
                                    class="text-xs bg-gray-50 border border-gray-200 rounded px-2 py-1"
                                    placeholder="Nasib Akhir">
                            </div>
                        </div>
                    </div>

                    <!-- Uraian -->
                    <div class="mt-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-align-left mr-2 text-indigo-500"></i>Uraian Arsip
                        </label>
                        <textarea name="description" id="description" rows="4"
                            class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors py-3 px-4"
                            required placeholder="Masukkan uraian atau deskripsi arsip">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Archive Details Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
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
                            <select name="tingkat_perkembangan" id="tingkat_perkembangan"
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors py-3 px-4"
                                required>
                                <option value="Asli" {{ old('tingkat_perkembangan') == 'Asli' ? 'selected' : '' }}>
                                    Asli</option>
                                <option value="Salinan"
                                    {{ old('tingkat_perkembangan') == 'Salinan' ? 'selected' : '' }}>Salinan</option>
                                <option value="Tembusan"
                                    {{ old('tingkat_perkembangan') == 'Tembusan' ? 'selected' : '' }}>Tembusan</option>
                            </select>
                            @error('tingkat_perkembangan')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Jumlah Berkas -->
                        <div>
                            <label for="jumlah_berkas" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-sort-numeric-up mr-2 text-red-500"></i>Jumlah Berkas
                            </label>
                            <input type="number" name="jumlah_berkas" id="jumlah_berkas" min="1" step="1"
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors py-3 px-4"
                                value="{{ old('jumlah_berkas', 1) }}" required placeholder="Masukkan jumlah berkas">
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
                            class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors py-3 px-4"
                            placeholder="Tambahkan keterangan tambahan jika diperlukan">{{ old('ket') }}</textarea>
                    </div>
                </div>

                <!-- Period Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                        Tanggal Arsip
                    </h3>

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="kurun_waktu_start" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar mr-2 text-orange-500"></i>Tanggal Arsip
                            </label>
                            <input type="date" name="kurun_waktu_start" id="kurun_waktu_start"
                                value="{{ old('kurun_waktu_start', date('Y-m-d')) }}"
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors py-3 px-4"
                                required>
                            @error('kurun_waktu_start')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between">
                    <a href="{{ route('intern.archives.index') }}"
                       class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                    <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-orange-600 to-pink-600 hover:from-orange-700 hover:to-pink-700 text-white font-semibold rounded-lg transition-colors"
                            id="submitBtn">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Arsip
                    </button>
                </div>

                <!-- Hidden field untuk category_id -->
                <input type="hidden" name="hidden_category_id" id="hidden_category_id" value="">
            </form>
        </div>
    </div>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            .select2-container--default .select2-selection--single {
                height: 56px !important;
                border: 1px solid #d1d5db !important;
                border-radius: 0.75rem !important;
                background-color: white !important;
                padding: 0 16px !important;
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
                display: flex !important;
                align-items: center !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: normal !important;
                padding-left: 0;
                color: #374151;
                text-align: left;
                flex: 1;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 56px !important;
                right: 12px;
                display: flex;
                align-items: center;
            }

            .select2-container--default .select2-selection--single:focus {
                outline: none;
                border-color: #ea580c;
                box-shadow: 0 0 0 2px rgba(234, 88, 12, 0.5);
            }

            .select2-dropdown {
                border-radius: 0.75rem;
                border: 1px solid #d1d5db;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function() {
                // Initialize Select2
                $('#category_id, #classification_id').select2({
                    placeholder: function() {
                        return $(this).data('placeholder');
                    },
                    allowClear: true,
                    width: '100%'
                });

                const allClassifications = @json($classifications);
                const allCategories = @json($categories);

                function updateRetentionInfoFromClassification(classificationId) {
                    const classification = allClassifications.find(c => c.id == classificationId);
                    if (classification && classification.category) {
                        $('#retention_active_info').val(`${classification.retention_aktif} tahun`);
                        $('#retention_inactive_info').val(`${classification.retention_inaktif} tahun`);
                        $('#nasib_akhir_info').val(classification.nasib_akhir);
                        $('#hidden_category_id').val(classification.category.id);
                    } else {
                        $('#retention_active_info').val('');
                        $('#retention_inactive_info').val('');
                        $('#nasib_akhir_info').val('');
                        $('#hidden_category_id').val('');
                    }
                }

                function populateClassifications(categoryId, selectedClassificationId = null) {
                    const classificationSelect = $('#classification_id');
                    classificationSelect.empty();
                    classificationSelect.append('<option value="">Pilih Klasifikasi...</option>');

                    const filteredClassifications = categoryId ?
                        allClassifications.filter(c => c.category_id == categoryId) :
                        allClassifications;

                    filteredClassifications.forEach(function(classification) {
                        const isSelected = classification.id == selectedClassificationId;
                        classificationSelect.append(new Option(
                            `${classification.code} - ${classification.nama_klasifikasi}`,
                            classification.id, false,
                            isSelected));
                    });
                    classificationSelect.trigger('change.select2');
                }

                $('#category_id').on('change', function() {
                    const categoryId = $(this).val();
                    $('#hidden_category_id').val(categoryId);

                    const currentClassificationId = $('#classification_id').val();
                    const currentClassification = allClassifications.find(c => c.id == currentClassificationId);

                    if (currentClassification && currentClassification.category_id != categoryId) {
                        $('#classification_id').val('').trigger('change.select2');
                        updateRetentionInfoFromClassification(categoryId);
                    } else if (categoryId) {
                        updateRetentionInfoFromClassification(categoryId);
                    } else {
                        updateRetentionInfoFromClassification(null);
                    }

                    populateClassifications(categoryId, $('#classification_id').val());
                });

                $('#classification_id').on('change', function() {
                    const classificationId = $(this).val();
                    updateRetentionInfoFromClassification(classificationId);

                    if (classificationId) {
                        const selectedClassification = allClassifications.find(c => c.id == classificationId);
                        if (selectedClassification && $('#category_id').val() != selectedClassification
                            .category_id) {
                            $('#category_id').val(selectedClassification.category_id).trigger('change.select2');
                        }
                    }
                });

                // Form submission with SWAL validation
                $('#archiveForm').on('submit', function(e) {
                    e.preventDefault();

                    // Validate required fields
                    const requiredFields = ['category_id', 'classification_id', 'index_number', 'description', 'tingkat_perkembangan', 'jumlah_berkas', 'kurun_waktu_start'];
                    if (!window.validateRequiredFields(requiredFields)) {
                        return false;
                    }

                    // Show create confirmation
                    window.showCreateConfirm('Apakah Anda yakin ingin menyimpan arsip ini?').then((result) => {
                        if (result.isConfirmed) {
                            // Show loading
                            const submitBtn = $('#submitBtn');
                            const originalText = submitBtn.html();
                            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...');

                            // Submit form
                            this.submit();
                        }
                    });
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
                    } else {
                        if (oldCategoryId) {
                            populateClassifications(oldCategoryId);
                            $('#category_id').val(oldCategoryId).trigger('change.select2');
                            updateRetentionInfoFromClassification(oldCategoryId);
                        } else {
                            populateClassifications(null);
                            updateRetentionInfoFromClassification(null);
                        }
                    }
                } else if (oldCategoryId) {
                    populateClassifications(oldCategoryId);
                    $('#category_id').val(oldCategoryId).trigger('change.select2');
                    updateRetentionInfoFromClassification(oldCategoryId);
                } else {
                    populateClassifications(null);
                    updateRetentionInfoFromClassification(null);
                }

                if (oldCategoryId !== '') {
                    $('#hidden_category_id').val(oldCategoryId);
                }
            });
        </script>
    @endpush
</x-app-layout>
