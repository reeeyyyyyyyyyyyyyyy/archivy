<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Classifications') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-2xl font-semibold">Classifications</h2>
                        <a href="{{ route('admin.classifications.create') }}" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">Add Classification</a>
                    </div>

                    @if (session('success'))
                        <div class="mb-4 px-4 py-2 bg-green-100 text-green-800 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b">Code</th>
                                <th class="py-2 px-4 border-b">Name</th>
                                <th class="py-2 px-4 border-b">Category</th>
                                <th class="py-2 px-4 border-b">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($classifications as $classification)
                                <tr>
                                    <td class="py-2 px-4 border-b">{{ $classification->code }}</td>
                                    <td class="py-2 px-4 border-b">{{ $classification->name }}</td>
                                    <td class="py-2 px-4 border-b">{{ $classification->category->name ?? 'N/A' }}</td>
                                    <td class="py-2 px-4 border-b">
                                        <a href="{{ route('admin.classifications.edit', $classification) }}" class="px-2 py-1 bg-blue-500 text-white rounded-md hover:bg-blue-600">Edit</a>
                                        <form action="{{ route('admin.classifications.destroy', $classification) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2 py-1 bg-red-500 text-white rounded-md hover:bg-red-600" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 px-4 text-center">No classifications found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $classifications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 