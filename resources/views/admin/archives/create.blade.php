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
                    <form action="{{ route('admin.archives.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Kolom Kiri -->
                            <div>
                                <div class="mb-4">
                                    <label for="category_id" class="block text-sm font-medium text-gray-700">Kategori</label>
                                    <select name="category_id" id="category_id" class="mt-1 block w-full" required>
                                        <option value="">Pilih Kategori...</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="classification_id" class="block text-sm font-medium text-gray-700">Klasifikasi</label>
                                    <select name="classification_id" id="classification_id" class="mt-1 block w-full" required>
                                        <option value="">Pilih Kategori terlebih dahulu...</option>
                                    </select>
                                    @error('classification_id')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Lampiran Arsip</label>
                                    <input type="text" class="mt-1 block w-full bg-gray-100 rounded-md border-gray-300 shadow-sm" value="(Akan digenerate otomatis)" readonly>
                                </div>

                                <div class="mb-4">
                                    <label for="uraian" class="block text-sm font-medium text-gray-700">Uraian Arsip</label>
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
                                    <input type="text" id="retention_info" class="mt-1 block w-full bg-gray-100 rounded-md border-gray-300 shadow-sm" readonly>
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

            $('#category_id').on('change', function() {
                const categoryId = $(this).val();
                const classificationSelect = $('#classification_id');
                classificationSelect.empty().append('<option value="">Memuat...</option>').trigger('change');
                $('#retention_info').val('');

                if (categoryId) {
                    $.ajax({
                        url: `{{ route('api.classifications.by_category') }}?category_id=${categoryId}`,
                        success: function(data) {
                            classificationSelect.empty().append('<option value="">Pilih Klasifikasi...</option>');
                            data.forEach(function(classification) {
                                classificationSelect.append(new Option(classification.name, classification.id, false, false));
                            });
                            classificationSelect.trigger('change');
                        }
                    });
                } else {
                    classificationSelect.empty().append('<option value="">Pilih Kategori terlebih dahulu...</option>').trigger('change');
                }
            });
            
            $('#classification_id').on('change', function() {
                const classificationId = $(this).val();
                updateRetentionInfo(classificationId);

                // Auto-select category if classification is chosen first
                if (classificationId) {
                    $.ajax({
                        url: `{{ url('/api/classifications') }}/${classificationId}`,
                        success: function(data) {
                            if ($('#category_id').val() != data.category_id) {
                                $('#category_id').val(data.category_id).trigger('change.select2'); // use .select2 to avoid loop
                            }
                        }
                    });
                }
            });

            // Re-populate classification on load if old category exists
            if ($('#category_id').val()) {
                $('#category_id').trigger('change');
            }
        });
    </script>
    @endpush
</x-app-layout> 