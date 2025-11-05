<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Alur Wisuda - {{ config('app.name', 'Sistem Absensi Wisuda') }}</title>
    
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
        
        .timeline-line {
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom, #3b82f6, #1e40af);
        }
        
        @media (max-width: 768px) {
            .timeline-line {
                left: 20px;
            }
        }
    </style>
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
                        <a href="{{ url('/admin/login') }}" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold text-base rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg shadow-blue-500/30 hover:shadow-xl hover:shadow-blue-500/40 transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            Login
                        </a>
                    @endauth
                </div>
                
                <div class="lg:hidden">
                    <button @click="mobileMenuOpen = true" type="button" class="inline-flex items-center justify-center p-2.5 rounded-xl text-gray-700 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200">
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

    <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="fixed top-0 right-0 bottom-0 w-full max-w-sm bg-white shadow-2xl z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Menu</h2>
            <button @click="mobileMenuOpen = false" type="button" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors duration-150">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <nav class="p-4 space-y-2">
            <a href="{{ url('/') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors duration-200">Beranda</a>
            <a href="{{ url('/data-wisudawan') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors duration-200">Data Wisudawan</a>
            <a href="{{ url('/alur-wisuda') }}" class="block px-4 py-3 bg-blue-50 text-blue-600 rounded-lg">Alur Wisuda</a>
            <a href="{{ url('/buku-wisuda') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors duration-200">Buku Wisuda</a>
            <a href="{{ url('/help-desk') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors duration-200">Help Desk</a>
            <div class="pt-4 border-t border-gray-200">
                @auth
                    <a href="{{ url('/admin') }}" class="block w-full px-4 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 text-center">Dashboard</a>
                @else
                    <a href="{{ url('/admin/login') }}" class="block w-full px-4 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 text-center">Login</a>
                @endauth
            </div>
        </nav>
    </div>
    
    <!-- Main Content -->
    <main class="pt-20">
        <!-- Page Header -->
        <section class="relative bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-700 pt-32 pb-20 overflow-hidden">
            <!-- Decorative elements -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-400 rounded-full mix-blend-multiply filter blur-xl opacity-30"></div>
                <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-indigo-400 rounded-full mix-blend-multiply filter blur-xl opacity-30"></div>
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-purple-400 rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
            </div>
            
            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-white text-sm font-medium mb-6 border border-white/20">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    Panduan Wisuda
                </div>
                <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold text-white mb-6 leading-tight">
                    Alur Wisuda
                </h1>
                <p class="text-lg sm:text-xl text-blue-50 max-w-3xl mx-auto leading-relaxed">
                    Panduan lengkap prosedur pelaksanaan wisuda
                </p>
            </div>
        </section>
        
        <!-- Timeline Section -->
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="relative">
                <!-- Timeline Line -->
                <div class="timeline-line hidden md:block"></div>
                
                <!-- Timeline Items -->
                <div class="space-y-12">
                    <!-- Step 1 -->
                    <div class="relative flex items-center md:justify-start">
                        <div class="absolute left-0 md:left-1/2 md:transform md:-translate-x-1/2 w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg z-10">
                            1
                        </div>
                        <div class="ml-16 md:ml-0 md:w-1/2 md:pr-12 md:text-right">
                            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                                <div class="flex items-center mb-3 md:justify-end">
                                    <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <h3 class="text-xl font-bold text-gray-900">Pendaftaran Wisuda</h3>
                                </div>
                                <p class="text-gray-600 leading-relaxed">
                                    Mahasiswa melakukan pendaftaran wisuda melalui sistem akademik dengan melengkapi persyaratan administrasi dan akademik yang telah ditentukan.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 2 -->
                    <div class="relative flex items-center md:justify-end">
                        <div class="absolute left-0 md:left-1/2 md:transform md:-translate-x-1/2 w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg z-10">
                            2
                        </div>
                        <div class="ml-16 md:ml-0 md:w-1/2 md:pl-12">
                            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                                <div class="flex items-center mb-3">
                                    <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <h3 class="text-xl font-bold text-gray-900">Verifikasi Berkas</h3>
                                </div>
                                <p class="text-gray-600 leading-relaxed">
                                    Tim akademik melakukan verifikasi kelengkapan berkas dan persyaratan wisuda. Mahasiswa akan diberitahu jika ada kekurangan dokumen.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 3 -->
                    <div class="relative flex items-center md:justify-start">
                        <div class="absolute left-0 md:left-1/2 md:transform md:-translate-x-1/2 w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg z-10">
                            3
                        </div>
                        <div class="ml-16 md:ml-0 md:w-1/2 md:pr-12 md:text-right">
                            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                                <div class="flex items-center mb-3 md:justify-end">
                                    <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <h3 class="text-xl font-bold text-gray-900">Pembayaran</h3>
                                </div>
                                <p class="text-gray-600 leading-relaxed">
                                    Mahasiswa melakukan pembayaran biaya wisuda sesuai dengan ketentuan yang berlaku melalui sistem pembayaran universitas.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 4 -->
                    <div class="relative flex items-center md:justify-end">
                        <div class="absolute left-0 md:left-1/2 md:transform md:-translate-x-1/2 w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg z-10">
                            4
                        </div>
                        <div class="ml-16 md:ml-0 md:w-1/2 md:pl-12">
                            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                                <div class="flex items-center mb-3">
                                    <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                    </svg>
                                    <h3 class="text-xl font-bold text-gray-900">Penerimaan Undangan</h3>
                                </div>
                                <p class="text-gray-600 leading-relaxed">
                                    Mahasiswa menerima undangan digital wisuda dengan QR Code yang dapat dibagikan kepada keluarga dan tamu undangan.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 5 -->
                    <div class="relative flex items-center md:justify-start">
                        <div class="absolute left-0 md:left-1/2 md:transform md:-translate-x-1/2 w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg z-10">
                            5
                        </div>
                        <div class="ml-16 md:ml-0 md:w-1/2 md:pr-12 md:text-right">
                            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                                <div class="flex items-center mb-3 md:justify-end">
                                    <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                                    </svg>
                                    <h3 class="text-xl font-bold text-gray-900">Gladi Bersih</h3>
                                </div>
                                <p class="text-gray-600 leading-relaxed">
                                    Mahasiswa mengikuti gladi bersih untuk mempersiapkan prosesi wisuda. Kehadiran wajib untuk memastikan kelancaran acara.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 6 -->
                    <div class="relative flex items-center md:justify-end">
                        <div class="absolute left-0 md:left-1/2 md:transform md:-translate-x-1/2 w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg z-10">
                            6
                        </div>
                        <div class="ml-16 md:ml-0 md:w-1/2 md:pl-12">
                            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                                <div class="flex items-center mb-3">
                                    <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <h3 class="text-xl font-bold text-gray-900">Hari H Wisuda</h3>
                                </div>
                                <p class="text-gray-600 leading-relaxed">
                                    Wisudawan hadir pada hari pelaksanaan wisuda. Absensi dilakukan dengan scan QR Code di pintu masuk untuk pencatatan kehadiran.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 7 -->
                    <div class="relative flex items-center md:justify-start">
                        <div class="absolute left-0 md:left-1/2 md:transform md:-translate-x-1/2 w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg z-10">
                            7
                        </div>
                        <div class="ml-16 md:ml-0 md:w-1/2 md:pr-12 md:text-right">
                            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                                <div class="flex items-center mb-3 md:justify-end">
                                    <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    <h3 class="text-xl font-bold text-gray-900">Penyerahan Ijazah</h3>
                                </div>
                                <p class="text-gray-600 leading-relaxed">
                                    Setelah prosesi wisuda selesai, wisudawan menerima ijazah dan transkrip nilai sebagai bukti kelulusan resmi dari universitas.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-white text-lg font-semibold mb-4">Universitas Sangga Buana</h3>
                    <p class="text-sm leading-relaxed">Portal wisuda Universitas Sangga Buana dengan sistem terintegrasi.</p>
                </div>
                <div>
                    <h3 class="text-white text-lg font-semibold mb-4">Menu Cepat</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ url('/') }}" class="hover:text-blue-400 transition-colors duration-200">Beranda</a></li>
                        <li><a href="{{ url('/data-wisudawan') }}" class="hover:text-blue-400 transition-colors duration-200">Data Wisudawan</a></li>
                        <li><a href="{{ url('/alur-wisuda') }}" class="hover:text-blue-400 transition-colors duration-200">Alur Wisuda</a></li>
                        <li><a href="{{ url('/buku-wisuda') }}" class="hover:text-blue-400 transition-colors duration-200">Buku Wisuda</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-white text-lg font-semibold mb-4">Bantuan</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ url('/help-desk') }}" class="hover:text-blue-400 transition-colors duration-200">Help Desk</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-white text-lg font-semibold mb-4">Kontak</h3>
                    <ul class="space-y-2 text-sm">
                        <li>wisuda@usbypkp.ac.id</li>
                        <li>+62 22 7275489</li>
                        <li>Jl. PH.H. Mustofa No.68, Cikutra, Kec. Cibeunying Kidul, Kota Bandung, Jawa Barat 40124</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                <p class="text-sm text-gray-400">&copy; {{ date('Y') }} Universitas Sangga Buana. All rights reserved.</p>
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
</body>
</html>
