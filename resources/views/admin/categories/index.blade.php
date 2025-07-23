<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b px-6 py-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Master Data Kategori</h1>
                <p class="text-sm text-gray-600 mt-1">Kelola kategori arsip berdasarkan JRA Pergub 1 & 30</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.categories.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Kategori
                </a>
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

                @if($categories->isEmpty())
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
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kategori</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Retensi Aktif</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Retensi Inaktif</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nasib Akhir</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
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
                                                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                                    <i class="fas fa-folder text-indigo-600"></i>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $category->classifications->count() }} klasifikasi</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                                {{ $category->retention_active }} tahun
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                                                {{ $category->retention_inactive }} tahun
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $nasibClasses = [
                                                    'Musnah' => 'bg-red-100 text-red-800',
                                                    'Permanen' => 'bg-purple-100 text-purple-800',
                                                    'Dinilai Kembali' => 'bg-blue-100 text-blue-800'
                                                ];
                                            @endphp
                                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $nasibClasses[$category->nasib_akhir] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ $category->nasib_akhir }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ route('admin.categories.edit', $category) }}" 
                                                   class="text-blue-600 hover:text-blue-800 hover:bg-blue-50 p-2 rounded-lg transition-colors" 
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form id="deleteForm{{ $category->id }}" action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" 
                                                            onclick="confirmCategoryDelete({{ $category->id }}, '{{ $category->name }}')"
                                                            class="text-red-600 hover:text-red-800 hover:bg-red-50 p-2 rounded-lg transition-colors" 
                                                            title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function confirmCategoryDelete(categoryId, categoryName) {
            showDeleteModal(
                `Yakin ingin menghapus kategori "${categoryName}"? Data klasifikasi dan arsip terkait akan ikut terhapus.`,
                function() {
                    document.getElementById('deleteForm' + categoryId).submit();
                }
            );
        }
    </script>
    @endpush
</x-app-layout> 