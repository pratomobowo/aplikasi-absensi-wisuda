<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>QnA - {{ config('app.name', 'Sistem Absensi Wisuda') }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .nav-scrolled {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
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
            <a href="{{ url('/alur-wisuda') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors duration-200">Alur Wisuda</a>
            <a href="{{ url('/buku-wisuda') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors duration-200">Buku Wisuda</a>
            <a href="{{ url('/help-desk') }}" class="block px-4 py-3 bg-blue-50 text-blue-600 rounded-lg">QnA</a>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Pertanyaan & Jawaban
                </div>
                <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold text-white mb-6 leading-tight">
                    QnA Wisuda
                </h1>
                <p class="text-lg sm:text-xl text-blue-50 max-w-3xl mx-auto leading-relaxed">
                    Temukan jawaban atas pertanyaan yang sering diajukan seputar wisuda
                </p>
            </div>
        </section>
        
        <!-- Content Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 gap-8">
                <!-- FAQ Section -->
                <div>
                    <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Pertanyaan yang Sering Diajukan (FAQ)</h2>
                        
                        <div class="space-y-4" x-data="{ openFaq: null }">
                            <!-- FAQ 1 -->
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <button 
                                    @click="openFaq = openFaq === 1 ? null : 1"
                                    class="w-full px-6 py-4 text-left bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center"
                                >
                                    <span class="font-semibold text-gray-900">Bagaimana cara mendaftar wisuda?</span>
                                    <svg 
                                        class="w-5 h-5 text-gray-500 transform transition-transform duration-200"
                                        :class="{ 'rotate-180': openFaq === 1 }"
                                        fill="none" 
                                        stroke="currentColor" 
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div 
                                    x-show="openFaq === 1"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                                    x-transition:enter-end="opacity-100 transform translate-y-0"
                                    class="px-6 py-4 bg-white"
                                    style="display: none;"
                                >
                                    <p class="text-gray-600 leading-relaxed">
                                        Pendaftaran wisuda dilakukan melalui sistem akademik universitas. Pastikan Anda telah memenuhi semua persyaratan akademik dan administrasi, kemudian login ke portal akademik dan ikuti petunjuk pendaftaran wisuda.
                                    </p>
                                </div>
                            </div>
                            
                            <!-- FAQ 2 -->
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <button 
                                    @click="openFaq = openFaq === 2 ? null : 2"
                                    class="w-full px-6 py-4 text-left bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center"
                                >
                                    <span class="font-semibold text-gray-900">Apa saja persyaratan untuk mengikuti wisuda?</span>
                                    <svg 
                                        class="w-5 h-5 text-gray-500 transform transition-transform duration-200"
                                        :class="{ 'rotate-180': openFaq === 2 }"
                                        fill="none" 
                                        stroke="currentColor" 
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div 
                                    x-show="openFaq === 2"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                                    x-transition:enter-end="opacity-100 transform translate-y-0"
                                    class="px-6 py-4 bg-white"
                                    style="display: none;"
                                >
                                    <p class="text-gray-600 leading-relaxed">
                                        Persyaratan wisuda meliputi: telah menyelesaikan seluruh mata kuliah, IPK memenuhi standar kelulusan, tidak memiliki tunggakan administrasi, telah menyelesaikan tugas akhir/skripsi, dan melengkapi berkas-berkas administrasi yang diperlukan.
                                    </p>
                                </div>
                            </div>
                            
                            <!-- FAQ 3 -->
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <button 
                                    @click="openFaq = openFaq === 3 ? null : 3"
                                    class="w-full px-6 py-4 text-left bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center"
                                >
                                    <span class="font-semibold text-gray-900">Bagaimana cara mendapatkan undangan wisuda digital?</span>
                                    <svg 
                                        class="w-5 h-5 text-gray-500 transform transition-transform duration-200"
                                        :class="{ 'rotate-180': openFaq === 3 }"
                                        fill="none" 
                                        stroke="currentColor" 
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div 
                                    x-show="openFaq === 3"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                                    x-transition:enter-end="opacity-100 transform translate-y-0"
                                    class="px-6 py-4 bg-white"
                                    style="display: none;"
                                >
                                    <p class="text-gray-600 leading-relaxed">
                                        Undangan wisuda digital akan dikirimkan melalui email setelah Anda menyelesaikan proses pendaftaran dan pembayaran. Undangan berisi QR Code yang dapat dibagikan kepada keluarga dan tamu undangan Anda.
                                    </p>
                                </div>
                            </div>
                            
                            <!-- FAQ 4 -->
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <button 
                                    @click="openFaq = openFaq === 4 ? null : 4"
                                    class="w-full px-6 py-4 text-left bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center"
                                >
                                    <span class="font-semibold text-gray-900">Berapa jumlah tamu yang bisa saya undang?</span>
                                    <svg 
                                        class="w-5 h-5 text-gray-500 transform transition-transform duration-200"
                                        :class="{ 'rotate-180': openFaq === 4 }"
                                        fill="none" 
                                        stroke="currentColor" 
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div 
                                    x-show="openFaq === 4"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                                    x-transition:enter-end="opacity-100 transform translate-y-0"
                                    class="px-6 py-4 bg-white"
                                    style="display: none;"
                                >
                                    <p class="text-gray-600 leading-relaxed">
                                        Setiap wisudawan dapat mengundang maksimal 4 tamu. Pastikan tamu Anda membawa undangan digital dengan QR Code untuk proses check-in di hari pelaksanaan wisuda.
                                    </p>
                                </div>
                            </div>
                            
                            <!-- FAQ 5 -->
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <button 
                                    @click="openFaq = openFaq === 5 ? null : 5"
                                    class="w-full px-6 py-4 text-left bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center"
                                >
                                    <span class="font-semibold text-gray-900">Kapan jadwal gladi bersih wisuda?</span>
                                    <svg 
                                        class="w-5 h-5 text-gray-500 transform transition-transform duration-200"
                                        :class="{ 'rotate-180': openFaq === 5 }"
                                        fill="none" 
                                        stroke="currentColor" 
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div 
                                    x-show="openFaq === 5"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                                    x-transition:enter-end="opacity-100 transform translate-y-0"
                                    class="px-6 py-4 bg-white"
                                    style="display: none;"
                                >
                                    <p class="text-gray-600 leading-relaxed">
                                        Jadwal gladi bersih akan diinformasikan melalui email dan portal akademik. Biasanya dilaksanakan 1-2 hari sebelum hari pelaksanaan wisuda. Kehadiran pada gladi bersih bersifat wajib.
                                    </p>
                                </div>
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
                    <h3 class="text-white text-lg font-semibold mb-4">Universitas Sanggabuana</h3>
                    <p class="text-sm leading-relaxed">Portal wisuda Universitas Sanggabuana dengan sistem terintegrasi.</p>
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
                        <li><a href="{{ url('/help-desk') }}" class="hover:text-blue-400 transition-colors duration-200">QnA</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-white text-lg font-semibold mb-4">Kontak</h3>
                    <ul class="space-y-2 text-sm">
                        <li>info@wisuda.ac.id</li>
                        <li>(021) 1234-5678</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                <p class="text-sm text-gray-400">&copy; {{ date('Y') }} Universitas Sanggabuana. All rights reserved.</p>
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
