<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-teal-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-alt text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Detail Arsip</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Staff: Informasi lengkap arsip {{ $archive->formatted_index_number }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('staff.archives.edit', $archive) }}"
                        class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Arsip
                    </a>
                    <a href="{{ route('staff.archives.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Arsip
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="p-6 space-y-6">

        <!-- Archive Header Card -->
        <div class="bg-gradient-to-r from-teal-600 to-cyan-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold mb-2">
                        {{ $archive->formatted_index_number }}
                    </h2>
                    <p class="text-teal-100 text-lg">{{ $archive->description }}</p>
                </div>
                <div class="text-right">
                    @php
                        $statusClasses = [
                            'Aktif' => 'bg-green-500',
                            'Inaktif' => 'bg-yellow-500',
                            'Permanen' => 'bg-purple-500',
                            'Musnah' => 'bg-red-500',
                        ];
                    @endphp
                    <div
                        class="inline-flex items-center px-4 py-2 {{ $statusClasses[$archive->status] ?? 'bg-gray-500' }} rounded-full text-white font-semibold">
                        <i class="fas fa-flag mr-2"></i>{{ $archive->status }}
                    </div>
                    <p class="text-teal-100 text-sm mt-2">Status Saat Ini</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            <!-- Basic Information -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-teal-500"></i>
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
                            <p class="mt-1 text-sm text-gray-900 font-medium">{{ $archive->formatted_index_number }}</p>
                        </div>
                    </div>

                    <!-- Date -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-calendar-alt text-orange-500 mr-2"></i>
                            <span class="text-sm font-medium text-gray-600">Tanggal Arsip</span>
                        </div>
                        <p class="text-lg font-semibold text-gray-900">{{ $archive->kurun_waktu_start->format('d F Y') }}</p>
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
                        <p class="text-lg font-semibold text-gray-900">{{ number_format($archive->jumlah_berkas) }} berkas</p>
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
                @if($archive->ket)
                <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-sticky-note text-blue-500 mr-2"></i>
                        <span class="text-sm font-medium text-blue-800">Keterangan</span>
                    </div>
                    <p class="text-blue-900">{{ $archive->ket }}</p>
                </div>
                @endif

                <!-- Storage Location -->
                <div class="mt-4 bg-white border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            <i class="fas fa-map-marker-alt text-teal-500 mr-2"></i>
                            <span class="text-sm font-medium text-gray-600">Lokasi Penyimpanan</span>
                        </div>
                        @if($archive->hasStorageLocation())
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                <i class="fas fa-check mr-1"></i>Ditempatkan
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                <i class="fas fa-times mr-1"></i>Belum Ditempatkan
                            </span>
                        @endif
                    </div>

                    @if($archive->hasStorageLocation())
                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Rak:</span>
                                    <span class="font-semibold text-gray-900">{{ $archive->rack_number }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Baris:</span>
                                    <span class="font-semibold text-gray-900">{{ $archive->row_number }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Box:</span>
                                    <span class="font-semibold text-gray-900">{{ $archive->box_number }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">File:</span>
                                    <span class="font-semibold text-gray-900">{{ $archive->file_number }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                            <p class="text-red-800 text-sm mb-3">Arsip ini belum memiliki lokasi penyimpanan yang ditentukan.</p>
                            <a href="{{ route('staff.storage.create', $archive->id) }}"
                               class="inline-flex items-center px-3 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium rounded-lg transition-colors">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                Set Lokasi
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Classification & Retention Info -->
            <div class="space-y-6">

                <!-- Classification Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-sitemap mr-2 text-cyan-500"></i>
                        Klasifikasi & Kategori
                    </h3>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-indigo-50 rounded-lg">
                            <div>
                                <p class="text-sm text-indigo-600 font-medium">Kategori</p>
                                <p class="text-indigo-900 font-semibold">{{ $archive->category->nama_kategori ?? 'N/A' }}</p>
                            </div>
                            <i class="fas fa-folder text-indigo-500 text-xl"></i>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-cyan-50 rounded-lg">
                            <div>
                                <p class="text-sm text-cyan-600 font-medium">Klasifikasi</p>
                                <p class="text-cyan-900 font-semibold">{{ $archive->classification->code ?? 'N/A' }}</p>
                                <p class="text-cyan-800 text-sm">{{ $archive->classification->nama_klasifikasi ?? 'N/A' }}</p>
                            </div>
                            <i class="fas fa-tags text-cyan-500 text-xl"></i>
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
                                <p class="text-xs text-gray-500">{{ $archive->transition_active_due->format('d F Y') }}</p>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-purple-500 rounded-full mr-3"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Transisi Final</p>
                                <p class="text-xs text-gray-500">{{ $archive->transition_inactive_due->format('d F Y') }}</p>
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
                <a href="{{ route('staff.archives.edit', $archive) }}"
                   class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-colors shadow-sm">
                    <i class="fas fa-edit mr-2"></i>Edit Arsip
                </a>

                {{-- <button onclick="exportSingle({{ $archive->id }})"
                        class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl transition-colors shadow-sm">
                    <i class="fas fa-file-excel mr-2"></i>Export Excel
                </button> --}}

                <button onclick="printArchive()"
                        class="inline-flex items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-xl transition-colors shadow-sm">
                    <i class="fas fa-print mr-2"></i>Cetak Detail
                </button>

                {{-- Staff cannot delete archives --}}
                {{-- <form id="deleteForm" action="{{ route('staff.archives.destroy', $archive) }}" method="POST" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="confirmDelete()"
                            class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl transition-colors shadow-sm">
                        <i class="fas fa-trash mr-2"></i>Hapus Arsip
                    </button>
                </form> --}}
                    {{-- @csrf
                    @method('DELETE')
                    <button type="button" onclick="confirmDelete()"
                            class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl transition-colors shadow-sm">
                        <i class="fas fa-trash mr-2"></i>Hapus Arsip
                    </button> --}}
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function exportSingle(archiveId) {
            // Implement single archive export
            alert('Fitur export tunggal akan segera tersedia!');
        }

        function printArchive() {
            window.print();
        }

        function confirmDelete() {
            showDeleteModal(
                `Apakah Anda yakin ingin menghapus arsip "{{ $archive->formatted_index_number }}"? Data akan hilang secara permanen.`,
                function() {
                    document.getElementById('deleteForm').submit();
                }
            );
        }
    </script>
    @endpush
</x-app-layout>
