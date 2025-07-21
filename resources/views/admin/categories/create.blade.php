<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Category') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-semibold mb-4">Create New Category</h2>

                    <form action="{{ route('admin.categories.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" name="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('name') }}" required>
                            @error('name')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="retention_active" class="block text-sm font-medium text-gray-700">Retensi Aktif (Tahun)</label>
                            <input type="number" name="retention_active" id="retention_active" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('retention_active', 0) }}" required>
                            @error('retention_active')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="retention_inactive" class="block text-sm font-medium text-gray-700">Retensi Inaktif (Tahun)</label>
                            <input type="number" name="retention_inactive" id="retention_inactive" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('retention_inactive', 0) }}" required>
                            @error('retention_inactive')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="nasib_akhir" class="block text-sm font-medium text-gray-700">Nasib Akhir</label>
                            <select name="nasib_akhir" id="nasib_akhir" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                <option value="Musnah" {{ old('nasib_akhir') == 'Musnah' ? 'selected' : '' }}>Musnah</option>
                                <option value="Permanen" {{ old('nasib_akhir') == 'Permanen' ? 'selected' : '' }}>Permanen</option>
                                <option value="Dinilai Kembali" {{ old('nasib_akhir') == 'Dinilai Kembali' ? 'selected' : '' }}>Dinilai Kembali</option>
                            </select>
                            @error('nasib_akhir')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                        </div>

                        <div>
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-black rounded-md hover:bg-blue-600">Create</button>
                            <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 bg-gray-300 text-black rounded-md hover:bg-gray-400">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#classification_id').select2({
                theme: "classic"
            });

            $('#classification_id').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const code = selectedOption.data('code');
                const retentionActive = selectedOption.data('retention-active');
                const retentionInactive = selectedOption.data('retention-inactive');

                $('#code').val(code);
                $('#retention_active').val(retentionActive);
                $('#retention_inactive').val(retentionInactive);
            });
        });
    </script>
    @endpush
</x-app-layout> 