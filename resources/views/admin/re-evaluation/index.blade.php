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

    <!-- Main Content -->
    <div class="p-6 space-y-6">
        @if ($archives->isEmpty())
            <div class="text-center py-16">
                <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak ada arsip dinilai kembali</h3>
                <p class="text-gray-500">Belum ada arsip yang memerlukan penilaian ulang saat ini.</p>
            </div>
        @else
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-list mr-2 text-indigo-500"></i>Daftar Arsip Dinilai Kembali
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Arsip</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uraian</th>
                                {{-- <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Klasifikasi</th> --}}
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Arsip</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($archives as $archive)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ ($archives->currentPage() - 1) * $archives->perPage() + $loop->iteration }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $archive->index_number }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="max-w-xs truncate" title="{{ $archive->description }}">
                                            {{ $archive->description }}
                                            <p class="text-xs text-gray-500">{{ $archive->category->nama_kategori ?? 'N/A' }}</p>
                                            <p class="text-xs text-gray-500">{{ $archive->classification ? $archive->classification->code . ' - ' . $archive->classification->nama_klasifikasi : 'N/A' }}</p>
                                        </div>
                                    </td>
                                    {{-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">

                                    </td> --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $archive->kurun_waktu_start->format('d-m-Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                            {{ $archive->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('admin.re-evaluation.show', $archive->id) }}"
                                                class="text-blue-600 hover:text-blue-800 hover:bg-blue-50 p-2 rounded-lg transition-colors"
                                                title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button onclick="showStatusChangeModal({{ $archive->id }}, '{{ $archive->index_number }}')"
                                                class="text-green-600 hover:text-green-800 hover:bg-green-50 p-2 rounded-lg transition-colors"
                                                title="Ubah Status">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $archives->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- Status Change Modal -->
    <div id="statusChangeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Ubah Status Arsip</h3>
                <form id="statusChangeForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label for="new_status" class="block text-sm font-medium text-gray-700 mb-2">Status Baru</label>
                        <select name="new_status" id="new_status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="Aktif">Aktif</option>
                            <option value="Inaktif">Inaktif</option>
                            <option value="Permanen">Permanen</option>
                            <option value="Musnah">Musnah</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="remarks" class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                        <textarea name="remarks" id="remarks" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Tambahkan catatan perubahan status..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeStatusChangeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function showStatusChangeModal(archiveId, archiveNumber) {
            document.getElementById('statusChangeForm').action = `/admin/re-evaluation/${archiveId}/status`;
            document.getElementById('statusChangeModal').classList.remove('hidden');
        }

        function closeStatusChangeModal() {
            document.getElementById('statusChangeModal').classList.add('hidden');
        }

        // Handle form submission
        document.getElementById('statusChangeForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message || 'Terjadi kesalahan saat mengubah status'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat mengubah status'
                });
            });
        });

        // Show success message if exists
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 1500
            });
        @endif
    </script>
    @endpush
</x-app-layout>
