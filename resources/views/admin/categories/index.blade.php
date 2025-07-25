<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-folder text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Master Data Kategori</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-layer-group mr-1"></i>Kelola kategori arsip dan retensi dokumen
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.categories.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Kategori
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
                        <i class="fas fa-folder mr-2 text-indigo-500"></i>Daftar Kategori Arsip
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Total: {{ $categories->count() }} kategori</p>
                </div>

                @if ($categories->isEmpty())
                    <div class="text-center py-16">
                        <i class="fas fa-folder-open text-gray-300 text-6xl mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum ada kategori</h3>
                        <p class="text-gray-500 mb-6">Mulai dengan menambahkan kategori arsip pertama.</p>
                        <a href="{{ route('admin.categories.create') }}"
                            class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-colors">
                            <i class="fas fa-plus mr-2"></i>Tambah Kategori Pertama
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
                                        Nama Kategori</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($categories as $category)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div
                                                    class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                                    <i class="fas fa-folder text-indigo-600"></i>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $category->nama_kategori }}</div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $category->classifications->count() }} klasifikasi</div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <!-- Edit Button -->
                                                <a href="{{ route('admin.categories.edit', $category) }}"
                                                    class="text-blue-600 hover:text-blue-800 hover:bg-blue-100 p-2 rounded-full transition"
                                                    title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <!-- Delete Button -->
                                                <form id="deleteForm{{ $category->id }}"
                                                    action="{{ route('admin.categories.destroy', $category) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        onclick="deleteCategory({{ $category->id }}, '{{ $category->nama_kategori }}')"
                                                        class="text-red-600 hover:text-red-800 hover:bg-red-100 p-2 rounded-full transition"
                                                        title="Hapus">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>

                                        <!-- Modal -->
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
                                                        <!-- Pesan akan disisipkan via JS -->
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
            function deleteCategory(id, name) {
                window.showDeleteConfirm(
                    `Apakah Anda yakin ingin menghapus kategori "${name}"? Tindakan ini tidak dapat dibatalkan dan akan menghapus semua klasifikasi terkait.`
                ).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById(`deleteForm${id}`);
                        form.submit();
                    }
                });
            }
        </script>
    @endpush

</x-app-layout>
