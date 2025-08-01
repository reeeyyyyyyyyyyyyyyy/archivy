<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-shield text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Buat Role Baru</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-user-shield mr-1"></i>Tambahkan role baru dengan permissions yang sesuai
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.roles.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

            <form action="{{ route('admin.roles.store') }}" method="POST" class="space-y-8" id="roleForm">
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

                <!-- Permissions Selection -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-key mr-2 text-yellow-500"></i>
                            Pilih Permissions
                        </h3>
                        <div class="flex space-x-2">
                            <button type="button" id="selectAll"
                                    class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors">
                                <i class="fas fa-check mr-1"></i>Pilih Semua
                            </button>
                            <button type="button" id="deselectAll"
                                    class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors">
                                <i class="fas fa-times mr-1"></i>Hapus Semua
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($permissions as $permission)
                        <label class="flex items-center space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                            <input type="checkbox"
                                   name="permissions[]"
                                   value="{{ $permission->id }}"
                                   class="w-4 h-4 text-purple-600 bg-gray-100 border-gray-300 rounded focus:ring-purple-500 focus:ring-2 cursor-pointer"
                                   {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900">{{ $permission->name }}</div>
                                <div class="text-xs text-gray-500">{{ $permission->guard_name }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>

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
                            class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-colors"
                            id="submitBtn">
                        <i class="fas fa-save mr-2"></i>
                        Buat Role
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

                    // Confirm create with SWAL
                    window.showCreateConfirm('Apakah Anda yakin ingin membuat role baru ini?')
                        .then((result) => {
                            if (result.isConfirmed) {
                                // Show loading state
                                submitBtn.disabled = true;
                                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Membuat Role...';

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
            });
        });
    </script>
    @endpush
</x-app-layout>
