<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Rak: {{ $rack->name }}
            </h2>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.storage-management.edit', $rack) }}"
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <a href="{{ route('admin.storage-management.index') }}"
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Rack Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Rak</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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
                    </div>

                    @if($rack->description)
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                            <div class="text-sm font-medium text-gray-600">Deskripsi</div>
                            <div class="text-gray-900">{{ $rack->description }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Preview Grid -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Preview Grid</h3>
                    <div class="grid grid-cols-4 gap-2">
                        @for($row = 1; $row <= $rack->total_rows; $row++)
                            @for($boxInRow = 1; $boxInRow <= 4; $boxInRow++)
                                @php
                                    $boxNumber = ($row - 1) * 4 + $boxInRow;
                                    $box = $rack->boxes->where('box_number', $boxNumber)->first();
                                    $status = $box ? $box->status : 'available';
                                    $archiveCount = $box ? $box->archive_count : 0;
                                    $capacity = $box ? $box->capacity : $rack->capacity_per_box;

                                    $statusClass = match($status) {
                                        'full' => 'bg-red-100 border-red-200 text-red-600',
                                        'partially_full' => 'bg-yellow-100 border-yellow-200 text-yellow-600',
                                        default => 'bg-green-100 border-green-200 text-green-600'
                                    };

                                    $statusText = match($status) {
                                        'full' => 'Penuh',
                                        'partially_full' => 'Sebagian',
                                        default => 'Tersedia'
                                    };
                                @endphp
                                <div class="{{ $statusClass }} border rounded p-2 text-center text-xs">
                                    <div class="font-semibold">Box {{ $boxNumber }}</div>
                                    <div class="{{ $statusClass.includes('text-green') ? 'text-green-600' : ($statusClass.includes('text-red') ? 'text-red-600' : 'text-yellow-600') }}">{{ $statusText }}</div>
                                    <div class="text-xs text-gray-500">{{ $archiveCount }}/{{ $capacity }}</div>
                                </div>
                            @endfor
                        @endfor
                    </div>
                </div>
            </div>

            <!-- Archives in this Rack -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Arsip dalam Rak Ini</h3>

                    @php
                        $archives = \App\Models\Archive::where('rack_number', $rack->id)
                            ->with(['category', 'classification', 'user'])
                            ->orderBy('box_number')
                            ->orderBy('file_number')
                            ->get();
                    @endphp

                    @if($archives->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Arsip</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uraian</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($archives as $index => $archive)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $archive->nomor_arsip }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">{{ $archive->uraian }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                Rak {{ $archive->rack_number }}, Baris {{ $archive->row_number }}, Box {{ $archive->box_number }}, File {{ $archive->file_number }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                    @if($archive->status === 'Aktif') bg-green-100 text-green-800
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
</x-app-layout>
