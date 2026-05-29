<div>
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-700 pt-32 pb-20 md:pt-40 md:pb-28 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-400 rounded-full mix-blend-multiply filter blur-xl opacity-30"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-indigo-400 rounded-full mix-blend-multiply filter blur-xl opacity-30"></div>
        </div>
        <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-white text-sm font-semibold mb-6 border border-white/20">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Panduan Resmi
            </div>
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold text-white mb-4 leading-tight">
                Panduan Wisuda <span class="text-amber-300">XXIII</span>
            </h1>
            <p class="text-lg sm:text-xl text-blue-50 max-w-2xl mx-auto leading-relaxed">
                Panduan lengkap pelaksanaan wisuda gelombang 1 tahun akademik 2025/2026
            </p>
            <div class="mt-6 flex items-center justify-center gap-4">
                <a href="{{ $downloadUrl }}" download
                   class="inline-flex items-center px-5 py-2.5 bg-white/10 backdrop-blur-sm border border-white/20 text-white font-medium rounded-xl hover:bg-white/20 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Download PDF
                </a>
            </div>
        </div>
    </section>

    <!-- Flipbook Section -->
    <section class="bg-gray-100 py-8">
        <div class="max-w-6xl mx-auto px-4">
            <!-- DearFlip Flipbook Container -->
            <div id="flipbook-container" style="width: 100%; height: 75vh; background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%); border-radius: 12px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
                <div class="flex items-center justify-center h-full">
                    <div class="text-center text-gray-500">
                        <div class="animate-spin mb-4">
                            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                            </svg>
                        </div>
                        <p>Memuat Flipbook...</p>
                    </div>
                </div>
            </div>

            <!-- Navigation Hint -->
            <div class="mt-4 text-center text-sm text-gray-500">
                <p>Klik atau swipe untuk navigasi halaman • Gunakan tombol zoom untuk memperbesar</p>
            </div>
        </div>
    </section>

    <!-- Footer Banner -->
    <section class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 py-8">
        <div class="max-w-4xl mx-auto px-4 text-center text-white">
            <div class="flex items-center justify-center gap-3 mb-4">
                <div class="w-2 h-2 rounded-full bg-amber-400"></div>
                <span class="text-xs font-bold tracking-widest uppercase">Universitas Sangga Buana YPKP</span>
                <div class="w-2 h-2 rounded-full bg-amber-400"></div>
            </div>
            <h3 class="text-xl font-bold mb-2">Selamat kepada seluruh Wisudawan/Wisudawati!</h3>
            <p class="text-blue-100 text-sm">Wisuda XXIII · Gelombang I · Tahun Akademik 2025/2026 · Bandung</p>
        </div>
    </section>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/dflip/css/dflip.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/dflip/css/themify-icons.min.css') }}">
@endpush

@push('scripts')
<script>
    window.PDFJS = window.PDFJS || {};
    window.PDFJS.workerSrc = "{{ asset('vendor/dflip/js/libs/pdf.worker.min.js') }}";
</script>
<script src="{{ asset('vendor/dflip/js/libs/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/dflip/js/libs/pdf.min.js') }}"></script>
<script src="{{ asset('vendor/dflip/js/libs/three.min.js') }}"></script>
<script src="{{ asset('vendor/dflip/js/libs/mockup.min.js') }}"></script>
<script src="{{ asset('vendor/dflip/js/dflip.min.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            initializeFlipbook();
        }, 1500);
    });

    function initializeFlipbook() {
        const pdfUrl = "{{ $pdfUrl }}";
        const container = document.getElementById('flipbook-container');
        
        if (!container) {
            console.error('Container not found');
            return;
        }

        if (typeof $ === 'undefined' || !$.fn.flipBook) {
            console.error('jQuery or flipBook plugin not loaded');
            container.innerHTML = '<p class="text-red-500 p-6 text-center">Error: Required libraries not loaded. Please refresh the page.</p>';
            return;
        }

        container.innerHTML = '';

        try {
            $(container).flipBook(pdfUrl, {
                height: '100%',
                width: '100%',
                duration: 800,
                mode: 'html5',
                shading: 0.5,
                pageMode: 'double',
                controlsPosition: 'bottom'
            });
        } catch (error) {
            console.error('Error initializing flipbook:', error);
            container.innerHTML = '<p class="text-red-500 p-6 text-center">Error initializing flipbook: ' + error.message + '</p>';
        }
    }
</script>
@endpush
