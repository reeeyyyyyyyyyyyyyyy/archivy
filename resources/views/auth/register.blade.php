<x-guest-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-600 via-purple-600 to-green-600 flex items-center justify-center px-4 py-8">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-20 right-20 w-32 h-32 border border-white/30 rounded-full animate-pulse"></div>
            <div class="absolute top-40 left-32 w-24 h-24 border border-white/20 rounded-full animate-pulse delay-1000"></div>
            <div class="absolute bottom-32 right-1/3 w-40 h-40 border border-white/25 rounded-full animate-pulse delay-2000"></div>
            <div class="absolute bottom-20 left-20 w-16 h-16 border border-white/15 rounded-full animate-pulse delay-3000"></div>
        </div>

        <!-- Register Card -->
        <div class="bg-white rounded-3xl shadow-2xl p-8 w-full max-w-md relative z-10">
            <!-- Header -->
            <div class="text-center mb-8">
                <!-- Logo -->
                <div class="flex items-center justify-center mb-6">
                    <div class="relative">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mr-3">
                            <i class="fas fa-archive text-white text-2xl"></i>
                        </div>
                        <div class="absolute -top-2 -right-1 w-6 h-6 bg-yellow-400 rounded-full flex items-center justify-center">
                            <i class="fas fa-bolt text-blue-600 text-xs"></i>
                        </div>
                    </div>
                    <div class="text-left">
                        <h1 class="text-2xl font-bold text-gray-800">ARSIPIN</h1>
                        <p class="text-gray-500 text-sm">Sistem Arsip Pintar</p>
                    </div>
                </div>

                <div class="bg-blue-50 rounded-xl p-3 mb-6">
                    <p class="text-blue-800 font-medium text-sm">
                        DPMPTSP Provinsi Jawa Timur
                    </p>
                </div>

                <h2 class="text-2xl font-bold text-gray-900 mb-2">Daftar Akun Baru</h2>
                <p class="text-gray-600">Registrasi untuk menggunakan sistem</p>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('register') }}" class="space-y-5" id="registerForm">
                @csrf

                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('Nama Lengkap')" class="text-gray-700 font-medium mb-2" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <x-text-input id="name"
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    type="text"
                                    name="name"
                                    :value="old('name')"
                                    required
                                    autofocus
                                    autocomplete="name"
                                    placeholder="Masukkan nama lengkap" />
                    </div>
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" class="text-gray-700 font-medium mb-2" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <x-text-input id="email"
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    type="email"
                                    name="email"
                                    :value="old('email')"
                                    required
                                    autocomplete="username"
                                    placeholder="contoh@email.com" />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('Password')" class="text-gray-700 font-medium mb-2" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <x-text-input id="password"
                                    class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    type="password"
                                    name="password"
                                    required
                                    autocomplete="new-password"
                                    placeholder="Minimal 8 karakter" />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button type="button" onclick="togglePassword('password')" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                <i id="password-icon" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" class="text-gray-700 font-medium mb-2" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <x-text-input id="password_confirmation"
                                    class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    type="password"
                                    name="password_confirmation"
                                    required
                                    autocomplete="new-password"
                                    placeholder="Ulangi password" />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button type="button" onclick="togglePassword('password_confirmation')" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                <i id="password_confirmation-icon" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit" class="w-full justify-center py-3 px-4 text-sm font-semibold rounded-xl bg-white text-blue-600 border-2 border-blue-600 hover:bg-blue-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-[1.02] flex items-center">
                        <i class="fas fa-user-plus mr-2"></i>
                        {{ __('Buat Akun') }}
                    </button>
                </div>

                <!-- Login Link -->
                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500 transition-colors">
                            Masuk sekarang
                        </a>
                    </p>
                </div>
            </form>

            <!-- Divider -->
            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300" />
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Atau</span>
                    </div>
                </div>
            </div>

            <!-- Back to Home -->
            <div class="mt-6">
                <a href="/" class="w-full inline-flex items-center justify-center px-4 py-3 border border-gray-300 rounded-xl shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <i class="fas fa-home mr-2"></i>
                    Kembali ke Beranda
                </a>
            </div>

            <!-- Info Notice -->
            <div class="mt-6 p-4 bg-blue-50 rounded-xl border border-blue-200">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-800">
                            <strong>Sistem ARSIPIN.</strong> Kelola arsip digital dengan mudah dan otomatis sesuai standar JRA.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>

    <script>
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const passwordIcon = document.getElementById(fieldId + '-icon');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }

        // Client-side validation for register form
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();
            const passwordConfirmation = document.getElementById('password_confirmation').value.trim();
            let hasError = false;

            // Clear previous warnings
            clearWarnings();

            // Check name
            if (!name) {
                showWarning('name', 'Nama lengkap wajib diisi');
                hasError = true;
            } else if (name.length < 2) {
                showWarning('name', 'Nama lengkap minimal 2 karakter');
                hasError = true;
            }

            // Check email
            if (!email) {
                showWarning('email', 'Email wajib diisi');
                hasError = true;
            } else if (!isValidEmail(email)) {
                showWarning('email', 'Format email tidak valid');
                hasError = true;
            }

            // Check password
            if (!password) {
                showWarning('password', 'Password wajib diisi');
                hasError = true;
            } else if (password.length < 8) {
                showWarning('password', 'Password minimal 8 karakter');
                hasError = true;
            }

            // Check password confirmation
            if (!passwordConfirmation) {
                showWarning('password_confirmation', 'Konfirmasi password wajib diisi');
                hasError = true;
            } else if (password !== passwordConfirmation) {
                showWarning('password_confirmation', 'Konfirmasi password tidak cocok');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                showGeneralWarning('Mohon lengkapi semua field yang wajib diisi dengan benar');
            }
        });

        function showWarning(fieldId, message) {
            const field = document.getElementById(fieldId);
            const warningDiv = document.createElement('div');
            warningDiv.className = 'flex items-center p-3 bg-yellow-50 border border-yellow-200 rounded-lg mt-2';
            warningDiv.innerHTML = `
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700 font-medium">${message}</p>
                </div>
            `;
            warningDiv.id = fieldId + '-warning';
            field.parentNode.parentNode.appendChild(warningDiv);
            field.classList.add('border-yellow-500', 'focus:border-yellow-500', 'focus:ring-yellow-500');
        }

        function showGeneralWarning(message) {
            const form = document.getElementById('registerForm');
            const warningDiv = document.createElement('div');
            warningDiv.className = 'flex items-center p-4 bg-yellow-50 border border-yellow-200 rounded-lg mb-4';
            warningDiv.innerHTML = `
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700 font-medium">${message}</p>
                </div>
            `;
            warningDiv.id = 'general-warning';
            form.insertBefore(warningDiv, form.firstChild);
        }

        function clearWarnings() {
            // Remove field-specific warnings
            document.querySelectorAll('[id$="-warning"]').forEach(el => el.remove());

            // Remove general warning
            const generalWarning = document.getElementById('general-warning');
            if (generalWarning) generalWarning.remove();

            // Reset field borders
            const fields = ['name', 'email', 'password', 'password_confirmation'];
            fields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.classList.remove('border-yellow-500', 'focus:border-yellow-500', 'focus:ring-yellow-500');
                }
            });
        }

        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        // Real-time validation
        document.getElementById('name').addEventListener('blur', function() {
            const name = this.value.trim();
            const warningId = 'name-warning';

            // Remove existing warning
            const existingWarning = document.getElementById(warningId);
            if (existingWarning) existingWarning.remove();

            if (name && name.length < 2) {
                showWarning('name', 'Nama lengkap minimal 2 karakter');
            }
        });

        document.getElementById('email').addEventListener('blur', function() {
            const email = this.value.trim();
            const warningId = 'email-warning';

            // Remove existing warning
            const existingWarning = document.getElementById(warningId);
            if (existingWarning) existingWarning.remove();

            if (email && !isValidEmail(email)) {
                showWarning('email', 'Format email tidak valid');
            }
        });

        document.getElementById('password').addEventListener('blur', function() {
            const password = this.value.trim();
            const warningId = 'password-warning';

            // Remove existing warning
            const existingWarning = document.getElementById(warningId);
            if (existingWarning) existingWarning.remove();

            if (password && password.length < 8) {
                showWarning('password', 'Password minimal 8 karakter');
            }
        });

        document.getElementById('password_confirmation').addEventListener('blur', function() {
            const passwordConfirmation = this.value.trim();
            const password = document.getElementById('password').value.trim();
            const warningId = 'password_confirmation-warning';

            // Remove existing warning
            const existingWarning = document.getElementById(warningId);
            if (existingWarning) existingWarning.remove();

            if (passwordConfirmation && password && passwordConfirmation !== password) {
                showWarning('password_confirmation', 'Konfirmasi password tidak cocok');
            }
        });
    </script>
</x-guest-layout>
