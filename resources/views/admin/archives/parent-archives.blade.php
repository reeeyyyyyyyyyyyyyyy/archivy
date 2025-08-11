<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-folder-tree text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Arsip Parent</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Kelola arsip parent untuk manajemen arsip terkait
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    @if($showAddButton)
                        <a href="{{ route('admin.archives.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Tambah Arsip Baru
                        </a>
                    @endif
                    <a href="{{ route('admin.archives.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Semua Arsip
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('admin.archives.parent') }}" class="flex gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Cari arsip parent berdasarkan deskripsi, nomor arsip, lampiran, kategori, atau klasifikasi...">
                    </div>
                </div>
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-search mr-2"></i>
                    Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.archives.parent') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Reset
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="p-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <!-- Table Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Daftar Arsip Parent ({{ $archives->total() }} arsip)
                    </h3>
                    <div class="text-sm text-gray-500">
                        Menampilkan arsip parent yang dapat dikelola arsip terkaitnya
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                No
                            </th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nomor Arsip
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Indeks - Deskripsi
                            </th>
                            {{-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kategori
                            </th> --}}
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Klasifikasi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tahun
                            </th>
                            {{-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th> --}}
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($archives as $index => $archive)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $archives->firstItem() + $index }}
                                </td>
                                <td class="px-4 py-2 truncate whitespace-nowrap text-sm text-gray-900">
                                    <span class="font-medium">{{ $archive->index_number }}</span>
                                </td>
                                <td class="px-6 py-4 truncate text-sm text-gray-900">
                                    <div class="max-w-xs truncate">
                                        <div class="font-medium text-gray-900">{{ $archive->lampiran_surat }} - {{ $archive->description }}</div>
                                        {{-- @if($archive->lampiran_surat)
                                            <div class="text-xs text-gray-500 mt-1">
                                                Lampiran: {{ $archive->lampiran_surat }}
                                            </div>
                                        @endif --}}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $archive->category ? $archive->category->nama_kategori : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $archive->kurun_waktu_start ? $archive->kurun_waktu_start->format('Y') : '-' }}
                                </td>
                                {{-- <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $archive->status === 'Aktif' ? 'bg-green-100 text-green-800' :
                                           ($archive->status === 'Inaktif' ? 'bg-yellow-100 text-yellow-800' :
                                           ($archive->status === 'Permanen' ? 'bg-blue-100 text-blue-800' :
                                           'bg-red-100 text-red-800')) }}">
                                        {{ $archive->status }}
                                    </span>
                                </td> --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <!-- Show Related Archives -->
                                        <a href="{{ route('admin.archives.related', $archive) }}"
                                            class="text-blue-600 hover:text-blue-900"
                                            title="Lihat Arsip Terkait">
                                            <i class="fas fa-link"></i>
                                        </a>

                                        <!-- Add Related Archive -->
                                        <a href="{{ route('admin.archives.create-related', $archive) }}"
                                            class="text-green-600 hover:text-green-900"
                                            title="Tambah Berkas Arsip yang Sama">
                                            <i class="fas fa-plus-circle"></i>
                                        </a>

                                        {{-- <!-- Show Archive Details -->
                                        <a href="{{ route('admin.archives.show', $archive) }}"
                                            class="text-indigo-600 hover:text-indigo-900"
                                            title="Detail Arsip">
                                            <i class="fas fa-eye"></i>
                                        </a> --}}

                                        {{-- <!-- Edit Archive -->
                                        <a href="{{ route('admin.archives.edit', $archive) }}"
                                            class="text-yellow-600 hover:text-yellow-900"
                                            title="Edit Arsip">
                                            <i class="fas fa-edit"></i>
                                        </a> --}}

                                        <!-- Delete Archive -->
                                        <form action="{{ route('admin.archives.destroy', $archive) }}" method="POST" class="inline"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus arsip ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900"
                                                title="Hapus Arsip">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                    <div class="flex flex-col items-center py-8">
                                        <i class="fas fa-folder-open text-4xl text-gray-300 mb-4"></i>
                                        <p class="text-lg font-medium text-gray-900 mb-2">Tidak ada arsip parent</p>
                                        <p class="text-sm text-gray-500">
                                            @if(request('search'))
                                                Tidak ada arsip parent yang sesuai dengan pencarian "{{ request('search') }}"
                                            @else
                                                Belum ada arsip parent yang dibuat
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($archives->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $archives->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
