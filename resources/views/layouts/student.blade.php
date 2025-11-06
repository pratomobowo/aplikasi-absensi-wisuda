<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Portal Mahasiswa - E-Wisuda Universitas Sangga Buana' }}</title>
    <meta name="description" content="Portal Mahasiswa E-Wisuda Universitas Sangga Buana">
    <meta name="author" content="Universitas Sangga Buana">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased">
    <!-- Main Content -->
    <div class="min-h-screen">
        {{ $slot }}
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
