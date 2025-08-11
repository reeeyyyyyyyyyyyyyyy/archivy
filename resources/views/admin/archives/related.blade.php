<x-app-layout>


    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-link text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Arsip Terkait</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Arsip dengan kategori/klasifikasi/lampiran yang sama
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.archives.create-related', $archive) }}"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Tambah Berkas Arsip yang Sama
                    </a>
                    <a href="javascript:window.history.back()"
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

        <!-- Archive Info Card -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold mb-2">
                        {{ $archive->description }}
                    </h2>
                    <p class="text-indigo-100 text-lg">
                        Kategori: {{ $archive->category->nama_kategori }} |
                        Klasifikasi: {{ $archive->classification->nama_klasifikasi }} |
                        Lampiran: {{ $archive->lampiran_surat }}
                    </p>
                </div>
                <div class="text-right">
                    <div class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 rounded-full text-white font-semibold">
                        <i class="fas fa-archive mr-2"></i>{{ $relatedArchives->count() }} Arsip Terkait
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Archives Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-list mr-2 text-indigo-500"></i>
                    Daftar Arsip Terkait
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tahun
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nomor Arsip
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Deskripsi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Lokasi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($relatedArchives as $relatedArchive)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $relatedArchive->kurun_waktu_start->format('Y') }}
                            </td>
                            <td class="px-6 py-4 truncate whitespace-nowrap text-sm text-gray-900" style="max-width: 100px;">
                                {{ $relatedArchive->index_number }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="max-w-xs truncate" title="{{ $relatedArchive->description }}">
                                    {{ $relatedArchive->description }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClasses = [
                                        'Aktif' => 'bg-green-100 text-green-800',
                                        'Inaktif' => 'bg-yellow-100 text-yellow-800',
                                        'Permanen' => 'bg-purple-100 text-purple-800',
                                        'Musnah' => 'bg-red-100 text-red-800',
                                        'Dinilai Kembali' => 'bg-indigo-100 text-indigo-800',
                                    ];
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses[$relatedArchive->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $relatedArchive->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($relatedArchive->rack_number)
                                    <div class="text-xs">
                                        Rak {{ $relatedArchive->rack_number }},
                                        Box {{ $relatedArchive->box_number }},
                                        Baris {{ $relatedArchive->row_number }}
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs">Belum ditentukan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.archives.show', $relatedArchive) }}"
                                        class="text-blue-600 hover:text-blue-800 hover:bg-blue-50 p-2 rounded-lg transition-colors"
                                        title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.archives.edit', $relatedArchive) }}"
                                        class="text-green-600 hover:text-green-800 hover:bg-green-50 p-2 rounded-lg transition-colors"
                                        title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                <div class="flex flex-col items-center py-8">
                                    <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                                    <p class="text-gray-500">Tidak ada arsip terkait ditemukan.</p>
                                    <p class="text-sm text-gray-400 mt-1">Arsip ini belum memiliki berkas terkait.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($relatedArchives->count() > 0)
        <!-- Summary Card -->
        <div class="bg-blue-50 rounded-xl p-6">
            <h3 class="text-lg font-medium text-blue-900 mb-4 flex items-center">
                <i class="fas fa-chart-bar mr-2"></i>
                Ringkasan Arsip Terkait
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $relatedArchives->count() }}</div>
                    <div class="text-sm text-gray-600">Total Arsip Terkait</div>
                </div>
                <div class="bg-white rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $relatedArchives->min('kurun_waktu_start')->format('Y') }}</div>
                    <div class="text-sm text-gray-600">Tahun Terlama</div>
                </div>
                <div class="bg-white rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ $relatedArchives->max('kurun_waktu_start')->format('Y') }}</div>
                    <div class="text-sm text-gray-600">Tahun Terbaru</div>
                </div>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
