<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-pink-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-search text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Pencarian Arsip</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-book mr-1"></i>Pelajari cara mencari dan menganalisis arsip digital
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('intern.archives.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Arsip
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="p-6 space-y-6">

        <!-- Search Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form id="internSearchForm" action="{{ route('intern.search.search') }}" method="GET">

                <!-- Main Search Input -->
                <div class="mb-6">
                    <label for="search_term" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-search mr-2 text-orange-500"></i>Kata Kunci Pencarian
                    </label>
                    <div class="relative">
                        <input type="text"
                               name="term"
                               id="search_term"
                               value="{{ request('term') }}"
                               placeholder="Cari berdasarkan nomor arsip atau uraian..."
                               class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors text-base">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Basic Filters -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Status Filter -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-info-circle mr-2 text-orange-500"></i>Status Arsip
                        </label>
                        <select name="status" id="status" class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="">Semua Status</option>
                            @foreach(['Aktif', 'Inaktif', 'Permanen', 'Musnah'] as $statusOption)
                                <option value="{{ $statusOption }}" {{ request('status') == $statusOption ? 'selected' : '' }}>
                                    {{ $statusOption }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Created By Filter (Only Intern Users) -->
                    <div>
                        <label for="created_by" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user-graduate mr-2 text-pink-500"></i>Dibuat Oleh
                        </label>
                        <select name="created_by" id="created_by" class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="">Semua Arsip</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('created_by') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div class="flex space-x-3">
                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-pink-500 hover:from-orange-600 hover:to-pink-600 text-white font-medium rounded-xl transition-colors shadow-sm">
                            <i class="fas fa-search mr-2"></i>Cari Arsip
                        </button>

                        <button type="button" id="resetForm" class="inline-flex items-center px-4 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-xl transition-colors">
                            <i class="fas fa-undo mr-2"></i>Reset
                        </button>
                    </div>
                </div>

            </form>
        </div>

        <!-- Search Results -->
        @if(isset($archives) && $archives->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-list mr-2 text-indigo-500"></i>Hasil Pencarian
                        <span class="ml-2 px-2 py-1 bg-indigo-100 text-indigo-800 text-sm rounded-full">{{ $archives->total() }}</span>
                    </h3>
                </div>

                <!-- Results Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Arsip</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                {{-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th> --}}
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat Oleh</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($archives as $archive)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $archive->index_number }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'Aktif' => 'bg-green-100 text-green-800',
                                                'Inaktif' => 'bg-yellow-100 text-yellow-800',
                                                'Permanen' => 'bg-purple-100 text-purple-800',
                                                'Musnah' => 'bg-red-100 text-red-800'
                                            ];
                                        @endphp
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$archive->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $archive->status }}
                                        </span>
                                    </td>
                                    {{-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $archive->kurun_waktu_start }} - {{ $archive->kurun_waktu_end }}
                                    </td> --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $archive->createdByUser->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('intern.archives.show', $archive) }}"
                                           class="text-indigo-600 hover:text-indigo-900">
                                            Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $archives->links() }}
                </div>
            </div>
        @elseif(request()->has('term'))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <i class="fas fa-search text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak Ada Hasil</h3>
                <p class="text-gray-500 mb-6">Tidak ditemukan arsip yang sesuai dengan kriteria pencarian Anda.</p>
            </div>
        @endif

    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Reset form functionality
            document.getElementById('resetForm').addEventListener('click', function() {
                document.getElementById('internSearchForm').reset();
                window.location.href = '{{ route('intern.search.index') }}';
            });
        });
    </script>
    @endpush
</x-app-layout>
