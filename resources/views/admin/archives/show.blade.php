<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Arsip: {{ $archive->index_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm font-medium text-gray-700">Nomor Berkas:</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $archive->index_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700">Uraian Arsip:</p>
                            <p class="mt-1 text-gray-900">{{ $archive->uraian }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700">Kategori:</p>
                            <p class="mt-1 text-gray-900">{{ $archive->category->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700">Klasifikasi:</p>
                            <p class="mt-1 text-gray-900">{{ $archive->classification->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700">Tanggal Arsip (Kurun Waktu Start):</p>
                            <p class="mt-1 text-gray-900">{{ $archive->kurun_waktu_start->format('d-m-Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700">Tingkat Perkembangan:</p>
                            <p class="mt-1 text-gray-900">{{ $archive->tingkat_perkembangan }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700">Jumlah Berkas:</p>
                            <p class="mt-1 text-gray-900">{{ $archive->jumlah }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700">Keterangan:</p>
                            <p class="mt-1 text-gray-900">{{ $archive->ket ?? '-' }}</p>
                        </div>
                        <div class="col-span-2 border-t pt-4 mt-4">
                            <p class="text-sm font-medium text-gray-700">Informasi Retensi:</p>
                            <p class="mt-1 text-gray-900">
                                Retensi Aktif: {{ $archive->retention_active }} tahun <br>
                                Retensi Inaktif: {{ $archive->retention_inactive }} tahun <br>
                                Status Saat Ini: <span class="font-bold">{{ $archive->status }}</span>
                            </p>
                            <p class="mt-1 text-gray-900">
                                Transisi Aktif Due: {{ $archive->transition_active_due->format('d-m-Y') }} <br>
                                Transisi Inaktif Due: {{ $archive->transition_inactive_due->format('d-m-Y') }}
                            </p>
                        </div>
                        <div class="col-span-2 border-t pt-4 mt-4">
                            <p class="text-sm font-medium text-gray-700">Dibuat Oleh:</p>
                            {{-- Changed creator to createdByUser --}}
                            <p class="mt-1 text-gray-900">{{ $archive->createdByUser->name ?? 'N/A' }} on {{ $archive->created_at->format('d M Y H:i') }}</p>
                            
                            <p class="text-sm font-medium text-gray-700 mt-2">Terakhir Diperbarui Oleh:</p>
                            {{-- Changed updater to updatedByUser --}}
                            <p class="mt-1 text-gray-900">{{ $archive->updatedByUser->name ?? 'N/A' }} on {{ $archive->updated_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <a href="{{ route('admin.archives.edit', $archive) }}" class="px-4 py-2 bg-indigo-500 text-white rounded-md hover:bg-indigo-600">Edit Arsip</a>
                        <form action="{{ route('admin.archives.destroy', $archive) }}" method="POST" class="inline-block ml-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600" onclick="return confirm('Apakah Anda yakin ingin menghapus arsip ini?')">Hapus Arsip</button>
                        </form>
                        <a href="{{ route('admin.archives.index') }}" class="ml-2 px-4 py-2 bg-gray-300 text-black rounded-md hover:bg-gray-400">Kembali ke Daftar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>