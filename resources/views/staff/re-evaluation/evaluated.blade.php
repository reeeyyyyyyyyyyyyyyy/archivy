<x-app-layout>
    {{-- Header --}}
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-emerald-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clipboard-check text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Arsip Sudah Dievaluasi</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Lihat detail arsip yang sudah dievaluasi
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

    {{-- Content --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Statistik --}}
            @if ($archives->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-gradient-to-r from-teal-500 to-teal-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-teal-100 text-sm font-medium">Total Dievaluasi</p>
                                <p class="text-3xl font-bold">{{ $archives->total() }}</p>
                            </div>
                            <div
                                class="w-12 h-12 bg-teal-400 bg-opacity-30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-check-circle text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-emerald-100 text-sm font-medium">Status Aktif</p>
                                <p class="text-3xl font-bold">
                                    {{ $archives->where('status', 'Aktif')->count() }}
                                </p>
                            </div>
                            <div
                                class="w-12 h-12 bg-emerald-400 bg-opacity-30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-play-circle text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-yellow-100 text-sm font-medium">Status Inaktif</p>
                                <p class="text-3xl font-bold">
                                    {{ $archives->where('status', 'Inaktif')->count() }}
                                </p>
                            </div>
                            <div
                                class="w-12 h-12 bg-amber-400 bg-opacity-30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-pause-circle text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-indigo-100 text-sm font-medium">Status Permanen</p>
                                <p class="text-3xl font-bold">
                                    {{ $archives->where('status', 'Permanen')->count() }}
                                </p>
                            </div>
                            <div
                                class="w-12 h-12 bg-indigo-400 bg-opacity-30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-archive text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-red-100 text-sm font-medium">Status Musnah</p>
                                <p class="text-3xl font-bold">
                                    {{ $archives->where('status', 'Musnah')->count() }}
                                </p>
                            </div>
                            <div class="w-12 h-12 bg-red-400 bg-opacity-30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-trash text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Tabel Arsip --}}
            @if ($archives->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Daftar Arsip Sudah Dievaluasi</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Nomor Arsip</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Uraian</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Lokasi</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Catatan Evaluasi</th>
                                        {{-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi --}}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($archives as $index => $archive)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ $index + 1 }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                {{ $archive->index_number ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate"
                                                title="{{ $archive->description }}">
                                                {{ $archive->description }}
                                                <p class="text-xs text-gray-500">
                                                    {{ $archive->category->nama_kategori ?? 'N/A' }}</p>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                    @if ($archive->status === 'Aktif') bg-green-100 text-green-800
                                                    @elseif($archive->status === 'Inaktif') bg-yellow-100 text-yellow-800
                                                    @elseif($archive->status === 'Permanen') bg-blue-100 text-blue-800
                                                    @elseif($archive->status === 'Musnah') bg-red-100 text-red-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ $archive->status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                @if ($archive->hasStorageLocation())
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                                        <i class="fas fa-check mr-1"></i>Ditempatkan :
                                                        @if ($archive->storageRack)
                                                            <span class="font-medium">{{ $archive->storageRack->name }}</span>,
                                                            Baris: {{ $archive->row_number }}, Box: {{ $archive->box_number }}, File: {{ $archive->file_number }}
                                                        @else
                                                            {{ $archive->storage_location }}
                                                        @endif
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                                        <i class="fas fa-times mr-1"></i>Belum Ditempatkan
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900 max-w-xs">
                                                <div class="bg-gray-50 p-2 rounded text-xs">
                                                    {{ $archive->evaluation_notes ?? 'Tidak ada catatan' }}
                                                </div>
                                            </td>
                                            {{-- <td class="px-6 py-4 text-sm font-medium">
                                                <a href="{{ route('staff.re-evaluation.show', $archive) }}"
                                                    class="text-indigo-600 hover:text-indigo-900">
                                                    <i class="fas fa-eye mr-1"></i>Detail
                                                </a>
                                            </td> --}}
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-6">
                            {{ $archives->links() }}
                        </div>
                    </div>
                </div>
            @else
                {{-- Fallback jika tidak ada arsip --}}
                <div class="text-center py-12">
                    <i class="fas fa-check-circle text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada arsip yang sudah dievaluasi</h3>
                    <p class="text-gray-500">Belum ada arsip yang telah selesai dievaluasi.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
