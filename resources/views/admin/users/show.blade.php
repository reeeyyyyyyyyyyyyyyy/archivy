<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    @php
                        $userRole = $user->roles->first();
                        $roleConfig = $userRole ? match($userRole->name) {
                            'admin' => [
                                'bg' => 'bg-red-600',
                                'icon' => 'fas fa-crown',
                                'color' => 'text-red-600',
                                'light_bg' => 'bg-red-50',
                                'badge' => 'bg-red-100 text-red-800'
                            ],
                            'staff' => [
                                'bg' => 'bg-green-600',
                                'icon' => 'fas fa-user-tie',
                                'color' => 'text-green-600',
                                'light_bg' => 'bg-green-50',
                                'badge' => 'bg-green-100 text-green-800'
                            ],
                            'intern' => [
                                'bg' => 'bg-orange-600',
                                'icon' => 'fas fa-graduation-cap',
                                'color' => 'text-orange-600',
                                'light_bg' => 'bg-orange-50',
                                'badge' => 'bg-orange-100 text-orange-800'
                            ],
                            default => [
                                'bg' => 'bg-purple-600',
                                'icon' => 'fas fa-user-cog',
                                'color' => 'text-purple-600',
                                'light_bg' => 'bg-purple-50',
                                'badge' => 'bg-purple-100 text-purple-800'
                            ]
                        } : [
                            'bg' => 'bg-gray-600',
                            'icon' => 'fas fa-user',
                            'color' => 'text-gray-600',
                            'light_bg' => 'bg-gray-50',
                            'badge' => 'bg-gray-100 text-gray-800'
                        ];
                    @endphp

                    <div class="w-12 h-12 {{ $roleConfig['bg'] }} rounded-xl flex items-center justify-center">
                        <i class="{{ $roleConfig['icon'] }} text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">
                            {{ $user->name }}
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-envelope mr-1"></i>{{ $user->email }}
                            <span class="mx-2">•</span>
                            <i class="fas fa-calendar mr-1"></i>Bergabung {{ $user->created_at->format('d F Y') }}
                            @if($userRole)
                                <span class="mx-2">•</span>
                                <i class="fas fa-shield-alt mr-1"></i>{{ ucfirst($userRole->name) }}
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.users.edit', $user) }}"
                        class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-semibold rounded-lg transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Edit User
                    </a>
                    <a href="{{ route('admin.roles.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- User Profile Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
                <div class="p-8">
                    <div class="flex items-center space-x-6">
                        <div class="w-24 h-24 {{ $roleConfig['bg'] }} rounded-full flex items-center justify-center">
                            <i class="{{ $roleConfig['icon'] }} text-white text-4xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h3>
                            <p class="text-gray-600 mt-1">
                                <i class="fas fa-at mr-1"></i>{{ $user->username }}
                                <span class="mx-2">•</span>
                                <i class="fas fa-envelope mr-1"></i>{{ $user->email }}
                            </p>
                            <div class="flex items-center space-x-4 mt-4">
                                @if($userRole)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $roleConfig['badge'] }}">
                                        <i class="{{ $roleConfig['icon'] }} mr-1"></i>
                                        {{ ucfirst($userRole->name) }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        No Role Assigned
                                    </span>
                                @endif

                                <span class="text-sm text-gray-500">
                                    <i class="fas fa-calendar mr-1"></i>
                                    Bergabung {{ $user->created_at->format('d F Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Details Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <!-- Account Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-user-circle mr-2 {{ $roleConfig['color'] }}"></i>
                            Informasi Akun
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between py-3 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-500">Nama Lengkap</span>
                            <span class="text-sm text-gray-900">{{ $user->name }}</span>
                        </div>
                        <div class="flex items-center justify-between py-3 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-500">Username</span>
                            <span class="text-sm text-gray-900">{{ $user->username }}</span>
                        </div>
                        <div class="flex items-center justify-between py-3 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-500">Email</span>
                            <span class="text-sm text-gray-900">{{ $user->email }}</span>
                        </div>
                        <div class="flex items-center justify-between py-3 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-500">Status Email</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $user->email_verified_at ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <i class="fas {{ $user->email_verified_at ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                                {{ $user->email_verified_at ? 'Verified' : 'Unverified' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between py-3">
                            <span class="text-sm font-medium text-gray-500">Tanggal Bergabung</span>
                            <span class="text-sm text-gray-900">{{ $user->created_at->format('d F Y H:i') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Role & Permissions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-shield-alt mr-2 {{ $roleConfig['color'] }}"></i>
                            Role & Permissions
                        </h3>
                    </div>
                    <div class="p-6">
                        @if($user->roles->count() > 0)
                            <div class="space-y-4">
                                @foreach($user->roles as $role)
                                    @php
                                        $roleConfig = match($role->name) {
                                            'admin' => ['badge' => 'bg-red-100 text-red-800', 'icon' => 'fas fa-crown'],
                                            'staff' => ['badge' => 'bg-green-100 text-green-800', 'icon' => 'fas fa-user-tie'],
                                            'intern' => ['badge' => 'bg-orange-100 text-orange-800', 'icon' => 'fas fa-graduation-cap'],
                                            default => ['badge' => 'bg-purple-100 text-purple-800', 'icon' => 'fas fa-user-cog']
                                        };
                                    @endphp

                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $roleConfig['badge'] }}">
                                                <i class="{{ $roleConfig['icon'] }} mr-1"></i>
                                                {{ ucfirst($role->name) }}
                                            </span>
                                            <span class="text-xs text-gray-500">{{ $role->permissions->count() }} permissions</span>
                                        </div>

                                        @if($role->permissions->count() > 0)
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($role->permissions->take(6) as $permission)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-700">
                                                        {{ $permission->name }}
                                                    </span>
                                                @endforeach
                                                @if($role->permissions->count() > 6)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-200 text-gray-600">
                                                        +{{ $role->permissions->count() - 6 }} more
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <p class="text-sm text-gray-500">No specific permissions</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-exclamation-triangle text-4xl mb-4 text-gray-300"></i>
                                <p>User ini belum memiliki role apapun</p>
                                <p class="text-sm mt-2">Assign role untuk memberikan akses ke sistem</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
