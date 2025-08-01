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

            <form action="{{ route('admin.roles.update', $role) }}" method="POST" class="space-y-8" id="roleForm">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                        Informasi Role
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Role <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="name"
                                   id="name"
                                   value="{{ old('name', $role->name) }}"
                                   class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors py-3 px-4"
                                   placeholder="Masukkan nama role"
                                   {{ in_array($role->name, ['admin', 'staff', 'intern']) ? 'readonly' : '' }}>
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="guard_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Guard Name
                            </label>
                            <input type="text"
                                   name="guard_name"
                                   id="guard_name"
                                   value="{{ old('guard_name', $role->guard_name) }}"
                                   class="w-full bg-gray-100 border border-gray-300 rounded-xl shadow-sm py-3 px-4 text-gray-600"
                                   readonly>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between">
                    <a href="{{ route('admin.roles.index') }}"
                       class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                    <button type="submit"
                            class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-colors"
                            id="submitBtn">
                        <i class="fas fa-save mr-2"></i>
                        Update Role
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('roleForm');
            const submitBtn = document.getElementById('submitBtn');

            // Form validation with SWAL
            if (form && submitBtn) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Field validation
                    const requiredFields = ['name'];

                    if (!window.validateRequiredFields(requiredFields)) {
                        return;
                    }

                    // Check if at least one permission is selected
                    const selectedPermissions = document.querySelectorAll('input[name="permissions[]"]:checked');
                    if (selectedPermissions.length === 0) {
                        window.showValidationError('Mohon pilih minimal satu permission untuk role ini!');
                        return;
                    }

                    // Confirm update with SWAL
                    window.showUpdateConfirm('Apakah Anda yakin ingin mengubah role ini?')
                        .then((result) => {
                            if (result.isConfirmed) {
                                // Show loading state
                                submitBtn.disabled = true;
                                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengupdate Role...';

                                // Submit form
                                form.submit();
                            }
                        });
                });
            }

            // Permission toggle functionality
            const selectAllBtn = document.getElementById('selectAll');
            const deselectAllBtn = document.getElementById('deselectAll');
            const permissionCheckboxes = document.querySelectorAll('input[name="permissions[]"]');

            if (selectAllBtn) {
                selectAllBtn.addEventListener('click', function() {
                    permissionCheckboxes.forEach(checkbox => {
                        checkbox.checked = true;
                    });
                });
            }

            if (deselectAllBtn) {
                deselectAllBtn.addEventListener('click', function() {
                    permissionCheckboxes.forEach(checkbox => {
                        checkbox.checked = false;
                    });
                });
            }

            // Make sure checkboxes are clickable
            permissionCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('click', function(e) {
                    // Ensure the click event works properly
                    e.stopPropagation();
                });

                // Add visual feedback
                checkbox.addEventListener('change', function() {
                    const label = this.closest('label');
                    if (this.checked) {
                        label.classList.add('bg-purple-50', 'border-purple-300');
                    } else {
                        label.classList.remove('bg-purple-50', 'border-purple-300');
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
