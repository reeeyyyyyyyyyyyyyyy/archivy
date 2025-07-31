<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-redo text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Arsip Dinilai Kembali</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Kelola arsip yang memerlukan penilaian ulang
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Arsip Dinilai Kembali
            </h2>
            <div class="flex items-center space-x-3">
                <a href="{{ route('staff.dashboard') }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-archive text-blue-500 text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500">Total Arsip</div>
                                <div class="text-2xl font-semibold text-gray-900">{{ $archives->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-clock text-yellow-500 text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500">Menunggu Evaluasi</div>
                                <div class="text-2xl font-semibold text-gray-900">
                                    {{ $archives->where('status', 'Dinilai Kembali')->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500">Sudah Dievaluasi</div>
                                <div class="text-2xl font-semibold text-gray-900">
                                    {{ $archives->whereNotIn('status', ['Dinilai Kembali'])->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-calendar text-purple-500 text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500">Tahun Ini</div>
                                <div class="text-2xl font-semibold text-gray-900">
                                    {{ $archives->where('kurun_waktu_start', '>=', now()->startOfYear())->count() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Archives Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Daftar Arsip Dinilai Kembali</h3>

                        <!-- Bulk Actions -->
                        <div class="flex items-center space-x-3">
                            <button onclick="bulkAction('aktif')"
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-colors">
                                <i class="fas fa-check mr-2"></i>Aktifkan Terpilih
                            </button>
                            <button onclick="bulkAction('permanen')"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors">
                                <i class="fas fa-archive mr-2"></i>Permanenkan Terpilih
                            </button>
                            <button onclick="bulkAction('musnah')"
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition-colors">
                                <i class="fas fa-trash mr-2"></i>Musnahkan Terpilih
                            </button>
                        </div>
                    </div>

                    @if ($archives->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <input type="checkbox" id="select-all"
                                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            No</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nomor Arsip</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Uraian</th>
                                        {{-- <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Kategori</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Klasifikasi</th> --}}
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tahun</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($archives as $index => $archive)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="checkbox" name="selected_archives[]"
                                                    value="{{ $archive->id }}"
                                                    class="archive-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $index + 1 }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $archive->index_number }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                                                {{ $archive->description }}
                                                <p class="text-xs text-gray-500">{{ $archive->classification ? ($archive->classification->code . ' - ' . $archive->classification->nama_klasifikasi) : 'N/A' }}</p>
                                                {{-- <p class="text-xs text-gray-400 mt-1">Oleh: {{ $archive->createdByUser->name ?? 'System' }}</p>
                                                <p class="text-xs text-gray-400 mt-1">Dibuat: {{ $archive->created_at ? $archive->created_at->diffForHumans() : 'Baru saja' }}</p>
                                            </td> --}}
                                            {{-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $archive->category->nama_kategori }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $archive->classification->nama_klasifikasi }}</td> --}}
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $archive->kurun_waktu_start }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                    @if ($archive->status === 'Aktif') bg-green-100 text-green-800
                                                    @elseif($archive->status === 'Inaktif') bg-yellow-100 text-yellow-800
                                                    @elseif($archive->status === 'Permanen') bg-blue-100 text-blue-800
                                                    @elseif($archive->status === 'Musnah') bg-red-100 text-red-800
                                                    @elseif($archive->status === 'Dinilai Kembali') bg-purple-100 text-purple-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ $archive->status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex items-center space-x-2">
                                                    <a href="{{ route('staff.archives.show', $archive) }}"
                                                        class="text-blue-600 hover:text-blue-900">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button onclick="changeStatus({{ $archive->id }}, 'aktif')"
                                                        class="text-green-600 hover:text-green-900" title="Aktifkan">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button onclick="changeStatus({{ $archive->id }}, 'permanen')"
                                                        class="text-blue-600 hover:text-blue-900" title="Permanenkan">
                                                        <i class="fas fa-archive"></i>
                                                    </button>
                                                    <button onclick="changeStatus({{ $archive->id }}, 'musnah')"
                                                        class="text-red-600 hover:text-red-900" title="Musnahkan">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-inbox text-gray-400 text-4xl mb-4"></i>
                            <p class="text-gray-500">Belum ada arsip dinilai kembali</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Select all functionality
            document.getElementById('select-all').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.archive-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });

            // Individual status change
            function changeStatus(archiveId, status) {
                Swal.fire({
                    title: 'Konfirmasi',
                    text: `Anda yakin ingin mengubah status arsip menjadi ${status.toUpperCase()}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Ubah!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/staff/re-evaluation/${archiveId}/status`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                },
                                body: JSON.stringify({
                                    status: status
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('Berhasil!', data.message, 'success').then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire('Error!', data.message, 'error');
                                }
                            })
                            .catch(error => {
                                Swal.fire('Error!', 'Terjadi kesalahan saat mengubah status', 'error');
                            });
                    }
                });
            }

            // Bulk actions
            function bulkAction(action) {
                const selectedArchives = document.querySelectorAll('.archive-checkbox:checked');

                if (selectedArchives.length === 0) {
                    Swal.fire('Peringatan', 'Pilih arsip terlebih dahulu!', 'warning');
                    return;
                }

                const archiveIds = Array.from(selectedArchives).map(checkbox => checkbox.value);

                Swal.fire({
                    title: 'Konfirmasi',
                    text: `Anda yakin ingin mengubah status ${selectedArchives.length} arsip menjadi ${action.toUpperCase()}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Ubah!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('/staff/re-evaluation/bulk-update', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                },
                                body: JSON.stringify({
                                    archive_ids: archiveIds,
                                    status: action
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('Berhasil!', data.message, 'success').then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire('Error!', data.message, 'error');
                                }
                            })
                            .catch(error => {
                                Swal.fire('Error!', 'Terjadi kesalahan saat mengubah status', 'error');
                            });
                    }
                });
            }
        </script>
    @endpush
</x-app-layout>
