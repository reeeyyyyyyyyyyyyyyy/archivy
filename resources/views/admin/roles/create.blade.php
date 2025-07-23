<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-plus text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-900">
                        Buat Role Baru
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        <i class="fas fa-shield-alt mr-1"></i>Buat role baru dengan permissions khusus
                    </p>
                </div>
            </div>
            <div class="text-right">
                <a href="{{ route('admin.roles.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <form action="{{ route('admin.roles.store') }}" method="POST" class="space-y-8">
                @csrf
                
                <!-- Role Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">
                        <i class="fas fa-user-tag mr-2 text-purple-600"></i>
                        Informasi Role
                    </h3>
                    
                    <div class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Role <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name') }}"
                                   placeholder="e.g. manager, supervisor, etc"
                                   class="w-full border-gray-300 rounded-lg focus:border-purple-500 focus:ring-purple-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-gray-500 text-sm mt-1">
                                <i class="fas fa-info-circle mr-1"></i>
                                Nama role akan otomatis diubah ke lowercase
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Permissions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">
                        <i class="fas fa-key mr-2 text-blue-600"></i>
                        Permissions
                    </h3>
                    
                    @if($permissions->count() > 0)
                        <div class="space-y-4">
                            @php
                                $groupedPermissions = $permissions->groupBy(function($permission) {
                                    return explode('.', $permission->name)[0];
                                });
                            @endphp
                            
                            @foreach($groupedPermissions as $group => $perms)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="font-medium text-gray-900 capitalize">
                                            <i class="fas fa-folder mr-2 text-gray-500"></i>
                                            {{ ucfirst($group) }}
                                        </h4>
                                        <span class="text-xs text-gray-500">{{ $perms->count() }} permissions</span>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                        @foreach($perms as $permission)
                                            <label class="flex items-center space-x-2 p-2 hover:bg-gray-50 rounded-md cursor-pointer">
                                                <input type="checkbox" 
                                                       name="permissions[]" 
                                                       value="{{ $permission->id }}"
                                                       {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}
                                                       class="rounded border-gray-300 text-purple-600 focus:border-purple-500 focus:ring-purple-500">
                                                <span class="text-sm text-gray-700 flex-1">
                                                    {{ str_replace($group . '.', '', $permission->name) }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-exclamation-triangle text-4xl mb-4 text-gray-300"></i>
                            <p>Tidak ada permissions yang tersedia</p>
                            <p class="text-sm mt-2">Jalankan seeder untuk membuat permissions</p>
                        </div>
                    @endif
                    
                    @error('permissions')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit -->
                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('admin.roles.index') }}" 
                       class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Buat Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout> 