<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users-cog text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-900">Manajemen Role & Permissions</h2>
                    <p class="text-sm text-gray-600 mt-1">
                        <i class="fas fa-shield-alt mr-1"></i>Kelola role dan pengguna sistem
                    </p>
                </div>
            </div>

            <!-- Action Buttons - Lebih Prominent -->
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('admin.users.create') }}"
                   class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-user-plus mr-2"></i>
                    Buat User Baru
                </a>
                <a href="{{ route('admin.roles.create') }}"
                   class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-shield-plus mr-2"></i>
                    Buat Role Baru
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Compact Stats Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-white border border-purple-200 rounded-xl p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-user-tag text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ $roleStats['total_roles'] }}</div>
                            <div class="text-sm text-gray-600">Total Roles</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-green-200 rounded-xl p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-users text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ $roleStats['total_users'] }}</div>
                            <div class="text-sm text-gray-600">Total Users</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-blue-200 rounded-xl p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-key text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ $roleStats['total_permissions'] }}</div>
                            <div class="text-sm text-gray-600">Permissions</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-orange-200 rounded-xl p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-user-check text-orange-600 text-xl"></i>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ $roleStats['users_with_roles'] }}</div>
                            <div class="text-sm text-gray-600">Active Users</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">

                <!-- Roles Management -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-user-tag mr-2 text-purple-600"></i>
                            Daftar Role
                        </h3>
                            <span class="text-sm bg-purple-100 text-purple-700 px-3 py-1 rounded-full">{{ $roles->count() }} roles</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($roles as $role)
                                @php
                                    $roleConfig = match($role->name) {
                                        'admin' => ['color' => 'red', 'icon' => 'fas fa-crown', 'desc' => 'Full Access'],
                                        'staff' => ['color' => 'green', 'icon' => 'fas fa-user-tie', 'desc' => 'Staff Access'],
                                        'intern' => ['color' => 'orange', 'icon' => 'fas fa-graduation-cap', 'desc' => 'Intern Access'],
                                        default => ['color' => 'purple', 'icon' => 'fas fa-user-cog', 'desc' => 'Custom Role']
                                    };
                                @endphp

                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:shadow-md transition-all">
                                        <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-{{ $roleConfig['color'] }}-100 rounded-lg flex items-center justify-center">
                                            <i class="{{ $roleConfig['icon'] }} text-{{ $roleConfig['color'] }}-600"></i>
                                            </div>
                                            <div>
                                            <h4 class="font-semibold text-gray-900 capitalize">{{ $role->name }}</h4>
                                            <div class="flex items-center space-x-3 text-xs text-gray-500">
                                                <span><i class="fas fa-users mr-1"></i>{{ $role->users_count }} users</span>
                                                <span><i class="fas fa-key mr-1"></i>{{ $role->permissions_count }} permissions</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('admin.roles.show', $role) }}"
                                           class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors"
                                           title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                            </a>
                                            @if(!in_array($role->name, ['admin', 'staff', 'intern']))
                                            <button onclick="editRole({{ $role->id }}, '{{ $role->name }}')"
                                                    class="p-2 text-yellow-600 hover:bg-yellow-100 rounded-lg transition-colors"
                                                    title="Edit Role">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                                <button onclick="deleteRole({{ $role->id }}, '{{ $role->name }}')"
                                                    class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors"
                                                    title="Hapus Role">
                                                <i class="fas fa-trash"></i>
                                                </button>
                                            @else
                                            <span class="p-2 text-gray-400" title="Protected Role">
                                                <i class="fas fa-lock"></i>
                                                </span>
                                            @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Quick User-Role Assignment -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-user-plus mr-2 text-green-600"></i>
                            Quick Assign Role
                        </h3>
                    </div>
                    <div class="p-6">
                        <form id="assignRoleForm" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih User</label>
                                <select name="user_id" id="user_id" class="w-full border-gray-300 rounded-lg focus:border-green-500 focus:ring-green-500">
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
                                <select name="role_id" id="role_id" class="w-full border-gray-300 rounded-lg focus:border-green-500 focus:ring-green-500">
                                    <option value="">-- Pilih Role --</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit"
                                    class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors">
                                <i class="fas fa-user-check mr-2"></i>Assign Role
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- User Table - Compact -->
            <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-users-cog mr-2 text-blue-600"></i>
                            Daftar User & Role
                        </h3>
                        <div class="flex items-center space-x-3">
                            <button id="selectAllUsers" onclick="selectAllUsers()"
                                    class="px-3 py-1 bg-gray-600 hover:bg-gray-700 text-white text-xs font-medium rounded-lg transition-colors">
                                <i class="fas fa-check-square mr-1"></i>Pilih Semua
                            </button>
                            <button id="selectNoneUsers" onclick="selectNoneUsers()"
                                    class="px-3 py-1 bg-gray-400 hover:bg-gray-500 text-white text-xs font-medium rounded-lg transition-colors">
                                <i class="fas fa-square mr-1"></i>Batal Pilih
                            </button>
                            <button id="bulkRemoveRolesBtn" onclick="bulkRemoveRoles()" disabled
                                    class="px-4 py-2 bg-orange-600 hover:bg-orange-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-medium rounded-lg transition-colors">
                                <i class="fas fa-user-minus mr-2"></i>Remove Role Terpilih
                            </button>
                        </div>
                    </div>
                </div>
                    <div class="overflow-x-auto">
                    <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    <input type="checkbox" id="selectAllCheckbox" class="w-4 h-4 text-orange-600 bg-gray-100 border-gray-300 rounded focus:ring-orange-500 focus:ring-2">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                        <tbody class="divide-y divide-gray-200">
                                @foreach($users as $user)
                                    <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        @if($user->roles->count() > 0)
                                            <input type="checkbox" class="user-role-checkbox w-4 h-4 text-orange-600 bg-gray-100 border-gray-300 rounded focus:ring-orange-500 focus:ring-2"
                                                   value="{{ $user->id }}" data-user-name="{{ $user->name }}">
                                        @else
                                            <div class="w-4 h-4"></div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                                    <i class="fas fa-user text-gray-600 text-sm"></i>
                                                </div>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                            </div>
                                            </div>
                                        </td>
                                    <td class="px-6 py-4">
                                                @forelse($user->roles as $role)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 mr-1">
                                                        {{ ucfirst($role->name) }}
                                                    </span>
                                                @empty
                                            <span class="text-sm text-gray-400">No role</span>
                                                @endforelse
                                        </td>
                                    <td class="px-6 py-4">
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ route('admin.users.show', $user) }}"
                                               class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors" title="Lihat">
                                                <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.users.edit', $user) }}"
                                               class="p-2 text-yellow-600 hover:bg-yellow-100 rounded-lg transition-colors" title="Edit">
                                                <i class="fas fa-edit"></i>
                                                </a>
                                                @if($user->roles->count() > 0)
                                                    <button onclick="removeUserRole({{ $user->id }}, '{{ $user->name }}')"
                                                        class="p-2 text-orange-600 hover:bg-orange-100 rounded-lg transition-colors" title="Remove Role">
                                                    <i class="fas fa-user-minus"></i>
                                                    </button>
                                                @endif
                                                @if($user->id !== auth()->id())
                                                    <button onclick="deleteUser({{ $user->id }}, '{{ $user->name }}')"
                                                        class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors" title="Delete User">
                                                    <i class="fas fa-trash"></i>
                                                    </button>
                                                @else
                                                <span class="p-2 text-gray-400" title="Current User">
                                                    <i class="fas fa-user-shield"></i>
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

    <!-- Modal for Edit Role -->
    <div id="editRoleModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeEditModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full" id="editModalContent">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Edit Role</h3>
                        </div>
                <form id="editRoleForm" class="p-6">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editRoleId" name="role_id">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Role</label>
                        <input type="text" id="editRoleName" name="name" class="w-full border-gray-300 rounded-lg focus:border-purple-500 focus:ring-purple-500" readonly>
                        <p class="text-xs text-gray-500 mt-1">Nama role tidak dapat diubah</p>
                    </div>
                    <div class="flex items-center justify-end space-x-3">
                        <button type="button" onclick="closeEditModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Batal
                    </button>
                        <a id="editRoleLink" href="#" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg">
                            Edit Permissions
                        </a>
                </div>
                </form>
            </div>
        </div>
    </div>



    @push('scripts')
    <script>
        // Enhanced Notification System - Center Screen
        function showCenterNotification(message, type = 'success', duration = 3000) {
            // Remove existing notifications
            document.querySelectorAll('.center-notification').forEach(el => el.remove());

            const notification = document.createElement('div');
            notification.className = `center-notification fixed inset-0 z-50 flex items-center justify-center`;

            const iconClass = type === 'success' ? 'fa-check-circle' : (type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle');
            const bgClass = type === 'success' ? 'bg-green-500' : (type === 'error' ? 'bg-red-500' : 'bg-blue-500');

            notification.innerHTML = `
                <div class="bg-black bg-opacity-50 absolute inset-0"></div>
                <div class="${bgClass} text-white p-6 rounded-xl shadow-2xl transform scale-95 animate-pulse relative max-w-md mx-4">
                    <div class="flex items-center justify-center text-center">
                        <i class="fas ${iconClass} text-4xl mb-4"></i>
                    </div>
                    <div class="text-center">
                        <h3 class="text-lg font-semibold mb-2">${type === 'success' ? 'Berhasil!' : (type === 'error' ? 'Gagal!' : 'Informasi')}</h3>
                        <p>${message}</p>
                    </div>
                </div>
            `;

            document.body.appendChild(notification);

            // Auto remove
                setTimeout(() => {
                notification.style.opacity = '0';
                    setTimeout(() => notification.remove(), 300);
                }, duration);
        }

        // Edit Role Modal
        function editRole(roleId, roleName) {
            document.getElementById('editRoleId').value = roleId;
            document.getElementById('editRoleName').value = roleName;
            document.getElementById('editRoleLink').href = `/admin/roles/${roleId}/edit`;
            document.getElementById('editRoleModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editRoleModal').classList.add('hidden');
        }

        // Enhanced Delete Role with User Selection
        function deleteRole(roleId, roleName) {
            // First, check if role has users
            fetch(`/admin/roles/${roleId}/users`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.users && data.users.length > 0) {
                    showDeleteRoleWithUsersModal(roleId, roleName, data.users);
                } else {
                    showSimpleDeleteConfirmation(roleId, roleName);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showSimpleDeleteConfirmation(roleId, roleName);
            });
        }

        // Simple delete confirmation for roles without users
        function showSimpleDeleteConfirmation(roleId, roleName) {
            showAdvancedModal({
                title: 'Hapus Role',
                message: `Apakah Anda yakin ingin menghapus role "${roleName}"?`,
                type: 'delete',
                confirmText: 'Hapus Role',
                onConfirm: () => executeDeleteRole(roleId, roleName)
            });
        }

        // Delete role with users selection
        function showDeleteRoleWithUsersModal(roleId, roleName, users) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 z-50 flex items-center justify-center';
            modal.innerHTML = `
                <div class="bg-black bg-opacity-50 absolute inset-0" onclick="closeCustomModal()"></div>
                <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 relative">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Hapus Role: ${roleName}</h3>
                                <p class="text-sm text-gray-600">Role ini digunakan oleh ${users.length} user(s)</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <p class="text-gray-700 mb-4">Pilih user yang akan kehilangan role ini:</p>
                        <div class="max-h-32 overflow-y-auto space-y-2">
                            ${users.map(user => `
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded-lg cursor-pointer">
                                    <input type="checkbox" class="user-checkbox" value="${user.id}" checked>
                                    <div class="ml-3">
                                        <div class="font-medium text-gray-900">${user.name}</div>
                                        <div class="text-sm text-gray-500">${user.email}</div>
                                    </div>
                                </label>
                            `).join('')}
                        </div>
                    </div>

                    <div class="p-6 border-t border-gray-200 flex items-center justify-end space-x-3">
                        <button onclick="closeCustomModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Batal
                        </button>
                        <button onclick="confirmDeleteRoleWithUsers(${roleId}, '${roleName}')" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">
                            Hapus Role
                        </button>
                    </div>
                </div>
            `;

            modal.id = 'customModal';
            document.body.appendChild(modal);
        }

        function closeCustomModal() {
            const modal = document.getElementById('customModal');
            if (modal) modal.remove();
        }

        function confirmDeleteRoleWithUsers(roleId, roleName) {
            const selectedUsers = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
            closeCustomModal();

            if (selectedUsers.length === 0) {
                showCenterNotification('Pilih minimal satu user', 'error');
                return;
            }

            executeDeleteRole(roleId, roleName, selectedUsers);
        }

        // Execute delete role
        function executeDeleteRole(roleId, roleName, affectedUsers = []) {
            fetch(`/admin/roles/${roleId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ affected_users: affectedUsers })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        // If not JSON, create a success response
                        return { success: true, message: `Role "${roleName}" berhasil dihapus!` };
                    }
                });
            })
            .then(data => {
                showCenterNotification(data.message || `Role "${roleName}" berhasil dihapus!`, 'success');
                setTimeout(() => location.reload(), 2000);
            })
            .catch(error => {
                console.error('Error:', error);
                showCenterNotification('Terjadi kesalahan saat menghapus role', 'error');
            });
        }

        // Enhanced Quick Assign Role Form
        document.getElementById('assignRoleForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const userId = formData.get('user_id');
            const roleId = formData.get('role_id');

            if (!userId || !roleId) {
                showCenterNotification('Pilih user dan role terlebih dahulu', 'error');
                return;
            }

            const userSelect = document.getElementById('user_id');
            const roleSelect = document.getElementById('role_id');
            const userName = userSelect.options[userSelect.selectedIndex].text;
            const roleName = roleSelect.options[roleSelect.selectedIndex].text;

            // Show loading
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
            submitBtn.disabled = true;

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
                    showCenterNotification(`Role "${roleName.split('(')[0].trim()}" berhasil di-assign ke "${userName.split('(')[0].trim()}"`, 'success');
                    this.reset();
                    setTimeout(() => location.reload(), 2000);
                        } else {
                    showCenterNotification(data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                showCenterNotification('Terjadi kesalahan saat assign role', 'error');
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });

        // Advanced Modal System
        function showAdvancedModal({title, message, type = 'info', confirmText = 'OK', cancelText = 'Batal', onConfirm = null, onCancel = null}) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 z-50 flex items-center justify-center';

            const iconConfig = {
                'success': { icon: 'fa-check-circle', color: 'text-green-600', bg: 'bg-green-100' },
                'error': { icon: 'fa-exclamation-circle', color: 'text-red-600', bg: 'bg-red-100' },
                'warning': { icon: 'fa-exclamation-triangle', color: 'text-yellow-600', bg: 'bg-yellow-100' },
                'delete': { icon: 'fa-trash', color: 'text-red-600', bg: 'bg-red-100' },
                'info': { icon: 'fa-info-circle', color: 'text-blue-600', bg: 'bg-blue-100' }
            };

            const config = iconConfig[type] || iconConfig['info'];

            modal.innerHTML = `
                <div class="bg-black bg-opacity-50 absolute inset-0"></div>
                <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 relative">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="w-12 h-12 ${config.bg} rounded-lg flex items-center justify-center mr-4">
                                <i class="fas ${config.icon} ${config.color} text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">${title}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <p class="text-gray-700">${message}</p>
                    </div>

                    <div class="p-6 border-t border-gray-200 flex items-center justify-end space-x-3">
                        <button onclick="closeAdvancedModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            ${cancelText}
                        </button>
                        <button onclick="confirmAdvancedModal()" class="px-4 py-2 ${type === 'delete' ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-600 hover:bg-blue-700'} text-white rounded-lg">
                            ${confirmText}
                        </button>
                    </div>
                </div>
            `;

            modal.id = 'advancedModal';
            document.body.appendChild(modal);

            // Store callbacks
            window.currentModalCallbacks = { onConfirm, onCancel };
        }

        function closeAdvancedModal() {
            const modal = document.getElementById('advancedModal');
            if (modal) {
                if (window.currentModalCallbacks?.onCancel) {
                    window.currentModalCallbacks.onCancel();
                }
                modal.remove();
            }
        }

        function confirmAdvancedModal() {
            const modal = document.getElementById('advancedModal');
            if (modal) {
                if (window.currentModalCallbacks?.onConfirm) {
                    window.currentModalCallbacks.onConfirm();
                }
                modal.remove();
            }
        }

        // Remove User Role
        function removeUserRole(userId, userName) {
            // First, get user's roles
            fetch(`/admin/roles/user/${userId}/roles`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.roles && data.roles.length > 0) {
                    showRemoveRoleModal(userId, userName, data.roles);
                } else {
                    showCenterNotification(`User "${userName}" tidak memiliki role`, 'warning');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showCenterNotification('Terjadi kesalahan saat mengambil data role', 'error');
            });
        }

        // Show modal for role selection
        function showRemoveRoleModal(userId, userName, roles) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 z-50 flex items-center justify-center';
            modal.innerHTML = `
                <div class="bg-black bg-opacity-50 absolute inset-0" onclick="closeRemoveRoleModal()"></div>
                <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 relative">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-user-minus text-orange-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Remove Role dari User</h3>
                                <p class="text-sm text-gray-600">${userName}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <p class="text-gray-700 mb-4">Pilih role yang ingin dihapus:</p>
                        <div class="space-y-3">
                            ${roles.map(role => `
                                <label class="flex items-center p-3 hover:bg-gray-50 rounded-lg cursor-pointer border border-gray-200">
                                    <input type="checkbox" class="role-remove-checkbox w-4 h-4 text-orange-600 bg-gray-100 border-gray-300 rounded focus:ring-orange-500 focus:ring-2" value="${role.id}" checked>
                                    <div class="ml-3">
                                        <div class="font-medium text-gray-900 capitalize">${role.name}</div>
                                        <div class="text-sm text-gray-500">${role.permissions_count || 0} permissions</div>
                                    </div>
                                </label>
                            `).join('')}
                        </div>
                    </div>

                    <div class="p-6 border-t border-gray-200 flex items-center justify-end space-x-3">
                        <button onclick="closeRemoveRoleModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Batal
                        </button>
                        <button onclick="confirmRemoveUserRoles(${userId}, '${userName}')" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg">
                            Remove Role
                        </button>
                    </div>
                </div>
            `;

            modal.id = 'removeRoleModal';
            document.body.appendChild(modal);
        }

        function closeRemoveRoleModal() {
            const modal = document.getElementById('removeRoleModal');
            if (modal) modal.remove();
        }

        function confirmRemoveUserRoles(userId, userName) {
            const selectedRoles = Array.from(document.querySelectorAll('.role-remove-checkbox:checked')).map(cb => cb.value);
            closeRemoveRoleModal();

            if (selectedRoles.length === 0) {
                showCenterNotification('Pilih minimal satu role untuk dihapus', 'error');
                return;
            }

            executeRemoveUserRoles(userId, userName, selectedRoles);
        }

        function executeRemoveUserRoles(userId, userName, roleIds) {
            fetch(`/admin/roles/remove-user-roles`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    user_id: userId,
                    role_ids: roleIds
                })
            })
            .then(response => response.json())
            .then(data => {
                showCenterNotification(data.message || `Role berhasil dihapus dari "${userName}"`, 'success');
                setTimeout(() => location.reload(), 2000);
            })
            .catch(error => {
                console.error('Error:', error);
                showCenterNotification('Terjadi kesalahan saat menghapus role', 'error');
            });
        }

        // Delete User
        function deleteUser(userId, userName) {
            showAdvancedModal({
                title: 'Delete User',
                message: `Hapus user "${userName}"? User akan dihapus permanen dari sistem.`,
                type: 'delete',
                confirmText: 'Hapus User',
                onConfirm: () => {
                    fetch(`/admin/users/${userId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        showCenterNotification(data.message, data.success ? 'success' : 'error');
                        if (data.success) {
                            setTimeout(() => location.reload(), 2000);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showCenterNotification('Terjadi kesalahan saat menghapus user', 'error');
                    });
                }
            });
        }

        // Bulk Delete Roles
        function bulkDeleteRoles() {
            const selectedRoles = Array.from(document.querySelectorAll('.role-checkbox:checked')).map(cb => cb.value);
            if (selectedRoles.length === 0) {
                showCenterNotification('Pilih minimal satu role untuk dihapus', 'error');
                return;
            }

            showAdvancedModal({
                title: 'Konfirmasi Hapus Bulk',
                message: `Apakah Anda yakin ingin menghapus ${selectedRoles.length} role(s) terpilih?`,
                type: 'delete',
                confirmText: 'Hapus Role',
                onConfirm: () => {
                    executeBulkDeleteRoles(selectedRoles);
                }
            });
        }

        function executeBulkDeleteRoles(roleIds) {
            fetch(`/admin/roles/bulk-destroy`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ role_ids: roleIds })
            })
            .then(response => response.json())
            .then(data => {
                showCenterNotification(data.message || `Role berhasil dihapus!`, 'success');
                setTimeout(() => location.reload(), 2000);
            })
            .catch(error => {
                console.error('Error:', error);
                showCenterNotification('Terjadi kesalahan saat menghapus role', 'error');
            });
        }

        // Role checkbox management
        document.addEventListener('DOMContentLoaded', function() {
            const roleCheckboxes = document.querySelectorAll('.role-checkbox');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            const selectAllBtn = document.getElementById('selectAllRoles');
            const selectNoneBtn = document.getElementById('selectNoneRoles');

            // Add event listeners to checkboxes
            roleCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateBulkDeleteButton);
            });

            function updateBulkDeleteButton() {
                const checkedRoles = document.querySelectorAll('.role-checkbox:checked');
                bulkDeleteBtn.disabled = checkedRoles.length === 0;

                if (checkedRoles.length > 0) {
                    bulkDeleteBtn.innerHTML = `<i class="fas fa-trash mr-2"></i>Hapus ${checkedRoles.length} Role`;
                } else {
                    bulkDeleteBtn.innerHTML = `<i class="fas fa-trash mr-2"></i>Hapus Terpilih`;
                }
            }

            // Select All Roles
            selectAllBtn.addEventListener('click', function() {
                roleCheckboxes.forEach(checkbox => {
                    checkbox.checked = true;
                });
                updateBulkDeleteButton();
            });

            // Select None Roles
            selectNoneBtn.addEventListener('click', function() {
                roleCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                updateBulkDeleteButton();
            });
        });

        // User Role checkbox management
        document.addEventListener('DOMContentLoaded', function() {
            const userRoleCheckboxes = document.querySelectorAll('.user-role-checkbox');
            const bulkRemoveRolesBtn = document.getElementById('bulkRemoveRolesBtn');
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');

            // Add event listeners to checkboxes
            userRoleCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateBulkRemoveRolesButton);
            });

            // Select all checkbox in header
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    const checked = this.checked;
                    userRoleCheckboxes.forEach(checkbox => {
                        checkbox.checked = checked;
                    });
                    updateBulkRemoveRolesButton();
                });
            }

            function updateBulkRemoveRolesButton() {
                const checkedUsers = document.querySelectorAll('.user-role-checkbox:checked');
                bulkRemoveRolesBtn.disabled = checkedUsers.length === 0;

                if (checkedUsers.length > 0) {
                    bulkRemoveRolesBtn.innerHTML = `<i class="fas fa-user-minus mr-2"></i>Remove Role ${checkedUsers.length} User`;
                } else {
                    bulkRemoveRolesBtn.innerHTML = `<i class="fas fa-user-minus mr-2"></i>Remove Role Terpilih`;
                }

                // Update select all checkbox state
                if (selectAllCheckbox) {
                    const allCheckboxes = document.querySelectorAll('.user-role-checkbox');
                    const checkedCheckboxes = document.querySelectorAll('.user-role-checkbox:checked');

                    if (allCheckboxes.length === 0) {
                        selectAllCheckbox.checked = false;
                        selectAllCheckbox.indeterminate = false;
                    } else if (checkedCheckboxes.length === allCheckboxes.length) {
                        selectAllCheckbox.checked = true;
                        selectAllCheckbox.indeterminate = false;
                    } else if (checkedCheckboxes.length > 0) {
                        selectAllCheckbox.checked = false;
                        selectAllCheckbox.indeterminate = true;
                    } else {
                        selectAllCheckbox.checked = false;
                        selectAllCheckbox.indeterminate = false;
                    }
                }
            }
        });

        // Global functions for onclick handlers
        function selectAllUsers() {
            document.querySelectorAll('.user-role-checkbox').forEach(checkbox => {
                checkbox.checked = true;
            });
            updateBulkRemoveRolesButton();
        }

        function selectNoneUsers() {
            document.querySelectorAll('.user-role-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            updateBulkRemoveRolesButton();
        }

        function updateBulkRemoveRolesButton() {
            const checkedUsers = document.querySelectorAll('.user-role-checkbox:checked');
            const bulkRemoveRolesBtn = document.getElementById('bulkRemoveRolesBtn');
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');

            if (bulkRemoveRolesBtn) {
                bulkRemoveRolesBtn.disabled = checkedUsers.length === 0;

                if (checkedUsers.length > 0) {
                    bulkRemoveRolesBtn.innerHTML = `<i class="fas fa-user-minus mr-2"></i>Remove Role ${checkedUsers.length} User`;
                } else {
                    bulkRemoveRolesBtn.innerHTML = `<i class="fas fa-user-minus mr-2"></i>Remove Role Terpilih`;
                }
            }

            // Update select all checkbox state
            if (selectAllCheckbox) {
                const allCheckboxes = document.querySelectorAll('.user-role-checkbox');
                const checkedCheckboxes = document.querySelectorAll('.user-role-checkbox:checked');

                if (allCheckboxes.length === 0) {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = false;
                } else if (checkedCheckboxes.length === allCheckboxes.length) {
                    selectAllCheckbox.checked = true;
                    selectAllCheckbox.indeterminate = false;
                } else if (checkedCheckboxes.length > 0) {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = true;
                } else {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = false;
                }
            }
        }

        // Bulk Remove Roles
        function bulkRemoveRoles() {
            const selectedUsers = Array.from(document.querySelectorAll('.user-role-checkbox:checked')).map(cb => cb.value);
            if (selectedUsers.length === 0) {
                showCenterNotification('Pilih minimal satu user untuk remove role', 'error');
                return;
            }

            showAdvancedModal({
                title: 'Konfirmasi Remove Role',
                message: `Apakah Anda yakin ingin menghapus role dari ${selectedUsers.length} user(s) terpilih?`,
                type: 'warning',
                confirmText: 'Remove Role',
                onConfirm: () => {
                    executeBulkRemoveRoles(selectedUsers);
                }
            });
        }

        function executeBulkRemoveRoles(userIds) {
            fetch(`/admin/roles/bulk-remove-users`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ user_ids: userIds })
            })
            .then(response => response.json())
            .then(data => {
                showCenterNotification(data.message || `Role berhasil dihapus dari ${userIds.length} user!`, 'success');
                setTimeout(() => location.reload(), 2000);
            })
            .catch(error => {
                console.error('Error:', error);
                showCenterNotification('Terjadi kesalahan saat remove role', 'error');
            });
        }
    </script>
    @endpush
</x-app-layout>
