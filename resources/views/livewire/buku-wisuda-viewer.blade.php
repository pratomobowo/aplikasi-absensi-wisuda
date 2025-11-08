<div class="w-full h-screen">
        @if ($bukuWisuda)
            <!-- DearFlip Flipbook Container - Full Screen -->
            <div id="flipbook-container" style="width: 100%; height: 100%; background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%); margin: 0; padding: 0;">
                <div class="flex items-center justify-center h-full">
                    <div class="text-center text-gray-500">
                        <div class="animate-spin mb-4">
                            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                            </svg>
                        </div>
                        <p>Loading Flipbook...</p>
                    </div>
                </div>
            </div>

            <!-- DearFlip Library (Self-hosted) -->
            <link rel="stylesheet" href="{{ asset('vendor/dflip/css/dflip.min.css') }}">
            <link rel="stylesheet" href="{{ asset('vendor/dflip/css/themify-icons.min.css') }}">

            <script>
                // Set worker source BEFORE loading libraries
                window.PDFJS = window.PDFJS || {};
                window.PDFJS.workerSrc = "{{ asset('vendor/dflip/js/libs/pdf.worker.min.js') }}";
                console.log('PDF.js worker source configured:', window.PDFJS.workerSrc);
            </script>

            <script src="{{ asset('vendor/dflip/js/libs/jquery.min.js') }}"></script>
            <script src="{{ asset('vendor/dflip/js/libs/pdf.min.js') }}"></script>
            <script src="{{ asset('vendor/dflip/js/libs/three.min.js') }}"></script>
            <script src="{{ asset('vendor/dflip/js/libs/mockup.min.js') }}"></script>
            <script src="{{ asset('vendor/dflip/js/dflip.min.js') }}"></script>

            <script>
                console.log('All DearFlip scripts loaded');
                console.log('DFLIP available:', typeof DFLIP !== 'undefined');
                console.log('jQuery available:', typeof jQuery !== 'undefined' || typeof $ !== 'undefined');

                // Initialize when page is fully loaded
                function initializeFlipbook() {
                    console.log('Initializing flipbook...');

                    const pdfUrl = "{{ $pdfUrl }}";
                    const container = document.getElementById('flipbook-container');
                    const $ = jQuery;

                    console.log('PDF URL:', pdfUrl);
                    console.log('Container found:', !!container);
                    console.log('jQuery available:', !!$);

                    if (!container) {
                        console.error('Container not found');
                        return;
                    }

                    if (typeof $ === 'undefined' || !$.fn.flipBook) {
                        console.error('jQuery or flipBook plugin not loaded');
                        container.innerHTML = '<p class="text-red-500 p-6">Error: Required libraries not loaded. Please refresh the page.</p>';
                        return;
                    }

                    // Clear loading content
                    container.innerHTML = '';

                    try {
                        console.log('Creating flipbook with jQuery plugin');

                        // Initialize flipbook using jQuery plugin method
                        $(container).flipBook(pdfUrl, {
                            height: '100%',
                            width: '100%',
                            duration: 800,
                            mode: 'html5',
                            shading: 0.5,
                            pageMode: DFLIP.PAGE_MODE.DOUBLE,
                            controlsPosition: 'bottom'
                        });

                        console.log('Flipbook initialized successfully');
                    } catch (error) {
                        console.error('Error initializing flipbook:', error);
                        container.innerHTML = '<p class="text-red-500 p-6">Error initializing flipbook: ' + error.message + '<br>Check browser console for details.</p>';
                    }
                }

                // Use multiple strategies to wait for full page load
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', function() {
                        console.log('DOMContentLoaded event fired');
                        setTimeout(initializeFlipbook, 1500);
                    });
                } else {
                    console.log('DOM already loaded, initializing immediately');
                    setTimeout(initializeFlipbook, 1500);
                }

                // Also try on window load
                window.addEventListener('load', function() {
                    console.log('Window load event fired');
                });
            </script>

            <style>
                #flipbook {
                    background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);
                }

                .dearflip-canvas {
                    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
                }

                .dearflip-controls {
                    background: rgba(0, 0, 0, 0.7);
                    border-top: 1px solid rgba(255, 255, 255, 0.1);
                }
            </style>
        @else
            <div class="w-full h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 flex items-center justify-center">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-purple-500/20 mb-4">
                        <svg class="w-8 h-8 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">Buku Wisuda Tidak Tersedia</h3>
                    <p class="text-purple-200">Saat ini belum ada buku wisuda untuk acara yang aktif. Silakan hubungi administrator.</p>
                </div>
            </div>
        @endif
</div>
