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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #B43237;
            --primary-dark: #8B251D;
            --primary-light: #E8595C;
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f5f5 0%, #e9e9e9 100%);
        }

        .primary-bg {
            background-color: var(--primary-color);
        }

        .primary-text {
            color: var(--primary-color);
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(180, 50, 55, 0.15);
        }

        .icon-circle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background-color: rgba(180, 50, 55, 0.1);
            color: var(--primary-color);
        }

        /* Reduce bold font weights */
        h1, h2, h3 {
            font-weight: 600;
        }

        .font-bold {
            font-weight: 600;
        }

        .font-semibold {
            font-weight: 500;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">
    <div class="max-w-5xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <!-- Main Title Card with Logo -->
        <div class="primary-bg text-white rounded-xl shadow-lg p-8 sm:p-10 mb-8">
            <div class="flex flex-col sm:flex-row items-center gap-6">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <img src="{{ asset('images/icons/logo-sanggabuana.png') }}" alt="Logo Sanggabuana" class="h-24 w-24 object-contain">
                </div>

                <!-- Header Text -->
                <div class="flex-1 text-center sm:text-left">
                    <p class="text-sm sm:text-base font-semibold text-red-100 mb-2 uppercase tracking-wide">Undangan Wisuda</p>
                    <h2 class="text-2xl sm:text-3xl font-bold mb-3">{{ $event->name }}</h2>
                    <p class="text-red-100 text-sm">{{ $event->date->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
                </div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Student Information Card -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-lg p-6 sm:p-8 card-hover">
                <div class="mb-6">
                    <h3 class="text-xl font-bold primary-text mb-6 flex items-center gap-2">
                        <span class="icon-circle">
                            <i class="fas fa-user-circle text-lg"></i>
                        </span>
                        Informasi Mahasiswa
                    </h3>
                </div>

                <div class="space-y-6">
                    <!-- Nama -->
                    <div class="flex items-start gap-4">
                        <div class="icon-circle">
                            <i class="fas fa-user text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Nama Mahasiswa</p>
                            <p class="text-lg font-bold text-gray-900 mt-1">{{ $mahasiswa->nama }}</p>
                        </div>
                    </div>

                    <!-- NPM -->
                    <div class="flex items-start gap-4">
                        <div class="icon-circle">
                            <i class="fas fa-id-card text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Nomor Pokok Mahasiswa</p>
                            <p class="text-lg font-bold text-gray-900 mt-1">{{ $mahasiswa->npm }}</p>
                        </div>
                    </div>

                    <!-- Program Studi -->
                    <div class="flex items-start gap-4">
                        <div class="icon-circle">
                            <i class="fas fa-graduation-cap text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Program Studi</p>
                            <p class="text-lg font-bold text-gray-900 mt-1">{{ $mahasiswa->program_studi }}</p>
                        </div>
                    </div>

                    <!-- Nomor Kursi -->
                    <div class="flex items-start gap-4">
                        <div class="icon-circle">
                            <i class="fas fa-chair text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Nomor Kursi</p>
                            <p class="text-lg font-bold text-gray-900 mt-1">{{ $mahasiswa->nomor_kursi ?? 'Belum ditentukan' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Event Date & Time Card -->
            <div class="bg-white rounded-xl shadow-lg p-6 sm:p-8 card-hover">
                <h3 class="text-xl font-bold primary-text mb-6 flex items-center gap-2">
                    <span class="icon-circle">
                        <i class="fas fa-calendar-alt text-lg"></i>
                    </span>
                </h3>

                <div class="space-y-6">
                    <!-- Tanggal -->
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Tanggal</p>
                        <p class="text-base font-bold text-gray-900">{{ $event->date->locale('id')->isoFormat('D MMMM YYYY') }}</p>
                        <p class="text-sm text-gray-600">{{ $event->date->locale('id')->isoFormat('dddd') }}</p>
                    </div>

                    <!-- Waktu -->
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Waktu</p>
                        <p class="text-base font-bold text-gray-900">{{ \Carbon\Carbon::parse($event->time)->format('H:i') }} WIB</p>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-gray-200"></div>

                    <!-- Lokasi -->
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Lokasi</p>
                        <p class="text-base font-bold text-gray-900 mb-1">{{ $event->location_name }}</p>
                        <p class="text-sm text-gray-600">{{ $event->location_address }}</p>
                    </div>

                    <!-- Maps Button -->
                    @if($event->maps_url)
                    <a href="{{ $event->maps_url }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="flex items-center justify-center gap-2 w-full primary-bg text-white font-semibold py-3 px-4 rounded-lg hover:brightness-110 transition duration-150">
                        <i class="fas fa-map-location-dot"></i>
                        Buka di Maps
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Google Maps -->
        @if($event->location_lat && $event->location_lng)
        <div class="bg-white rounded-xl shadow-lg p-6 sm:p-8 mb-8 card-hover">
            <h3 class="text-xl font-bold primary-text mb-4 flex items-center gap-2">
                <span class="icon-circle">
                    <i class="fas fa-map text-lg"></i>
                </span>
                Peta Lokasi
            </h3>
            <div class="rounded-lg overflow-hidden shadow-md">
                <iframe
                    width="100%"
                    height="350"
                    frameborder="0"
                    style="border:0"
                    src="https://www.google.com/maps/embed/v1/place?key={{ config('services.google_maps.api_key') }}&q={{ $event->location_lat }},{{ $event->location_lng }}&zoom=15"
                    allowfullscreen>
                </iframe>
            </div>
        </div>
        @endif

        <!-- QR Codes Section -->
        <div class="bg-white rounded-xl shadow-lg p-6 sm:p-8 mb-8 card-hover">
            <h2 class="text-2xl font-bold primary-text mb-2 flex items-center gap-3">
                <span class="icon-circle" style="width: 56px; height: 56px;">
                    <i class="fas fa-qrcode text-xl"></i>
                </span>
                QR Code Absensi
            </h2>
            <p class="text-gray-600 mb-8 ml-20">Tunjukkan QR Code kepada panitia untuk proses absensi</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Mahasiswa -->
                <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-6 text-center card-hover">
                    <div class="bg-white rounded-lg p-4 mb-4 inline-block shadow-lg">
                        <img src="{{ $qrCodes['mahasiswa'] }}" alt="QR Code Mahasiswa" class="w-40 h-40">
                    </div>
                    <div class="primary-bg text-white rounded-lg py-3 px-4">
                        <p class="font-bold">Mahasiswa</p>
                        <p class="text-xs text-red-100">Pengguna Utama</p>
                    </div>
                </div>

                <!-- Pendamping 1 -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 text-center card-hover">
                    <div class="bg-white rounded-lg p-4 mb-4 inline-block shadow-lg">
                        <img src="{{ $qrCodes['pendamping1'] }}" alt="QR Code Pendamping 1" class="w-40 h-40">
                    </div>
                    <div class="bg-blue-600 text-white rounded-lg py-3 px-4">
                        <p class="font-bold">Pendamping 1</p>
                        <p class="text-xs text-blue-100">Orang Tua/Wali</p>
                    </div>
                </div>

                <!-- Pendamping 2 -->
                <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl p-6 text-center card-hover">
                    <div class="bg-white rounded-lg p-4 mb-4 inline-block shadow-lg">
                        <img src="{{ $qrCodes['pendamping2'] }}" alt="QR Code Pendamping 2" class="w-40 h-40">
                    </div>
                    <div class="bg-emerald-600 text-white rounded-lg py-3 px-4">
                        <p class="font-bold">Pendamping 2</p>
                        <p class="text-xs text-emerald-100">Orang Tua/Wali</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Download PDF -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
            <a href="{{ route('invitation.download', ['token' => $token]) }}"
               class="flex items-center justify-center gap-3 primary-bg text-white font-semibold py-4 px-8 rounded-xl shadow-lg hover:brightness-110 transition duration-150">
                <i class="fas fa-download"></i>
                Unduh Undangan PDF
            </a>
        </div>

        <!-- Important Note -->
        <div class="bg-amber-50 border-l-4 border-amber-400 rounded-lg p-6 mb-8">
            <div class="flex gap-4">
                <svg class="w-6 h-6 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <p class="font-bold text-amber-900 mb-2">Penting untuk Diperhatikan</p>
                    <ul class="text-amber-800 text-sm space-y-1">
                        <li>• Simpan halaman ini atau unduh PDF untuk dibawa saat acara wisuda</li>
                        <li>• Setiap QR Code hanya dapat digunakan sekali untuk absensi</li>
                        <li>• Pastikan membawa undangan ini saat menghadiri acara</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center py-6 border-t border-gray-200">
            <p class="text-gray-600 text-sm">
                &copy; {{ date('Y') }} Sistem Absensi Wisuda Digital
            </p>
            <p class="text-gray-500 text-xs mt-2">
                Universitas Sanggabuana YPKP
            </p>
        </div>
    </div>
</body>
</html>
