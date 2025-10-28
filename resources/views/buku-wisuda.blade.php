<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buku Wisuda - {{ config('app.name', 'Sistem Absensi Wisuda') }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white font-sans antialiased" x-data="graduatesData()" @keydown.escape.window="mobileMenuOpen = false">
    <script>
        function graduatesData() {
            return {
                mobileMenuOpen: false,
                selectedFaculty: 'all',
                sortBy: 'name',
                searchQuery: '',
                graduates: [
                    { name: 'Ahmad Fauzi', npm: '2019010001', faculty: 'teknik', program: 'Teknik Informatika', gpa: 3.85, predicate: 'Cum Laude', color: 'blue' },
                    { name: 'Siti Nurhaliza', npm: '2019020045', faculty: 'ekonomi', program: 'Manajemen', gpa: 3.92, predicate: 'Cum Laude', color: 'green' },
                    { name: 'Budi Santoso', npm: '2019030078', faculty: 'hukum', program: 'Hukum', gpa: 3.67, predicate: 'Sangat Memuaskan', color: 'purple' },
                    { name: 'Dewi Lestari', npm: '2019040112', faculty: 'pertanian', program: 'Agroteknologi', gpa: 3.78, predicate: 'Cum Laude', color: 'amber' },
                    { name: 'Rizki Pratama', npm: '2019050089', faculty: 'ilkom', program: 'Sistem Informasi', gpa: 3.55, predicate: 'Sangat Memuaskan', color: 'red' },
                    { name: 'Maya Anggraini', npm: '2019060134', faculty: 'ekonomi', program: 'Akuntansi', gpa: 3.88, predicate: 'Cum Laude', color: 'indigo' },
                    { name: 'Andi Wijaya', npm: '2019010025', faculty: 'teknik', program: 'Teknik Sipil', gpa: 3.72, predicate: 'Cum Laude', color: 'blue' },
                    { name: 'Rina Kusuma', npm: '2019020067', faculty: 'ekonomi', program: 'Ekonomi Pembangunan', gpa: 3.45, predicate: 'Sangat Memuaskan', color: 'green' },
                    { name: 'Hendra Gunawan', npm: '2019030091', faculty: 'hukum', program: 'Hukum Pidana', gpa: 3.81, predicate: 'Cum Laude', color: 'purple' },
                ],
                get filteredGraduates() {
                    let filtered = this.graduates;
                    
                    // Filter by faculty
                    if (this.selectedFaculty !== 'all') {
                        filtered = filtered.filter(g => g.faculty === this.selectedFaculty);
                    }
                    
                    // Filter by search query
                    if (this.searchQuery.trim() !== '') {
                        const query = this.searchQuery.toLowerCase();
                        filtered = filtered.filter(g => 
                            g.name.toLowerCase().includes(query) || 
                            g.npm.includes(query) ||
                            g.program.toLowerCase().includes(query)
                        );
                    }
                    
                    // Sort
                    filtered = [...filtered].sort((a, b) => {
                        switch(this.sortBy) {
                            case 'name':
                                return a.name.localeCompare(b.name);
                            case 'npm':
                                return a.npm.localeCompare(b.npm);
                            case 'faculty':
                                return a.faculty.localeCompare(b.faculty);
                            case 'gpa':
                                return b.gpa - a.gpa; // Descending
                            default:
                                return 0;
                        }
                    });
                    
                    return filtered;
                },
                get totalGraduates() {
                    return this.graduates.length;
                },
                get s1Count() {
                    return Math.floor(this.graduates.length * 0.75);
                },
                get s2Count() {
                    return Math.floor(this.graduates.length * 0.21);
                },
                get s3Count() {
                    return this.graduates.length - this.s1Count - this.s2Count;
                }
            }
        }
    </script>
    <!-- Navigation Bar -->
    <nav id="navbar" class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-lg transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="{{ url('/') }}" class="flex items-center space-x-3 group">
                        <img src="{{ asset('images/icons/logo-sanggabuana.png') }}" alt="Logo E-Wisuda" class="h-12 w-auto transform group-hover:scale-110 transition-transform duration-200">
                        <span class="text-xl md:text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                            E-Wisuda
                        </span>
                    </a>
                </div>
                
                <!-- Desktop Menu -->
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
                        Help Desk
                    </x-nav-link>
                </div>
                
                <!-- Login Button (Desktop) -->
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
                
                <!-- Mobile Menu Button -->
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
    <div 
        x-show="mobileMenuOpen"
        x-transition:enter="transition-opacity ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="mobileMenuOpen = false"
        class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-40"
        style="display: none;"
    ></div>

    <div 
        x-show="mobileMenuOpen"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed top-0 right-0 bottom-0 w-full max-w-sm bg-white shadow-2xl z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Menu</h2>
            <button 
                @click="mobileMenuOpen = false"
                type="button"
                class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors duration-150"
            >
                <span class="sr-only">Close menu</span>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <nav class="p-4 space-y-2">
            <a href="{{ url('/') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors duration-200">
                Beranda
            </a>
            <a href="{{ url('/data-wisudawan') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors duration-200">
                Data Wisudawan
            </a>
            <a href="{{ url('/alur-wisuda') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors duration-200">
                Alur Wisuda
            </a>
            <a href="{{ url('/buku-wisuda') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors duration-200 bg-blue-50 text-blue-600">
                Buku Wisuda
            </a>
            <a href="{{ url('/help-desk') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors duration-200">
                Help Desk
            </a>
            <div class="pt-4 border-t border-gray-200">
                @auth
                    <a href="{{ url('/admin') }}" class="block w-full px-4 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 text-center">
                        Dashboard
                    </a>
                @else
                    <a href="{{ url('/admin/login') }}" class="block w-full px-4 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 text-center">
                        Login
                    </a>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    Buku Wisuda Digital
                </div>
                <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold text-white mb-6 leading-tight">
                    Buku Wisuda
                </h1>
                <p class="text-lg sm:text-xl text-blue-50 max-w-3xl mx-auto leading-relaxed">
                    Katalog digital profil wisudawan Universitas Sanggabuana
                </p>
            </div>
        </section>
        
        <!-- Filter & Sort Section -->
        <section class="bg-white border-b border-gray-200 sticky top-16 z-40 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex flex-col gap-4">
                    <!-- Search Bar -->
                    <div class="w-full">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cari Wisudawan</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                x-model="searchQuery"
                                placeholder="Cari berdasarkan nama, NPM, atau program studi..."
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            >
                        </div>
                    </div>
                    
                    <div class="flex flex-col md:flex-row gap-4 items-start md:items-end">
                        <!-- Filter by Faculty -->
                        <div class="w-full md:flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Filter Fakultas</label>
                            <select x-model="selectedFaculty" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="all">Semua Fakultas</option>
                                <option value="teknik">Fakultas Teknik</option>
                                <option value="ekonomi">Fakultas Ekonomi</option>
                                <option value="hukum">Fakultas Hukum</option>
                                <option value="pertanian">Fakultas Pertanian</option>
                                <option value="ilkom">Fakultas Ilmu Komputer</option>
                            </select>
                        </div>
                        
                        <!-- Sort Options -->
                        <div class="w-full md:flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Urutkan</label>
                            <select x-model="sortBy" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="name">Nama (A-Z)</option>
                                <option value="npm">NPM</option>
                                <option value="faculty">Fakultas</option>
                                <option value="gpa">IPK (Tertinggi)</option>
                            </select>
                        </div>
                        
                        <!-- Results Count -->
                        <div class="w-full md:w-auto">
                            <div class="px-4 py-2.5 bg-blue-50 text-blue-700 rounded-lg font-medium text-sm">
                                <span x-text="filteredGraduates.length"></span> wisudawan ditemukan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Graduates Grid Section -->
        <section class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Stats Summary -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
                    <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Total Wisudawan</p>
                                <p class="text-3xl font-bold text-blue-600" x-text="totalGraduates"></p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Sarjana (S1)</p>
                                <p class="text-3xl font-bold text-green-600" x-text="s1Count"></p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Magister (S2)</p>
                                <p class="text-3xl font-bold text-purple-600" x-text="s2Count"></p>
                            </div>
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Doktor (S3)</p>
                                <p class="text-3xl font-bold text-amber-600" x-text="s3Count"></p>
                            </div>
                            <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Graduates Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <template x-for="graduate in filteredGraduates" :key="graduate.npm">
                        <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden">
                            <div class="h-32 bg-gradient-to-br" :class="{
                                'from-blue-500 to-blue-700': graduate.color === 'blue',
                                'from-green-500 to-green-700': graduate.color === 'green',
                                'from-purple-500 to-purple-700': graduate.color === 'purple',
                                'from-amber-500 to-amber-700': graduate.color === 'amber',
                                'from-red-500 to-red-700': graduate.color === 'red',
                                'from-indigo-500 to-indigo-700': graduate.color === 'indigo'
                            }"></div>
                            <div class="p-6 -mt-16">
                                <div class="w-24 h-24 bg-gray-200 rounded-full border-4 border-white mx-auto mb-4 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="text-center">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-1" x-text="graduate.name"></h3>
                                    <p class="text-sm text-gray-600 mb-2">NPM: <span x-text="graduate.npm"></span></p>
                                    <span class="inline-flex items-center font-medium px-2 py-0.5 text-xs rounded" :class="{
                                        'bg-blue-100 text-blue-800': graduate.color === 'blue',
                                        'bg-green-100 text-green-800': graduate.color === 'green',
                                        'bg-purple-100 text-purple-800': graduate.color === 'purple',
                                        'bg-amber-100 text-amber-800': graduate.color === 'amber',
                                        'bg-red-100 text-red-800': graduate.color === 'red',
                                        'bg-indigo-100 text-indigo-800': graduate.color === 'indigo'
                                    }" x-text="graduate.program"></span>
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">IPK:</span>
                                            <span class="font-semibold text-gray-900" x-text="graduate.gpa.toFixed(2)"></span>
                                        </div>
                                        <div class="flex justify-between text-sm mt-2">
                                            <span class="text-gray-600">Predikat:</span>
                                            <span class="font-semibold" :class="{
                                                'text-green-600': graduate.predicate === 'Cum Laude',
                                                'text-blue-600': graduate.predicate === 'Sangat Memuaskan'
                                            }" x-text="graduate.predicate"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                
                <!-- Empty State -->
                <div x-show="filteredGraduates.length === 0" class="text-center py-16">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Tidak ada wisudawan ditemukan</h3>
                    <p class="mt-2 text-sm text-gray-500">Coba ubah filter atau kata kunci pencarian Anda.</p>
                </div>
                
                <!-- Info Text -->
                <div class="mt-12 text-center" x-show="filteredGraduates.length > 0">
                    <p class="text-sm text-gray-600">
                        Menampilkan <span class="font-semibold text-gray-900" x-text="filteredGraduates.length"></span> wisudawan
                        <span x-show="selectedFaculty !== 'all'"> dari <span class="font-semibold" x-text="selectedFaculty"></span></span>
                    </p>
                </div>
            </div>
        </section>
    </main>
    
    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                <div>
                    <h3 class="text-white font-semibold text-lg mb-4">Universitas Sanggabuana</h3>
                    <p class="text-sm text-gray-400">
                        Sistem Absensi Wisuda Digital untuk memudahkan pengelolaan kehadiran wisudawan.
                    </p>
                </div>
                <div>
                    <h3 class="text-white font-semibold text-lg mb-4">Menu</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ url('/') }}" class="text-gray-400 hover:text-white transition-colors">Beranda</a></li>
                        <li><a href="{{ url('/data-wisudawan') }}" class="text-gray-400 hover:text-white transition-colors">Data Wisudawan</a></li>
                        <li><a href="{{ url('/alur-wisuda') }}" class="text-gray-400 hover:text-white transition-colors">Alur Wisuda</a></li>
                        <li><a href="{{ url('/buku-wisuda') }}" class="text-gray-400 hover:text-white transition-colors">Buku Wisuda</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-white font-semibold text-lg mb-4">Kontak</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li>Email: info@unsan.ac.id</li>
                        <li>Telp: (022) 1234-5678</li>
                        <li>Alamat: Bandung, Jawa Barat</li>
                    </ul>
                </div>
            </div>
            <div class="text-center pt-8 border-t border-gray-800">
                <p class="text-sm text-gray-400">
                    &copy; {{ date('Y') }} Universitas Sanggabuana. All rights reserved.
                </p>
            </div>
        </div>
    </footer>
    
    <script>
        // Add shadow to navbar on scroll
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 10) {
                navbar.classList.add('shadow-md');
            } else {
                navbar.classList.remove('shadow-md');
            }
        });
    </script>
</body>
</html>
