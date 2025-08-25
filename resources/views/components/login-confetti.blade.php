@if (session('success') && str_contains(session('success'), 'Selamat datang kembali'))
    @php
        $user = Auth::user();
        $isAdmin = $user->roles->contains('name', 'admin');
        $isStaff = $user->roles->contains('name', 'staff');
        $isIntern = $user->roles->contains('name', 'intern');

        // Tema berdasarkan role - Lebih formal dan profesional
        if ($isAdmin) {
            $primaryColor = 'from-blue-600 to-indigo-700';
            $secondaryColor = 'from-indigo-600 to-blue-700';
            $accentColor = 'from-blue-500 to-indigo-600';
            $bgGradient = 'from-blue-50 to-indigo-50';
            $iconBg = 'from-blue-600 to-indigo-700';
            $buttonBg = 'from-blue-600 to-indigo-700';
            $buttonHover = 'from-blue-700 to-indigo-800';
            $cardBg = 'from-blue-50 to-indigo-50';
            $roleTitle = 'Administrator';
            $roleIcon = 'fas fa-user-shield';
        } elseif ($isStaff) {
            $primaryColor = 'from-emerald-600 to-teal-700';
            $secondaryColor = 'from-teal-600 to-emerald-700';
            $accentColor = 'from-emerald-500 to-teal-600';
            $bgGradient = 'from-emerald-50 to-teal-50';
            $iconBg = 'from-emerald-600 to-teal-700';
            $buttonBg = 'from-emerald-600 to-teal-700';
            $buttonHover = 'from-emerald-700 to-teal-800';
            $cardBg = 'from-emerald-50 to-teal-50';
            $roleTitle = 'Staff';
            $roleIcon = 'fas fa-user-tie';
        } else {
            $primaryColor = 'from-orange-600 to-amber-700';
            $secondaryColor = 'from-amber-600 to-orange-700';
            $accentColor = 'from-orange-500 to-amber-600';
            $bgGradient = 'from-orange-50 to-amber-50';
            $iconBg = 'from-orange-600 to-amber-700';
            $buttonBg = 'from-orange-600 to-amber-700';
            $buttonHover = 'from-orange-700 to-amber-800';
            $cardBg = 'from-orange-50 to-amber-50';
            $roleTitle = 'Intern';
            $roleIcon = 'fas fa-user-graduate';
        }
    @endphp

    <!-- Professional Login Success Notification -->
    <div id="loginSuccessModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 backdrop-blur-sm hidden">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full mx-4 transform scale-0 opacity-0 transition-all duration-700 ease-out"
            id="modalContent">

            <!-- Success Content -->
            <div class="relative p-8 text-center overflow-hidden">

                <!-- Animated Background with Role Colors -->
                <div class="absolute inset-0 bg-gradient-to-br {{ $bgGradient }} opacity-10 rounded-2xl"></div>

                <!-- Floating Elements - Subtle and Professional -->
                <div class="absolute inset-0">
                    <div class="absolute top-6 left-6 w-3 h-3 bg-gradient-to-r {{ $primaryColor }} rounded-full opacity-40 animate-pulse"></div>
                    <div class="absolute top-12 right-8 w-2 h-2 bg-gradient-to-r {{ $secondaryColor }} rounded-full opacity-30 animate-ping"></div>
                    <div class="absolute bottom-8 left-8 w-2.5 h-2.5 bg-gradient-to-r {{ $accentColor }} rounded-full opacity-35 animate-bounce"></div>
                    <div class="absolute bottom-6 right-6 w-1.5 h-1.5 bg-gradient-to-r {{ $primaryColor }} rounded-full opacity-25 animate-pulse"></div>
                </div>

                <!-- Main Content -->
                <div class="relative z-10">

                    <!-- Success Icon - Elegant with Role Colors -->
                    <div class="w-28 h-28 bg-gradient-to-br {{ $iconBg }} rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl transform scale-0"
                        id="successIcon">
                        <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-lg">
                            <i class="fas fa-check text-3xl bg-gradient-to-r {{ $primaryColor }} bg-clip-text text-transparent transform scale-0" id="checkmark"></i>
                        </div>
                    </div>

                    <!-- Welcome Title - Professional and Clean -->
                    <h2 class="text-2xl font-bold text-gray-800 mb-4 transform translate-y-8 opacity-0"
                        id="welcomeTitle">
                        Selamat Datang Kembali
                    </h2>

                    <!-- Role Badge - Elegant with Role Colors -->
                    <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r {{ $primaryColor }} text-white rounded-full text-sm font-semibold mb-4 transform translate-y-8 opacity-0 shadow-lg"
                        id="roleBadge">
                        <i class="{{ $roleIcon }} mr-2"></i>
                        {{ $roleTitle }}
                    </div>

                    <!-- Success Message - Clean and Professional -->
                    <p class="text-gray-600 mb-6 transform translate-y-8 opacity-0" id="successMessage">
                        Anda telah berhasil masuk ke sistem ARSIPIN
                    </p>

                    <!-- User Profile Card - Elegant Design -->
                    <div class="bg-gradient-to-r {{ $cardBg }} rounded-xl p-4 mb-6 transform translate-y-8 opacity-0 shadow-md"
                        id="userCard">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-gradient-to-br {{ $iconBg }} rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg">
                                {{ substr(Auth::user()->username ?? Auth::user()->name, 0, 1) }}
                            </div>
                            <div class="text-left flex-1">
                                <h3 class="font-semibold text-gray-800 text-lg">
                                    {{ Auth::user()->username ?? Auth::user()->name }}</h3>
                                <p class="text-gray-600 text-sm">{{ Auth::user()->email }}</p>
                                <div class="flex items-center mt-2">
                                    <div class="w-2 h-2 bg-gradient-to-r {{ $primaryColor }} rounded-full mr-2 animate-pulse"></div>
                                    <span class="text-xs text-gray-600 font-medium">Aktif</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Button - Professional with Role Colors -->
                    <div class="transform translate-y-8 opacity-0" id="actionButton">
                        <button onclick="goToDashboard()"
                            class="w-full bg-gradient-to-r {{ $buttonBg }} hover:{{ $buttonHover }} text-white px-8 py-3 rounded-xl font-semibold transition-all duration-300 hover:shadow-lg hover:scale-105 transform hover:-translate-y-1">
                            <i class="fas fa-arrow-right mr-2"></i>
                            Lanjutkan ke Dashboard
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Prevent multiple executions
        if (window.loginNotificationShown) {
            document.getElementById('loginSuccessModal').remove();
        } else {
            window.loginNotificationShown = true;

            document.addEventListener('DOMContentLoaded', function() {
                // Show notification with elegant pop-up animations
                setTimeout(() => {
                    const modal = document.getElementById('loginSuccessModal');
                    const content = document.getElementById('modalContent');

                    modal.classList.remove('hidden');

                    // Elegant pop-up animation
                    content.classList.remove('scale-0', 'opacity-0');
                    content.classList.add('scale-100', 'opacity-100');

                    // Sequential element animations - elegant and professional
                    setTimeout(() => {
                        document.getElementById('successIcon').classList.remove('scale-0');
                        document.getElementById('successIcon').classList.add('scale-100');
                    }, 300);

                    setTimeout(() => {
                        document.getElementById('checkmark').classList.remove('scale-0');
                        document.getElementById('checkmark').classList.add('scale-100');
                    }, 600);

                    setTimeout(() => {
                        document.getElementById('welcomeTitle').classList.remove('translate-y-8', 'opacity-0');
                        document.getElementById('welcomeTitle').classList.add('translate-y-0', 'opacity-100');
                    }, 900);

                    setTimeout(() => {
                        document.getElementById('roleBadge').classList.remove('translate-y-8', 'opacity-0');
                        document.getElementById('roleBadge').classList.add('translate-y-0', 'opacity-100');
                    }, 1100);

                    setTimeout(() => {
                        document.getElementById('successMessage').classList.remove('translate-y-8', 'opacity-0');
                        document.getElementById('successMessage').classList.add('translate-y-0', 'opacity-100');
                    }, 1300);

                    setTimeout(() => {
                        document.getElementById('userCard').classList.remove('translate-y-8', 'opacity-0');
                        document.getElementById('userCard').classList.add('translate-y-0', 'opacity-100');
                    }, 1500);

                    setTimeout(() => {
                        document.getElementById('actionButton').classList.remove('translate-y-8', 'opacity-0');
                        document.getElementById('actionButton').classList.add('translate-y-0', 'opacity-100');
                    }, 1700);

                }, 500);

                // Auto-close after 10 seconds (longer for elegant experience)
                setTimeout(() => {
                    closeLoginModal();
                }, 10000);
            });
        }

        function closeLoginModal() {
            const modal = document.getElementById('loginSuccessModal');
            const content = document.getElementById('modalContent');

            // Smooth closing animation
            content.classList.add('scale-0', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 500);
        }

        function goToDashboard() {
            closeLoginModal();

            // Redirect to dashboard based on user role
            @if (auth()->check())
                @if (auth()->user()->roles->contains('name', 'admin'))
                    window.location.href = '{{ route('admin.dashboard') }}';
                @elseif (auth()->user()->roles->contains('name', 'staff'))
                    window.location.href = '{{ route('staff.dashboard') }}';
                @elseif (auth()->user()->roles->contains('name', 'intern'))
                    window.location.href = '{{ route('intern.dashboard') }}';
                @else
                    window.location.href = '{{ route('admin.dashboard') }}';
                @endif
            @endif
        }

        // Close on background click
        document.getElementById('loginSuccessModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLoginModal();
            }
        });

        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLoginModal();
            }
        });
    </script>

    <style>
        /* Professional, elegant animations */
        .transition-all {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Smooth scale transitions */
        .transform {
            transition: transform 0.7s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        /* Hover effects - elegant and professional */
        .hover\:shadow-lg:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .hover\:scale-105:hover {
            transform: scale(1.05);
        }

        .hover\:-translate-y-1:hover {
            transform: translateY(-0.25rem);
        }

        /* Modal transitions - smooth and professional */
        .modal-hidden {
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.5s ease-out, visibility 0.5s ease-out;
        }

        .modal-visible {
            opacity: 1;
            visibility: visible;
            transition: opacity 0.5s ease-in, visibility 0.5s ease-in;
        }

        /* Floating elements animations */
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        .animate-ping {
            animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;
        }

        .animate-bounce {
            animation: bounce 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }

        @keyframes ping {
            75%, 100% {
                transform: scale(2);
                opacity: 0;
            }
        }

        @keyframes bounce {
            0%, 20%, 53%, 80%, 100% {
                transform: translate3d(0, 0, 0);
            }
            40%, 43% {
                transform: translate3d(0, -8px, 0);
            }
            70% {
                transform: translate3d(0, -4px, 0);
            }
            90% {
                transform: translate3d(0, -2px, 0);
            }
        }

        /* Text gradient for checkmark */
        .bg-clip-text {
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
@endif
