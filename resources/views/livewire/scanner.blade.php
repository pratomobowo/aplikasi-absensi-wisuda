<!-- Scanner Component - Simplified Version -->
<div class="min-h-screen bg-gray-50">
    <!-- Reset Feedback Toast -->
    <div id="reset-toast" wire:ignore class="hidden fixed top-4 right-4 z-50 bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-4 rounded-lg shadow-2xl animate-slide-in-right border-2 border-blue-400">
        <div class="flex items-center space-x-3">
            <div class="flex-shrink-0">
                <svg class="w-7 h-7 animate-spin-once" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
            </div>
            <div>
                <p class="font-bold text-lg">Scanner Direset Manual</p>
                <p class="text-sm text-blue-100 mt-1">Semua state dibersihkan, siap memindai kembali</p>
            </div>
            <button onclick="document.getElementById('reset-toast').classList.add('hidden')" class="ml-4 text-blue-200 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Success Notification Toast (Bottom) -->
    <div id="success-toast" wire:ignore class="hidden fixed bottom-4 left-4 right-4 md:left-4 md:right-auto md:w-96 z-50 bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-4 rounded-lg shadow-2xl animate-slide-in-up border-l-4 border-green-400">
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0 mt-0.5">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="flex-1">
                <p class="font-bold">Berhasil!</p>
                <p id="success-message" class="text-sm text-green-100 mt-1"></p>
            </div>
            <button onclick="document.getElementById('success-toast').classList.add('hidden')" class="text-green-200 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Error Notification Toast (Bottom) -->
    <div id="error-toast" wire:ignore class="hidden fixed bottom-4 left-4 right-4 md:left-4 md:right-auto md:w-96 z-50 bg-gradient-to-r from-red-500 to-red-600 text-white px-6 py-4 rounded-lg shadow-2xl animate-slide-in-up border-l-4 border-red-400">
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0 mt-0.5">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="flex-1">
                <p class="font-bold">Gagal!</p>
                <p id="error-message" class="text-sm text-red-100 mt-1"></p>
            </div>
            <button onclick="document.getElementById('error-toast').classList.add('hidden')" class="text-red-200 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    @if($status === 'ready' || $status === 'scanning')
        <!-- Camera Scanner View -->
        <div class="min-h-screen flex flex-col">
            <!-- Header bar -->
            <div class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-4 md:px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h1 class="text-xl md:text-2xl font-bold text-gray-900">Scanner Absensi</h1>
                        
                        <form method="POST" action="{{ route('filament.admin.auth.logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 flex items-center space-x-2 shadow-sm hover:shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="flex-1 p-4 md:p-6">
                <div class="w-full max-w-2xl mx-auto">
                    <!-- Camera container -->
                    <div class="relative aspect-square bg-black rounded-2xl border-4 border-blue-600 shadow-2xl overflow-hidden">
                        <div id="qr-reader" class="w-full h-full"></div>
                        
                        <!-- Scanning overlay -->
                        @if($status === 'scanning')
                        <div class="absolute inset-0 bg-black bg-opacity-50 flex flex-col items-center justify-center z-10">
                            <div class="text-center">
                                <div class="relative inline-block">
                                    <div class="animate-ping absolute inline-flex h-20 w-20 rounded-full bg-blue-500 opacity-75"></div>
                                    <div class="relative inline-flex rounded-full h-20 w-20 bg-blue-600 items-center justify-center">
                                        <svg class="w-10 h-10 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-white font-semibold mt-4 text-lg">Memproses...</p>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Camera Error Messages -->
                        <!-- Permission Denied -->
                        <div id="camera-permission-denied" class="hidden absolute inset-0 bg-gray-900 flex items-center justify-center p-6 z-20">
                            <div class="bg-white rounded-xl p-6 md:p-8 text-center max-w-md shadow-2xl">
                                <div class="flex justify-center mb-4">
                                    <div class="bg-red-100 rounded-full p-4">
                                        <svg class="w-16 h-16 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900 mb-3">Akses Kamera Ditolak</h3>
                                <p class="text-gray-700 mb-4 leading-relaxed">Scanner memerlukan akses kamera untuk memindai QR Code. Silakan izinkan akses kamera di browser Anda.</p>
                                
                                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 text-left">
                                    <p class="text-sm font-semibold text-blue-900 mb-2">Cara mengizinkan akses kamera:</p>
                                    <ol class="text-sm text-blue-800 space-y-1 list-decimal list-inside">
                                        <li>Klik ikon kunci/info di address bar</li>
                                        <li>Pilih "Izinkan" untuk kamera</li>
                                        <li>Klik tombol "Coba Lagi" di bawah</li>
                                    </ol>
                                </div>
                                
                                <div class="flex flex-col space-y-3">
                                    <button onclick="location.reload()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-200 shadow-md hover:shadow-lg flex items-center justify-center space-x-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        <span>Coba Lagi</span>
                                    </button>
                                    <a href="{{ route('filament.admin.pages.dashboard') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium transition-colors">
                                        Kembali ke Dashboard
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Camera Not Found -->
                        <div id="camera-not-found" class="hidden absolute inset-0 bg-gray-900 flex items-center justify-center p-6 z-20">
                            <div class="bg-white rounded-xl p-6 md:p-8 text-center max-w-md shadow-2xl">
                                <div class="flex justify-center mb-4">
                                    <div class="bg-yellow-100 rounded-full p-4">
                                        <svg class="w-16 h-16 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            <line x1="1" y1="1" x2="23" y2="23" stroke-linecap="round" stroke-width="2"></line>
                                        </svg>
                                    </div>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900 mb-3">Kamera Tidak Ditemukan</h3>
                                <p class="text-gray-700 mb-4 leading-relaxed">Tidak dapat menemukan kamera pada perangkat Anda. Pastikan kamera terpasang dan tidak digunakan aplikasi lain.</p>
                                
                                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6 text-left">
                                    <p class="text-sm font-semibold text-yellow-900 mb-2">Solusi yang dapat dicoba:</p>
                                    <ul class="text-sm text-yellow-800 space-y-1 list-disc list-inside">
                                        <li>Pastikan kamera tidak digunakan aplikasi lain</li>
                                        <li>Coba gunakan browser berbeda</li>
                                        <li>Restart perangkat Anda</li>
                                        <li>Gunakan perangkat dengan kamera</li>
                                    </ul>
                                </div>
                                
                                <div class="flex flex-col space-y-3">
                                    <button onclick="location.reload()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-200 shadow-md hover:shadow-lg flex items-center justify-center space-x-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        <span>Coba Lagi</span>
                                    </button>
                                    <a href="{{ route('filament.admin.pages.dashboard') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium transition-colors">
                                        Kembali ke Dashboard
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Camera Not Supported -->
                        <div id="camera-not-supported" class="hidden absolute inset-0 bg-gray-900 flex items-center justify-center p-6 z-20">
                            <div class="bg-white rounded-xl p-6 md:p-8 text-center max-w-md shadow-2xl">
                                <div class="flex justify-center mb-4">
                                    <div class="bg-red-100 rounded-full p-4">
                                        <svg class="w-16 h-16 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                        </svg>
                                    </div>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900 mb-3">Browser Tidak Didukung</h3>
                                <p class="text-gray-700 mb-4 leading-relaxed">Browser Anda tidak mendukung akses kamera. Silakan gunakan browser modern seperti Chrome, Firefox, atau Safari.</p>
                                
                                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 text-left">
                                    <p class="text-sm font-semibold text-red-900 mb-2">Browser yang didukung:</p>
                                    <ul class="text-sm text-red-800 space-y-1 list-disc list-inside">
                                        <li>Google Chrome (versi terbaru)</li>
                                        <li>Mozilla Firefox (versi terbaru)</li>
                                        <li>Safari (iOS 11+ / macOS)</li>
                                        <li>Microsoft Edge (versi terbaru)</li>
                                    </ul>
                                </div>
                                
                                <div class="flex flex-col space-y-3">
                                    <a href="{{ route('filament.admin.pages.dashboard') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-200 shadow-md hover:shadow-lg inline-block">
                                        Kembali ke Dashboard
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Generic Camera Error -->
                        <div id="camera-generic-error" class="hidden absolute inset-0 bg-gray-900 flex items-center justify-center p-6 z-20">
                            <div class="bg-white rounded-xl p-6 md:p-8 text-center max-w-md shadow-2xl">
                                <div class="flex justify-center mb-4">
                                    <div class="bg-orange-100 rounded-full p-4">
                                        <svg class="w-16 h-16 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900 mb-3">Gagal Mengakses Kamera</h3>
                                <p class="text-gray-700 mb-4 leading-relaxed">Terjadi kesalahan saat mengakses kamera. Silakan coba lagi atau hubungi administrator jika masalah berlanjut.</p>
                                
                                <div id="error-details" class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6 text-left hidden">
                                    <p class="text-xs font-semibold text-gray-700 mb-1">Detail Error:</p>
                                    <p class="text-xs text-gray-600 font-mono break-all" id="error-message-text"></p>
                                </div>
                                
                                <div class="flex flex-col space-y-3">
                                    <button onclick="location.reload()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-200 shadow-md hover:shadow-lg flex items-center justify-center space-x-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        <span>Coba Lagi</span>
                                    </button>
                                    <a href="{{ route('filament.admin.pages.dashboard') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium transition-colors">
                                        Kembali ke Dashboard
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status card -->
                    <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 rounded-xl p-6 shadow-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center flex-1">
                                <div class="flex-shrink-0">
                                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-lg font-semibold text-blue-900">Siap Memindai</p>
                                    <p class="text-sm text-blue-700 mt-1">Arahkan kamera ke QR Code untuk memindai</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button wire:click="forceReset" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 shadow-sm hover:shadow-md flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    <span>Reset</span>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    @elseif($status === 'success')
        <!-- Success Screen -->
        <div class="min-h-screen flex flex-col">
            <!-- Header bar -->
            <div class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-4 md:px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h1 class="text-xl md:text-2xl font-bold text-gray-900">Scanner Absensi</h1>
                        <form method="POST" action="{{ route('filament.admin.auth.logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 flex items-center space-x-2 shadow-sm hover:shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Success Content -->
            <div class="flex-1 p-4 md:p-6 flex items-center justify-center">
                <div class="w-full max-w-2xl">
                    <div class="bg-green-50 border-l-4 border-green-500 rounded-xl p-6 md:p-8 shadow-2xl animate-scale-in">
                        <!-- Success Icon -->
                        <div class="flex justify-center mb-4">
                            <div class="animate-bounce-once">
                                <svg class="w-16 h-16 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>

                        <h2 class="text-2xl font-bold text-green-900 text-center">Absensi Berhasil!</h2>

                        @if($scanResult)
                        <p class="text-center text-gray-700 mt-2 font-semibold">{{ $scanResult['mahasiswa_name'] }}</p>
                        <p class="text-center text-sm text-gray-600 mt-1">
                            @switch($scanResult['role'] ?? 'mahasiswa')
                                @case('mahasiswa')
                                    Mahasiswa
                                    @break
                                @case('pendamping1')
                                    Pendamping 1
                                    @break
                                @case('pendamping2')
                                    Pendamping 2
                                    @break
                                @default
                                    Peserta
                            @endswitch
                        </p>
                        @endif

                        <p class="text-sm text-green-700 text-center mt-3">Kembali ke scanner dalam 3 detik...</p>
                    </div>
                </div>
            </div>
        </div>

    @elseif($status === 'error')
        <!-- Error Screen -->
        <div class="min-h-screen flex flex-col">
            <!-- Header bar -->
            <div class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-4 md:px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h1 class="text-xl md:text-2xl font-bold text-gray-900">Scanner Absensi</h1>
                        <form method="POST" action="{{ route('filament.admin.auth.logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 flex items-center space-x-2 shadow-sm hover:shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Error Content -->
            <div class="flex-1 p-4 md:p-6 flex items-center justify-center">
                <div class="w-full max-w-2xl">
                    <div class="bg-red-50 border-l-4 border-red-500 rounded-xl p-6 md:p-8 shadow-2xl animate-shake">
                        <!-- Error Icon -->
                        <div class="flex justify-center mb-4">
                            <svg class="w-16 h-16 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>

                        <h2 class="text-2xl font-bold text-red-900 text-center mb-2">Gagal!</h2>

                        <p class="text-center text-gray-700 text-sm leading-relaxed">{{ $errorMessage }}</p>

                        <p class="text-sm text-red-700 text-center mt-3">Kembali ke scanner dalam 3 detik...</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
    @keyframes scale-in {
        from { opacity: 0; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1); }
    }
    
    @keyframes bounce-once {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-20px); }
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-10px); }
        20%, 40%, 60%, 80% { transform: translateX(10px); }
    }
    
    @keyframes slide-in-right {
        from { opacity: 0; transform: translateX(100%); }
        to { opacity: 1; transform: translateX(0); }
    }
    
    @keyframes slide-out-right {
        from { opacity: 1; transform: translateX(0); }
        to { opacity: 0; transform: translateX(100%); }
    }
    
    @keyframes spin-once {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    @keyframes slide-in-up {
        from { opacity: 0; transform: translateY(100%); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes slide-out-up {
        from { opacity: 1; transform: translateY(0); }
        to { opacity: 0; transform: translateY(100%); }
    }

    .animate-scale-in { animation: scale-in 0.3s ease-out; }
    .animate-bounce-once { animation: bounce-once 0.6s ease-out; }
    .animate-shake { animation: shake 0.5s ease-out; }
    .animate-slide-in-right { animation: slide-in-right 0.3s ease-out; }
    .animate-slide-out-right { animation: slide-out-right 0.3s ease-out; }
    .animate-spin-once { animation: spin-once 0.5s ease-out; }
    .animate-slide-in-up { animation: slide-in-up 0.3s ease-out; }
    .animate-slide-out-up { animation: slide-out-up 0.3s ease-out; }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
document.addEventListener('livewire:initialized', () => {
    let html5QrCode = null;
    let isProcessing = false;
    let resetTimeout = null;

    // Frontend duplicate prevention cache (5 second TTL)
    // Prevents same QR code from being scanned twice within 5 seconds
    const recentScans = new Map(); // Format: { qrData: timestamp }
    const DUPLICATE_PREVENTION_TTL_MS = 5000; // 5 seconds


    /**
     * Check if QR code was recently scanned (within 5 seconds)
     * @param {string} qrData
     * @returns {boolean}
     */
    function isDuplicateScan(qrData) {
        if (recentScans.has(qrData)) {
            const lastScanTime = recentScans.get(qrData);
            const timeSinceScan = Date.now() - lastScanTime;

            if (timeSinceScan < DUPLICATE_PREVENTION_TTL_MS) {
                console.log('Scanner: Frontend duplicate prevention - QR code scanned too recently', {
                    qr_data_preview: qrData.substring(0, 30) + '...',
                    time_since_scan_ms: timeSinceScan,
                    ttl_ms: DUPLICATE_PREVENTION_TTL_MS,
                });
                return true;
            }
        }

        return false;
    }

    /**
     * Record QR code scan in cache
     * @param {string} qrData
     */
    function recordScan(qrData) {
        recentScans.set(qrData, Date.now());
        console.log('Scanner: QR code recorded in frontend cache', {
            qr_data_preview: qrData.substring(0, 30) + '...',
            cache_size: recentScans.size,
        });
    }

    /**
     * Clean up expired entries from cache
     */
    function cleanupExpiredScans() {
        const now = Date.now();
        let cleaned = 0;

        for (const [qrData, timestamp] of recentScans.entries()) {
            if (now - timestamp > DUPLICATE_PREVENTION_TTL_MS) {
                recentScans.delete(qrData);
                cleaned++;
            }
        }

        if (cleaned > 0) {
            console.log('Scanner: Cleaned up expired scans from cache', {
                cleaned_count: cleaned,
                cache_size: recentScans.size,
            });
        }
    }

    // Clean up cache every 5 seconds
    setInterval(cleanupExpiredScans, DUPLICATE_PREVENTION_TTL_MS);

    /**
     * Show success notification toast
     * @param {string} message - Message to display
     */
    function showSuccessToast(message) {
        const toast = document.getElementById('success-toast');
        const messageEl = document.getElementById('success-message');

        if (toast && messageEl) {
            messageEl.textContent = message;
            toast.classList.remove('hidden');
            toast.classList.remove('animate-slide-out-up');
            toast.classList.add('animate-slide-in-up');

            // Ensure toast stays on top by forcing z-index
            toast.style.zIndex = '9999';

            console.log('[Toast] Success toast displayed with message:', message);

            // Auto-hide after 4 seconds
            const hideTimer = setTimeout(() => {
                toast.classList.remove('animate-slide-in-up');
                toast.classList.add('animate-slide-out-up');

                setTimeout(() => {
                    toast.classList.add('hidden');
                    toast.style.zIndex = '50';
                }, 300);
            }, 4000);

            // Store timer ID in case we need to cancel it
            toast.dataset.timeoutId = hideTimer;
        }
    }

    /**
     * Show error notification toast
     * @param {string} message - Message to display
     */
    function showErrorToast(message) {
        const toast = document.getElementById('error-toast');
        const messageEl = document.getElementById('error-message');

        if (toast && messageEl) {
            messageEl.textContent = message;
            toast.classList.remove('hidden');
            toast.classList.remove('animate-slide-out-up');
            toast.classList.add('animate-slide-in-up');

            // Ensure toast stays on top by forcing z-index
            toast.style.zIndex = '9999';

            console.log('[Toast] Error toast displayed with message:', message);

            // Auto-hide after 4 seconds
            const hideTimer = setTimeout(() => {
                toast.classList.remove('animate-slide-in-up');
                toast.classList.add('animate-slide-out-up');

                setTimeout(() => {
                    toast.classList.add('hidden');
                    toast.style.zIndex = '50';
                }, 300);
            }, 4000);

            // Store timer ID in case we need to cancel it
            toast.dataset.timeoutId = hideTimer;
        }
    }

    // Initialize scanner with comprehensive error handling
    function initScanner() {
        console.log('Scanner: Initializing camera scanner...');
        
        // Check if browser supports getUserMedia
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            console.error('Scanner: Browser does not support camera access');
            showCameraError('not-supported');
            return;
        }
        
        // Responsive QR box size based on screen width
        const screenWidth = window.innerWidth;
        const qrboxSize = screenWidth < 768 ? 300 : (screenWidth < 1024 ? 400 : 450);

        const config = {
            fps: 15,
            qrbox: { width: qrboxSize, height: qrboxSize },
            aspectRatio: 1.0
        };
        
        html5QrCode = new Html5Qrcode("qr-reader");
        
        console.log('Scanner: Starting camera with config:', config);
        
        html5QrCode.start(
            { facingMode: "environment" },
            config,
            onScanSuccess,
            onScanFailure
        ).then(() => {
            console.log('Scanner: Camera started successfully');
        }).catch(err => {
            console.error('Scanner: Failed to start camera:', err);
            handleCameraError(err);
        });
    }
    
    // Handle different camera error types
    function handleCameraError(error) {
        console.error('Scanner: Camera error details:', {
            name: error.name,
            message: error.message,
            type: typeof error,
            error: error
        });
        
        const errorMessage = error.message || error.toString();
        const errorName = error.name || '';
        
        // Determine error type and show appropriate message
        if (errorName === 'NotAllowedError' || errorMessage.includes('Permission denied') || errorMessage.includes('permission')) {
            console.log('Scanner: Camera permission denied by user');
            showCameraError('permission-denied');
        } else if (errorName === 'NotFoundError' || errorMessage.includes('not found') || errorMessage.includes('No camera')) {
            console.log('Scanner: No camera found on device');
            showCameraError('not-found');
        } else if (errorName === 'NotSupportedError' || errorMessage.includes('not supported')) {
            console.log('Scanner: Camera not supported by browser');
            showCameraError('not-supported');
        } else if (errorName === 'NotReadableError' || errorMessage.includes('Could not start video source')) {
            console.log('Scanner: Camera is already in use or hardware error');
            showCameraError('not-found'); // Show not-found error with solutions
        } else {
            console.log('Scanner: Generic camera error');
            showCameraError('generic', errorMessage);
        }
    }
    
    // Show appropriate camera error message
    function showCameraError(errorType, errorMessage = '') {
        console.log('Scanner: Showing camera error UI:', errorType);
        
        // Hide all error messages first
        document.getElementById('camera-permission-denied')?.classList.add('hidden');
        document.getElementById('camera-not-found')?.classList.add('hidden');
        document.getElementById('camera-not-supported')?.classList.add('hidden');
        document.getElementById('camera-generic-error')?.classList.add('hidden');
        
        // Show the appropriate error message
        switch(errorType) {
            case 'permission-denied':
                document.getElementById('camera-permission-denied')?.classList.remove('hidden');
                break;
            case 'not-found':
                document.getElementById('camera-not-found')?.classList.remove('hidden');
                break;
            case 'not-supported':
                document.getElementById('camera-not-supported')?.classList.remove('hidden');
                break;
            case 'generic':
            default:
                const genericError = document.getElementById('camera-generic-error');
                if (genericError) {
                    genericError.classList.remove('hidden');
                    
                    // Show error details if available
                    if (errorMessage) {
                        const errorDetails = document.getElementById('error-details');
                        const errorMessageText = document.getElementById('error-message-text');
                        if (errorDetails && errorMessageText) {
                            errorMessageText.textContent = errorMessage;
                            errorDetails.classList.remove('hidden');
                        }
                    }
                }
                break;
        }
    }
    
    function onScanSuccess(decodedText) {
        // Check for frontend duplicate scan (QR code scanned within last 5 seconds)
        if (isDuplicateScan(decodedText)) {
            console.log('Scanner: Ignoring duplicate scan detected by frontend cache', {
                qr_length: decodedText.length,
            });
            return;
        }

        // Only check if already processing to prevent duplicate scans during processing
        if (isProcessing) {
            console.log('Scanner: Already processing, ignoring scan');
            return;
        }

        // Check if status is ready before processing
        if (@this.status !== 'ready') {
            console.log('Scanner: Status not ready (' + @this.status + '), ignoring scan');
            return;
        }

        console.log('Scanner: QR detected, length=' + decodedText.length);

        // Record this scan in the frontend cache
        recordScan(decodedText);
        
        // Immediately pause scanner to prevent duplicate scans
        if (html5QrCode) {
            try {
                html5QrCode.pause(true);
                console.log('Scanner: Paused successfully');
            } catch (err) {
                console.error('Scanner: Failed to pause:', err);
            }
        }
        
        // Set processing flag
        isProcessing = true;
        
        // Call Livewire method to process the scan
        @this.scanQRCode(decodedText);
    }
    
    function onScanFailure(error) {
        // Silent - called frequently during scanning
    }
    
    /**
     * Resume scanner after pausing for a scan
     * Performs full stop and restart for clean state
     */
    function resumeScanner() {
        const resetStartTime = performance.now();

        console.log('Scanner: Resuming scanner...', {
            isProcessing: isProcessing,
            timestamp: new Date().toISOString()
        });

        // Clear processing flag
        isProcessing = false;

        // Full restart for clean state
        resumeScannerStandardMode(resetStartTime);
    }

    /**
     * Full restart - complete stop and reinitialize
     * Ensures clean state for reliable scanning
     */
    function resumeScannerStandardMode(startTime) {
        if (!html5QrCode) {
            console.error('Scanner: html5QrCode not initialized for standard resume');
            initScanner();
            return;
        }

        try {
            const state = html5QrCode.getState();
            console.log('Scanner: Standard mode - current state:', state);

            // Stop the scanner completely
            html5QrCode.stop().then(() => {
                console.log('Scanner: Stopped successfully for standard mode restart');

                // Wait before restarting to ensure clean state
                setTimeout(() => {
                    console.log('Scanner: Restarting scanner for standard mode fresh state...');
                    initScanner();

                    const duration = (performance.now() - startTime).toFixed(2);
                    console.log('Scanner: Standard mode restart completed', {
                        duration_ms: duration,
                        timestamp: new Date().toISOString()
                    });
                }, 100); // Optimized: 100ms delay before restart (faster than before)

            }).catch(err => {
                console.error('Scanner: Failed to stop in standard mode:', err);
                // If stop fails, try to reinitialize anyway
                console.log('Scanner: Attempting to reinitialize despite stop error...');
                setTimeout(() => {
                    initScanner();
                }, 100);
            });

        } catch (err) {
            console.error('Scanner: Error during standard mode restart:', err);
            // If error, try to reinitialize
            console.log('Scanner: Attempting to reinitialize...');
            setTimeout(() => {
                initScanner();
            }, 100);
        }
    }
    
    // Listen for scanner-ready event to resume scanning
    Livewire.on('scanner-ready', () => {
        const currentStatus = @this.status;
        
        console.log('Scanner: Received scanner-ready event', {
            current_status: currentStatus,
            isProcessing: isProcessing,
            timestamp: new Date().toISOString()
        });
        
        // Add minimal delay to ensure UI has fully updated and state is stable
        setTimeout(() => {
            // Double-check status before resuming
            const verifiedStatus = @this.status;

            if (verifiedStatus === 'ready') {
                console.log('Scanner: Status verified as ready, resuming...', {
                    verified_status: verifiedStatus,
                    delay_completed: true
                });
                resumeScanner();
            } else {
                console.log('Scanner: Status not ready, skipping resume', {
                    expected: 'ready',
                    actual: verifiedStatus,
                    reason: 'status_mismatch'
                });
            }
        }, 200); // Optimized: 200ms delay for faster scanning
    });

    // Protect toast elements from Livewire re-rendering
    // Add wire:ignore to toast elements in template to prevent Livewire from touching them
    // This ensures toast stays visible even during state updates

    // Listen for scan notification to show toast
    Livewire.on('show-scan-notification', (data) => {
        console.log('[Notification] Raw event data:', data);
        console.log('[Notification] Data type:', typeof data);
        console.log('[Notification] Data keys:', Object.keys(data));

        let notificationType = 'success';
        let notificationMessage = '';

        // Handle data - could be array with object inside or direct object
        if (typeof data === 'object' && data !== null) {
            // Check if it's an array with object inside: [{type: '...', message: '...'}]
            if (Array.isArray(data) && data.length > 0 && typeof data[0] === 'object') {
                notificationType = data[0].type || 'success';
                notificationMessage = data[0].message || '';
                console.log('[Notification] Got data from array with object inside');
            }
            // Try to get from object properties directly
            else if (data.type !== undefined && data.message !== undefined) {
                notificationType = data.type;
                notificationMessage = data.message;
                console.log('[Notification] Got data from object properties');
            }
        }

        console.log('[Notification] Processed - Type:', notificationType, 'Message:', notificationMessage);

        if (notificationMessage && notificationMessage.trim()) {
            console.log('[Notification] ✓ Showing', notificationType, 'toast with message:', notificationMessage);

            if (notificationType === 'success') {
                console.log('[Notification] Calling showSuccessToast()');
                showSuccessToast(notificationMessage);
            } else if (notificationType === 'error') {
                console.log('[Notification] Calling showErrorToast()');
                showErrorToast(notificationMessage);
            }
        } else {
            console.warn('[Notification] ✗ Message is empty, skipping toast');
        }
    });

    // Auto-reset after success/error
    Livewire.on('scanner-auto-reset', (event) => {
        const delay = event.delay || 3000;
        const currentStatus = @this.status;
        
        console.log('Scanner: Auto-reset scheduled', {
            delay_ms: delay,
            current_status: currentStatus,
            timestamp: new Date().toISOString()
        });
        
        // Clear any existing timeout to prevent multiple resets
        if (resetTimeout) {
            console.log('Scanner: Clearing previous reset timeout');
            clearTimeout(resetTimeout);
            resetTimeout = null;
        }
        
        // Verify scanner is paused (should be from onScanSuccess)
        if (html5QrCode) {
            try {
                const state = html5QrCode.getState();
                console.log('Scanner: Current scanner state:', state);
                
                // Ensure scanner is paused during the delay period
                if (state !== Html5QrcodeScannerState.PAUSED) {
                    console.log('Scanner: Pausing scanner for auto-reset delay');
                    html5QrCode.pause(true);
                }
            } catch (err) {
                console.error('Scanner: Error checking scanner state:', err);
            }
        }
        
        // Schedule the reset with exact 3 second delay
        resetTimeout = setTimeout(() => {
            console.log('Scanner: Executing auto-reset after delay', {
                delay_ms: delay,
                previous_status: currentStatus,
                timestamp: new Date().toISOString()
            });
            
            // Clear the timeout reference
            resetTimeout = null;
            
            // Call doReset to clean state and return to ready
            @this.doReset();
        }, delay);
    });
    
    // Listen for force reset completion to show feedback
    Livewire.on('scanner-force-reset-complete', () => {
        const previousState = {
            isProcessing: isProcessing,
            hasResetTimeout: !!resetTimeout
        };
        
        console.log('Scanner: Force reset completed, showing feedback toast', {
            previous_state: previousState,
            timestamp: new Date().toISOString()
        });
        
        // Clear any pending auto-reset timeout
        if (resetTimeout) {
            console.log('Scanner: Clearing auto-reset timeout due to force reset', {
                timeout_id: resetTimeout
            });
            clearTimeout(resetTimeout);
            resetTimeout = null;
        }
        
        // Clear all JavaScript state immediately on force reset
        isProcessing = false;
        
        console.log('Scanner: JavaScript state cleared on force reset', {
            previous_state: previousState,
            new_state: {
                isProcessing: false,
                hasResetTimeout: false
            },
            state_fully_cleared: true
        });
        
        // Verify scanner state and ensure it's ready to scan
        if (html5QrCode) {
            try {
                const scannerState = html5QrCode.getState();
                console.log('Scanner: Current scanner state after force reset:', scannerState);
                
                // If scanner is paused, it will be resumed by scanner-ready event
                if (scannerState === Html5QrcodeScannerState.PAUSED) {
                    console.log('Scanner: Scanner is paused, will be resumed by scanner-ready event');
                }
            } catch (err) {
                console.error('Scanner: Error checking scanner state after force reset:', err);
            }
        }
        
        // Show feedback toast with enhanced styling
        const toast = document.getElementById('reset-toast');
        if (toast) {
            console.log('Scanner: Displaying reset feedback toast');
            
            toast.classList.remove('hidden');
            toast.classList.remove('animate-slide-out-right');
            toast.classList.add('animate-slide-in-right');
            
            // Hide toast after 3 seconds (longer to ensure user sees it)
            setTimeout(() => {
                console.log('Scanner: Hiding reset feedback toast');
                toast.classList.remove('animate-slide-in-right');
                toast.classList.add('animate-slide-out-right');
                
                // Remove from DOM after animation completes
                setTimeout(() => {
                    toast.classList.add('hidden');
                }, 300);
            }, 3000);
        } else {
            console.error('Scanner: Reset toast element not found in DOM');
        }
    });


    // Initialize on load
    initScanner();
});
</script>
@endpush
