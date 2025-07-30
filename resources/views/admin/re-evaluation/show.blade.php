<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Arsip Dinilai Kembali') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if(session('success'))
                        <x-success-message>{{ session('success') }}</x-success-message>
                    @endif

                    <!-- Archive Information -->
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-800">Informasi Arsip</h3>
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.re-evaluation.index') }}"
                                   class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-400 focus:outline-none focus:border-gray-600 focus:ring focus:ring-gray-200 active:bg-gray-600 disabled:opacity-25 transition ease-in-out duration-150">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Kembali
                                </a>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Arsip</label>
                                    <p class="text-lg font-semibold text-gray-900">{{ $archive->index_number }}</p>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Saat Ini</label>
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                        {{ $archive->status }}
                                    </span>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Arsip</label>
                                    <p class="text-sm text-gray-900">{{ $archive->kurun_waktu_start->format('d F Y') }}</p>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                                    <p class="text-sm text-gray-900">{{ $archive->category->nama_kategori }}</p>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Klasifikasi</label>
                                    <p class="text-sm text-gray-900">{{ $archive->classification->nama_klasifikasi }}</p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Uraian</label>
                                    <p class="text-sm text-gray-900">{{ $archive->description }}</p>
                                </div>

                                @if($archive->lampiran_surat)
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Lampiran Surat</label>
                                        <p class="text-sm text-gray-900">{{ $archive->lampiran_surat }}</p>
                                    </div>
                                @endif

                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">SKKAD</label>
                                    <p class="text-sm text-gray-900">{{ $archive->skkad }}</p>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Berkas</label>
                                    <p class="text-sm text-gray-900">{{ $archive->jumlah_berkas }} berkas</p>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tingkat Perkembangan</label>
                                    <p class="text-sm text-gray-900">{{ $archive->tingkat_perkembangan }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Storage Location -->
                        <div class="mt-6 bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi Penyimpanan</label>
                            <p class="text-sm text-gray-900">{{ $archive->storage_location }}</p>
                        </div>

                        <!-- Creation Info -->
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Dibuat Oleh</label>
                                <p class="text-sm text-gray-900">{{ $archive->createdByUser->name }}</p>
                                <p class="text-xs text-gray-500">{{ $archive->created_at->format('d F Y, H:i') }}</p>
                            </div>

                            @if($archive->updatedByUser)
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Diperbarui Oleh</label>
                                    <p class="text-sm text-gray-900">{{ $archive->updatedByUser->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $archive->updated_at->format('d F Y, H:i') }}</p>
                                </div>
                            @endif
                        </div>

                        @if($archive->ket)
                            <div class="mt-6 bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                                <p class="text-sm text-gray-900 whitespace-pre-line">{{ $archive->ket }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Status Change Form -->
                    <div class="border-t pt-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-6">Ubah Status Arsip</h3>

                        <form method="POST" action="{{ route('admin.re-evaluation.update-status', $archive->id) }}" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-orange-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-medium text-orange-800">
                                            Peringatan
                                        </h4>
                                        <div class="mt-2 text-sm text-orange-700">
                                            <p>Perubahan status arsip ini akan dicatat dalam log sistem dan tidak dapat dibatalkan. Pastikan status yang dipilih sudah sesuai.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="new_status" :value="__('Status Baru')" />
                                    <select id="new_status"
                                            name="new_status"
                                            required
                                            class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="">-- Pilih Status Baru --</option>
                                        <option value="Aktif" {{ old('new_status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="Inaktif" {{ old('new_status') == 'Inaktif' ? 'selected' : '' }}>Inaktif</option>
                                        <option value="Permanen" {{ old('new_status') == 'Permanen' ? 'selected' : '' }}>Permanen</option>
                                        <option value="Musnah" {{ old('new_status') == 'Musnah' ? 'selected' : '' }}>Musnah</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('new_status')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="remarks" :value="__('Keterangan (Opsional)')" />
                                    <textarea id="remarks"
                                              name="remarks"
                                              rows="3"
                                              placeholder="Alasan perubahan status atau keterangan tambahan"
                                              class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('remarks') }}</textarea>
                                    <x-input-error :messages="$errors->get('remarks')" class="mt-2" />
                                </div>
                            </div>

                            <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                                <x-secondary-button type="button" onclick="window.history.back()">
                                    {{ __('Batal') }}
                                </x-secondary-button>
                                <x-primary-button onclick="return confirm('Apakah Anda yakin ingin mengubah status arsip ini?')">
                                    {{ __('Ubah Status') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
