<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Debugging: Check if $archives is empty or not --}}
                    @if($archives->isEmpty())
                        <p>No archives found for "{{ $title }}".</p>
                        {{-- Debugging: Dump $archives to see its content if it's unexpectedly empty --}}
                        {{-- @php dd($archives); @endphp --}}
                    @else
                        {{-- Debugging: Dump the first archive to check its attributes if it exists --}}
                        {{-- @php dd($archives->first()); @endphp --}}
                    @endif

                    <div class="flex justify-between items-center mb-4">
                        <a href="{{ route('admin.archives.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Create New Archive</a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Berkas</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uraian</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Klasifikasi</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Arsip</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($archives as $archive)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $archive->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $archive->index_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $archive->uraian }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $archive->category->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $archive->classification->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $archive->kurun_waktu_start->format('d-m-Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $archive->status }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('admin.archives.show', $archive) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                            <a href="{{ route('admin.archives.edit', $archive) }}" class="ml-2 text-indigo-600 hover:text-indigo-900">Edit</a>
                                            <form action="{{ route('admin.archives.destroy', $archive) }}" method="POST" class="inline-block ml-2">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this archive?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    {{-- This block will now only show if $archives is truly empty after dd() is commented out --}}
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">No archives found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $archives->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>