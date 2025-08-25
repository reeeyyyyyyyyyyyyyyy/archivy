<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-plus text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Buat User Baru</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Tambahkan user baru ke sistem dengan role yang sesuai
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.roles.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="p-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-8" id="userForm">
                @csrf

                <!-- User Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">
                        <i class="fas fa-user mr-2 text-green-600"></i>
                        Informasi User
                    </h3>

                    <div class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                placeholder="Masukkan Nama Lengkap"
                                class="w-full border-gray-300 rounded-lg focus:border-green-500 focus:ring-green-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                                Username <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="username" id="username" value="{{ old('username') }}"
                                placeholder="Masukkan Username/Nama Panggilan"
                                class="w-full border-gray-300 rounded-lg focus:border-green-500 focus:ring-green-500 @error('username') border-red-500 @enderror">
                            @error('username')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                placeholder="Masukkan Email"
                                class="w-full border-gray-300 rounded-lg focus:border-green-500 focus:ring-green-500 @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Password <span class="text-red-500">*</span>
                                </label>
                                <input type="password" name="password" id="password" placeholder="Minimal 8 karakter"
                                    class="w-full border-gray-300 rounded-lg focus:border-green-500 focus:ring-green-500 @error('password') border-red-500 @enderror">
                                @error('password')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                    Konfirmasi Password <span class="text-red-500">*</span>
                                </label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    placeholder="Ulangi password"
                                    class="w-full border-gray-300 rounded-lg focus:border-green-500 focus:ring-green-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Role Assignment -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">
                        <i class="fas fa-shield-alt mr-2 text-purple-600"></i>
                        Assign Role
                    </h3>

                    <div class="space-y-4">
                        @foreach ($roles as $role)
                            @php
                                $roleConfig = match ($role->name) {
                                    'admin' => [
                                        'bg' => 'bg-red-50',
                                        'border' => 'border-red-200',
                                        'text' => 'text-red-700',
                                        'icon' => 'fas fa-crown',
                                        'desc' => 'Akses penuh ke semua fitur sistem',
                                    ],
                                    'staff' => [
                                        'bg' => 'bg-green-50',
                                        'border' => 'border-green-200',
                                        'text' => 'text-green-700',
                                        'icon' => 'fas fa-user-tie',
                                        'desc' => 'Akses lengkap untuk manajemen arsip dan analytics',
                                    ],
                                    'intern' => [
                                        'bg' => 'bg-orange-50',
                                        'border' => 'border-orange-200',
                                        'text' => 'text-orange-700',
                                        'icon' => 'fas fa-graduation-cap',
                                        'desc' => 'Akses terbatas untuk viewing dan export data',
                                    ],
                                    default => [
                                        'bg' => 'bg-purple-50',
                                        'border' => 'border-purple-200',
                                        'text' => 'text-purple-700',
                                        'icon' => 'fas fa-user-cog',
                                        'desc' => 'Role custom dengan permissions khusus',
                                    ],
                                };
                            @endphp

                            <label
                                class="flex items-center p-4 border-2 {{ $roleConfig['border'] }} {{ $roleConfig['bg'] }} rounded-lg cursor-pointer hover:shadow-md transition-all">
                                <input type="radio" name="role" value="{{ $role->name }}"
                                    {{ old('role') === $role->name ? 'checked' : '' }} class="sr-only peer">
                                <div class="flex items-center space-x-4 w-full peer-checked:{{ $roleConfig['text'] }}">
                                    <div
                                        class="w-12 h-12 {{ $roleConfig['bg'] }} rounded-lg flex items-center justify-center peer-checked:bg-white">
                                        <i class="{{ $roleConfig['icon'] }} {{ $roleConfig['text'] }} text-xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-semibold {{ $roleConfig['text'] }} capitalize text-lg">
                                            {{ $role->name }}</div>
                                        <div class="text-sm text-gray-600">{{ $roleConfig['desc'] }}</div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $role->permissions_count ?? 0 }} permissions •
                                            {{ $role->users_count ?? 0 }} users
                                        </div>
                                    </div>
                                    <div
                                        class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:border-{{ $roleConfig['text'] }} peer-checked:bg-{{ $roleConfig['text'] }} flex items-center justify-center">
                                        <i class="fas fa-check text-white text-xs hidden peer-checked:block"></i>
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
                        class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors"
                        id="submitBtn">
                        <i class="fas fa-user-plus mr-2"></i>
                        Buat User
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('userForm');
                const submitBtn = document.getElementById('submitBtn');

                // Form validation with SWAL
                if (form && submitBtn) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();

                        // Field validation
                        const requiredFields = ['name', 'username', 'email', 'password', 'password_confirmation'];

                        if (!window.validateRequiredFields(requiredFields)) {
                            return;
                        }

                        // Password confirmation validation
                        const password = document.getElementById('password').value;
                        const passwordConfirmation = document.getElementById('password_confirmation').value;

                        if (password !== passwordConfirmation) {
                            window.showValidationError('Konfirmasi password tidak sesuai dengan password!');
                            return;
                        }

                        // Check if role is selected
                        const selectedRole = document.querySelector('input[name="role"]:checked');
                        if (!selectedRole) {
                            window.showValidationError('Mohon pilih role untuk user ini!');
                            return;
                        }

                        // Confirm create with SWAL
                        window.showCreateConfirm('Apakah Anda yakin ingin membuat user baru ini?')
                            .then((result) => {
                                if (result.isConfirmed) {
                                    // Show loading state
                                    submitBtn.disabled = true;
                                    submitBtn.innerHTML =
                                        '<i class="fas fa-spinner fa-spin mr-2"></i>Membuat User...';

                                    // Submit form
                                    form.submit();
                                }
                            });
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>
