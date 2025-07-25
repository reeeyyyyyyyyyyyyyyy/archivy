<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-cyan-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-tags text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Master Data Klasifikasi</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-sitemap mr-1"></i>Kelola klasifikasi arsip sesuai standar JRA
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.classifications.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Klasifikasi
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="p-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6">
                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                <!-- Table Header -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-tags mr-2 text-cyan-500"></i>Daftar Klasifikasi Arsip
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Total: {{ $classifications->count() }} klasifikasi</p>
                </div>

                @if ($classifications->isEmpty())
                    <div class="text-center py-16">
                        <i class="fas fa-tags text-gray-300 text-6xl mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum ada klasifikasi</h3>
                        <p class="text-gray-500 mb-6">Mulai dengan menambahkan klasifikasi arsip pertama.</p>
                        <a href="{{ route('admin.classifications.create') }}"
                            class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-colors">
                            <i class="fas fa-plus mr-2"></i>Tambah Klasifikasi Pertama
                        </a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        No</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kode</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nama Klasifikasi</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kategori</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Retensi Aktif</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Retensi Inaktif</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nasib Akhir</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Jumlah Arsip</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($classifications as $classification)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-mono font-medium">
                                                {{ $classification->code }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div
                                                    class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center mr-3">
                                                    <i class="fas fa-tag text-cyan-600"></i>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $classification->nama_klasifikasi }}</div>
                                                    <div class="text-sm text-gray-500">{{ $classification->code }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div
                                                    class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-2">
                                                    <i class="fas fa-folder text-indigo-600 text-xs"></i>
                                                </div>
                                                <span
                                                    class="text-sm text-gray-900">{{ $classification->category->nama_kategori ?? 'N/A' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <span
                                                class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                                {{ $classification->retention_aktif }} tahun
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <span
                                                class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                                                {{ $classification->retention_inaktif }} tahun
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $nasibClasses = [
                                                    'Musnah' => 'bg-red-100 text-red-800',
                                                    'Permanen' => 'bg-purple-100 text-purple-800',
                                                ];
                                            @endphp
                                            <span
                                                class="px-3 py-1 rounded-full text-xs font-medium {{ $nasibClasses[$classification->nasib_akhir] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ $classification->nasib_akhir }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <span
                                                class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">
                                                {{ $classification->archives->count() }} arsip
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <!-- Tombol Edit -->
                                                <a href="{{ route('admin.classifications.edit', $classification) }}"
                                                    class="text-blue-600 hover:text-blue-800 hover:bg-blue-100 p-2 rounded-full transition"
                                                    title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <!-- Tombol Hapus dengan Modal -->
                                                <form id="deleteForm{{ $classification->id }}"
                                                    action="{{ route('admin.classifications.destroy', $classification) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        onclick="deleteClassification({{ $classification->id }}, '{{ $classification->nama_klasifikasi }}')"
                                                        class="text-red-600 hover:text-red-800 hover:bg-red-100 p-2 rounded-full transition"
                                                        title="Hapus">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                        <!-- Modal Konfirmasi Hapus -->
                                        <div id="deleteModal"
                                            class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center">
                                            <div
                                                class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 animate-fadeIn">
                                                <div class="p-6">
                                                    <div class="flex items-center space-x-3 mb-4">
                                                        <div class="bg-red-100 text-red-600 p-2 rounded-full">
                                                            <i class="fas fa-exclamation-triangle fa-lg"></i>
                                                        </div>
                                                        <h3 class="text-lg font-semibold text-gray-800">Konfirmasi Hapus
                                                        </h3>
                                                    </div>
                                                    <p id="deleteModalMessage"
                                                        class="text-gray-600 text-sm mb-6 leading-relaxed">
                                                        <!-- Pesan akan diisi lewat JS -->
                                                    </p>
                                                    <div class="flex justify-end space-x-3">
                                                        <button onclick="hideDeleteModal()"
                                                            class="px-4 py-2 text-sm rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200">
                                                            Batal
                                                        </button>
                                                        <button id="confirmDeleteButton"
                                                            class="px-4 py-2 text-sm rounded-md bg-red-600 text-white hover:bg-red-700 shadow-sm">
                                                            Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Delete Form -->
    <form id="deleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
        <script>
            let deleteCallback = null;

            function showDeleteModal(message, onConfirm) {
                const modal = document.getElementById('deleteModal');
                const messageBox = document.getElementById('deleteModalMessage');
                const confirmBtn = document.getElementById('confirmDeleteButton');

                messageBox.textContent = message;
                deleteCallback = onConfirm;

                modal.classList.remove('hidden');
                confirmBtn.onclick = () => {
                    if (deleteCallback) deleteCallback();
                    hideDeleteModal();
                };
            }

            function hideDeleteModal() {
                const modal = document.getElementById('deleteModal');
                modal.classList.add('hidden');
            }

            function confirmClassificationDelete(classificationId) {
                showDeleteModal(
                    'Yakin ingin menghapus klasifikasi ini? Semua arsip terkait akan dihapus!',
                    () => document.getElementById('deleteForm' + classificationId).submit()
                );
            }

            function deleteClassification(id, name) {
                window.showDeleteConfirm(
                    `Apakah Anda yakin ingin menghapus klasifikasi "${name}"? Tindakan ini tidak dapat dibatalkan dan akan menghapus semua arsip terkait.`
                ).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('deleteForm');
                        form.action = `{{ route('admin.classifications.index') }}/${id}`;
                        form.submit();
                    }
                });
            }
        </script>
    @endpush

</x-app-layout>
