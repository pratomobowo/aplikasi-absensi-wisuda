<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'E-Wisuda Universitas Sangga Buana' }}</title>
    <meta name="description" content="Portal E-Wisuda Universitas Sangga Buana - Sistem informasi wisuda terintegrasi untuk mahasiswa dan keluarga">
    <meta name="keywords" content="E-Wisuda, Universitas Sangga Buana, Wisuda, Graduation, USBP">
    <meta name="author" content="Universitas Sangga Buana">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="E-Wisuda Universitas Sangga Buana">
    <meta property="og:description" content="Portal E-Wisuda Universitas Sangga Buana - Sistem informasi wisuda terintegrasi untuk mahasiswa dan keluarga">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="E-Wisuda Universitas Sangga Buana">
    <meta name="twitter:description" content="Portal E-Wisuda Universitas Sangga Buana - Sistem informasi wisuda terintegrasi untuk mahasiswa dan keluarga">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .nav-scrolled {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased" x-data="{ mobileMenuOpen: false }" @keydown.escape.window="mobileMenuOpen = false">
    <!-- Navigation Bar -->
    <nav id="navbar" class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-lg transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex-shrink-0">
                    <a href="{{ url('/') }}" class="flex items-center space-x-3 group">
                        <img src="{{ asset('images/icons/logo-sanggabuana.png') }}" alt="Logo E-Wisuda" class="h-12 w-auto transform group-hover:scale-110 transition-transform duration-200">
                        <span class="text-xl md:text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                            E-Wisuda
                        </span>
                    </a>
                </div>
                
                <div class="hidden lg:flex items-center space-x-1">
                    <x-nav-link href="{{ url('/') }}" :active="request()->is('/')">
                        Beranda
                    </x-nav-link>
                    <x-nav-link href="{{ url('/data-wisudawan') }}" :active="request()->is('data-wisudawan')">
                        Data Wisudawan
                    </x-nav-link>
                    <x-nav-link href="{{ url('/alur-wisuda') }}" :active="request()->is('alur-wisuda')">
                        Alur Wisuda
                    </x-nav-link>
                    <x-nav-link href="{{ url('/buku-wisuda') }}" :active="request()->is('buku-wisuda')">
                        Buku Wisuda
                    </x-nav-link>
                    <x-nav-link href="{{ url('/help-desk') }}" :active="request()->is('help-desk')">
                        QnA
                    </x-nav-link>
                </div>
                
                <div class="hidden lg:block">
                    @auth
                        <a href="{{ url('/admin') }}" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold text-base rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg shadow-blue-500/30 hover:shadow-xl hover:shadow-blue-500/40 transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                            </svg>
                            Dashboard
                        </a>
                    @else
                        <a href="{{ url('/student/login') }}" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold text-base rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg shadow-blue-500/30 hover:shadow-xl hover:shadow-blue-500/40 transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            Login Mahasiswa
                        </a>
                    @endauth
                </div>
                
                <div class="lg:hidden">
                    <button @click="mobileMenuOpen = true" type="button" class="inline-flex items-center justify-center p-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-md">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Mobile Menu -->
    <div x-show="mobileMenuOpen" x-transition:enter="transition-opacity ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="mobileMenuOpen = false" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-40" style="display: none;"></div>

    <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="fixed top-0 right-0 bottom-0 w-3/5 bg-white shadow-2xl z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Menu</h2>
            <button @click="mobileMenuOpen = false" type="button" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors duration-150">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <nav class="p-4 space-y-2">
            <a href="{{ url('/') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors duration-200 {{ request()->is('/') ? 'bg-blue-50 text-blue-600' : '' }}">Beranda</a>
            <a href="{{ url('/data-wisudawan') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors duration-200 {{ request()->is('data-wisudawan') ? 'bg-blue-50 text-blue-600' : '' }}">Data Wisudawan</a>
            <a href="{{ url('/alur-wisuda') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors duration-200 {{ request()->is('alur-wisuda') ? 'bg-blue-50 text-blue-600' : '' }}">Alur Wisuda</a>
            <a href="{{ url('/buku-wisuda') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors duration-200 {{ request()->is('buku-wisuda') ? 'bg-blue-50 text-blue-600' : '' }}">Buku Wisuda</a>
            <a href="{{ url('/help-desk') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors duration-200 {{ request()->is('help-desk') ? 'bg-blue-50 text-blue-600' : '' }}">QnA</a>
            <div class="pt-4 border-t border-gray-200">
                @auth
                    <a href="{{ url('/admin') }}" class="block w-full px-4 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 text-center">Dashboard</a>
                @else
                    <a href="{{ url('/student/login') }}" class="block w-full px-4 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 text-center">Login Mahasiswa</a>
                @endauth
            </div>
        </nav>
    </div>
    
    <!-- Main Content -->
    {{ $slot }}
    
    <!-- Footer -->
    <footer class="relative bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 text-white mt-16 overflow-hidden">
        <!-- Decorative background elements -->
        <div class="absolute inset-0 overflow-hidden opacity-10">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-400 rounded-full mix-blend-multiply filter blur-xl"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-indigo-400 rounded-full mix-blend-multiply filter blur-xl"></div>
        </div>
        
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <img src="{{ asset('images/icons/logo-sanggabuana.png') }}" alt="Logo" class="h-10 w-auto">
                        <h3 class="text-white text-lg font-bold">E-Wisuda</h3>
                    </div>
                    <p class="text-sm leading-relaxed text-blue-100">Portal E-Wisuda Universitas Sangga Buana dengan sistem terintegrasi.</p>
                </div>
                <div>
                    <h3 class="text-white text-lg font-semibold mb-4">Menu Cepat</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ url('/') }}" class="text-blue-100 hover:text-white transition-colors duration-200 flex items-center group">
                            <svg class="w-4 h-4 mr-2 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            Beranda
                        </a></li>
                        <li><a href="{{ url('/data-wisudawan') }}" class="text-blue-100 hover:text-white transition-colors duration-200 flex items-center group">
                            <svg class="w-4 h-4 mr-2 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            Data Wisudawan
                        </a></li>
                        <li><a href="{{ url('/alur-wisuda') }}" class="text-blue-100 hover:text-white transition-colors duration-200 flex items-center group">
                            <svg class="w-4 h-4 mr-2 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            Alur Wisuda
                        </a></li>
                        <li><a href="{{ url('/buku-wisuda') }}" class="text-blue-100 hover:text-white transition-colors duration-200 flex items-center group">
                            <svg class="w-4 h-4 mr-2 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            Buku Wisuda
                        </a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-white text-lg font-semibold mb-4">Bantuan</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ url('/help-desk') }}" class="text-blue-100 hover:text-white transition-colors duration-200 flex items-center group">
                            <svg class="w-4 h-4 mr-2 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            QnA
                        </a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-white text-lg font-semibold mb-4">Kontak</h3>
                    <ul class="space-y-3 text-sm">
                        <li class="flex items-start text-blue-100">
                            <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            wisuda@usbypkp.ac.id
                        </li>
                        <li class="flex items-start text-blue-100">
                            <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            +62 22 7275489
                        </li>
                        <li class="flex items-start text-blue-100">
                            <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Jl. PH.H. Mustofa No.68, Cikutra, Kec. Cibeunying Kidul, Kota Bandung, Jawa Barat 40124
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/20 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm text-blue-100">&copy; {{ date('Y') }} E-Wisuda Universitas Sangga Buana. All rights reserved.</p>
                <div class="flex space-x-6 mt-4 md:mt-0">
                    <a href="#" class="text-blue-100 hover:text-white transition-colors duration-200 text-sm">Privacy</a>
                    <a href="#" class="text-blue-100 hover:text-white transition-colors duration-200 text-sm">Terms</a>
                </div>
            </div>
        </div>
    </footer>
    
    <script>
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 0) {
                navbar.classList.add('nav-scrolled');
            } else {
                navbar.classList.remove('nav-scrolled');
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>
