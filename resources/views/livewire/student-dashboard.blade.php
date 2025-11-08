<div>
    <!-- Main Content -->
    <main>
        <!-- Page Header -->
        <section class="relative bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-700 pt-20 pb-12 overflow-hidden">
            <!-- Decorative elements -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-400 rounded-full mix-blend-multiply filter blur-xl opacity-30"></div>
                <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-indigo-400 rounded-full mix-blend-multiply filter blur-xl opacity-30"></div>
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-purple-400 rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
            </div>

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <div class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-white text-sm font-medium mb-4 border border-white/20">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Dashboard Mahasiswa
                        </div>
                        <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-white mb-3 leading-tight">
                            Selamat Datang, {{ $mahasiswa->nama }}!
                        </h1>
                        <p class="text-base sm:text-lg text-blue-50">
                            NPM: <span class="font-semibold">{{ $mahasiswa->npm }}</span> • {{ $mahasiswa->program_studi }}
                        </p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <form method="POST" action="{{ route('student.logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-white/10 backdrop-blur-sm text-white font-semibold rounded-lg hover:bg-white/20 transition-all duration-200 border border-white/20">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- Dashboard Content with Sidebar -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
            <!-- Success Message -->
            @if (session()->has('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg animate-fade-in">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-green-800 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <div class="flex flex-col lg:flex-row gap-6 lg:gap-8">
                <!-- Sidebar -->
                <aside class="lg:w-64 flex-shrink-0">
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <nav class="flex flex-row lg:flex-col overflow-x-auto lg:overflow-x-visible">
                            <!-- Menu Item: Informasi Mahasiswa -->
                            <button
                                wire:click="setActiveMenu('informasi')"
                                class="flex items-center px-4 py-4 text-left transition-colors duration-200 border-b lg:border-b border-gray-100 whitespace-nowrap
                                    {{ $activeMenu === 'informasi' ? 'bg-blue-50 text-blue-700 border-l-4 border-l-blue-600' : 'text-gray-700 hover:bg-gray-50' }}"
                            >
                                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="font-medium">Informasi Mahasiswa</span>
                            </button>

                            <!-- Menu Item: Foto Wisuda -->
                            <button
                                wire:click="setActiveMenu('foto-wisuda')"
                                class="flex flex-col items-start px-4 py-4 text-left transition-colors duration-200 border-b lg:border-b border-gray-100 relative
                                    {{ $activeMenu === 'foto-wisuda' ? 'bg-blue-50 text-blue-700 border-l-4 border-l-blue-600' : 'text-gray-700 hover:bg-gray-50' }}"
                            >
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="font-medium">Foto</span>
                                </div>
                                @if(!$mahasiswa->hasFotoWisuda())
                                    <span class="mt-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Belum Upload
                                    </span>
                                @else
                                    <span class="mt-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Tersedia
                                    </span>
                                @endif
                            </button>

                            <!-- Menu Item: Undangan Wisuda -->
                            <button
                                wire:click="setActiveMenu('undangan')"
                                class="flex flex-col items-start px-4 py-4 text-left transition-colors duration-200 border-b lg:border-b border-gray-100 relative
                                    {{ $activeMenu === 'undangan' ? 'bg-blue-50 text-blue-700 border-l-4 border-l-blue-600' : 'text-gray-700 hover:bg-gray-50' }}"
                            >
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                    </svg>
                                    <span class="font-medium">Undangan Wisuda</span>
                                </div>
                                @if($undanganWisuda)
                                    <span class="mt-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Tersedia
                                    </span>
                                @else
                                    <span class="mt-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Belum Tersedia
                                    </span>
                                @endif
                            </button>

                            <!-- Menu Item: Buku Wisuda -->
                            <button
                                wire:click="setActiveMenu('buku-wisuda')"
                                class="flex flex-col items-start px-4 py-4 text-left transition-colors duration-200 border-b lg:border-b border-gray-100 relative
                                    {{ $activeMenu === 'buku-wisuda' ? 'bg-blue-50 text-blue-700 border-l-4 border-l-blue-600' : 'text-gray-700 hover:bg-gray-50' }}"
                            >
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    <span class="font-medium">Buku Wisuda</span>
                                </div>
                                <span class="mt-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Siap Diunduh
                                </span>
                            </button>

                            <!-- Menu Item: Keamanan -->
                            <button
                                wire:click="setActiveMenu('keamanan')"
                                class="flex items-center px-4 py-4 text-left transition-colors duration-200 whitespace-nowrap
                                    {{ $activeMenu === 'keamanan' ? 'bg-blue-50 text-blue-700 border-l-4 border-l-blue-600' : 'text-gray-700 hover:bg-gray-50' }}"
                            >
                                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                <span class="font-medium">Keamanan</span>
                            </button>
                        </nav>
                    </div>
                </aside>

                <!-- Main Content Area -->
                <div class="flex-1 min-w-0">
                    <!-- Informasi Mahasiswa Content -->
                    @if($activeMenu === 'informasi')
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Informasi Mahasiswa
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <div>
                                        <p class="text-sm text-gray-600 mb-1">NPM</p>
                                        <p class="text-lg font-semibold text-gray-900">{{ $mahasiswa->npm }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600 mb-1">Nama Lengkap</p>
                                        <p class="text-lg font-semibold text-gray-900">{{ $mahasiswa->nama }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600 mb-1">Program Studi</p>
                                        <p class="text-lg font-semibold text-gray-900">{{ $mahasiswa->program_studi }}</p>
                                    </div>
                                </div>
                                <div class="space-y-4">
                                    <div>
                                        <p class="text-sm text-gray-600 mb-1">IPK</p>
                                        <p class="text-lg font-semibold text-gray-900">{{ number_format($mahasiswa->ipk, 2, '.', '') }}</p>
                                    </div>
                                    @if($mahasiswa->yudisium)
                                        <div>
                                            <p class="text-sm text-gray-600 mb-1">Yudisium</p>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                                {{ $mahasiswa->yudisium === 'Dengan Pujian' ? 'bg-yellow-100 text-yellow-800' :
                                                   ($mahasiswa->yudisium === 'Sangat Memuaskan' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800') }}">
                                                {{ $mahasiswa->yudisium }}
                                            </span>
                                        </div>
                                    @endif
                                    @if($mahasiswa->judul_skripsi)
                                        <div>
                                            <p class="text-sm text-gray-600 mb-1">Judul Skripsi/Tugas Akhir</p>
                                            <p class="text-sm font-medium text-gray-900 leading-relaxed">{{ $mahasiswa->judul_skripsi }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Foto Wisuda Content -->
                    @if($activeMenu === 'foto-wisuda')
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Foto Wisuda
                            </h2>

                            <!-- Current Photo Display -->
                            @if($mahasiswa->hasFotoWisuda())
                                <div class="mb-6">
                                    <p class="text-sm text-gray-600 mb-3">Foto Anda Saat Ini:</p>
                                    <div class="relative inline-block">
                                        <img
                                            src="{{ $mahasiswa->foto_wisuda_url }}"
                                            alt="Foto Wisuda {{ $mahasiswa->nama }}"
                                            class="max-w-sm w-full h-auto rounded-lg shadow-lg border-4 border-blue-200"
                                        >
                                        <button
                                            wire:click="deleteFoto"
                                            wire:confirm="Apakah Anda yakin ingin menghapus foto ini?"
                                            class="absolute top-2 right-2 bg-red-600 text-white p-2 rounded-full hover:bg-red-700 transition-colors duration-200 shadow-lg"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="mb-6 p-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300 text-center">
                                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="text-gray-600 font-medium">Belum ada foto wisuda</p>
                                    <p class="text-sm text-gray-500 mt-1">Upload foto wisuda Anda di bawah ini</p>
                                </div>
                            @endif

                            <!-- Upload Form -->
                            <form wire:submit="uploadFoto">
                                <div class="mb-4">
                                    <label for="foto" class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ $mahasiswa->hasFotoWisuda() ? 'Ganti Foto Wisuda' : 'Upload Foto Wisuda' }}
                                    </label>
                                    <input
                                        type="file"
                                        id="foto"
                                        wire:model="foto"
                                        accept="image/jpeg,image/jpg,image/png"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('foto') border-red-500 @enderror"
                                    >
                                    @error('foto')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror

                                    <!-- Loading Indicator -->
                                    <div wire:loading wire:target="foto" class="mt-2 flex items-center text-sm text-blue-600">
                                        <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Memuat file...
                                    </div>
                                </div>

                                <!-- Upload Button -->
                                <button
                                    type="submit"
                                    wire:loading.attr="disabled"
                                    wire:target="foto,uploadFoto"
                                    class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
                                >
                                    <span wire:loading.remove wire:target="uploadFoto">
                                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                        {{ $mahasiswa->hasFotoWisuda() ? 'Ganti Foto' : 'Upload Foto' }}
                                    </span>
                                    <span wire:loading wire:target="uploadFoto" class="flex items-center">
                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Mengupload...
                                    </span>
                                </button>
                            </form>

                            <!-- Info -->
                            <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <p class="text-sm text-blue-800 font-semibold mb-2">Ketentuan Foto:</p>
                                <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                                    <li>Format: JPG, JPEG, atau PNG</li>
                                    <li>Ukuran maksimal: 2MB</li>
                                    <li>Foto akan otomatis tersimpan dengan nama NPM Anda</li>
                                    <li>Foto lama akan otomatis terganti saat upload foto baru</li>
                                </ul>
                            </div>
                        </div>
                    @endif

                    <!-- Undangan Wisuda Content -->
                    @if($activeMenu === 'undangan')
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                </svg>
                                Undangan Wisuda
                            </h2>

                            @if($undanganWisuda)
                                <!-- Display Invitation Details -->
                                <div class="space-y-6">
                                    <div class="p-6 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg border-2 border-blue-200">
                                        <div class="flex items-center justify-between mb-4">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                                Undangan Tersedia
                                            </span>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                            <div>
                                                <p class="text-sm text-gray-600 mb-1">Token Undangan</p>
                                                <p class="text-lg font-mono font-bold text-gray-900 bg-white px-3 py-2 rounded border border-gray-200 truncate">{{ $undanganWisuda->magic_link_token }}</p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-600 mb-1">Status Undangan</p>
                                                <p class="text-lg font-semibold text-green-600 flex items-center">
                                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Aktif
                                                </p>
                                            </div>
                                        </div>

                                        <div class="flex flex-col sm:flex-row gap-3">
                                            <a
                                                href="{{ route('invitation.show', $undanganWisuda->magic_link_token) }}"
                                                target="_blank"
                                                class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors duration-200"
                                            >
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                Lihat Undangan
                                            </a>
                                            <a
                                                href="{{ route('invitation.download', $undanganWisuda->magic_link_token) }}"
                                                class="inline-flex items-center justify-center px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors duration-200"
                                            >
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                Download PDF
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Info -->
                                    <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                                        <p class="text-sm text-blue-800 font-semibold mb-2">Informasi:</p>
                                        <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                                            <li>Undangan dapat dibagikan kepada keluarga dan tamu</li>
                                            <li>Setiap tamu harus membawa undangan digital saat acara wisuda</li>
                                            <li>QR Code pada undangan akan digunakan untuk absensi</li>
                                            <li>Simpan token undangan Anda dengan baik</li>
                                        </ul>
                                    </div>
                                </div>
                            @else
                                <!-- Empty State - No Invitation -->
                                <div class="text-center py-12">
                                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-6">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Undangan Belum Tersedia</h3>
                                    <p class="text-gray-600 max-w-md mx-auto mb-6">
                                        Undangan wisuda Anda belum di-generate oleh admin. Silakan hubungi admin atau tunggu hingga undangan Anda dibuat.
                                    </p>
                                    <div class="inline-flex items-center px-4 py-2 bg-yellow-50 text-yellow-800 rounded-lg border border-yellow-200">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm font-medium">Mohon bersabar, undangan akan segera tersedia</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Buku Wisuda Content -->
                    @if($activeMenu === 'buku-wisuda')
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                Buku Wisuda
                            </h2>

                            @php
                                $event = \App\Models\GraduationEvent::active()->first();
                                $bukuWisuda = $event ? $event->bukuWisuda()->first() : null;
                            @endphp

                            @if($bukuWisuda)
                                <!-- Book Info Card -->
                                <div class="mb-6 p-6 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg border-2 border-blue-200">
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-600 mb-1">Nama File</p>
                                        <p class="text-lg font-semibold text-gray-900">{{ $bukuWisuda->filename }}</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <p class="text-sm text-gray-600 mb-1">Ukuran File</p>
                                            <p class="text-sm font-semibold text-gray-900">{{ $bukuWisuda->getHumanFileSize() }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600 mb-1">Total Downloads</p>
                                            <p class="text-sm font-semibold text-gray-900">{{ $bukuWisuda->download_count }} kali</p>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600 mb-1">Acara Wisuda</p>
                                        <p class="text-sm font-semibold text-gray-900">{{ $bukuWisuda->graduationEvent->name }}</p>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <a href="{{ route('buku-wisuda.viewer', ['id' => $bukuWisuda->id]) }}"
                                       target="_blank"
                                       class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Lihat Flipbook
                                    </a>

                                    <a href="{{ route('buku-wisuda.download', ['id' => $bukuWisuda->id]) }}"
                                       class="inline-flex items-center justify-center px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors duration-200">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        Download PDF
                                    </a>
                                </div>

                                <!-- Info -->
                                <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                                    <p class="text-sm text-blue-800 font-semibold mb-2">Informasi:</p>
                                    <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                                        <li>Klik "Lihat Flipbook" untuk membuka viewer dengan mode flipbook di tab baru</li>
                                        <li>Gunakan tombol navigasi untuk berpindah halaman</li>
                                        <li>Fitur zoom tersedia untuk memperbesar/memperkecil tampilan</li>
                                        <li>Klik "Download PDF" untuk mengunduh file ke perangkat Anda</li>
                                    </ul>
                                </div>
                            @else
                                <!-- Empty State -->
                                <div class="text-center py-12">
                                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-6">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Buku Wisuda Belum Tersedia</h3>
                                    <p class="text-gray-600 max-w-md mx-auto">
                                        Buku wisuda untuk acara yang aktif belum di-upload oleh admin. Silakan hubungi admin atau tunggu hingga buku wisuda tersedia.
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Keamanan Content -->
                    @if($activeMenu === 'keamanan')
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                Pengaturan Keamanan
                            </h2>

                            <!-- Change Password Section -->
                            <div class="mb-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                    </svg>
                                    Ubah Password
                                </h3>
                                <p class="text-gray-600 text-sm mb-4">
                                    Perbarui password akun Anda untuk menjaga keamanan. Gunakan password yang kuat dan unik.
                                </p>
                                <a href="{{ route('student.change-password') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Ubah Password
                                </a>
                            </div>

                            <!-- Security Tips -->
                            <div class="p-6 bg-amber-50 rounded-lg border border-amber-200">
                                <h3 class="text-lg font-semibold text-amber-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    Tips Keamanan Akun
                                </h3>
                                <ul class="space-y-2 text-sm text-amber-800">
                                    <li class="flex items-start">
                                        <span class="inline-flex items-center justify-center h-5 w-5 rounded-full bg-amber-600 text-white text-xs font-bold mr-3 flex-shrink-0">✓</span>
                                        Gunakan password minimal 8 karakter dengan kombinasi huruf, angka, dan simbol
                                    </li>
                                    <li class="flex items-start">
                                        <span class="inline-flex items-center justify-center h-5 w-5 rounded-full bg-amber-600 text-white text-xs font-bold mr-3 flex-shrink-0">✓</span>
                                        Jangan bagikan password Anda kepada siapapun termasuk admin
                                    </li>
                                    <li class="flex items-start">
                                        <span class="inline-flex items-center justify-center h-5 w-5 rounded-full bg-amber-600 text-white text-xs font-bold mr-3 flex-shrink-0">✓</span>
                                        Gunakan password yang berbeda untuk setiap akun online
                                    </li>
                                    <li class="flex items-start">
                                        <span class="inline-flex items-center justify-center h-5 w-5 rounded-full bg-amber-600 text-white text-xs font-bold mr-3 flex-shrink-0">✓</span>
                                        Logout dari akun Anda setelah selesai menggunakan sistem
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>
</div>
