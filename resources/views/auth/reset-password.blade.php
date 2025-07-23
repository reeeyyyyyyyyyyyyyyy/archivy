<x-guest-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-600 via-purple-600 to-green-600 flex items-center justify-center px-4">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-20 right-20 w-32 h-32 border border-white/30 rounded-full animate-pulse"></div>
            <div class="absolute top-40 left-32 w-24 h-24 border border-white/20 rounded-full animate-pulse delay-1000"></div>
            <div class="absolute bottom-32 right-1/3 w-40 h-40 border border-white/25 rounded-full animate-pulse delay-2000"></div>
            <div class="absolute bottom-20 left-20 w-16 h-16 border border-white/15 rounded-full animate-pulse delay-3000"></div>
        </div>

        <!-- Reset Password Card -->
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
                
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Reset Password</h2>
                <p class="text-gray-600 text-sm">Masukkan password baru untuk akun Anda</p>
            </div>
            
            <!-- Form -->
            <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

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
                                    :value="old('email', $request->email)" 
                                    required 
                                    autofocus 
                                    autocomplete="username"
                                    readonly />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('Password Baru')" class="text-gray-700 font-medium mb-2" />
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
                                    placeholder="Ulangi password baru" />
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
                        <i class="fas fa-key mr-2"></i>
                        {{ __('Reset Password') }}
                    </button>
                </div>
                
                <!-- Back to Login -->
                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Sudah selesai? 
                        <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500 transition-colors">
                            Kembali ke Login
                        </a>
                    </p>
                </div>
            </form>
            
            <!-- Security Notice -->
            <div class="mt-6 p-4 bg-green-50 rounded-xl border border-green-200">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-shield-alt text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-800">
                            <strong>Keamanan Terjaga.</strong> Password baru akan mengenkripsi akun Anda dengan standar keamanan tinggi.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
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
    </script>
</x-guest-layout>
