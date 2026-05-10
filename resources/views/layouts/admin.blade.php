<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin') - E-Wisuda</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body class="antialiased bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-gray-200 flex-shrink-0">
            <div class="h-16 flex items-center px-6 border-b border-gray-200">
                <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold text-primary-600">
                    E-Wisuda
                </a>
            </div>

            <nav class="p-4 space-y-1">
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('admin.mahasiswa.index') }}" 
                   class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('admin.mahasiswa.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                    </svg>
                    List Wisudawan
                </a>

                <a href="{{ route('admin.siakad-sync.index') }}" 
                   class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('admin.siakad-sync.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                    </svg>
                    Sync SIAKAD
                </a>

                <a href="{{ route('admin.graduation-events.index') }}" 
                   class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('admin.graduation-events.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Acara Wisuda
                </a>

                <a href="{{ route('admin.buku-wisuda.index') }}" 
                   class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('admin.buku-wisuda.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Buku Wisuda
                </a>

                <a href="{{ route('admin.graduation-tickets.index') }}" 
                   class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('admin.graduation-tickets.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                    Tiket Wisuda
                </a>

                <a href="{{ route('admin.attendance.index') }}" 
                   class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('admin.attendance.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Kehadiran
                </a>

                <a href="{{ route('admin.konsumsi.index') }}" 
                   class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('admin.konsumsi.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Data Konsumsi
                </a>

                <a href="{{ route('admin.users.index') }}" 
                   class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('admin.users.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    User Management
                </a>

                <a href="{{ route('admin.activity-logs.index') }}" 
                   class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('admin.activity-logs.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Activity Logs
                </a>

                <div class="pt-4 mt-4 border-t border-gray-200">
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center w-full px-4 py-2.5 text-sm font-medium text-red-600 rounded-lg hover:bg-red-50">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Top Header -->
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8">
                <h1 class="text-lg font-semibold text-gray-900">@yield('title', 'Dashboard')</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ auth()->user()->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-green-100 text-green-700' }}">
                        {{ ucfirst(auth()->user()->role) }}
                    </span>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-8 overflow-auto">
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>