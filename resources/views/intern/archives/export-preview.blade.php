<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-600 to-pink-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-eye text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">
                            Preview Export Data Arsip
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Preview mode - Data yang akan diexport
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('intern.export.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Preview Info Card -->
            <div class="bg-gradient-to-r from-orange-50 to-pink-100 rounded-xl shadow-sm border border-orange-200 p-6 mb-6">
                <div class="flex items-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-orange-600 to-pink-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-eye text-white text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Preview Export Data Arsip {{ $statusTitle }}</h3>
                        <p class="text-gray-600 mt-1">Ini adalah preview data yang akan diexport (mode pembelajaran intern)</p>
                        <div class="flex items-center space-x-4 mt-2 text-sm">
                            <span class="bg-white px-3 py-1 rounded-full text-orange-600 font-medium">
                                <i class="fas fa-file-alt mr-1"></i>{{ $archives->count() }} Arsip
                            </span>
                            <span class="bg-white px-3 py-1 rounded-full text-pink-600 font-medium">
                                <i class="fas fa-user mr-1"></i>{{ auth()->user()->name }}
                            </span>
                            <span class="bg-white px-3 py-1 rounded-full text-orange-600 font-medium">
                                <i class="fas fa-calendar mr-1"></i>{{ now()->format('d M Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Summary -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-filter mr-2 text-orange-500"></i>Filter yang Diterapkan
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="text-sm text-gray-600">Status</div>
                        <div class="font-semibold text-gray-900">{{ $statusTitle }}</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="text-sm text-gray-600">Tahun Dari</div>
                        <div class="font-semibold text-gray-900">{{ $yearFrom ?: 'Semua' }}</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="text-sm text-gray-600">Tahun Sampai</div>
                        <div class="font-semibold text-gray-900">{{ $yearTo ?: 'Semua' }}</div>
                    </div>
                </div>
            </div>

            <!-- Data Preview Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-table mr-2 text-orange-500"></i>Preview Data Arsip
                        <span class="ml-2 text-sm text-gray-500">({{ $archives->count() }} arsip)</span>
                    </h3>
                </div>

                @if($archives->isEmpty())
                    <div class="text-center py-16">
                        <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak ada data untuk diexport</h3>
                        <p class="text-gray-500">Coba ubah filter atau pilih status yang berbeda</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Klasifikasi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Indeks</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uraian</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kurun Waktu</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tingkat Perkembangan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ket.</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Definitif</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Boks</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rak</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Baris</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jangka Simpan dan Nasib Akhir</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($archives as $index => $archive)
                                    @php
                                        // Format kurun waktu to only show year
                                        $kurunWaktu = $archive->kurun_waktu_start ? $archive->kurun_waktu_start->format('Y') : '';

                                        // Format jangka simpan - retensi aktif + inaktif
                                        $retentionAktif = $archive->classification->retention_aktif ?? 0;
                                        $retentionInaktif = $archive->classification->retention_inaktif ?? 0;
                                        $totalRetention = $retentionAktif + $retentionInaktif;
                                        $jangkaSimpan = $totalRetention . ' Tahun (' . ($archive->classification->nasib_akhir ?? 'Permanen') . ')';
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $archive->classification->code ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $archive->index_number ?? '-' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <div class="max-w-xs truncate" title="{{ $archive->description }}">
                                                {{ $archive->description ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $kurunWaktu }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $archive->tingkat_perkembangan ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $archive->jumlah_berkas ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $archive->description ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $archive->file_number ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $archive->box_number ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $archive->rack_number ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $archive->row_number ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $jangkaSimpan }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Preview Notice -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-6">
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-info-circle text-white text-sm"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-blue-900 mb-2">Preview Mode - Intern</h4>
                        <p class="text-blue-800 text-sm">
                            Ini adalah preview data yang akan diexport. Dalam mode intern, fitur export hanya untuk pembelajaran
                            dan tidak akan menghasilkan file Excel yang sebenarnya. Data di atas menunjukkan bagaimana hasil export
                            akan terlihat jika fitur ini digunakan oleh admin atau staff.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
