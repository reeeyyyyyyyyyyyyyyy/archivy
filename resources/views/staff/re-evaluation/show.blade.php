<x-app-layout>
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-teal-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clipboard-check text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Detail Arsip Dinilai Kembali</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Staff: Lihat detail arsip yang memerlukan penilaian ulang
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('staff.re-evaluation.index') }}"
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
                            @elseif($archive->status === 'Permanen') bg-indigo-100 text-indigo-800
                            @elseif($archive->status === 'Musnah') bg-red-100 text-red-800
                            @elseif($archive->status === 'Dinilai Kembali') bg-teal-100 text-teal-800
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
                                        <a href="{{ route('staff.storage.create', $archive->id) }}"
                                            class="inline-flex items-center px-3 py-1 mt-2 text-sm bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition-colors">
                                            <i class="fas fa-map-marker-alt mr-1"></i>Set Lokasi
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Arsip</label>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    {{ $archive->kurun_waktu_start ? $archive->kurun_waktu_start->format('d F Y') : 'N/A' }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Berkas</label>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    {{ number_format($archive->jumlah_berkas) }} berkas
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Dibuat Oleh</label>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    {{ $archive->createdByUser->name ?? 'N/A' }}
                                    <span class="text-xs text-gray-500">
                                        ({{ $archive->created_at->format('d/m/Y H:i') }})
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Evaluation Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-clipboard-check mr-2 text-teal-500"></i>
                        Evaluasi Arsip
                    </h3>

                    <form id="evaluationForm" action="{{ route('staff.re-evaluation.update-status', $archive) }}" method="POST">
                        @csrf
                        <div class="space-y-6">
                            <!-- Current Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status Saat Ini</label>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full
                                        @if ($archive->status === 'Aktif') bg-green-100 text-green-800
                                        @elseif($archive->status === 'Inaktif') bg-yellow-100 text-yellow-800
                                        @elseif($archive->status === 'Permanen') bg-indigo-100 text-indigo-800
                                        @elseif($archive->status === 'Musnah') bg-red-100 text-red-800
                                        @elseif($archive->status === 'Dinilai Kembali') bg-teal-100 text-teal-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $archive->status }}
                                    </span>
                                </div>
                            </div>

                            <!-- New Status -->
                            <div>
                                <label for="new_status" class="block text-sm font-medium text-gray-700 mb-2">Status Baru</label>
                                <select name="new_status" id="new_status" required
                                    class="w-full bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors py-3 px-4">
                                    <option value="">Pilih Status Baru</option>
                                    <option value="Aktif" {{ old('new_status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="Inaktif" {{ old('new_status') == 'Inaktif' ? 'selected' : '' }}>Inaktif</option>
                                    <option value="Permanen" {{ old('new_status') == 'Permanen' ? 'selected' : '' }}>Permanen</option>
                                    <option value="Musnah" {{ old('new_status') == 'Musnah' ? 'selected' : '' }}>Musnah</option>
                                </select>
                            </div>

                            <!-- Evaluation Notes -->
                            <div>
                                <label for="evaluation_notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan Evaluasi</label>
                                <textarea name="evaluation_notes" id="evaluation_notes" rows="4"
                                    class="w-full bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors py-3 px-4"
                                    placeholder="Masukkan catatan evaluasi atau alasan perubahan status...">{{ old('evaluation_notes', $archive->evaluation_notes) }}</textarea>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end space-x-3">
                                <a href="{{ route('staff.re-evaluation.index') }}"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-times mr-2"></i>Batal
                                </a>
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition-colors">
                                    <i class="fas fa-save mr-2"></i>Simpan Evaluasi
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Retention Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-clock mr-2 text-teal-500"></i>
                        Informasi Retensi
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Retensi Aktif</label>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    {{ $archive->retention_aktif ?? 'N/A' }} tahun
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Retensi Inaktif</label>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    {{ $archive->retention_inaktif ?? 'N/A' }} tahun
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Jatuh Tempo Aktif</label>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    {{ $archive->transition_active_due ? $archive->transition_active_due->format('d F Y') : 'N/A' }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Jatuh Tempo Inaktif</label>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    {{ $archive->transition_inactive_due ? $archive->transition_inactive_due->format('d F Y') : 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Form submission with confirmation
            document.getElementById('evaluationForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const newStatus = document.getElementById('new_status').value;
                const evaluationNotes = document.getElementById('evaluation_notes').value;

                if (!newStatus) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Silakan pilih status baru terlebih dahulu.',
                        confirmButtonColor: '#14B8A6'
                    });
                    return;
                }

                Swal.fire({
                    icon: 'question',
                    title: 'Konfirmasi Evaluasi',
                    text: `Apakah Anda yakin ingin mengubah status arsip menjadi "${newStatus}"?`,
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#14B8A6',
                    cancelButtonColor: '#6B7280'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
