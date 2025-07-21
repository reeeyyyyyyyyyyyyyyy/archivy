<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Archive: {{ $archive->index_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.archives.update', $archive) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="category_id" id="hidden_category_id" value="{{ old('category_id', $archive->category_id) }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Kolom Kiri -->
                            <div>
                                <div class="mb-4">
                                    <label for="category_id" class="block text-sm font-medium text-gray-700">Kategori</label>
                                    <select id="category_id" class="mt-1 block w-full" required>
                                        <option value="">Pilih Kategori...</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $archive->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="classification_id" class="block text-sm font-medium text-gray-700">Klasifikasi</label>
                                    <select name="classification_id" id="classification_id" class="mt-1 block w-full" required>
                                        {{-- Populated by JS --}}
                                    </select>
                                    @error('classification_id')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Lampiran Arsip</label>
                                    <input type="text" class="mt-1 block w-full bg-gray-100 rounded-md border-gray-300 shadow-sm" value="{{ $archive->index_number }}" readonly>
                                </div>

                                <div class="mb-4">
                                    <label for="uraian" class="block text-sm font-medium text-gray-700">Uraian Arsip</label>
                                    <textarea name="uraian" id="uraian" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>{{ old('uraian', $archive->uraian) }}</textarea>
                                    @error('uraian')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <!-- Kolom Kanan -->
                            <div>
                                <div class="mb-4">
                                    <label for="kurun_waktu_start" class="block text-sm font-medium text-gray-700">Tanggal Arsip</label>
                                    <input type="date" name="kurun_waktu_start" id="kurun_waktu_start" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('kurun_waktu_start', $archive->kurun_waktu_start->format('Y-m-d')) }}" required>
                                    @error('kurun_waktu_start')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </div>
                                
                                <div class="mb-4">
                                    <label for="tingkat_perkembangan" class="block text-sm font-medium text-gray-700">Tingkat Perkembangan</label>
                                    <select name="tingkat_perkembangan" id="tingkat_perkembangan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                        <option value="Asli" {{ old('tingkat_perkembangan', $archive->tingkat_perkembangan) == 'Asli' ? 'selected' : '' }}>Asli</option>
                                        <option value="Salinan" {{ old('tingkat_perkembangan', $archive->tingkat_perkembangan) == 'Salinan' ? 'selected' : '' }}>Salinan</option>
                                        <option value="Tembusan" {{ old('tingkat_perkembangan', $archive->tingkat_perkembangan) == 'Tembusan' ? 'selected' : '' }}>Tembusan</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="jumlah" class="block text-sm font-medium text-gray-700">Jumlah Berkas</label>
                                    <input type="number" name="jumlah" id="jumlah" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('jumlah', $archive->jumlah) }}" required>
                                    @error('jumlah')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </div>

                                <div class="mb-4">
                                    <label for="ket" class="block text-sm font-medium text-gray-700">Keterangan</label>
                                    <textarea name="ket" id="ket" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('ket', $archive->ket) }}</textarea>
                                </div>
                                
                                <div class="border-t pt-4 mt-4">
                                    <p class="text-sm font-medium text-gray-700">Informasi Retensi (Otomatis)</p>
                                    <input type="text" id="retention_info" class="mt-1 block w-full bg-gray-100 rounded-md border-gray-300 shadow-sm" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Update Arsip</button>
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

            const initialCategoryId = '{{ old('category_id', $archive->category_id) }}';
            const initialClassificationId = '{{ old('classification_id', $archive->classification_id) }}';

            function updateRetentionInfo(classificationId) {
                if (!classificationId) {
                    $('#retention_info').val('');
                    return;
                }
                $.ajax({
                    url: `{{ url('/api/classifications') }}/${classificationId}`,
                    success: function(data) {
                        const info = `Aktif: ${data.category.retention_active} thn, Inaktif: ${data.category.retention_inactive} thn, Nasib: ${data.category.nasib_akhir}`;
                        $('#retention_info').val(info);
                    }
                });
            }

            function populateClassifications(categoryId, selectedId = null) {
                const classificationSelect = $('#classification_id');
                 if (!categoryId) {
                    classificationSelect.empty().append('<option value="">Pilih Kategori terlebih dahulu...</option>').trigger('change');
                    return;
                }

                $.ajax({
                    url: `{{ route('api.classifications.by_category') }}?category_id=${categoryId}`,
                    success: function(data) {
                        classificationSelect.empty().append('<option value="">Pilih Klasifikasi...</option>');
                        data.forEach(function(classification) {
                            const isSelected = classification.id == selectedId;
                            classificationSelect.append(new Option(classification.name, classification.id, isSelected, isSelected));
                        });
                        classificationSelect.trigger('change');
                    }
                });
            }
            
            $('#category_id').on('change', function() {
                const categoryId = $(this).val();
                $('#hidden_category_id').val(categoryId);
                populateClassifications(categoryId);
                $('#retention_info').val('');
            });
            
            $('#classification_id').on('change', function() {
                updateRetentionInfo($(this).val());
            });

            // Initial load
            if (initialCategoryId) {
                populateClassifications(initialCategoryId, initialClassificationId);
                updateRetentionInfo(initialClassificationId);
            }
        });
    </script>
    @endpush
</x-app-layout> 