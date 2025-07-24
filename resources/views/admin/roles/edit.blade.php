<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-edit text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-900">
                        Edit Role: {{ ucfirst($role->name) }}
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        <i class="fas fa-shield-alt mr-1"></i>Ubah permissions untuk role {{ $role->name }}
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
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <form action="{{ route('admin.roles.update', $role) }}" method="POST" class="space-y-8">
                @csrf
                @method('PUT')
                
                <!-- Role Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">
                        <i class="fas fa-user-tag mr-2 text-purple-600"></i>
                        Informasi Role
                    </h3>
                    
                    <div class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Role
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ $role->name }}"
                                   readonly
                                   class="w-full border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed">
                            <p class="text-gray-500 text-sm mt-1">
                                <i class="fas fa-info-circle mr-1"></i>
                                Nama role tidak dapat diubah. Hanya permissions yang dapat dimodifikasi.
                            </p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                                <div class="text-center">
                                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                                        <i class="fas fa-users text-purple-600 text-xl"></i>
                                    </div>
                                    <div class="text-2xl font-bold text-purple-900">{{ $role->users_count ?? 0 }}</div>
                                    <div class="text-sm text-purple-700">Users dengan role ini</div>
                                </div>
                            </div>
                            
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="text-center">
                                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                                        <i class="fas fa-key text-blue-600 text-xl"></i>
                                    </div>
                                    <div class="text-2xl font-bold text-blue-900">{{ $role->permissions->count() }}</div>
                                    <div class="text-sm text-blue-700">Current Permissions</div>
                                </div>
                            </div>
                            
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <div class="text-center">
                                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                                        <i class="fas fa-clock text-green-600 text-xl"></i>
                                    </div>
                                    <div class="text-lg font-bold text-green-900">{{ $role->created_at->format('M Y') }}</div>
                                    <div class="text-sm text-green-700">Dibuat</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permissions Management -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-key mr-2 text-blue-600"></i>
                            Permissions Management
                        </h3>
                        <div class="flex items-center space-x-4">
                            <button type="button" onclick="selectAll()" class="text-sm bg-green-100 text-green-700 px-3 py-1 rounded-full hover:bg-green-200 transition-colors">
                                <i class="fas fa-check-double mr-1"></i>Select All
                            </button>
                            <button type="button" onclick="deselectAll()" class="text-sm bg-red-100 text-red-700 px-3 py-1 rounded-full hover:bg-red-200 transition-colors">
                                <i class="fas fa-times mr-1"></i>Deselect All
                            </button>
                        </div>
                    </div>
                    
                    @if($permissions->count() > 0)
                        <div class="space-y-6">
                            @php
                                $groupedPermissions = $permissions->groupBy(function($permission) {
                                    return explode('.', $permission->name)[0];
                                });
                                $rolePermissions = $role->permissions->pluck('id')->toArray();
                            @endphp
                            
                            @foreach($groupedPermissions as $group => $perms)
                                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                                @switch($group)
                                                    @case('archive')
                                                        <i class="fas fa-archive text-blue-600"></i>
                                                        @break
                                                    @case('category')
                                                        <i class="fas fa-folder text-blue-600"></i>
                                                        @break
                                                    @case('classification')
                                                        <i class="fas fa-tags text-blue-600"></i>
                                                        @break
                                                    @case('user')
                                                        <i class="fas fa-users text-blue-600"></i>
                                                        @break
                                                    @case('role')
                                                        <i class="fas fa-user-cog text-blue-600"></i>
                                                        @break
                                                    @default
                                                        <i class="fas fa-cog text-blue-600"></i>
                                                @endswitch
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-gray-900 capitalize text-lg">
                                                    {{ ucfirst($group) }} Management
                                                </h4>
                                                <p class="text-sm text-gray-500">{{ $perms->count() }} permissions available</p>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center space-x-2">
                                            <button type="button" 
                                                    onclick="toggleGroup('{{ $group }}')" 
                                                    class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded-full hover:bg-blue-200 transition-colors">
                                                <i class="fas fa-toggle-on mr-1"></i>Toggle Group
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach($perms as $permission)
                                            <label class="flex items-start space-x-3 p-3 hover:bg-gray-50 rounded-lg cursor-pointer border-2 transition-all {{ in_array($permission->id, $rolePermissions) ? 'border-purple-200 bg-purple-50' : 'border-transparent' }}">
                                                <input type="checkbox" 
                                                       name="permissions[]" 
                                                       value="{{ $permission->id }}"
                                                       {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}
                                                       class="mt-1 rounded border-gray-300 text-purple-600 focus:border-purple-500 focus:ring-purple-500 permission-checkbox"
                                                       data-group="{{ $group }}">
                                                <div class="flex-1">
                                                    <div class="font-medium text-gray-900 text-sm">
                                                        {{ str_replace($group . '.', '', $permission->name) }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        {{ $permission->name }}
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-500">
                            <i class="fas fa-exclamation-triangle text-4xl mb-4 text-gray-300"></i>
                            <p class="text-lg">Tidak ada permissions yang tersedia</p>
                            <p class="text-sm mt-2">Jalankan seeder untuk membuat permissions</p>
                        </div>
                    @endif
                    
                    @error('permissions')
                        <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-red-700 text-sm">
                                <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                            </p>
                        </div>
                    @enderror
                </div>

                <!-- Save Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                            Pastikan untuk review permissions sebelum menyimpan. Perubahan akan berlaku untuk semua user dengan role ini.
                        </div>
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('admin.roles.index') }}" 
                               class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                <i class="fas fa-times mr-2"></i>Batal
                            </a>
                            <button type="submit" 
                                    class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-colors">
                                <i class="fas fa-save mr-2"></i>Update Permissions
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // Select All Permissions
        function selectAll() {
            const checkboxes = document.querySelectorAll('.permission-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
                updateCheckboxStyle(checkbox);
            });
        }

        // Deselect All Permissions
        function deselectAll() {
            const checkboxes = document.querySelectorAll('.permission-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
                updateCheckboxStyle(checkbox);
            });
        }

        // Toggle Group Permissions
        function toggleGroup(groupName) {
            const groupCheckboxes = document.querySelectorAll(`[data-group="${groupName}"]`);
            const allChecked = Array.from(groupCheckboxes).every(cb => cb.checked);
            
            groupCheckboxes.forEach(checkbox => {
                checkbox.checked = !allChecked;
                updateCheckboxStyle(checkbox);
            });
        }

        // Update checkbox styling
        function updateCheckboxStyle(checkbox) {
            const label = checkbox.closest('label');
            if (checkbox.checked) {
                label.classList.add('border-purple-200', 'bg-purple-50');
                label.classList.remove('border-transparent');
            } else {
                label.classList.remove('border-purple-200', 'bg-purple-50');
                label.classList.add('border-transparent');
            }
        }

        // Initialize checkbox styling on page load
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.permission-checkbox');
            checkboxes.forEach(checkbox => {
                updateCheckboxStyle(checkbox);
                
                // Add event listener for checkbox changes
                checkbox.addEventListener('change', function() {
                    updateCheckboxStyle(this);
                });
            });
        });

        // Show notification
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg text-white ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
                    <span>${message}</span>
                </div>
            `;
            document.body.appendChild(notification);
            setTimeout(() => notification.remove(), 5000);
        }

        // Form submission with confirmation
        document.querySelector('form').addEventListener('submit', function(e) {
            const selectedPermissions = document.querySelectorAll('.permission-checkbox:checked').length;
            
            if (selectedPermissions === 0) {
                e.preventDefault();
                showNotification('Harap pilih minimal 1 permission untuk role ini', 'error');
                return;
            }

            // Show loading state
            const submitBtn = document.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';
            submitBtn.disabled = true;

            // Reset button after 5 seconds (in case of error)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 5000);
        });
    </script>
    @endpush
</x-app-layout> 