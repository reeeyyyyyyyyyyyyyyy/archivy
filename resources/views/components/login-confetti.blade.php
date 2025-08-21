@if (session('success') && str_contains(session('success'), 'Selamat datang kembali'))
    @php
        $user = Auth::user();
        $isAdmin = $user->roles->contains('name', 'admin');
        $isStaff = $user->roles->contains('name', 'staff');
        $isIntern = $user->roles->contains('name', 'intern');

        // Tema berdasarkan role
        if ($isAdmin) {
            $primaryColor = 'from-purple-500 to-blue-600';
            $secondaryColor = 'from-indigo-500 to-purple-600';
            $accentColor = 'from-blue-500 to-indigo-600';
            $bgGradient = 'from-purple-400 via-blue-500 to-indigo-600';
            $iconBg = 'from-purple-500 to-blue-600';
            $buttonBg = 'from-purple-500 to-blue-600';
            $buttonHover = 'from-purple-600 to-blue-700';
            $cardBg = 'from-purple-50 to-blue-50';
            $confettiColors = ['#8b5cf6', '#3b82f6', '#6366f1', '#a855f7', '#06b6d4', '#0ea5e9'];
            $roleTitle = 'Administrator';
            $roleIcon = 'fas fa-user-shield';
        } elseif ($isStaff) {
            $primaryColor = 'from-emerald-500 to-teal-600';
            $secondaryColor = 'from-green-500 to-emerald-600';
            $accentColor = 'from-teal-500 to-green-600';
            $bgGradient = 'from-emerald-400 via-teal-500 to-green-600';
            $iconBg = 'from-emerald-500 to-teal-600';
            $buttonBg = 'from-emerald-500 to-teal-600';
            $buttonHover = 'from-emerald-600 to-teal-700';
            $cardBg = 'from-emerald-50 to-teal-50';
            $confettiColors = ['#10b981', '#059669', '#0d9488', '#14b8a6', '#16a34a', '#22c55e'];
            $roleTitle = 'Staff';
            $roleIcon = 'fas fa-user-tie';
        } else {
            $primaryColor = 'from-orange-500 to-pink-600';
            $secondaryColor = 'from-pink-500 to-rose-600';
            $accentColor = 'from-orange-500 to-rose-600';
            $bgGradient = 'from-orange-400 via-pink-500 to-rose-600';
            $iconBg = 'from-orange-500 to-pink-600';
            $buttonBg = 'from-orange-500 to-pink-600';
            $buttonHover = 'from-orange-600 to-pink-700';
            $cardBg = 'from-orange-50 to-pink-50';
            $confettiColors = ['#f97316', '#ea580c', '#ec4899', '#db2777', '#f59e0b', '#eab308'];
            $roleTitle = 'Intern';
            $roleIcon = 'fas fa-user-graduate';
        }
    @endphp

    <!-- Confetti Container -->
    <div id="confettiContainer" class="fixed inset-0 pointer-events-none z-40"></div>

    <!-- Login Success Modal -->
    <div id="loginSuccessModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm">
        <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full mx-4 transform scale-0 opacity-0 transition-all duration-700 ease-out"
            id="modalContent">
            <!-- Success Animation Container -->
            <div class="relative p-8 text-center overflow-hidden">
                <!-- Animated Background -->
                <div class="absolute inset-0 bg-gradient-to-br {{ $bgGradient }} opacity-5">
                </div>

                <!-- Floating Particles -->
                <div class="absolute inset-0">
                    <div
                        class="absolute top-4 left-4 w-2 h-2 bg-gradient-to-r {{ $primaryColor }} rounded-full animate-ping">
                    </div>
                    <div
                        class="absolute top-8 right-8 w-3 h-3 bg-gradient-to-r {{ $secondaryColor }} rounded-full animate-pulse">
                    </div>
                    <div
                        class="absolute bottom-8 left-8 w-2 h-2 bg-gradient-to-r {{ $accentColor }} rounded-full animate-bounce">
                    </div>
                    <div
                        class="absolute bottom-4 right-4 w-1 h-1 bg-gradient-to-r {{ $primaryColor }} rounded-full animate-ping">
                    </div>
                </div>

                <!-- Main Content -->
                <div class="relative z-10">
                    <!-- Success Icon with Animation -->
                    <div class="w-32 h-32 bg-gradient-to-br {{ $iconBg }} rounded-full flex items-center justify-center mx-auto mb-6 shadow-2xl transform scale-0"
                        id="successIcon">
                        <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-4xl text-gradient-to-r {{ $primaryColor }} transform scale-0"
                                id="checkmark"></i>
                        </div>
                    </div>

                    <!-- Welcome Title -->
                    <h2 class="text-3xl font-bold text-gray-800 mb-4 transform translate-y-8 opacity-0"
                        id="welcomeTitle">
                        🎉 Selamat Datang Kembali!
                    </h2>

                    <!-- Role Badge -->
                    <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r {{ $primaryColor }} text-white rounded-full text-sm font-semibold mb-4 transform translate-y-8 opacity-0"
                        id="roleBadge">
                        <i class="{{ $roleIcon }} mr-2"></i>
                        {{ $roleTitle }}
                    </div>

                    <!-- Success Message -->
                    <p class="text-gray-600 mb-6 transform translate-y-8 opacity-0" id="successMessage">
                        {{ session('success') }}
                    </p>

                    <!-- User Profile Card -->
                    <div class="bg-gradient-to-r {{ $cardBg }} rounded-2xl p-4 mb-6 transform translate-y-8 opacity-0"
                        id="userCard">
                        <div class="flex items-center space-x-4">
                            <div
                                class="w-16 h-16 bg-gradient-to-br {{ $iconBg }} rounded-full flex items-center justify-center text-white font-bold text-2xl shadow-lg">
                                {{ substr(Auth::user()->username ?? Auth::user()->name, 0, 1) }}
                            </div>
                            <div class="text-left flex-1">
                                <h3 class="font-bold text-gray-800 text-lg">
                                    {{ Auth::user()->username ?? Auth::user()->name }}</h3>
                                <p class="text-gray-600 text-sm">{{ Auth::user()->email }}</p>
                                <div class="flex items-center mt-1">
                                    <div
                                        class="w-2 h-2 bg-gradient-to-r {{ $primaryColor }} rounded-full mr-2 animate-pulse">
                                    </div>
                                    <span class="text-xs text-gray-600 font-medium">Online</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-3 transform translate-y-8 opacity-0" id="actionButtons">
                        <button onclick="goToDashboard()"
                            class="flex-1 bg-gradient-to-r {{ $buttonBg }} hover:{{ $buttonHover }} text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg transform hover:-translate-y-1">
                            <i class="fas fa-rocket mr-2"></i>
                            Lanjutkan ke Dashboard
                        </button>
                        <button onclick="closeLoginModal()"
                            class="px-6 py-3 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-all duration-300">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Start confetti with role-specific colors
            createConfetti(@json($confettiColors));

            // Show modal with animations
            setTimeout(() => {
                const modal = document.getElementById('loginSuccessModal');
                const content = document.getElementById('modalContent');

                modal.classList.remove('hidden');
                content.classList.remove('scale-0', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');

                // Animate elements sequentially
                setTimeout(() => {
                    document.getElementById('successIcon').classList.remove('scale-0');
                    document.getElementById('successIcon').classList.add('scale-100');
                }, 300);

                setTimeout(() => {
                    document.getElementById('checkmark').classList.remove('scale-0');
                    document.getElementById('checkmark').classList.add('scale-100');
                }, 600);

                setTimeout(() => {
                    document.getElementById('welcomeTitle').classList.remove('translate-y-8',
                        'opacity-0');
                    document.getElementById('welcomeTitle').classList.add('translate-y-0',
                        'opacity-100');
                }, 900);

                setTimeout(() => {
                    document.getElementById('roleBadge').classList.remove('translate-y-8',
                        'opacity-0');
                    document.getElementById('roleBadge').classList.add('translate-y-0',
                        'opacity-100');
                }, 1100);

                setTimeout(() => {
                    document.getElementById('successMessage').classList.remove('translate-y-8',
                        'opacity-0');
                    document.getElementById('successMessage').classList.add('translate-y-0',
                        'opacity-100');
                }, 1300);

                setTimeout(() => {
                    document.getElementById('userCard').classList.remove('translate-y-8',
                        'opacity-0');
                    document.getElementById('userCard').classList.add('translate-y-0',
                        'opacity-100');
                }, 1500);

                setTimeout(() => {
                    document.getElementById('actionButtons').classList.remove('translate-y-8',
                        'opacity-0');
                    document.getElementById('actionButtons').classList.add('translate-y-0',
                        'opacity-100');
                }, 1700);

            }, 500);

            // Auto-close after 10 seconds
            setTimeout(() => {
                closeLoginModal();
            }, 10000);
        });

        function createConfetti(colors) {
            const container = document.getElementById('confettiContainer');

            for (let i = 0; i < 150; i++) {
                setTimeout(() => {
                    const confetti = document.createElement('div');
                    confetti.style.position = 'absolute';
                    confetti.style.width = Math.random() * 10 + 5 + 'px';
                    confetti.style.height = Math.random() * 10 + 5 + 'px';
                    confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
                    confetti.style.left = Math.random() * 100 + '%';
                    confetti.style.top = '-10px';
                    confetti.style.borderRadius = Math.random() > 0.5 ? '50%' : '0';
                    confetti.style.animation = `fall ${Math.random() * 3 + 2}s linear forwards`;
                    confetti.style.zIndex = '1';

                    container.appendChild(confetti);

                    // Remove confetti after animation
                    setTimeout(() => {
                        if (confetti.parentNode) {
                            confetti.parentNode.removeChild(confetti);
                        }
                    }, 5000);
                }, i * 20);
            }
        }

        function closeLoginModal() {
            const modal = document.getElementById('loginSuccessModal');
            const content = document.getElementById('modalContent');

            // Jangan hapus modal, biarkan tetap tampil
            // Bisa tetap pakai efek scale/opacity jika ingin animasi
            content.classList.remove('scale-95', 'opacity-0');
        }

        function goToDashboard() {
            // Close modal first
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
        @keyframes fall {
            to {
                transform: translateY(100vh) rotate(360deg);
                opacity: 0;
            }
        }

        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-30px);
            }

            60% {
                transform: translateY(-15px);
            }
        }

        .animate-bounce {
            animation: bounce 1s infinite;
        }

        /* Smooth transitions */
        .transition-all {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Hover effects */
        .hover\:scale-105:hover {
            transform: scale(1.05);
        }

        .hover\:-translate-y-1:hover {
            transform: translateY(-0.25rem);
        }

        /* Text gradient for checkmark */
        .text-gradient-to-r {
            background: linear-gradient(to right, var(--tw-gradient-stops));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
@endif
