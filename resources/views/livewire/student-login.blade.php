<div>
    <!-- Split Screen Layout -->
    <div class="min-h-screen flex">
        <!-- Left Side - Hero Section -->
        <div class="hidden lg:flex lg:w-1/2 relative bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-700 overflow-hidden">
            <!-- Decorative elements -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-400 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-pulse"></div>
                <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-indigo-400 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-pulse" style="animation-delay: 1s;"></div>
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-purple-400 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse" style="animation-delay: 2s;"></div>
            </div>

            <!-- Hero Content -->
            <div class="relative z-10 flex flex-col justify-center px-12 xl:px-16 text-white">
                <!-- Logo -->
                <div class="mb-8">
                    <img src="{{ asset('images/icons/logo-sanggabuana.png') }}" alt="Logo USB" class="h-20 w-auto">
                </div>

                <!-- Welcome Text -->
                <div class="mb-8">
                    <div class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-white text-sm font-medium mb-6 border border-white/20">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Portal Mahasiswa
                    </div>
                    <h1 class="text-5xl xl:text-6xl font-bold mb-4 leading-tight">
                        Selamat Datang di<br/>Portal E-Wisuda
                    </h1>
                    <p class="text-xl text-blue-50 leading-relaxed">
                        Login untuk mengakses dashboard Anda dan upload foto wisuda
                    </p>
                </div>

                <!-- Features -->
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg">Upload Foto Wisuda</h3>
                            <p class="text-blue-100 text-sm">Upload dan kelola foto wisuda Anda dengan mudah</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg">Informasi Akademik</h3>
                            <p class="text-blue-100 text-sm">Lihat informasi akademik dan data wisuda Anda</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg">Dashboard Pribadi</h3>
                            <p class="text-blue-100 text-sm">Akses dashboard pribadi untuk kebutuhan wisuda</p>
                        </div>
                    </div>
                </div>

                <!-- Back to Home Link -->
                <div class="mt-12">
                    <a href="{{ url('/') }}" class="inline-flex items-center text-white hover:text-blue-100 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span class="font-medium">Kembali ke Beranda</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12 bg-gray-50">
            <div class="w-full max-w-md">
                <!-- Mobile Logo & Back Button -->
                <div class="lg:hidden mb-8 text-center">
                    <a href="{{ url('/') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 transition-colors duration-200 mb-4">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span class="font-medium">Kembali ke Beranda</span>
                    </a>
                    <img src="{{ asset('images/icons/logo-sanggabuana.png') }}" alt="Logo USB" class="h-16 w-auto mx-auto mb-4">
                </div>

                <!-- Login Card -->
                <div class="bg-white rounded-2xl shadow-xl p-8 lg:p-10">
                    <!-- Header -->
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">Login Mahasiswa</h2>
                        <p class="text-gray-600">Silakan masukkan NPM dan password Anda</p>
                    </div>

                    <!-- Login Form -->
                    <form wire:submit="login">
                        <!-- NPM Input -->
                        <div class="mb-6">
                            <label for="npm" class="block text-sm font-semibold text-gray-700 mb-2">
                                NPM (Nomor Pokok Mahasiswa)
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <input
                                    type="text"
                                    id="npm"
                                    wire:model="npm"
                                    placeholder="Contoh: 2023040123"
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('npm') border-red-500 @enderror"
                                    autofocus
                                >
                            </div>
                            @error('npm')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Password Input -->
                        <div class="mb-6">
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                Password
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <input
                                    type="{{ $showPassword ? 'text' : 'password' }}"
                                    id="password"
                                    wire:model="password"
                                    placeholder="Masukkan password Anda"
                                    class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('password') border-red-500 @enderror"
                                >
                                <button
                                    type="button"
                                    wire:click="togglePasswordVisibility"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none transition-colors duration-200"
                                >
                                    @if($showPassword)
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-4.803m5.596-3.856a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0c0 1.657-.672 3.157-1.757 4.243A6 6 0 0121 12c0-3.314-2.686-6-6-6-1.657 0-3.157.672-4.243 1.757M15 12a3 3 0 01-6 0m6 0a3 3 0 00-6 0m0 0a6 6 0 01-12 0c0-3.314 2.686-6 6-6s6 2.686 6 6"></path>
                                        </svg>
                                    @else
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    @endif
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="mb-6">
                            <label class="flex items-center cursor-pointer">
                                <input
                                    type="checkbox"
                                    wire:model="remember"
                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 transition-all duration-200"
                                >
                                <span class="ml-2 text-sm font-medium text-gray-700">Ingat saya selama 30 hari</span>
                            </label>
                        </div>

                        <!-- Login Button -->
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 px-4 rounded-lg font-semibold hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-4 focus:ring-blue-300 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                        >
                            <span wire:loading.remove wire:target="login" class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                </svg>
                                Login ke Dashboard
                            </span>
                            <span wire:loading wire:target="login" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Memproses...
                            </span>
                        </button>
                    </form>

                    <!-- Info -->
                    <div class="mt-8 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-100">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-blue-900 mb-1">Informasi Login</p>
                                <p class="text-sm text-blue-800">
                                    Gunakan password default <strong>yang sudah diberikan</strong>. Jika mengalami kesulitan login, silakan hubungi admin.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <p class="text-center text-sm text-gray-600 mt-8">
                    &copy; {{ date('Y') }} Universitas Sangga Buana. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</div>
