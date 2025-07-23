<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-yellow-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-edit text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-900">
                        Edit User: {{ $user->name }}
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        <i class="fas fa-envelope mr-1"></i>{{ $user->email }}
                        <span class="mx-2">•</span>
                        <i class="fas fa-calendar mr-1"></i>Bergabung {{ $user->created_at->format('d F Y') }}
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
            
            <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-8">
                @csrf
                @method('PUT')
                
                <!-- User Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">
                        <i class="fas fa-user mr-2 text-yellow-600"></i>
                        Informasi User
                    </h3>
                    
                    <div class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name', $user->name) }}"
                                   placeholder="e.g. John Doe"
                                   class="w-full border-gray-300 rounded-lg focus:border-yellow-500 focus:ring-yellow-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   value="{{ old('email', $user->email) }}"
                                   placeholder="e.g. john@example.com"
                                   class="w-full border-gray-300 rounded-lg focus:border-yellow-500 focus:ring-yellow-500 @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Password Baru (Opsional)
                                </label>
                                <input type="password" 
                                       name="password" 
                                       id="password" 
                                       placeholder="Kosongkan jika tidak ingin mengubah"
                                       class="w-full border-gray-300 rounded-lg focus:border-yellow-500 focus:ring-yellow-500 @error('password') border-red-500 @enderror">
                                @error('password')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                    Konfirmasi Password
                                </label>
                                <input type="password" 
                                       name="password_confirmation" 
                                       id="password_confirmation" 
                                       placeholder="Ulangi password baru"
                                       class="w-full border-gray-300 rounded-lg focus:border-yellow-500 focus:ring-yellow-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Role Assignment -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">
                        <i class="fas fa-shield-alt mr-2 text-purple-600"></i>
                        Update Role
                    </h3>
                    
                    <div class="space-y-4">
                        @foreach($roles as $role)
                            @php
                                $roleConfig = match($role->name) {
                                    'admin' => [
                                        'bg' => 'bg-red-50',
                                        'border' => 'border-red-200',
                                        'text' => 'text-red-700',
                                        'icon' => 'fas fa-crown',
                                        'desc' => 'Akses penuh ke semua fitur sistem'
                                    ],
                                    'staff' => [
                                        'bg' => 'bg-green-50',
                                        'border' => 'border-green-200',
                                        'text' => 'text-green-700',
                                        'icon' => 'fas fa-user-tie',
                                        'desc' => 'Akses lengkap untuk manajemen arsip dan analytics'
                                    ],
                                    'intern' => [
                                        'bg' => 'bg-orange-50',
                                        'border' => 'border-orange-200',
                                        'text' => 'text-orange-700',
                                        'icon' => 'fas fa-graduation-cap',
                                        'desc' => 'Akses terbatas untuk viewing dan export data'
                                    ],
                                    default => [
                                        'bg' => 'bg-purple-50',
                                        'border' => 'border-purple-200',
                                        'text' => 'text-purple-700',
                                        'icon' => 'fas fa-user-cog',
                                        'desc' => 'Role custom dengan permissions khusus'
                                    ]
                                };
                                
                                $isSelected = $user->hasRole($role->name) || old('role') === $role->name;
                            @endphp
                            
                            <label class="flex items-center p-4 border-2 {{ $roleConfig['border'] }} {{ $roleConfig['bg'] }} rounded-lg cursor-pointer hover:shadow-md transition-all {{ $isSelected ? 'ring-2 ring-blue-500' : '' }}">
                                <input type="radio" 
                                       name="role" 
                                       value="{{ $role->name }}" 
                                       {{ $isSelected ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="flex items-center space-x-4 w-full peer-checked:{{ $roleConfig['text'] }}">
                                    <div class="w-12 h-12 {{ $roleConfig['bg'] }} rounded-lg flex items-center justify-center peer-checked:bg-white">
                                        <i class="{{ $roleConfig['icon'] }} {{ $roleConfig['text'] }} text-xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-semibold {{ $roleConfig['text'] }} capitalize text-lg">{{ $role->name }}</div>
                                        <div class="text-sm text-gray-600">{{ $roleConfig['desc'] }}</div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $role->permissions_count ?? 0 }} permissions • {{ $role->users_count ?? 0 }} users
                                        </div>
                                    </div>
                                    <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:border-{{ $roleConfig['text'] }} peer-checked:bg-{{ $roleConfig['text'] }} flex items-center justify-center">
                                        <i class="fas fa-check text-white text-xs {{ $isSelected ? 'block' : 'hidden' }}"></i>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    
                    @error('role')
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
                            class="px-6 py-3 bg-yellow-600 hover:bg-yellow-700 text-white font-semibold rounded-lg transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout> 