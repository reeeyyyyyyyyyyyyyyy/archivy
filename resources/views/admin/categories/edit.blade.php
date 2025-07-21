<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Category') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-semibold mb-4">Edit Category</h2>

                    {{-- Display validation errors if any --}}
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.categories.update', $category) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        {{-- Removed 'code' field as per user request and database schema --}}
                        {{-- <div class="mb-4">
                            <label for="code" class="block text-sm font-medium text-gray-700">Code</label>
                            <input type="text" name="code" id="code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('code', $category->code) }}" required>
                            @error('code')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                        </div> --}}

                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" name="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('name', $category->name) }}" required>
                            @error('name')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="retention_active" class="block text-sm font-medium text-gray-700">Retensi Aktif (Tahun)</label>
                            <input type="number" name="retention_active" id="retention_active" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('retention_active', $category->retention_active) }}" required>
                            @error('retention_active')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="retention_inactive" class="block text-sm font-medium text-gray-700">Retensi Inaktif (Tahun)</label>
                            <input type="number" name="retention_inactive" id="retention_inactive" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('retention_inactive', $category->retention_inactive) }}" required>
                            @error('retention_inactive')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="nasib_akhir" class="block text-sm font-medium text-gray-700">Nasib Akhir</label>
                            <select name="nasib_akhir" id="nasib_akhir" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                <option value="Musnah" {{ old('nasib_akhir', $category->nasib_akhir) == 'Musnah' ? 'selected' : '' }}>Musnah</option>
                                <option value="Permanen" {{ old('nasib_akhir', $category->nasib_akhir) == 'Permanen' ? 'selected' : '' }}>Permanen</option>
                                <option value="Dinilai Kembali" {{ old('nasib_akhir', $category->nasib_akhir) == 'Dinilai Kembali' ? 'selected' : '' }}>Dinilai Kembali</option>
                            </select>
                            @error('nasib_akhir')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                        </div>

                        <div>
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Update</button>
                            <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 bg-gray-300 text-black rounded-md hover:bg-gray-400">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>