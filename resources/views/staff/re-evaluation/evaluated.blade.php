<x-app-layout>
    {{-- Header --}}
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-teal-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-check-circle text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Arsip Sudah Dievaluasi</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Staff: Lihat detail arsip yang sudah dievaluasi
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('staff.re-evaluation.index') }}"
                        class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Statistik --}}
            @if ($archives->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-gradient-to-r from-teal-500 to-teal-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-teal-100 text-sm font-medium">Total Dievaluasi</p>
                                <p class="text-3xl font-bold">{{ $archives->total() }}</p>
                            </div>
                            <div
                                class="w-12 h-12 bg-teal-400 bg-opacity-30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-check-circle text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-emerald-100 text-sm font-medium">Status Aktif</p>
                                <p class="text-3xl font-bold">
                                    {{ $archives->where('status', 'Aktif')->count() }}
                                </p>
                            </div>
                            <div
                                class="w-12 h-12 bg-emerald-400 bg-opacity-30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-play-circle text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-amber-100 text-sm font-medium">Status Inaktif</p>
                                <p class="text-3xl font-bold">
                                    {{ $archives->where('status', 'Inaktif')->count() }}
                                </p>
                            </div>
                            <div
                                class="w-12 h-12 bg-amber-400 bg-opacity-30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-pause-circle text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-indigo-100 text-sm font-medium">Status Permanen</p>
                                <p class="text-3xl font-bold">
                                    {{ $archives->where('status', 'Permanen')->count() }}
                                </p>
                            </div>
                            <div
                                class="w-12 h-12 bg-indigo-400 bg-opacity-30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-archive text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-rose-500 to-rose-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-rose-100 text-sm font-medium">Status Musnah</p>
                                <p class="text-3xl font-bold">
                                    {{ $archives->where('status', 'Musnah')->count() }}
                                </p>
                            </div>
                            <div
                                class="w-12 h-12 bg-rose-400 bg-opacity-30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-trash text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Tabel Arsip --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-list mr-2 text-teal-500"></i>
                        Daftar Arsip Sudah Dievaluasi
                    </h3>
                </div>

                @if ($archives->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        No. Arsip
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Uraian
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal Evaluasi
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Catatan Evaluasi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($archives as $archive)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $archive->index_number }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ Str::limit($archive->description, 50) }}
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
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$archive->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ $archive->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $archive->updated_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 max-w-xs">
                                            <div class="bg-gray-50 p-2 rounded text-xs">
                                                {{ $archive->evaluation_notes ?? 'Tidak ada catatan' }}
                                            </div>
                                        </td>
                                        {{-- <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('staff.re-evaluation.show', $archive) }}"
                                                class="text-teal-600 hover:text-teal-900 mr-3">
                                                <i class="fas fa-eye"></i> Lihat
                                            </a>
                                        </td> --}}
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $archives->links() }}
                    </div>
                @else
                    <div class="p-6 text-center">
                        <div class="text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-4"></i>
                            <p class="text-lg font-medium">Tidak ada arsip yang sudah dievaluasi</p>
                            <p class="text-sm">Belum ada arsip yang telah melalui proses evaluasi.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
