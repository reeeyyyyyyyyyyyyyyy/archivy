<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-teal-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-warehouse text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Detail Rak: {{ $rack->name }}</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Informasi lengkap rak penyimpanan
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('staff.storage-management.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                    <a href="{{ route('staff.storage-management.edit', $rack) }}"
                        class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-edit mr-2"></i>Edit Rak
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Rack Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-teal-500"></i>Informasi Rak
                    </h3>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full
                        {{ $rack->status === 'active' ? 'bg-green-100 text-green-800' :
                           ($rack->status === 'inactive' ? 'bg-gray-100 text-gray-800' :
                           'bg-red-100 text-red-800') }}">
                        {{ ucfirst($rack->status) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-gradient-to-r from-teal-500 to-teal-600 rounded-lg p-4 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-teal-100 text-sm font-medium">Total Baris</p>
                                <p class="text-2xl font-bold">{{ $rack->total_rows }}</p>
                            </div>
                            <div class="w-10 h-10 bg-teal-400 bg-opacity-30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-layer-group text-lg"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm font-medium">Total Box</p>
                                <p class="text-2xl font-bold">{{ $rack->total_boxes }}</p>
                            </div>
                            <div class="w-10 h-10 bg-blue-400 bg-opacity-30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-box text-lg"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm font-medium">Kapasitas per Box</p>
                                <p class="text-2xl font-bold">{{ $rack->capacity_per_box }}</p>
                            </div>
                            <div class="w-10 h-10 bg-green-400 bg-opacity-30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-archive text-lg"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg p-4 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-orange-100 text-sm font-medium">Utilisasi</p>
                                <p class="text-2xl font-bold">{{ $rack->getUtilizationPercentage() }}%</p>
                            </div>
                            <div class="w-10 h-10 bg-orange-400 bg-opacity-30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-chart-pie text-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>

                @if($rack->description)
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-2">Deskripsi</h4>
                        <p class="text-gray-700">{{ $rack->description }}</p>
                    </div>
                @endif

                @if($rack->year_start || $rack->year_end)
                    <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-2">Filter Tahun</h4>
                        <div class="flex items-center space-x-4">
                            @if($rack->year_start)
                                <span class="text-sm text-gray-600">Dari: <span class="font-semibold">{{ $rack->year_start }}</span></span>
                            @endif
                            @if($rack->year_end)
                                <span class="text-sm text-gray-600">Sampai: <span class="font-semibold">{{ $rack->year_end }}</span></span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Box Status Overview -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-boxes mr-2 text-teal-500"></i>Status Box
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-check text-white text-lg"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900">Box Tersedia</h4>
                        <p class="text-2xl font-bold text-green-600">{{ $rack->getAvailableBoxesCount() }}</p>
                        <p class="text-sm text-gray-600">Siap digunakan</p>
                    </div>

                    <div class="text-center p-4 bg-yellow-50 rounded-lg">
                        <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-exclamation-triangle text-white text-lg"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900">Box Sebagian Penuh</h4>
                        <p class="text-2xl font-bold text-yellow-600">{{ $rack->getPartiallyFullBoxesCount() }}</p>
                        <p class="text-sm text-gray-600">Masih bisa diisi</p>
                    </div>

                    <div class="text-center p-4 bg-red-50 rounded-lg">
                        <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-times text-white text-lg"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900">Box Penuh</h4>
                        <p class="text-2xl font-bold text-red-600">{{ $rack->getFullBoxesCount() }}</p>
                        <p class="text-sm text-gray-600">Tidak bisa diisi lagi</p>
                    </div>
                </div>
            </div>

            <!-- Boxes Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-list mr-2 text-teal-500"></i>Detail Box
                </h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    No. Box
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Baris
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jumlah Arsip
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($rack->boxes->sortBy('box_number') as $box)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        Box {{ $box->box_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        Baris {{ $box->row_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $box->archive_count }} arsip
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $capacity = $box->capacity;
                                            $halfN = $capacity / 2;
                                            $archiveCount = $box->archive_count;

                                            if ($archiveCount >= 1 && $archiveCount < $halfN) {
                                                $statusClass = 'bg-green-100 text-green-800';
                                                $statusText = 'Tersedia';
                                            } elseif ($archiveCount >= $halfN && $archiveCount < $capacity) {
                                                $statusClass = 'bg-yellow-100 text-yellow-800';
                                                $statusText = 'Sebagian';
                                            } elseif ($archiveCount >= $capacity) {
                                                $statusClass = 'bg-red-100 text-red-800';
                                                $statusText = 'Penuh';
                                            } else {
                                                $statusClass = 'bg-gray-100 text-gray-800';
                                                $statusText = 'Kosong';
                                            }
                                        @endphp
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="showBoxContents({{ $box->box_number }})"
                                            class="text-purple-600 hover:text-purple-800 hover:bg-purple-50 p-2 rounded-lg transition-colors"
                                            title="Lihat Isi Box">
                                            <i class="fas fa-box"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function showBoxContents(boxNumber) {
            // Show loading
            Swal.fire({
                title: 'Memuat isi box...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Fetch box contents
            fetch(`{{ route('staff.storage.box.contents', 'BOX_NUMBER') }}`.replace('BOX_NUMBER', boxNumber))
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    let contentHtml = `
                        <div class="text-left">
                            <h3 class="font-semibold text-gray-900 mb-3">Isi Box ${boxNumber}</h3>
                            <div class="max-h-64 overflow-y-auto">
                    `;

                    if (data.length > 0) {
                        data.forEach((archive, index) => {
                            const truncatedDescription = archive.description.length > 50
                                ? archive.description.substring(0, 50) + '...'
                                : archive.description;

                            contentHtml += `
                                <div class="border-b border-gray-200 py-2">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-xs text-gray-500 font-medium">${index + 1}.</span>
                                                <span class="font-medium text-gray-900">${archive.index_number}</span>
                                            </div>
                                            <p class="text-sm text-gray-600 mt-1">${truncatedDescription}</p>
                                            <p class="text-xs text-gray-500 mt-1">File ${archive.file_number}</p>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        contentHtml += `
                            <div class="text-center py-4 text-gray-500">
                                <i class="fas fa-inbox text-2xl mb-2"></i>
                                <p>Box ${boxNumber} kosong</p>
                            </div>
                        `;
                    }

                    contentHtml += `
                            </div>
                        </div>
                    `;

                    Swal.fire({
                        title: `Box ${boxNumber}`,
                        html: contentHtml,
                        width: '600px',
                        confirmButtonColor: '#4F46E5',
                        confirmButtonText: 'Tutup'
                    });
                })
                .catch(error => {
                    console.error('Error fetching box contents:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal memuat isi box: ' + error.message,
                        confirmButtonColor: '#dc2626'
                    });
                });
        }
    </script>
    @endpush
</x-app-layout>
