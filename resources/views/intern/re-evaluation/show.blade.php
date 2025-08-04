<x-app-layout>
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clipboard-check text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Detail Arsip Dinilai Kembali</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Lihat detail arsip yang memerlukan penilaian ulang
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('intern.re-evaluation.index') }}"
                        class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Archive Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Informasi Arsip</h3>
                        <span
                            class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                            @if ($archive->status === 'Aktif') bg-green-100 text-green-800
                            @elseif($archive->status === 'Inaktif') bg-yellow-100 text-yellow-800
                            @elseif($archive->status === 'Permanen') bg-blue-100 text-blue-800
                            @elseif($archive->status === 'Musnah') bg-red-100 text-red-800
                            @elseif($archive->status === 'Dinilai Kembali') bg-indigo-100 text-indigo-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $archive->status }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Information -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Arsip</label>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    {{ $archive->index_number ?? 'N/A' }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Uraian</label>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    {{ $archive->description }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    {{ $archive->category->nama_kategori ?? 'N/A' }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Klasifikasi</label>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    {{ $archive->classification->code ?? 'N/A' }} -
                                    {{ $archive->classification->nama_klasifikasi ?? 'N/A' }}
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi Penyimpanan</label>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    @if ($archive->hasStorageLocation())
                                        <span
                                            class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                            <i class="fas fa-check mr-1"></i>Ditempatkan
                                        </span>
                                        <p class="text-sm text-gray-600 mt-2">
                                            Rak {{ $archive->rack_number }}, Baris {{ $archive->row_number }},
                                            Box {{ $archive->box_number }}, File {{ $archive->file_number }}
                                        </p>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                            <i class="fas fa-times mr-1"></i>Belum Ditempatkan
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Dibuat Oleh</label>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    {{ $archive->createdByUser->name ?? 'N/A' }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Dibuat</label>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    {{ $archive->created_at ? $archive->created_at->format('d/m/Y H:i') : 'N/A' }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Terakhir Diupdate</label>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    {{ $archive->updated_at ? $archive->updated_at->format('d/m/Y H:i') : 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Archives -->
            {{-- @if ($relatedArchives->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Arsip Terkait</h3>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            No</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nomor Arsip</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Uraian</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($relatedArchives as $index => $relatedArchive)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $index + 1 }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $relatedArchive->index_number ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                <div class="max-w-xs truncate"
                                                    title="{{ $relatedArchive->description }}">
                                                    {{ $relatedArchive->description }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                    @if ($relatedArchive->status === 'Aktif') bg-green-100 text-green-800
                                                    @elseif($relatedArchive->status === 'Inaktif') bg-yellow-100 text-yellow-800
                                                    @elseif($relatedArchive->status === 'Permanen') bg-blue-100 text-blue-800
                                                    @elseif($relatedArchive->status === 'Musnah') bg-red-100 text-red-800
                                                    @elseif($relatedArchive->status === 'Dinilai Kembali') bg-indigo-100 text-indigo-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ $relatedArchive->status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('intern.re-evaluation.show', $relatedArchive) }}"
                                                    class="text-indigo-600 hover:text-indigo-900">
                                                    <i class="fas fa-eye mr-1"></i>Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div> --}}

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Evaluation form submission
            $('#evaluationForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                Swal.fire({
                    title: 'Memproses...',
                    text: 'Menyimpan evaluasi',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '{{ route('intern.re-evaluation.update-status', $archive) }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil!', response.message, 'success');
                            setTimeout(() => {
                                window.location.href = '{{ route('intern.re-evaluation.index') }}';
                            }, 1500);
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Terjadi kesalahan saat menyimpan evaluasi', 'error');
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
