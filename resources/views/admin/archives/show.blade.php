<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-alt text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Detail Arsip</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Informasi lengkap arsip {{ $archive->index_number }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.archives.edit', $archive) }}"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Arsip
                    </a>
                    <a href="javascript:history.back()"
                        class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="p-6 space-y-6">

        <!-- Archive Header Card -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold mb-2">
                        {{ $archive->index_number }}
                    </h2>
                    <p class="text-blue-100 text-lg">{{ $archive->description }}</p>
                </div>
                <div class="text-right">
                    @php
                        $statusClasses = [
                            'Aktif' => 'bg-green-500',
                            'Inaktif' => 'bg-yellow-500',
                            'Permanen' => 'bg-purple-500',
                            'Musnah' => 'bg-red-500',
                            'Dinilai Kembali' => 'bg-indigo-500',
                        ];
                    @endphp
                    <div
                        class="inline-flex items-center px-4 py-2 {{ $statusClasses[$archive->status] ?? 'bg-gray-500' }} rounded-full text-white font-semibold">
                        <i class="fas fa-flag mr-2"></i>{{ $archive->status }}
                    </div>
                    <p class="text-blue-100 text-sm mt-2">Status Saat Ini</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Basic Information -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                    Informasi Dasar
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Index Number -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-hashtag text-green-500 mr-2"></i>
                            <span class="text-sm font-medium text-gray-600">Nomor Berkas</span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nomor Arsip</label>
                            <p class="mt-1 text-sm text-gray-900 font-medium">{{ $archive->index_number }}</p>
                        </div>
                    </div>

                    <!-- Date -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-calendar-alt text-orange-500 mr-2"></i>
                            <span class="text-sm font-medium text-gray-600">Tanggal Arsip</span>
                        </div>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $archive->kurun_waktu_start->format('d F Y') }}</p>
                    </div>

                    <!-- Development Level -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-layer-group text-yellow-500 mr-2"></i>
                            <span class="text-sm font-medium text-gray-600">Tingkat Perkembangan</span>
                        </div>
                        <p class="text-lg font-semibold text-gray-900">{{ $archive->tingkat_perkembangan }}</p>
                    </div>

                    <!-- File Count -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-sort-numeric-up text-red-500 mr-2"></i>
                            <span class="text-sm font-medium text-gray-600">Jumlah Berkas</span>
                        </div>
                        <p class="text-lg font-semibold text-gray-900">{{ number_format($archive->jumlah_berkas) }}
                            berkas</p>
                    </div>

                    {{-- Lampiran Surat --}}
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-paperclip text-blue-500 mr-2"></i>
                            <span class="text-sm font-medium text-gray-600">Lampiran Surat</span>
                        </div>
                        <p class="text-lg font-semibold text-gray-900">{{ $archive->lampiran_surat }}</p>
                    </div>

                    <!-- Storage Location -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-map-marker-alt text-blue-500 mr-2"></i>
                            <span class="text-sm font-medium text-gray-600">Lokasi Penyimpanan</span>
                        </div>
                        @if ($archive->box_number)
                            <p class="text-lg font-semibold text-gray-900">
                                Rak {{ $archive->rack_number }}, Baris {{ $archive->row_number }}, Box
                                {{ $archive->box_number }}, No. Arsip {{ $archive->file_number }}
                            </p>
                        @else
                            <p class="text-lg font-semibold text-gray-500">Lokasi belum diatur</p>
                        @endif
                    </div>
                </div>

                <!-- Description -->
                <div class="mt-6 bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-file-alt text-purple-500 mr-2"></i>
                        <span class="text-sm font-medium text-gray-600">Uraian Arsip</span>
                    </div>
                    <p class="text-gray-900 leading-relaxed">{{ $archive->description }}</p>
                </div>

                <!-- Notes -->
                @if ($archive->ket)
                    <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-sticky-note text-blue-500 mr-2"></i>
                            <span class="text-sm font-medium text-blue-800">Keterangan</span>
                        </div>
                        <p class="text-blue-900">{{ $archive->ket }}</p>
                    </div>
                @endif
            </div>

            <!-- Classification & Retention Info -->
            <div class="space-y-6">

                <!-- Classification Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-sitemap text-cyan-500 mr-2"></i>
                        Klasifikasi & Kategori
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Kategori -->
                        <div class="bg-gray-50 rounded-lg p-5 border border-gray-100 shadow-sm">
                            <div class="flex items-center mb-3">
                                <i class="fas fa-folder text-indigo-500 mr-2"></i>
                                <span class="text-sm font-medium text-gray-600">Kategori</span>
                            </div>
                            <p class="text-indigo-900 text-base font-semibold">
                                {{ $archive->category->nama_kategori ?? 'N/A' }}
                            </p>
                        </div>

                        <!-- Klasifikasi -->
                        <div class="bg-gray-50 rounded-lg p-5 border border-gray-100 shadow-sm">
                            <div class="flex items-center mb-3">
                                <i class="fas fa-tags text-cyan-500 mr-2"></i>
                                <span class="text-sm font-medium text-gray-600">Klasifikasi</span>
                            </div>
                            <p class="text-cyan-900 truncate-255 text-base font-semibold">
                                {{ $archive->classification ? $archive->classification->code . ' - ' . $archive->classification->nama_klasifikasi : 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>


                <!-- Retention Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-clock mr-2 text-blue-500"></i>
                        Informasi Retensi
                    </h3>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                            <div>
                                <p class="text-sm text-green-600 font-medium">Retensi Aktif</p>
                                <p class="text-green-900 font-semibold">{{ $archive->retention_aktif }} tahun</p>
                            </div>
                            <i class="fas fa-calendar-check text-green-500 text-xl"></i>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                            <div>
                                <p class="text-sm text-yellow-600 font-medium">Retensi Inaktif</p>
                                <p class="text-yellow-900 font-semibold">{{ $archive->retention_inaktif }} tahun</p>
                            </div>
                            <i class="fas fa-calendar-times text-yellow-500 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-history mr-2 text-purple-500"></i>
                        Timeline Transisi
                    </h3>

                    <div class="space-y-3">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Transisi ke Inaktif</p>
                                <p class="text-xs text-gray-500">
                                    {{ $archive->transition_active_due->format('d F Y') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-purple-500 rounded-full mr-3"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Transisi Final</p>
                                <p class="text-xs text-gray-500">
                                    {{ $archive->transition_inactive_due->format('d F Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- System Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-cog mr-2 text-gray-500"></i>
                Informasi Sistem
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-user-plus text-blue-500 mr-2"></i>
                        <span class="text-sm font-medium text-gray-600">Dibuat Oleh</span>
                    </div>
                    <p class="font-semibold text-gray-900">{{ $archive->createdByUser->name ?? 'N/A' }}</p>
                    <p class="text-sm text-gray-500">{{ $archive->created_at->format('d F Y, H:i') }} WIB</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-user-edit text-green-500 mr-2"></i>
                        <span class="text-sm font-medium text-gray-600">Terakhir Diperbarui</span>
                    </div>
                    <p class="font-semibold text-gray-900">{{ $archive->updatedByUser->name ?? 'N/A' }}</p>
                    <p class="text-sm text-gray-500">{{ $archive->updated_at->format('d F Y, H:i') }} WIB</p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-tools mr-2 text-orange-500"></i>
                Aksi Tersedia
            </h3>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.archives.edit', $archive) }}"
                    class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-colors shadow-sm">
                    <i class="fas fa-edit mr-2"></i>Edit Arsip
                </a>

                <a href="{{ route('admin.archives.related', $archive) }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-link mr-2"></i>
                    Arsip Terkait
                </a>
                <a href="{{ route('admin.archives.create-related', $archive) }}"
                    class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Tambah Berkas Arsip yang Sama
                </a>

                @if (Auth::user()->hasRole('admin'))
                    <button type="button"
                        onclick="confirmDeleteArchive('{{ $archive->index_number }}', '{{ $archive->description }}')"
                        class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl transition-colors shadow-sm">
                        <i class="fas fa-trash mr-2"></i>Hapus Arsip
                    </button>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function exportSingle(archiveId) {
                // Implement single archive export
                alert('Fitur export tunggal akan segera tersedia!');
            }

            function printArchive() {
                window.print();
            }

            function confirmDeleteArchive(indexNumber, description) {
                Swal.fire({
                    title: 'Konfirmasi Hapus Arsip',
                    html: `
                        <div class="text-left">
                            <p class="mb-3">Apakah Anda yakin ingin menghapus arsip ini?</p>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <p class="font-semibold text-gray-800">Nomor Arsip: ${indexNumber}</p>
                                <p class="text-gray-600 text-sm">${description}</p>
                            </div>
                            <p class="text-red-600 text-sm mt-3">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Data akan hilang secara permanen dan tidak dapat dikembalikan!
                            </p>
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '<i class="fas fa-trash mr-2"></i>Hapus Arsip',
                    cancelButtonText: '<i class="fas fa-times mr-2"></i>Batal',
                    reverseButtons: true,
                    customClass: {
                        confirmButton: 'swal2-confirm',
                        cancelButton: 'swal2-cancel'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Menghapus Arsip...',
                            text: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Create form and submit
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route('admin.archives.destroy', $archive) }}';

                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';

                        const methodField = document.createElement('input');
                        methodField.type = 'hidden';
                        methodField.name = '_method';
                        methodField.value = 'DELETE';

                        form.appendChild(csrfToken);
                        form.appendChild(methodField);
                        document.body.appendChild(form);

                        form.submit();
                    }
                });
            }
        </script>
    @endpush
</x-app-layout>
