<x-guest-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-600 via-purple-600 to-green-600 flex items-center justify-center px-4">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-20 left-20 w-32 h-32 border border-white/30 rounded-full animate-pulse"></div>
            <div class="absolute top-40 right-32 w-24 h-24 border border-white/20 rounded-full animate-pulse delay-1000"></div>
            <div class="absolute bottom-32 left-1/3 w-40 h-40 border border-white/25 rounded-full animate-pulse delay-2000"></div>
            <div class="absolute bottom-20 right-20 w-16 h-16 border border-white/15 rounded-full animate-pulse delay-3000"></div>
        </div>

        <!-- Forgot Password Card -->
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
                
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Lupa Password?</h2>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Tidak masalah! Masukkan email Anda dan kami akan mengirimkan link reset password ke email Anda.
                </p>
            </div>
            
            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />
            
            <!-- Form -->
            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf
                
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
                                    autofocus 
                                    autocomplete="username"
                                    placeholder="Masukkan email Anda" />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                
                <!-- Submit Button -->
                <div>
                    <button type="submit" class="w-full justify-center py-3 px-4 text-sm font-semibold rounded-xl bg-white text-blue-600 border-2 border-blue-600 hover:bg-blue-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-[1.02] flex items-center">
                        <i class="fas fa-paper-plane mr-2"></i>
                        {{ __('Kirim Link Reset Password') }}
                    </button>
                </div>
                
                <!-- Back to Login -->
                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Ingat password Anda? 
                        <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500 transition-colors">
                            Kembali ke Login
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
            
            <!-- Help Notice -->
            <div class="mt-6 p-4 bg-amber-50 rounded-xl border border-amber-200">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-question-circle text-amber-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-amber-800">
                            <strong>Butuh bantuan?</strong> Hubungi admin sistem jika tidak menerima email reset dalam 5 menit.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
