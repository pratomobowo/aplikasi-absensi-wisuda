<!-- Task 6.1: Scanner page layout with gray-50 background -->
<div class="min-h-screen bg-gray-50" x-data="scannerComponent()" wire:key="scanner-{{ $status }}">
    @if($status === 'ready' || $status === 'scanning')
        <!-- Camera Scanner View -->
        <div class="min-h-screen flex flex-col">
            <!-- Task 6.1: Header bar with white background and shadow -->
            <div class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-4 md:px-6 py-4">
                    <div class="flex items-center justify-between">
                        <!-- Title on the left -->
                        <h1 class="text-xl md:text-2xl font-bold text-gray-900">Scanner Absensi</h1>
                        
                        <!-- Logout button on the right -->
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

            <!-- Main Content Area with responsive padding -->
            <div class="flex-1 p-4 md:p-6">
                <!-- Task 6.2: Camera preview container -->
                <div class="w-full max-w-2xl mx-auto">
                    <!-- Camera container with aspect-video ratio -->
                    <div class="relative aspect-video bg-black rounded-2xl border-4 border-blue-600 shadow-2xl overflow-hidden">
                        <div id="qr-reader" class="w-full h-full"></div>
                        
                        <!-- Task 6.3: Scanning overlay with animation -->
                        @if($status === 'scanning')
                        <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center z-10">
                            <div class="text-center">
                                <!-- Pulse animation effect -->
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
                        
                        <!-- Camera Permission Message -->
                        <div id="camera-permission-message" class="hidden absolute inset-0 bg-gray-900 flex items-center justify-center p-6 z-20">
                            <div class="bg-white rounded-xl p-6 md:p-8 text-center max-w-md shadow-2xl">
                                <svg class="w-16 h-16 mx-auto mb-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Izin Kamera Diperlukan</h3>
                                <p class="text-gray-600 mb-4">Izinkan akses kamera untuk memindai QR Code</p>
                                <button onclick="location.reload()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-200 shadow-md hover:shadow-lg">
                                    Coba Lagi
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Task 6.4: Status card - Ready state -->
                    <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 rounded-xl p-6 shadow-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
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
                            <button @click="forceReset()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 shadow-sm hover:shadow-md">
                                Reset
                            </button>
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
            <div class="flex-1 p-4 md:p-6 flex items-center justify-center" x-init="playSuccessSound()">
                <div class="w-full max-w-2xl">
                    <!-- Task 6.4: Success status card with animations -->
                    <div class="bg-green-50 border-l-4 border-green-500 rounded-xl p-6 md:p-8 shadow-2xl animate-scale-in">
                        <!-- Success Icon with animation -->
                        <div class="flex justify-center mb-6">
                            <div class="animate-bounce-once">
                                <svg class="w-20 h-20 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Success Message -->
                        <h2 class="text-3xl font-bold text-green-900 text-center mb-6">Absensi Berhasil!</h2>
                        
                        @if($scanResult)
                        <!-- Task 6.5: Result display section -->
                        <div class="bg-white rounded-lg p-6 mb-6 shadow-md">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Nama</p>
                                    <p class="text-base font-semibold text-gray-900 mt-1">{{ $scanResult['mahasiswa_name'] }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600">NPM</p>
                                    <p class="text-base font-semibold text-gray-900 mt-1">{{ $scanResult['npm'] }}</p>
                                </div>
                            </div>
                            
                            <div class="border-t border-gray-200 pt-4 mt-4">
                                <p class="text-sm font-medium text-gray-600">Status</p>
                                <p class="text-lg font-semibold text-gray-900 mt-1 uppercase">
                                    @if($scanResult['role'] === 'mahasiswa')
                                        ✓ Mahasiswa
                                    @elseif($scanResult['role'] === 'pendamping1')
                                        ✓ Pendamping 1
                                    @elseif($scanResult['role'] === 'pendamping2')
                                        ✓ Pendamping 2
                                    @endif
                                </p>
                            </div>
                            
                            <div class="border-t border-gray-200 pt-4 mt-4">
                                <p class="text-sm font-medium text-gray-600">Waktu</p>
                                <p class="text-base font-semibold text-gray-900 mt-1">{{ now()->format('H:i:s') }}</p>
                            </div>
                        </div>
                        @endif

                        <!-- Task 6.6: Action buttons -->
                        <div class="flex flex-col md:flex-row gap-3">
                            <button wire:click="doReset" class="w-full md:flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-200 shadow-md hover:shadow-lg">
                                Scan Lagi
                            </button>
                        </div>

                        <p class="text-sm text-green-700 text-center mt-4">Kembali ke scanner dalam 3 detik...</p>
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
                    <!-- Task 6.4: Error status card with shake animation -->
                    <div class="bg-red-50 border-l-4 border-red-500 rounded-xl p-6 md:p-8 shadow-2xl animate-shake">
                        <!-- Error Icon with animation -->
                        <div class="flex justify-center mb-6">
                            <svg class="w-20 h-20 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>

                        <!-- Error Message -->
                        <h2 class="text-3xl font-bold text-red-900 text-center mb-6">Gagal!</h2>
                        
                        <div class="bg-white rounded-lg p-6 mb-6 shadow-md">
                            <p class="text-lg font-semibold text-gray-900 text-center leading-relaxed">{{ $errorMessage }}</p>
                        </div>

                        <!-- Task 6.6: Action buttons -->
                        <div class="flex flex-col md:flex-row gap-3">
                            <button wire:click="doReset" class="w-full md:flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-200 shadow-md hover:shadow-lg">
                                Scan Lagi
                            </button>
                        </div>
                        
                        <p class="text-sm text-red-700 text-center mt-4">Kembali ke scanner dalam 3 detik...</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>


@push('styles')
<style>
    /* Task 6.4: Scale-in and fade-in animations for success */
    @keyframes scale-in {
        from { 
            opacity: 0; 
            transform: scale(0.9); 
        }
        to { 
            opacity: 1; 
            transform: scale(1); 
        }
    }
    
    /* Task 6.3 & 6.4: Bounce animation for success icon */
    @keyframes bounce-once {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-20px); }
    }
    
    /* Task 6.4: Shake animation for error state */
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-10px); }
        20%, 40%, 60%, 80% { transform: translateX(10px); }
    }
    
    .animate-scale-in {
        animation: scale-in 0.3s ease-out;
    }
    
    .animate-bounce-once {
        animation: bounce-once 0.6s ease-out;
    }
    
    .animate-shake {
        animation: shake 0.5s ease-out;
    }
</style>
@endpush

@push('scripts')
<!-- html5-qrcode Library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
// Audio context for beep sound
let audioContext = null;

function playSuccessSound() {
    try {
        if (!audioContext) {
            audioContext = new (window.AudioContext || window.webkitAudioContext)();
        }
        
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.value = 800; // Frequency in Hz
        oscillator.type = 'sine';
        
        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);
        
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.2);
    } catch (error) {
        console.log('Audio playback not supported:', error);
    }
}

function scannerComponent() {
    return {
        html5QrCode: null,
        isScanning: false,
        scanTimeout: null,
        
        init() {
            this.initializeScanner();
            this.setupLivewireListeners();
            
            // Listen for Livewire updates
            this.$watch('$wire.status', (value) => {
                console.log('Status changed to:', value);
            });
        },
        
        initializeScanner() {
            const config = {
                fps: 10,
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0
            };
            
            this.html5QrCode = new Html5Qrcode("qr-reader");
            
            // Start scanning
            this.html5QrCode.start(
                { facingMode: "environment" }, // Use back camera
                config,
                this.onScanSuccess.bind(this),
                this.onScanFailure.bind(this)
            ).catch(err => {
                console.error('Failed to start scanner:', err);
                this.showCameraPermissionMessage();
            });
        },
        
        onScanSuccess(decodedText, decodedResult) {
            if (this.isScanning) {
                console.log('Already scanning, ignoring duplicate scan');
                return; // Prevent multiple scans
            }
            
            console.log('QR Code detected:', decodedText.substring(0, 20) + '...');
            this.isScanning = true;
            
            // Clear any existing timeout
            if (this.scanTimeout) {
                clearTimeout(this.scanTimeout);
            }
            
            // Stop scanner temporarily
            if (this.html5QrCode) {
                this.html5QrCode.pause(true);
                console.log('Scanner paused for processing');
            }
            
            // Set a safety timeout to reset scanner if something goes wrong
            this.scanTimeout = setTimeout(() => {
                console.warn('Scanner timeout (10s) - forcing reset');
                this.forceReset();
            }, 10000); // 10 seconds timeout
            
            // Send to Livewire
            @this.call('scanQRCode', decodedText)
                .then(() => {
                    console.log('Livewire call completed successfully');
                })
                .catch(err => {
                    console.error('Livewire call failed:', err);
                    // Reset on error
                    this.forceReset();
                });
        },
        
        forceReset() {
            console.log('Force reset triggered');
            if (this.scanTimeout) {
                clearTimeout(this.scanTimeout);
                this.scanTimeout = null;
            }
            this.isScanning = false;
            if (this.html5QrCode) {
                try {
                    this.html5QrCode.resume();
                    console.log('Scanner force resumed');
                } catch (err) {
                    console.error('Failed to resume scanner:', err);
                }
            }
        },
        
        onScanFailure(error) {
            // Silent fail - this is called frequently during scanning
        },
        
        showCameraPermissionMessage() {
            document.getElementById('camera-permission-message').classList.remove('hidden');
        },
        
        setupLivewireListeners() {
            // Listen for scanner reset
            Livewire.on('scanner-reset', () => {
                console.log('Scanner reset event received');
                if (this.scanTimeout) {
                    clearTimeout(this.scanTimeout);
                    this.scanTimeout = null;
                }
                this.isScanning = false;
                if (this.html5QrCode) {
                    try {
                        this.html5QrCode.resume();
                        console.log('Scanner resumed successfully');
                    } catch (err) {
                        console.error('Failed to resume scanner:', err);
                    }
                }
            });
            
            // Listen for schedule reset
            Livewire.on('schedule-reset', (event) => {
                // Handle both Livewire v2 and v3 event formats
                const delay = event?.delay || event?.[0]?.delay || 3000;
                console.log('Schedule reset with delay:', delay);
                setTimeout(() => {
                    @this.call('doReset').catch(err => {
                        console.error('Failed to reset:', err);
                        this.forceReset();
                    });
                }, delay);
            });
            
            // Listen for scan success - clear timeout since backend processed successfully
            Livewire.on('scan-success', () => {
                console.log('Scan success event received');
                // Clear the safety timeout since backend responded
                if (this.scanTimeout) {
                    clearTimeout(this.scanTimeout);
                    this.scanTimeout = null;
                }
                this.isScanning = true;
                if (this.html5QrCode) {
                    this.html5QrCode.pause(true);
                }
            });
            
            // Listen for scan error - clear timeout since backend processed
            Livewire.on('scan-error', () => {
                console.log('Scan error event received');
                // Clear the safety timeout since backend responded
                if (this.scanTimeout) {
                    clearTimeout(this.scanTimeout);
                    this.scanTimeout = null;
                }
                this.isScanning = true;
                if (this.html5QrCode) {
                    this.html5QrCode.pause(true);
                }
            });
        }
    }
}
</script>
@endpush
