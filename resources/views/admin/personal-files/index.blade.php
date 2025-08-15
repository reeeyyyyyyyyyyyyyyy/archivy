<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-friends text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Berkas Perseorangan</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Kelola arsip dengan nasib akhir "Masuk ke Berkas Perseorangan"
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    {{-- <a href="{{ route('admin.archives.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Berkas Perseorangan
                    </a> --}}
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
            <form method="GET" action="{{ route('admin.personal-files.index') }}" class="space-y-4">
                <!-- Search Row -->
                <div class="flex gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Cari berkas perseorangan berdasarkan deskripsi, nomor arsip, lampiran, kategori, atau klasifikasi...">
                        </div>
                    </div>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-search mr-2"></i>
                        Cari
                    </button>
                    @if(request('search') || request('category_filter'))
                        <a href="{{ route('admin.personal-files.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                            <i class="fas fa-times mr-2"></i>
                            Reset
                        </a>
                    @endif
                </div>

                <!-- Category Filter Row -->
                {{-- <div class="flex gap-4 items-center">
                    <label class="text-sm font-medium text-gray-700">Filter Kategori:</label>
                    <div class="flex gap-2">
                        <label class="inline-flex items-center">
                            <input type="radio" name="category_filter" value="" {{ !request('category_filter') ? 'checked' : '' }}
                                class="form-radio text-blue-600" onchange="this.form.submit()">
                            <span class="ml-2 text-sm text-gray-700">Semua</span>
                        </label>
                        @foreach($categories as $category)
                            <label class="inline-flex items-center">
                                <input type="radio" name="category_filter" value="{{ $category->nama_kategori }}"
                                    {{ request('category_filter') == $category->nama_kategori ? 'checked' : '' }}
                                    class="form-radio text-blue-600" onchange="this.form.submit()">
                                <span class="ml-2 text-sm text-gray-700 font-medium">{{ $category->nama_kategori }}</span>
                            </label>
                        @endforeach
                    </div>
                </div> --}}
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
                        Daftar Berkas Perseorangan ({{ $archives->total() }} arsip)
                    </h3>
                    <div class="text-sm text-gray-500">
                        Arsip dengan nasib akhir "Masuk ke Berkas Perseorangan"
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kategori
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Klasifikasi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tahun
                            </th>
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
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @php
                                        $categoryColors = [
                                            'JRA' => 'bg-blue-100 text-blue-800',
                                            'LAINNYA' => 'bg-orange-100 text-orange-800',
                                            'NON KEPEGAWAIAN' => 'bg-purple-100 text-purple-800',
                                            'NON KEUANGAN' => 'bg-green-100 text-green-800',
                                        ];
                                        $categoryColor = $categoryColors[$archive->category->nama_kategori ?? ''] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $categoryColor }}">
                                        {{ $archive->category ? $archive->category->nama_kategori : '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $archive->classification ? $archive->classification->nama_klasifikasi : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $archive->kurun_waktu_start ? $archive->kurun_waktu_start->format('Y') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <!-- Show Archive Details -->
                                        <a href="{{ route('admin.archives.show', $archive) }}"
                                            class="text-indigo-600 hover:text-indigo-900"
                                            title="Detail Arsip">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <!-- Edit Archive -->
                                        <a href="{{ route('admin.archives.edit', $archive) }}"
                                            class="text-yellow-600 hover:text-yellow-900"
                                            title="Edit Arsip">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- Delete Archive -->
                                        <button onclick="confirmDeleteArchive({{ $archive->id }}, '{{ $archive->index_number }}', '{{ $archive->description }}')"
                                            class="text-red-600 hover:text-red-900"
                                            title="Hapus Arsip">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    <div class="flex flex-col items-center py-8">
                                        <i class="fas fa-user-friends text-4xl text-gray-300 mb-4"></i>
                                        <p class="text-lg font-medium text-gray-900 mb-2">Tidak ada berkas perseorangan</p>
                                        <p class="text-sm text-gray-500">
                                            @if(request('search'))
                                                Tidak ada berkas perseorangan yang sesuai dengan pencarian "{{ request('search') }}"
                                            @else
                                                Belum ada arsip dengan nasib akhir "Masuk ke Berkas Perseorangan"
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
                    {{ $archives->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function confirmDeleteArchive(archiveId, indexNumber, description) {
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    html: `
                        <div class="text-left">
                            <p class="mb-3">Apakah Anda yakin ingin menghapus berkas perseorangan ini?</p>
                            <div class="bg-red-50 p-3 rounded-lg">
                                <p class="text-sm font-medium text-red-800">${indexNumber}</p>
                                <p class="text-xs text-red-600">${description}</p>
                            </div>
                            <p class="text-sm text-gray-600 mt-3">Tindakan ini tidak dapat dibatalkan.</p>
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Create and submit form
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/admin/personal-files/${archiveId}`;

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
