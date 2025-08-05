<x-app-layout>
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-archive text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Detail Rak: {{ $rack->name }}</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Informasi detail rak penyimpanan arsip
                        </p>
                    </div>
                </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.storage-management.edit', $rack) }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <a href="{{ route('admin.storage-management.index') }}"
                        class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
                </div>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Rack Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Rak</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-blue-600">Nama Rak</div>
                            <div class="text-lg font-semibold text-blue-900">{{ $rack->name }}</div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-green-600">Total Baris</div>
                            <div class="text-lg font-semibold text-green-900">{{ $rack->total_rows }}</div>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-purple-600">Total Box</div>
                            <div class="text-lg font-semibold text-purple-900">{{ $rack->total_boxes }}</div>
                        </div>
                        <div class="bg-orange-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-orange-600">Kapasitas per Box</div>
                            <div class="text-lg font-semibold text-orange-900">{{ $rack->capacity_per_box }}</div>
                        </div>
                        <div class="bg-indigo-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-indigo-600">Filter Tahun</div>
                            <div class="text-lg font-semibold text-indigo-900">
                                @if($rack->year_start && $rack->year_end)
                                    {{ $rack->year_start }} - {{ $rack->year_end }}
                                @elseif($rack->year_start)
                                    {{ $rack->year_start }} - Sekarang
                                @else
                                    Semua Tahun
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($rack->description)
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                            <div class="text-sm font-medium text-gray-600">Deskripsi</div>
                            <div class="text-gray-900">{{ $rack->description }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Preview Grid -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-th mr-2 text-cyan-500"></i>Preview Visual Grid
                </h3>
                <div id="visual_grid" class="space-y-4">
                    <!-- Will be populated by JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Archives in this Rack -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Arsip dalam Rak Ini</h3>

                    @php
                        $archives = \App\Models\Archive::where('rack_number', $rack->id)
                        ->with(['category', 'classification', 'createdByUser'])
                            ->orderBy('box_number')
                            ->orderBy('file_number')
                            ->get();
                    @endphp

                @if ($archives->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        No</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nomor Arsip</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Uraian</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Lokasi</th>
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
                                        <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $archive->index_number }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                                            {{ $archive->description }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            Rak {{ $archive->rack_number }}, Baris {{ $archive->row_number }}, Box
                                            {{ $archive->box_number }}, File {{ $archive->file_number }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                    @if ($archive->status === 'Aktif') bg-green-100 text-green-800
                                                    @elseif($archive->status === 'Inaktif') bg-yellow-100 text-yellow-800
                                                    @elseif($archive->status === 'Permanen') bg-blue-100 text-blue-800
                                                    @elseif($archive->status === 'Musnah') bg-red-100 text-red-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ $archive->status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('admin.archives.show', $archive) }}"
                                                   class="text-blue-600 hover:text-blue-900">Lihat</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-inbox text-gray-400 text-4xl mb-4"></i>
                            <p class="text-gray-500">Belum ada arsip dalam rak ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Rack data from PHP
            const rack = @json($rack);
            const archives = @json($archives ?? []);

            // Calculate box data
            function calculateBoxData() {
                const boxData = {};
                const capacity = rack.capacity_per_box;
                const n = capacity;
                const halfN = n / 2;

                // Group archives by box
                archives.forEach(archive => {
                    const boxNumber = archive.box_number;
                    if (!boxData[boxNumber]) {
                        boxData[boxNumber] = {
                            box_number: boxNumber,
                            row_number: Math.ceil(boxNumber / 4),
                            archive_count: 0,
                            capacity: capacity,
                            archives: []
                        };
                    }
                    boxData[boxNumber].archive_count++;
                    boxData[boxNumber].archives.push(archive);
                });

                // Calculate status for each box
                Object.values(boxData).forEach(box => {
                    if (box.archive_count >= n) {
                        box.status = 'full';
                        box.statusClass = 'bg-red-100 border-red-200 text-red-600';
                        box.statusText = 'Penuh';
                    } else if (box.archive_count >= halfN) {
                        box.status = 'partially_full';
                        box.statusClass = 'bg-yellow-100 border-yellow-200 text-yellow-600';
                        box.statusText = 'Sebagian';
                    } else {
                        box.status = 'available';
                        box.statusClass = 'bg-green-100 border-green-200 text-green-600';
                        box.statusText = 'Tersedia';
                    }
                });

                return boxData;
            }

            function updateVisualGrid() {
                const visualGrid = document.getElementById('visual_grid');
                const boxData = calculateBoxData();

                if (!rack) {
                    visualGrid.innerHTML = '<p class="text-gray-500 text-center">Data rak tidak tersedia</p>';
                    return;
                }

                let gridHTML = `
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-900 mb-3">${rack.name}</h4>
                        <div class="grid grid-cols-4 gap-2">
                `;

                // Get actual boxes for this rack from the database
                const rackBoxes = @json($rack->boxes->pluck('box_number')->sort()->values());
                const usedBoxNumbers = Object.keys(boxData).map(Number).sort((a, b) => a - b);
                const allBoxNumbers = [];

                // Add all boxes that belong to this rack
                rackBoxes.forEach(boxNum => allBoxNumbers.push(boxNum));

                // Sort the boxes
                allBoxNumbers.sort((a, b) => a - b);

                // Create grid
                allBoxNumbers.forEach(boxNumber => {
                    const box = boxData[boxNumber];

                    if (box) {
                        // Box has archives
                        gridHTML += `
                            <div class="${box.statusClass} border rounded p-2 text-center text-xs">
                                <div class="font-semibold">Box ${box.box_number}</div>
                                <div class="${box.statusClass.includes('text-green') ? 'text-green-600' : box.statusClass.includes('text-red') ? 'text-red-600' : 'text-yellow-600'}">${box.statusText}</div>
                                <div class="text-xs text-gray-500">${box.archive_count}/${box.capacity}</div>
                            </div>
                        `;
                    } else {
                        // Empty box
                        gridHTML += `
                            <div class="bg-green-100 border border-green-200 rounded p-2 text-center text-xs">
                                <div class="font-semibold">Box ${boxNumber}</div>
                                <div class="text-green-600">Tersedia</div>
                                <div class="text-xs text-gray-500">0/${rack.capacity_per_box}</div>
                            </div>
                        `;
                    }
                });

                gridHTML += '</div></div>';
                visualGrid.innerHTML = gridHTML;
            }

            // Initialize on page load
            document.addEventListener('DOMContentLoaded', function() {
                updateVisualGrid();

                // Show success message if exists
                @if (session('success'))
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: '{{ session('success') }}',
                        showConfirmButton: false,
                        timer: 1500
                    });
                @endif
            });
        </script>
    @endpush
</x-app-layout>
