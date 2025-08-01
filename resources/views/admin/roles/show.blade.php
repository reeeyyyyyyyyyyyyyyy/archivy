<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    @php
                        $roleConfig = match($role->name) {
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
                        };
                    @endphp

                    <div class="w-12 h-12 {{ $roleConfig['bg'] }} rounded-xl flex items-center justify-center">
                        <i class="{{ $roleConfig['icon'] }} text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900 capitalize">
                            Role: {{ $role->name }}
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-users mr-1"></i>{{ $role->users->count() }} users memiliki role ini
                            <span class="mx-2">•</span>
                            <i class="fas fa-key mr-1"></i>{{ $role->permissions->count() }} permissions aktif
                            <span class="mx-2">•</span>
                            <i class="fas fa-calendar mr-1"></i>{{ $role->created_at->format('d F Y') }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.roles.edit', $role) }}"
                        class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-semibold rounded-lg transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Role
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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Role Overview -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">

                <!-- Role Info Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="text-center">
                        <div class="w-20 h-20 {{ $roleConfig['bg'] }} rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="{{ $roleConfig['icon'] }} text-white text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 capitalize mb-2">{{ $role->name }}</h3>
                        <div class="space-y-2">
                            <div class="flex items-center justify-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $roleConfig['badge'] }}">
                                    <i class="fas fa-shield-check mr-1"></i>
                                    {{ ucfirst($role->name) }} Role
                                </span>
                            </div>
                            @if(in_array($role->name, ['admin', 'staff', 'intern']))
                                <div class="flex items-center justify-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-lock mr-1"></i>
                                        System Protected
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-3xl font-bold">{{ $role->users->count() }}</div>
                                <div class="text-blue-100">Active Users</div>
                            </div>
                            <i class="fas fa-users text-4xl text-blue-200"></i>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-3xl font-bold">{{ $role->permissions->count() }}</div>
                                <div class="text-green-100">Permissions</div>
                            </div>
                            <i class="fas fa-key text-4xl text-green-200"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users with this Role -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <!-- Users List -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-users mr-2 {{ $roleConfig['color'] }}"></i>
                            Users dengan Role {{ ucfirst($role->name) }}
                        </h3>
                    </div>
                    <div class="p-6">
                        @if($role->users->count() > 0)
                            <div class="space-y-4">
                                @foreach($role->users as $user)
                                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 {{ $roleConfig['light_bg'] }} rounded-full flex items-center justify-center">
                                                <i class="fas fa-user {{ $roleConfig['color'] }}"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                        <div class="text-sm text-gray-400">
                                            <i class="fas fa-calendar mr-1"></i>
                                            {{ $user->created_at->format('M Y') }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-users text-4xl mb-4 text-gray-300"></i>
                                <p>Belum ada user yang memiliki role ini</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Permissions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-key mr-2 {{ $roleConfig['color'] }}"></i>
                            Permissions
                        </h3>
                    </div>
                    <div class="p-6">
                        @if($role->permissions->count() > 0)
                            @php
                                $groupedPermissions = $role->permissions->groupBy(function($permission) {
                                    return explode('.', $permission->name)[0];
                                });
                            @endphp

                            <div class="space-y-4">
                                @foreach($groupedPermissions as $group => $permissions)
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <h4 class="font-medium text-gray-900 mb-3 capitalize">
                                            <i class="fas fa-folder mr-2 {{ $roleConfig['color'] }}"></i>
                                            {{ $group }} Permissions
                                        </h4>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($permissions as $permission)
                                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium {{ $roleConfig['badge'] }}">
                                                    {{ $permission->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-key text-4xl mb-4 text-gray-300"></i>
                                <p>Role ini belum memiliki permissions</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
