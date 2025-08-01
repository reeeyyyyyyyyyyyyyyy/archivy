<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Set Lokasi Penyimpanan Arsip') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Archive Information -->
                    <div class="mb-8 p-6 bg-gray-50 rounded-lg border">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Arsip</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nomor Arsip</label>
                                <p class="mt-1 text-sm text-gray-900 font-medium">{{ $archive->index_number }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal Arsip</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $archive->kurun_waktu_start->format('d/m/Y') }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Uraian</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $archive->description }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kategori</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $archive->category->nama_kategori }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Klasifikasi</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $archive->classification->nama_klasifikasi }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Storage Location Form -->
                    <form method="POST" action="{{ route(auth()->user()->isAdmin() ? 'admin.storage.store' : (auth()->user()->isStaff() ? 'staff.storage.store' : 'intern.storage.store'), $archive->id) }}" class="space-y-6">
                        @csrf

                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Set Lokasi Penyimpanan</h3>
                            <p class="text-sm text-gray-600 mb-6">
                                Isi informasi lokasi penyimpanan untuk arsip ini. Nomor file akan ditentukan otomatis berdasarkan nomor box yang dipilih.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Box Number -->
                            <div>
                                <x-input-label for="box_number" :value="__('Nomor Box')" />
                                <x-text-input id="box_number"
                                            class="block mt-1 w-full"
                                            type="number"
                                            name="box_number"
                                            :value="old('box_number', $nextBoxNumber)"
                                            required
                                            min="1"
                                            oninput="updateFileNumber()" />
                                <x-input-error :messages="$errors->get('box_number')" class="mt-2" />
                                <p class="mt-1 text-xs text-gray-500">
                                    Disarankan: Box {{ $nextBoxNumber }} (Box baru)
                                </p>
                            </div>

                            <!-- File Number Display -->
                            <div>
                                <x-input-label :value="__('Nomor File (Otomatis)')" />
                                <div id="file_number_display"
                                     class="block mt-1 w-full px-3 py-2 border border-gray-300 bg-gray-100 rounded-md shadow-sm text-gray-700">
                                    Loading...
                                </div>
                                <p class="mt-1 text-xs text-gray-500">
                                    Nomor file akan ditentukan otomatis berdasarkan box yang dipilih
                                </p>
                            </div>

                            <!-- Rack Number -->
                            <div>
                                <x-input-label for="rack_number" :value="__('Nomor Rak')" />
                                <x-text-input id="rack_number"
                                            class="block mt-1 w-full"
                                            type="number"
                                            name="rack_number"
                                            :value="old('rack_number')"
                                            required
                                            min="1" />
                                <x-input-error :messages="$errors->get('rack_number')" class="mt-2" />
                                <p class="mt-1 text-xs text-gray-500">
                                    Contoh: 1, 2, 3, dst.
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Row Number -->
                            <div>
                                <x-input-label for="row_number" :value="__('Nomor Baris')" />
                                <x-text-input id="row_number"
                                            class="block mt-1 w-full"
                                            type="number"
                                            name="row_number"
                                            :value="old('row_number')"
                                            required
                                            min="1" />
                                <x-input-error :messages="$errors->get('row_number')" class="mt-2" />
                                <p class="mt-1 text-xs text-gray-500">
                                    Contoh: 1, 2, 3, dst. (dari atas ke bawah)
                                </p>
                            </div>

                            <!-- Box Contents Info -->
                            <div id="box_contents_info" class="hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Isi Box Saat Ini</label>
                                <div class="bg-blue-50 border border-blue-200 rounded-md p-3 max-h-32 overflow-y-auto">
                                    <div id="box_contents_list" class="text-xs text-gray-700">
                                        <!-- Will be populated by JavaScript -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                            <x-secondary-button type="button" onclick="window.history.back()">
                                {{ __('Batal') }}
                            </x-secondary-button>
                            <x-primary-button>
                                {{ __('Simpan Lokasi Penyimpanan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for dynamic file number and box contents -->
    <script>
        let currentBoxNumber = {{ $nextBoxNumber }};

        function updateFileNumber() {
            const boxNumber = document.getElementById('box_number').value;
            const fileNumberDisplay = document.getElementById('file_number_display');
            const boxContentsInfo = document.getElementById('box_contents_info');
            const boxContentsList = document.getElementById('box_contents_list');

            if (boxNumber && boxNumber > 0) {
                // Get suggested file number
                fetch(`{{ route(auth()->user()->isAdmin() ? 'admin.storage.box.next-file' : (auth()->user()->isStaff() ? 'staff.storage.box.next-file' : 'intern.storage.box.next-file'), '') }}/${boxNumber}`)
                    .then(response => response.json())
                    .then(data => {
                        fileNumberDisplay.textContent = data.next_file_number;
                    })
                    .catch(error => {
                        fileNumberDisplay.textContent = '1';
                        console.error('Error:', error);
                    });

                // Get box contents if box exists
                fetch(`{{ route(auth()->user()->isAdmin() ? 'admin.storage.box.contents' : (auth()->user()->isStaff() ? 'staff.storage.box.contents' : 'intern.storage.box.contents'), '') }}/${boxNumber}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            boxContentsInfo.classList.remove('hidden');
                            boxContentsList.innerHTML = data.map(archive =>
                                `<div class="mb-1">File ${archive.file_number}: ${archive.index_number} - ${archive.description.substring(0, 40)}${archive.description.length > 40 ? '...' : ''}</div>`
                            ).join('');
                        } else {
                            boxContentsInfo.classList.add('hidden');
                        }
                    })
                    .catch(error => {
                        boxContentsInfo.classList.add('hidden');
                        console.error('Error:', error);
                    });
            } else {
                fileNumberDisplay.textContent = '1';
                boxContentsInfo.classList.add('hidden');
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateFileNumber();
        });
    </script>
</x-app-layout>
