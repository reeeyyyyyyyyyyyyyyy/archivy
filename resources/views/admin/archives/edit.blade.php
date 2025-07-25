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
                            <i class="fas fa-pencil-alt mr-1"></i>Ubah informasi dan data arsip digital
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.archives.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="py-6">
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

                <!-- Basic Information Section -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                        Informasi Dasar
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Kategori -->
                        <div>
                            <input type="hidden" name="category_id" id="hidden_category_id"
                                value="{{ old('category_id', $archive->category_id) }}">
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-folder mr-2 text-indigo-500"></i>Kategori
                            </label>
                            <select name="category_id_select" id="category_id"
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4">
                                <option value="">Pilih Kategori...</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id', $archive->category_id) == $category->id ? 'selected' : '' }}>
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
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
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
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                value="{{ old('index_number', $archive->index_number) }}" required
                                placeholder="Masukkan nomor berkas">
                            @error('index_number')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Jumlah Berkas -->
                        <div>
                            <label for="jumlah_berkas" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-copy mr-2 text-yellow-500"></i>Jumlah Berkas
                            </label>
                            <input type="number" name="jumlah_berkas" id="jumlah_berkas" min="1" step="1"
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                value="{{ old('jumlah_berkas', $archive->jumlah_berkas) }}" required placeholder="Masukkan jumlah berkas">
                            @error('jumlah_berkas')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Tanggal Arsip -->
                        <div>
                            <label for="kurun_waktu_start" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-alt mr-2 text-orange-500"></i>Tanggal Arsip
                            </label>
                            <input type="date" name="kurun_waktu_start" id="kurun_waktu_start"
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                value="{{ old('kurun_waktu_start', $archive->kurun_waktu_start->format('Y-m-d')) }}"
                                required>
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
                            class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                            required placeholder="Masukkan uraian atau deskripsi arsip">{{ old('description', $archive->description) }}</textarea>
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
                            <select name="tingkat_perkembangan" id="tingkat_perkembangan"
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                required>
                                <option value="Asli"
                                    {{ old('tingkat_perkembangan', $archive->tingkat_perkembangan) == 'Asli' ? 'selected' : '' }}>
                                    Asli</option>
                                <option value="Salinan"
                                    {{ old('tingkat_perkembangan', $archive->tingkat_perkembangan) == 'Salinan' ? 'selected' : '' }}>
                                    Salinan</option>
                                <option value="Tembusan"
                                    {{ old('tingkat_perkembangan', $archive->tingkat_perkembangan) == 'Tembusan' ? 'selected' : '' }}>
                                    Tembusan</option>
                            </select>
                            @error('tingkat_perkembangan')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Jumlah Berkas -->
                        {{-- <div>
                            <label for="jumlah" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-sort-numeric-up mr-2 text-red-500"></i>Jumlah Berkas
                            </label>
                            {{-- <input type="number" name="jumlah" id="jumlah"
                                class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                                value="{{ old('jumlah', $archive->jumlah) }}" required min="1"
                                placeholder="Jumlah berkas">
                            @error('jumlah')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror --}}
                        {{-- </div> --}}
                    </div>

                    <!-- Keterangan -->
                    <div class="mt-6">
                        <label for="ket" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-sticky-note mr-2 text-pink-500"></i>Keterangan (Opsional)
                        </label>
                        <textarea name="ket" id="ket" rows="3"
                            class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4"
                            placeholder="Tambahkan keterangan tambahan jika diperlukan">{{ old('ket', $archive->ket) }}</textarea>
                    </div>
                </div>

                <!-- Retention Information Section -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-clock mr-2 text-blue-500"></i>
                        Informasi Retensi (Otomatis)
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="retention_active_info"
                                class="block text-sm font-medium text-gray-600 mb-2">Retensi Aktif</label>
                            <input type="text" id="retention_active_info"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-600"
                                readonly>
                        </div>
                        <div>
                            <label for="retention_inactive_info"
                                class="block text-sm font-medium text-gray-600 mb-2">Retensi Inaktif</label>
                            <input type="text" id="retention_inactive_info"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-600"
                                readonly>
                        </div>
                        <div>
                            <label for="nasib_akhir_info" class="block text-sm font-medium text-gray-600 mb-2">Nasib
                                Akhir</label>
                            <input type="text" id="nasib_akhir_info"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-600"
                                readonly>
                        </div>
                    </div>

                    <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-xl">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                            <p class="text-sm text-blue-700">
                                <strong>Informasi:</strong> Data retensi akan otomatis terisi berdasarkan kategori dan
                                klasifikasi yang dipilih sesuai dengan JRA Pergub 1 & 30.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-6 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <button type="submit"
                                class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-colors shadow-sm">
                                <i class="fas fa-save mr-2"></i>
                                Update Arsip
                            </button>
                            <a href="{{ route('admin.archives.index') }}"
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

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            .select2-container--default .select2-selection--single {
                height: 56px !important;
                border: 1px solid #d1d5db;
                border-radius: 0.75rem;
                padding: 0 1rem;
                /* hilangkan padding vertikal */
                display: flex;
                align-items: center;
                /* vertikal tengah */
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: normal !important;
                padding-left: 0;
                color: #374151;
                text-align: left;
                flex: 1;
                /* biar teks memenuhi sisa ruang */
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 56px !important;
                right: 12px;
                display: flex;
                align-items: center;
                /* agar panah juga vertikal tengah */
            }

            .select2-container--default .select2-selection--single:focus {
                outline: none;
                border-color: #3b82f6;
                box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
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

                function updateRetentionInfoFromCategory(categoryId) {
                    const category = allCategories.find(c => c.id == categoryId);
                    if (category) {
                        $('#retention_active_info').val(`${category.retention_aktif} tahun`);
                        $('#retention_inactive_info').val(`${category.retention_inaktif} tahun`);
                        $('#nasib_akhir_info').val(category.nasib_akhir);
                    } else {
                        $('#retention_active_info').val('');
                        $('#retention_inactive_info').val('');
                        $('#nasib_akhir_info').val('');
                    }
                }

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
                            `${classification.code} - ${classification.nama_klasifikasi}`, classification.id, false,
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
                        updateRetentionInfoFromCategory(categoryId);
                    } else if (categoryId) {
                        updateRetentionInfoFromCategory(categoryId);
                    } else {
                        updateRetentionInfoFromCategory(null);
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

                // Initial load with existing data
                const currentCategoryId = '{{ old('category_id', $archive->category_id) }}';
                const currentClassificationId = '{{ old('classification_id', $archive->classification_id) }}';

                if (currentCategoryId) {
                    populateClassifications(currentCategoryId, currentClassificationId);
                    updateRetentionInfoFromCategory(currentCategoryId);
                }

                if (currentClassificationId) {
                    updateRetentionInfoFromClassification(currentClassificationId);
                }
            });
        </script>
    @endpush
</x-app-layout>
