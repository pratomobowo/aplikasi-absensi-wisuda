<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undangan Wisuda - {{ $mahasiswa->nama }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">

    <!-- Open Graph Meta Tags for Social Media -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Undangan Wisuda - {{ $mahasiswa->nama }}">
    <meta property="og:description" content="Undangan Wisuda {{ $event->name }} untuk {{ $mahasiswa->nama }} ({{ $mahasiswa->npm }})">
    <meta property="og:url" content="{{ request()->url() }}">
    @if($event->feature_image)
    <meta property="og:image" content="{{ asset('storage/' . $event->feature_image) }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="675">
    @endif

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Undangan Wisuda - {{ $mahasiswa->nama }}">
    <meta name="twitter:description" content="Undangan Wisuda {{ $event->name }} untuk {{ $mahasiswa->nama }} ({{ $mahasiswa->npm }})">
    @if($event->feature_image)
    <meta name="twitter:image" content="{{ asset('storage/' . $event->feature_image) }}">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-t-lg shadow-lg p-6 sm:p-8 text-white">
            <div class="text-center">
                <h1 class="text-2xl sm:text-3xl font-bold mb-2">Undangan Wisuda Universitas Sanggabuana YPKP</h1>
                <p class="text-blue-100 text-sm sm:text-base">{{ $event->name }}</p>
            </div>
        </div>

        <!-- Student Information -->
        <div class="bg-white shadow-lg p-6 sm:p-8">
            <div class="border-b border-gray-200 pb-6 mb-6">
                <h2 class="text-xl sm:text-2xl font-semibold text-gray-900 mb-4">Informasi Mahasiswa</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Nama</p>
                        <p class="text-base sm:text-lg font-medium text-gray-900">{{ $mahasiswa->nama }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">NPM</p>
                        <p class="text-base sm:text-lg font-medium text-gray-900">{{ $mahasiswa->npm }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Program Studi</p>
                        <p class="text-base sm:text-lg font-medium text-gray-900">{{ $mahasiswa->program_studi }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Nomor Kursi</p>
                        <p class="text-base sm:text-lg font-medium text-gray-900">{{ $mahasiswa->nomor_kursi ?? '-' }}</p>
                    </div>
                    @if($mahasiswa->judul_skripsi)
                    <div class="sm:col-span-2">
                        <p class="text-sm text-gray-600">Judul Skripsi</p>
                        <p class="text-base sm:text-lg font-medium text-gray-900">{{ $mahasiswa->judul_skripsi }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Event Details -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <h2 class="text-xl sm:text-2xl font-semibold text-gray-900 mb-4">Informasi Acara</h2>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Left Column: Tanggal & Waktu -->
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-600">Tanggal</p>
                            <p class="text-base font-medium text-gray-900">{{ $event->date->isoFormat('dddd, D MMMM YYYY') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Waktu</p>
                            <p class="text-base font-medium text-gray-900">{{ \Carbon\Carbon::parse($event->time)->format('H:i') }} WIB</p>
                        </div>
                    </div>

                    <!-- Right Column: Lokasi & Maps Button -->
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-600">Lokasi</p>
                            <p class="text-base font-medium text-gray-900">{{ $event->location_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Alamat</p>
                            <p class="text-base font-medium text-gray-900">{{ $event->location_address }}</p>
                        </div>
                        @if($event->maps_url)
                        <div>
                            <a href="{{ $event->maps_url }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition duration-150 ease-in-out">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                Buka di Maps
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Google Maps -->
            @if($event->location_lat && $event->location_lng)
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Peta Lokasi</h3>
                <div class="rounded-lg overflow-hidden shadow-md">
                    <iframe 
                        width="100%" 
                        height="300" 
                        frameborder="0" 
                        style="border:0"
                        src="https://www.google.com/maps/embed/v1/place?key={{ config('services.google_maps.api_key') }}&q={{ $event->location_lat }},{{ $event->location_lng }}&zoom=15"
                        allowfullscreen>
                    </iframe>
                </div>
            </div>
            @endif

            <!-- QR Codes -->
            <div class="border-t border-gray-200 pt-6">
                <h2 class="text-xl sm:text-2xl font-semibold text-gray-900 mb-4">QR Code Absensi</h2>
                <p class="text-sm text-gray-600 mb-6">Tunjukkan QR Code berikut kepada panitia untuk absensi</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- QR Code Mahasiswa -->
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6 text-center">
                        <div class="bg-white rounded-lg p-4 mb-3 inline-block">
                            <img src="{{ $qrCodes['mahasiswa'] }}" alt="QR Code Mahasiswa" class="w-48 h-48 mx-auto">
                        </div>
                        <div class="bg-blue-600 text-white rounded-lg py-2 px-4">
                            <p class="font-semibold">Mahasiswa</p>
                        </div>
                    </div>

                    <!-- QR Code Pendamping 1 -->
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-6 text-center">
                        <div class="bg-white rounded-lg p-4 mb-3 inline-block">
                            <img src="{{ $qrCodes['pendamping1'] }}" alt="QR Code Pendamping 1" class="w-48 h-48 mx-auto">
                        </div>
                        <div class="bg-green-600 text-white rounded-lg py-2 px-4">
                            <p class="font-semibold">Pendamping 1</p>
                        </div>
                    </div>

                    <!-- QR Code Pendamping 2 -->
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-6 text-center">
                        <div class="bg-white rounded-lg p-4 mb-3 inline-block">
                            <img src="{{ $qrCodes['pendamping2'] }}" alt="QR Code Pendamping 2" class="w-48 h-48 mx-auto">
                        </div>
                        <div class="bg-purple-600 text-white rounded-lg py-2 px-4">
                            <p class="font-semibold">Pendamping 2</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Download Button -->
            <div class="mt-8 text-center">
                <a href="{{ route('invitation.download', ['token' => $token]) }}" 
                   class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-lg transition duration-150 ease-in-out">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Unduh PDF
                </a>
            </div>

            <!-- Information Note -->
            <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            <strong>Penting:</strong> Simpan halaman ini atau unduh PDF untuk dibawa saat acara wisuda. Setiap QR Code hanya dapat digunakan sekali.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-100 rounded-b-lg shadow-lg p-4 text-center text-sm text-gray-600">
            <p>&copy; {{ date('Y') }} Sistem Absensi Wisuda Digital</p>
        </div>
    </div>
</body>
</html>
