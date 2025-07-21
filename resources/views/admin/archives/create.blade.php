<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Create New Archive
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Display validation errors if any --}}
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.archives.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Kolom Kiri -->
                            <div>
                                <input type="hidden" name="category_id" id="hidden_category_id" value="{{ old('category_id') }}">
                                <div class="mb-4">
                                    <label for="category_id" class="block text-sm font-medium text-gray-700">Kategori</label>
                                    <select name="category_id_select" id="category_id" class="mt-1 block w-full" >
                                        <option value="">Pilih Kategori...</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </div>

                                <div class="mb-4">
                                    <label for="classification_id" class="block text-sm font-medium text-gray-700">Klasifikasi</label>
                                    <select name="classification_id" id="classification_id" class="mt-1 block w-full" required>
                                        <option value="">Pilih Klasifikasi...</option>
                                        {{-- Options will be populated by JavaScript --}}
                                    </select>
                                    @error('classification_id')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </div>
                                
                                <div class="mb-4">
                                    <label for="index_number" class="block text-sm font-medium text-gray-700">Nomor Berkas/Lampiran Arsip</label>
                                    <input type="text" name="index_number" id="index_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('index_number') }}" required>
                                    @error('index_number')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </div>

                                <div class="mb-4">
                                    <label for="uraian" class="block text-sm font-medium text-gray-700">Uraian Arsip</label>
                                    {{-- Reverted name from 'description' to 'uraian' --}}
                                    <textarea name="uraian" id="uraian" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>{{ old('uraian') }}</textarea>
                                    @error('uraian')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <!-- Kolom Kanan -->
                            <div>
                                <div class="mb-4">
                                    <label for="kurun_waktu_start" class="block text-sm font-medium text-gray-700">Tanggal Arsip</label>
                                    <input type="date" name="kurun_waktu_start" id="kurun_waktu_start" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('kurun_waktu_start', date('Y-m-d')) }}" required>
                                    @error('kurun_waktu_start')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </div>
                                
                                <div class="mb-4">
                                    <label for="tingkat_perkembangan" class="block text-sm font-medium text-gray-700">Tingkat Perkembangan</label>
                                    <select name="tingkat_perkembangan" id="tingkat_perkembangan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                        <option value="Asli" {{ old('tingkat_perkembangan') == 'Asli' ? 'selected' : '' }}>Asli</option>
                                        <option value="Salinan" {{ old('tingkat_perkembangan') == 'Salinan' ? 'selected' : '' }}>Salinan</option>
                                        <option value="Tembusan" {{ old('tingkat_perkembangan') == 'Tembusan' ? 'selected' : '' }}>Tembusan</option>
                                    </select>
                                    @error('tingkat_perkembangan')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </div>

                                <div class="mb-4">
                                    <label for="jumlah" class="block text-sm font-medium text-gray-700">Jumlah Berkas</label>
                                    <input type="number" name="jumlah" id="jumlah" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('jumlah', 1) }}" required>
                                    @error('jumlah')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </div>

                                <div class="mb-4">
                                    <label for="ket" class="block text-sm font-medium text-gray-700">Keterangan</label>
                                    <textarea name="ket" id="ket" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('ket') }}</textarea>
                                </div>
                                
                                <div class="border-t pt-4 mt-4">
                                    <p class="text-sm font-medium text-gray-700">Informasi Retensi (Otomatis)</p>
                                    <div class="mb-2">
                                        <label for="retention_active_info" class="block text-xs font-medium text-gray-600">Retensi Aktif</label>
                                        <input type="text" id="retention_active_info" class="mt-1 block w-full bg-gray-100 rounded-md border-gray-300 shadow-sm" readonly>
                                    </div>
                                    <div class="mb-2">
                                        <label for="retention_inactive_info" class="block text-xs font-medium text-gray-600">Retensi Inaktif</label>
                                        <input type="text" id="retention_inactive_info" class="mt-1 block w-full bg-gray-100 rounded-md border-gray-300 shadow-sm" readonly>
                                    </div>
                                    <div class="mb-2">
                                        <label for="nasib_akhir_info" class="block text-xs font-medium text-gray-600">Nasib Akhir</label>
                                        <input type="text" id="nasib_akhir_info" class="mt-1 block w-full bg-gray-100 rounded-md border-gray-300 shadow-sm" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Simpan Arsip</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#category_id, #classification_id').select2({ theme: "classic" });

            const allClassifications = @json($classifications);
            const allCategories = @json($categories);

            function updateRetentionInfoFromCategory(categoryId) {
                const category = allCategories.find(c => c.id == categoryId);
                if (category) {
                    $('#retention_active_info').val(`${category.retention_active} thn`);
                    $('#retention_inactive_info').val(`${category.retention_inactive} thn`);
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
                    $('#retention_active_info').val(`${classification.category.retention_active} thn`);
                    $('#retention_inactive_info').val(`${classification.category.retention_inactive} thn`);
                    $('#nasib_akhir_info').val(classification.category.nasib_akhir);
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

                const filteredClassifications = categoryId
                    ? allClassifications.filter(c => c.category_id == categoryId)
                    : allClassifications;
                
                filteredClassifications.forEach(function(classification) {
                    const isSelected = classification.id == selectedClassificationId;
                    classificationSelect.append(new Option(`${classification.name} (${classification.code})`, classification.id, false, isSelected));
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
                    if (selectedClassification && $('#category_id').val() != selectedClassification.category_id) {
                        $('#category_id').val(selectedClassification.category_id).trigger('change.select2');
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
                } else {
                    if (oldCategoryId) {
                        populateClassifications(oldCategoryId);
                        $('#category_id').val(oldCategoryId).trigger('change.select2');
                        updateRetentionInfoFromCategory(oldCategoryId);
                    } else {
                        populateClassifications(null);
                        updateRetentionInfoFromCategory(null);
                    }
                }
            } else if (oldCategoryId) {
                populateClassifications(oldCategoryId);
                $('#category_id').val(oldCategoryId).trigger('change.select2');
                updateRetentionInfoFromCategory(oldCategoryId);
            } else {
                populateClassifications(null);
                updateRetentionInfoFromCategory(null);
            }

            if (oldCategoryId !== '') {
                $('#hidden_category_id').val(oldCategoryId);
            }
        });
    </script>
    @endpush
</x-app-layout>