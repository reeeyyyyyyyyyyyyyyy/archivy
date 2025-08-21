@if (session('success') && str_contains(session('success'), 'Selamat datang kembali'))
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
                <div class="absolute inset-0 bg-gradient-to-br from-green-400 via-emerald-500 to-teal-600 opacity-5">
                </div>

                <!-- Floating Particles -->
                <div class="absolute inset-0">
                    <div class="absolute top-4 left-4 w-2 h-2 bg-green-400 rounded-full animate-ping"></div>
                    <div class="absolute top-8 right-8 w-3 h-3 bg-blue-400 rounded-full animate-pulse"></div>
                    <div class="absolute bottom-8 left-8 w-2 h-2 bg-purple-400 rounded-full animate-bounce"></div>
                    <div class="absolute bottom-4 right-4 w-1 h-1 bg-yellow-400 rounded-full animate-ping"></div>
                </div>

                <!-- Main Content -->
                <div class="relative z-10">
                    <!-- Success Icon with Animation -->
                    <div class="w-32 h-32 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-2xl transform scale-0"
                        id="successIcon">
                        <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-4xl text-green-500 transform scale-0" id="checkmark"></i>
                        </div>
                    </div>

                    <!-- Welcome Title -->
                    <h2 class="text-3xl font-bold text-gray-800 mb-4 transform translate-y-8 opacity-0"
                        id="welcomeTitle">
                        🎉 Selamat Datang Kembali!
                    </h2>

                    <!-- Success Message -->
                    <p class="text-gray-600 mb-6 transform translate-y-8 opacity-0" id="successMessage">
                        {{ session('success') }}
                    </p>

                    <!-- User Profile Card -->
                    <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-2xl p-4 mb-6 transform translate-y-8 opacity-0"
                        id="userCard">
                        <div class="flex items-center space-x-4">
                            <div
                                class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-2xl shadow-lg">
                                {{ substr(Auth::user()->username ?? Auth::user()->name, 0, 1) }}
                            </div>
                            <div class="text-left flex-1">
                                <h3 class="font-bold text-gray-800 text-lg">
                                    {{ Auth::user()->username ?? Auth::user()->name }}</h3>
                                <p class="text-gray-600 text-sm">{{ Auth::user()->email }}</p>
                                <div class="flex items-center mt-1">
                                    <div class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></div>
                                    <span class="text-xs text-green-600 font-medium">Online</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-3 transform translate-y-8 opacity-0" id="actionButtons">
                        <button onclick="goToDashboard()"
                            class="flex-1 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg transform hover:-translate-y-1">
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
            // Start confetti
            createConfetti();

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
                    document.getElementById('successMessage').classList.remove('translate-y-8',
                        'opacity-0');
                    document.getElementById('successMessage').classList.add('translate-y-0',
                        'opacity-100');
                }, 1100);

                setTimeout(() => {
                    document.getElementById('userCard').classList.remove('translate-y-8',
                        'opacity-0');
                    document.getElementById('userCard').classList.add('translate-y-0',
                        'opacity-100');
                }, 1300);

                setTimeout(() => {
                    document.getElementById('actionButtons').classList.remove('translate-y-8',
                        'opacity-0');
                    document.getElementById('actionButtons').classList.add('translate-y-0',
                        'opacity-100');
                }, 1500);

            }, 500);

            // Auto-close after 8 seconds
            setTimeout(() => {
                closeLoginModal();
            }, 8000);
        });

        function createConfetti() {
            const container = document.getElementById('confettiContainer');
            const colors = ['#f56565', '#48bb78', '#ed8936', '#4299e1', '#9f7aea', '#f687b3'];

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
    </style>
@endif
