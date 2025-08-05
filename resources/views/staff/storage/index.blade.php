<x-app-layout>
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-teal-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-map-marker-alt text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Lokasi Penyimpanan Arsip</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Staff: Kelola lokasi penyimpanan arsip
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('staff.dashboard') }}"
                        class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (session('success'))
                        <x-success-message>{{ session('success') }}</x-success-message>
                    @endif

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">
                            Arsip yang Belum Memiliki Lokasi Penyimpanan
                        </h3>
                        <p class="text-sm text-gray-600">
                            Berikut adalah arsip yang Anda inputkan yang belum memiliki lokasi penyimpanan.
                            Klik "Set Lokasi" untuk mengatur lokasi penyimpanan arsip.
                        </p>
                    </div>

                    <!-- Filter Section -->
                    <div class="mb-6 bg-white rounded-lg border border-gray-200 p-4">
                        <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-filter mr-2 text-teal-600"></i>
                            Filter Arsip
                        </h4>
                        <form method="GET" action="{{ request()->url() }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label for="year_filter" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-calendar mr-1 text-teal-500"></i>
                                    Tahun Arsip
                                </label>
                                <select name="year_filter" id="year_filter"
                                    class="w-full bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all py-3 px-4 text-sm">
                                    <option value="">Semua Tahun</option>
                                    @for ($year = date('Y'); $year >= 2010; $year--)
                                        <option value="{{ $year }}" {{ request('year_filter') == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label for="status_filter" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-flag mr-1 text-teal-500"></i>
                                    Status
                                </label>
                                <select name="status_filter" id="status_filter"
                                    class="w-full bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all py-3 px-4 text-sm">
                                    <option value="">Semua Status</option>
                                    <option value="Aktif" {{ request('status_filter') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="Inaktif" {{ request('status_filter') == 'Inaktif' ? 'selected' : '' }}>Inaktif</option>
                                    <option value="Permanen" {{ request('status_filter') == 'Permanen' ? 'selected' : '' }}>Permanen</option>
                                    <option value="Musnah" {{ request('status_filter') == 'Musnah' ? 'selected' : '' }}>Musnah</option>
                                    <option value="Dinilai Kembali" {{ request('status_filter') == 'Dinilai Kembali' ? 'selected' : '' }}>Dinilai Kembali</option>
                                </select>
                            </div>
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-search mr-1 text-teal-500"></i>
                                    Pencarian
                                </label>
                                <input type="text" name="search" id="search" value="{{ request('search') }}"
                                    placeholder="Cari nomor arsip atau uraian..."
                                    class="w-full bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all py-3 px-4 text-sm">
                            </div>
                            <div class="flex items-end space-x-2">
                                <button type="submit"
                                    class="bg-teal-600 hover:bg-teal-700 text-white font-medium py-3 px-4 rounded-lg transition-colors shadow-sm text-sm">
                                    <i class="fas fa-filter mr-1"></i>Filter
                                </button>
                                <a href="{{ route('staff.storage.index') }}"
                                    class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-3 px-4 rounded-lg transition-colors shadow-sm text-sm">
                                    <i class="fas fa-times mr-1"></i>Reset
                                </a>
                            </div>
                        </form>
                    </div>

                    @if ($archives->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nomor Arsip
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Uraian
                                        </th>
                                        {{-- <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Kategori/Klasifikasi
                                        </th> --}}
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tanggal Arsip
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($archives as $archive)
                                        <tr class="hover:bg-gray-50">
                                            {{-- Truncate index number --}}
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ Str::limit($archive->index_number, 20) }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                <div class="max-w-xs truncate" title="{{ $archive->description }}">
                                                    {{ Str::limit($archive->description, 30) }}
                                                </div>
                                            </td>
                                            {{-- <td class="px-6 py-4 text-sm text-gray-900">
                                                <div class="text-xs">
                                                    <div class="font-medium">{{ $archive->category->nama_kategori }}</div>
                                                    <div class="text-gray-500">{{ $archive->classification->nama_klasifikasi }}</div>
                                                </div>
                                            </td> --}}
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    @if ($archive->status === 'Aktif') bg-green-100 text-green-800
                                                    @elseif($archive->status === 'Inaktif') bg-yellow-100 text-yellow-800
                                                    @elseif($archive->status === 'Permanen') bg-blue-100 text-blue-800
                                                    @elseif($archive->status === 'Musnah') bg-red-100 text-red-800
                                                    @elseif($archive->status === 'Dinilai Kembali') bg-purple-100 text-purple-800 @endif">
                                                    {{ $archive->status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $archive->kurun_waktu_start->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('staff.storage.create', $archive->id) }}"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:border-teal-700 focus:ring focus:ring-teal-200 active:bg-teal-800 transition ease-in-out duration-150">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                        </path>
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z">
                                                        </path>
                                                    </svg>
                                                    Set Lokasi
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
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak Ada Arsip</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Semua arsip yang Anda inputkan sudah memiliki lokasi penyimpanan.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
