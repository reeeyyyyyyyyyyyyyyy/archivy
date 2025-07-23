<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users-cog text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-900">
                        Manajemen Role & Permissions
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        <i class="fas fa-shield-alt mr-1"></i>Kelola role pengguna dan hak akses sistem
                        <span class="mx-2">•</span>
                        <i class="fas fa-users mr-1"></i>{{ $roleStats['total_users'] }} Users
                        <span class="mx-2">•</span>
                        <i class="fas fa-key mr-1"></i>{{ $roleStats['total_permissions'] }} Permissions
                    </p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.users.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors">
                    <i class="fas fa-user-plus mr-2"></i>
                    Buat User
                </a>
                <a href="{{ route('admin.roles.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Buat Role
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-2xl font-bold">{{ $roleStats['total_roles'] }}</div>
                            <div class="text-purple-100 text-sm">Total Roles</div>
                        </div>
                        <i class="fas fa-user-tag text-3xl text-purple-200"></i>
                    </div>
                </div>
                
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-2xl font-bold">{{ $roleStats['total_permissions'] }}</div>
                            <div class="text-blue-100 text-sm">Permissions</div>
                        </div>
                        <i class="fas fa-key text-3xl text-blue-200"></i>
                    </div>
                </div>
                
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-2xl font-bold">{{ $roleStats['total_users'] }}</div>
                            <div class="text-green-100 text-sm">Total Users</div>
                        </div>
                        <i class="fas fa-users text-3xl text-green-200"></i>
                    </div>
                </div>
                
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-2xl font-bold">{{ $roleStats['users_with_roles'] }}</div>
                            <div class="text-orange-100 text-sm">Users with Roles</div>
                        </div>
                        <i class="fas fa-user-check text-3xl text-orange-200"></i>
                    </div>
                </div>
            </div>

            <!-- Roles Management -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- Roles List -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-user-tag mr-2 text-purple-600"></i>
                            Daftar Role
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($roles as $role)
                                @php
                                    $roleConfig = match($role->name) {
                                        'admin' => [
                                            'bg' => 'bg-red-100',
                                            'border' => 'border-red-200',
                                            'icon_bg' => 'bg-red-500',
                                            'icon' => 'fas fa-crown',
                                            'text' => 'text-red-700',
                                            'badge' => 'bg-red-100 text-red-800'
                                        ],
                                        'staff' => [
                                            'bg' => 'bg-green-100',
                                            'border' => 'border-green-200',
                                            'icon_bg' => 'bg-green-500',
                                            'icon' => 'fas fa-user-tie',
                                            'text' => 'text-green-700',
                                            'badge' => 'bg-green-100 text-green-800'
                                        ],
                                        'intern' => [
                                            'bg' => 'bg-orange-100',
                                            'border' => 'border-orange-200',
                                            'icon_bg' => 'bg-orange-500',
                                            'icon' => 'fas fa-graduation-cap',
                                            'text' => 'text-orange-700',
                                            'badge' => 'bg-orange-100 text-orange-800'
                                        ],
                                        default => [
                                            'bg' => 'bg-purple-100',
                                            'border' => 'border-purple-200',
                                            'icon_bg' => 'bg-purple-500',
                                            'icon' => 'fas fa-user-cog',
                                            'text' => 'text-purple-700',
                                            'badge' => 'bg-purple-100 text-purple-800'
                                        ]
                                    };
                                @endphp
                                
                                <div class="border {{ $roleConfig['border'] }} {{ $roleConfig['bg'] }} rounded-lg p-4 hover:shadow-md transition-all duration-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-12 h-12 {{ $roleConfig['icon_bg'] }} rounded-lg flex items-center justify-center">
                                                <i class="{{ $roleConfig['icon'] }} text-white text-lg"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-semibold {{ $roleConfig['text'] }} capitalize text-lg">{{ $role->name }}</h4>
                                                <div class="flex items-center space-x-3 text-sm text-gray-600">
                                                    <span class="flex items-center">
                                                        <i class="fas fa-users mr-1"></i>
                                                        {{ $role->users_count }} users
                                                    </span>
                                                    <span class="flex items-center">
                                                        <i class="fas fa-key mr-1"></i>
                                                        {{ $role->permissions_count }} permissions
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('admin.roles.show', $role) }}" 
                                               class="px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm transition-colors">
                                                <i class="fas fa-eye mr-1"></i>Detail
                                            </a>
                                            @if(!in_array($role->name, ['admin', 'staff', 'intern']))
                                                <a href="{{ route('admin.roles.edit', $role) }}" 
                                                   class="px-3 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg text-sm transition-colors">
                                                    <i class="fas fa-edit mr-1"></i>Edit
                                                </a>
                                                <button onclick="deleteRole({{ $role->id }}, '{{ $role->name }}')"
                                                        class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm transition-colors">
                                                    <i class="fas fa-trash mr-1"></i>Hapus
                                                </button>
                                            @else
                                                <span class="px-3 py-2 bg-gray-100 text-gray-500 rounded-lg text-sm">
                                                    <i class="fas fa-lock mr-1"></i>Protected
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- User Role Assignment -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-user-plus mr-2 text-green-600"></i>
                            Assign Role ke User
                        </h3>
                    </div>
                    <div class="p-6">
                        <form id="assignRoleForm" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih User</label>
                                <select name="user_id" id="user_id" class="w-full border-gray-300 rounded-lg focus:border-purple-500 focus:ring-purple-500">
                                    <option value="">-- Pilih User --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Role</label>
                                <select name="role_id" id="role_id" class="w-full border-gray-300 rounded-lg focus:border-purple-500 focus:ring-purple-500">
                                    <option value="">-- Pilih Role --</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <button type="submit" 
                                    class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                                <i class="fas fa-plus mr-2"></i>Assign Role
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Current User-Role Assignments -->
            <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-users-cog mr-2 text-blue-600"></i>
                        Assignment User & Role Saat Ini
                    </h3>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roles</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($users as $user)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                                    <i class="fas fa-user text-gray-600 text-sm"></i>
                                                </div>
                                                <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $user->email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex flex-wrap gap-1">
                                                @forelse($user->roles as $role)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                        {{ ucfirst($role->name) }}
                                                    </span>
                                                @empty
                                                    <span class="text-sm text-gray-400">No roles assigned</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ route('admin.users.show', $user) }}" 
                                                   class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm transition-colors">
                                                    <i class="fas fa-eye mr-1"></i>Show
                                                </a>
                                                
                                                <a href="{{ route('admin.users.edit', $user) }}" 
                                                   class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg text-sm transition-colors">
                                                    <i class="fas fa-edit mr-1"></i>Edit
                                                </a>
                                                
                                                @if($user->roles->count() > 0)
                                                    <button onclick="removeUserRole({{ $user->id }}, '{{ $user->name }}')" 
                                                            class="px-3 py-1 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-sm transition-colors">
                                                        <i class="fas fa-user-minus mr-1"></i>Remove Role
                                                    </button>
                                                @endif
                                                
                                                @if($user->id !== auth()->id())
                                                    <button onclick="deleteUser({{ $user->id }}, '{{ $user->name }}')" 
                                                            class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm transition-colors">
                                                        <i class="fas fa-trash mr-1"></i>Delete
                                                    </button>
                                                @else
                                                    <span class="px-3 py-1 bg-gray-100 text-gray-500 rounded-lg text-sm">
                                                        <i class="fas fa-user-shield mr-1"></i>Current User
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Modal Notifications -->
    <div id="confirmModal" class="fixed inset-0 z-50 hidden">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
        
        <!-- Modal -->
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full transform transition-all scale-95 opacity-0" id="modalContent">
                <!-- Modal Header -->
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center mr-4" id="modalIcon">
                            <i class="text-2xl" id="modalIconClass"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Konfirmasi</h3>
                            <p class="text-sm text-gray-500" id="modalSubtitle">Apakah Anda yakin?</p>
                        </div>
                    </div>
                </div>
                
                <!-- Modal Body -->
                <div class="p-6">
                    <p class="text-gray-700" id="modalMessage">Konfirmasi tindakan Anda.</p>
                </div>
                
                <!-- Modal Footer -->
                <div class="p-6 border-t border-gray-200 flex items-center justify-end space-x-3">
                    <button onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button id="confirmButton" class="px-4 py-2 rounded-lg text-white font-semibold transition-colors">
                        Konfirmasi
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Custom Modal System
        function showModal(type, title, subtitle, message, onConfirm) {
            const modal = document.getElementById('confirmModal');
            const modalContent = document.getElementById('modalContent');
            const modalIcon = document.getElementById('modalIcon');
            const modalIconClass = document.getElementById('modalIconClass');
            const modalTitle = document.getElementById('modalTitle');
            const modalSubtitle = document.getElementById('modalSubtitle');
            const modalMessage = document.getElementById('modalMessage');
            const confirmButton = document.getElementById('confirmButton');
            
            // Configure modal based on type
            const config = {
                'assign': {
                    icon: 'fas fa-user-plus',
                    iconBg: 'bg-green-100',
                    iconColor: 'text-green-600',
                    buttonBg: 'bg-green-600 hover:bg-green-700'
                },
                'remove': {
                    icon: 'fas fa-user-minus',
                    iconBg: 'bg-orange-100',
                    iconColor: 'text-orange-600',
                    buttonBg: 'bg-orange-600 hover:bg-orange-700'
                },
                'delete': {
                    icon: 'fas fa-trash',
                    iconBg: 'bg-red-100',
                    iconColor: 'text-red-600',
                    buttonBg: 'bg-red-600 hover:bg-red-700'
                }
            };
            
            const typeConfig = config[type] || config['assign'];
            
            modalIcon.className = `w-12 h-12 rounded-full flex items-center justify-center mr-4 ${typeConfig.iconBg}`;
            modalIconClass.className = `text-2xl ${typeConfig.icon} ${typeConfig.iconColor}`;
            modalTitle.textContent = title;
            modalSubtitle.textContent = subtitle;
            modalMessage.textContent = message;
            confirmButton.className = `px-4 py-2 rounded-lg text-white font-semibold transition-colors ${typeConfig.buttonBg}`;
            
            // Set confirm action
            confirmButton.onclick = function() {
                closeModal();
                onConfirm();
            };
            
            // Show modal with animation
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }
        
        function closeModal() {
            const modal = document.getElementById('confirmModal');
            const modalContent = document.getElementById('modalContent');
            
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 200);
        }

        // Enhanced Notification system
        window.showNotification = function(message, type = 'info', duration = 5000) {
            // Remove existing notifications
            const existingNotifications = document.querySelectorAll('.notification');
            existingNotifications.forEach(notification => notification.remove());
            
            // Create new notification
            const notification = document.createElement('div');
            notification.className = `notification fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-xl transform transition-all duration-300 translate-x-full`;
            
            const colors = {
                'success': 'bg-green-500 text-white border-green-600',
                'error': 'bg-red-500 text-white border-red-600',
                'warning': 'bg-yellow-500 text-black border-yellow-600',
                'info': 'bg-blue-500 text-white border-blue-600'
            };
            
            const icons = {
                'success': 'fas fa-check-circle',
                'error': 'fas fa-exclamation-circle',
                'warning': 'fas fa-exclamation-triangle',
                'info': 'fas fa-info-circle'
            };
            
            notification.classList.add(...colors[type].split(' '));
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="${icons[type]} mr-3 text-lg"></i>
                    <span class="font-medium flex-1">${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-lg hover:opacity-75 focus:outline-none">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            // Auto remove
            if (duration > 0) {
                setTimeout(() => {
                    notification.classList.add('translate-x-full');
                    setTimeout(() => notification.remove(), 300);
                }, duration);
            }
        };

        // Assign Role Form with Modal
        document.getElementById('assignRoleForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const userId = formData.get('user_id');
            const roleId = formData.get('role_id');
            
            if (!userId || !roleId) {
                window.showNotification('Harap pilih user dan role!', 'warning');
                return;
            }
            
            const userSelect = document.getElementById('user_id');
            const roleSelect = document.getElementById('role_id');
            const userName = userSelect.options[userSelect.selectedIndex].text;
            const roleName = roleSelect.options[roleSelect.selectedIndex].text;
            
            showModal(
                'assign',
                'Assign Role',
                'Konfirmasi Assignment',
                `Assign role "${roleName}" kepada user "${userName}"?`,
                function() {
                    fetch('{{ route("admin.roles.assign-user") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.showNotification(data.message, 'success');
                            document.getElementById('assignRoleForm').reset();
                            setTimeout(() => location.reload(), 2000);
                        } else {
                            window.showNotification(data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        window.showNotification('Terjadi kesalahan saat assign role', 'error');
                    });
                }
            );
        });
        
        // Remove User Role with Modal
        function removeUserRole(userId, userName) {
            // Find user's roles from the table
            const tableRows = document.querySelectorAll('tbody tr');
            let userRoles = [];
            
            tableRows.forEach(row => {
                const nameCell = row.querySelector('td:first-child .font-medium');
                if (nameCell && nameCell.textContent.trim() === userName) {
                    const roleSpans = row.querySelectorAll('.bg-red-100, .bg-green-100, .bg-orange-100, .bg-purple-100');
                    roleSpans.forEach(span => {
                        const roleName = span.textContent.trim().toLowerCase();
                        userRoles.push(roleName);
                    });
                }
            });
            
            if (userRoles.length === 0) {
                window.showNotification('User tidak memiliki role untuk dicabut', 'warning');
                return;
            }
            
            const roleName = userRoles[0];
            
            showModal(
                'remove',
                'Remove Role',
                'Konfirmasi Pencabutan',
                `Cabut role "${roleName}" dari user "${userName}"?`,
                function() {
                    const roles = @json($roles->pluck('id', 'name'));
                    const roleId = roles[roleName];
                    
                    if (!roleId) {
                        window.showNotification('Role tidak ditemukan', 'error');
                        return;
                    }
                    
                    fetch('{{ route("admin.roles.remove-user") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            user_id: userId,
                            role_id: roleId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.showNotification(data.message, 'success');
                            setTimeout(() => location.reload(), 2000);
                        } else {
                            window.showNotification(data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        window.showNotification('Terjadi kesalahan saat mencabut role', 'error');
                    });
                }
            );
        }
        
        // Delete Role with Modal
        function deleteRole(roleId, roleName) {
            showModal(
                'delete',
                'Delete Role',
                'Konfirmasi Penghapusan',
                `Hapus role "${roleName}"? Semua user yang memiliki role ini akan kehilangan akses!`,
                function() {
                    fetch(`{{ route("admin.roles.index") }}/${roleId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => {
                        if (response.ok) {
                            window.showNotification(`Role "${roleName}" berhasil dihapus!`, 'success');
                            setTimeout(() => location.reload(), 2000);
                        } else {
                            return response.json().then(data => {
                                throw new Error(data.message || 'Terjadi kesalahan');
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        window.showNotification(`Gagal menghapus role: ${error.message}`, 'error');
                    });
                }
            );
        }
        
        // Delete User with Modal
        function deleteUser(userId, userName) {
            showModal(
                'delete',
                'Delete User',
                'Konfirmasi Penghapusan',
                `Hapus user "${userName}"? User akan dihapus permanen dari sistem!`,
                function() {
                    fetch(`{{ route("admin.users.destroy", ":userId") }}`.replace(':userId', userId), {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.showNotification(data.message, 'success');
                            setTimeout(() => location.reload(), 2000);
                        } else {
                            window.showNotification(data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        window.showNotification('Terjadi kesalahan saat menghapus user', 'error');
                    });
                }
            );
        }
        
        // Close modal on backdrop click
        document.getElementById('confirmModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
        
        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
    @endpush
</x-app-layout> 