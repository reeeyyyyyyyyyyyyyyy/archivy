<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b px-6 py-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Pencarian Lanjutan</h1>
                <p class="text-sm text-gray-600 mt-1">Cari arsip dengan filter yang lebih detail</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('staff.archives.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali ke Arsip
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="p-6 space-y-6">

        <!-- Search Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-search mr-2 text-blue-500"></i>Filter Pencarian
            </h3>

            <form method="GET" action="{{ route('staff.search.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Search Term -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-search mr-2 text-green-500"></i>Kata Kunci
                    </label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                        placeholder="Cari no. arsip, description, atau keterangan..."
                        class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4">
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-flag mr-2 text-yellow-500"></i>Status
                    </label>
                    <select id="status" name="status"
                        class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4">
                        <option value="">Semua Status</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-folder mr-2 text-indigo-500"></i>Kategori
                    </label>
                    <select id="category_id" name="category_id"
                        class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $category)
                                                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Classification -->
                <div>
                    <label for="classification_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tags mr-2 text-cyan-500"></i>Klasifikasi
                    </label>
                    <select id="classification_id" name="classification_id"
                        class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4">
                        <option value="">Semua Klasifikasi</option>
                        @foreach ($classifications as $classification)
                            <option value="{{ $classification->id }}" {{ request('classification_id') == $classification->id ? 'selected' : '' }}
                                data-category-id="{{ $classification->category_id }}">
                                {{ $classification->code }} - {{ $classification->nama_klasifikasi }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Created By -->
                <div>
                    <label for="created_by" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2 text-purple-500"></i>Dibuat Oleh
                    </label>
                    <select id="created_by" name="created_by"
                        class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4">
                        <option value="">Semua User (TU & Intern)</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ request('created_by') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Range -->
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-2 text-red-500"></i>Tanggal Dari
                    </label>
                    <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}"
                        class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4">
                </div>

                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-2 text-red-500"></i>Tanggal Sampai
                    </label>
                    <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}"
                        class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4">
                </div>

                <!-- Action Buttons -->
                <div class="flex items-end space-x-2">
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-xl transition-colors shadow-sm">
                        <i class="fas fa-search mr-2"></i>Cari
                    </button>
                    <a href="{{ route('staff.search.index') }}"
                       class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-3 px-4 rounded-xl transition-colors">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- Search Results -->
        @if($archives !== null)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6">
                    <!-- Results Header -->
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <i class="fas fa-list mr-2 text-green-500"></i>Hasil Pencarian
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                Ditemukan {{ $archives->total() }} arsip
                                @if(request()->hasAny(['search', 'status', 'category_id', 'classification_id', 'created_by', 'date_from', 'date_to']))
                                    berdasarkan filter yang dipilih
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($archives->count() > 0)
                        <!-- Results Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            No. Arsip
                                        </th>
                                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Description
                                </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Kategori
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Dibuat Oleh
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tanggal
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($archives as $archive)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $archive->index_number ?? 'N/A' }}
                                            </td>
                                                                        <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 font-medium">{{ Str::limit($archive->description, 50) }}</div>
                                <div class="text-sm text-gray-500 truncate max-w-xs">{{ Str::limit($archive->description, 50) }}</div>
                            </td>
                                                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $archive->category->nama_kategori ?? 'N/A' }}
                                        </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $statusClasses = [
                                                        'Aktif' => 'bg-green-100 text-green-800',
                                                        'Inaktif' => 'bg-yellow-100 text-yellow-800',
                                                        'Permanen' => 'bg-purple-100 text-purple-800',
                                                        'Musnah' => 'bg-red-100 text-red-800',
                                                    ];
                                                @endphp
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses[$archive->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                    {{ $archive->status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $archive->createdByUser->name ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $archive->created_at->diffForHumans() }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex items-center space-x-2">
                                                    <a href="{{ route('staff.archives.show', $archive) }}"
                                                       class="text-blue-600 hover:text-blue-900">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('staff.archives.edit', $archive) }}"
                                                       class="text-green-600 hover:text-green-900">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $archives->appends(request()->query())->links() }}
                        </div>
                    @else
                        <!-- No Results -->
                        <div class="text-center py-12">
                            <i class="fas fa-search text-gray-400 text-4xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada hasil ditemukan</h3>
                            <p class="text-gray-500">Coba ubah filter pencarian Anda</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Handle category-classification filter dependencies
                const allClassifications = @json($classifications);

                $('#category_id').on('change', function() {
                    const categoryId = $(this).val();
                    const classificationSelect = $('#classification_id');

                    // Clear current options
                    classificationSelect.empty().append('<option value="">Semua Klasifikasi</option>');

                    if (categoryId) {
                        // Filter classifications by category
                        const filteredClassifications = allClassifications.filter(c => c.category_id == categoryId);
                        filteredClassifications.forEach(c => {
                            classificationSelect.append(`<option value="${c.id}">${c.code} - ${c.name}</option>`);
                        });
                    } else {
                        // Show all classifications
                        allClassifications.forEach(c => {
                            classificationSelect.append(`<option value="${c.id}">${c.code} - ${c.name}</option>`);
                        });
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
