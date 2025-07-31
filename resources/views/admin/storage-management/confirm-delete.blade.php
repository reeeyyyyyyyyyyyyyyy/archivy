<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Konfirmasi Hapus Rak
            </h2>
            <a href="{{ route('admin.storage-management.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Konfirmasi Penghapusan Rak
                        </h3>

                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">
                                        Peringatan
                                    </h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>Anda akan menghapus rak <strong>{{ $rack->name }}</strong> dengan detail:</p>
                                        <ul class="list-disc list-inside mt-2">
                                            <li>Total Baris: {{ $rack->total_rows }}</li>
                                            <li>Total Box: {{ $rack->total_boxes }}</li>
                                            <li>Kapasitas per Box: {{ $rack->capacity_per_box }}</li>
                                            <li>Status: {{ $rack->status }}</li>
                                        </ul>
                                        <p class="mt-2 font-semibold">Tindakan ini tidak dapat dibatalkan!</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('admin.storage-management.index') }}"
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors">
                                <i class="fas fa-times mr-2"></i>Batal
                            </a>
                            <button onclick="confirmDelete()"
                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition-colors">
                                <i class="fas fa-trash mr-2"></i>Hapus Rak
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete() {
            Swal.fire({
                title: 'Konfirmasi Penghapusan',
                text: `Anda yakin ingin menghapus rak "${@json($rack->name)}"? Tindakan ini tidak dapat dibatalkan!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create form and submit
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('admin.storage-management.destroy', $rack) }}';

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';

                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';

                    const confirmField = document.createElement('input');
                    confirmField.type = 'hidden';
                    confirmField.name = 'confirm_delete';
                    confirmField.value = '1';

                    form.appendChild(csrfToken);
                    form.appendChild(methodField);
                    form.appendChild(confirmField);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
    @endpush
</x-app-layout>
