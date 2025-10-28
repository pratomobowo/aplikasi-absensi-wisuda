<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Tidak Valid - Undangan Wisuda</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-lg shadow-xl overflow-hidden">
            <!-- Error Icon -->
            <div class="bg-red-600 p-6 text-center">
                <svg class="w-20 h-20 mx-auto text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>

            <!-- Error Message -->
            <div class="p-8 text-center">
                <h1 class="text-2xl font-bold text-gray-900 mb-4">Link Tidak Valid</h1>
                <p class="text-gray-600 mb-6">
                    {{ $message ?? 'Link undangan yang Anda akses tidak valid atau sudah kadaluarsa.' }}
                </p>

                <!-- Information Box -->
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 text-left">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <strong>Kemungkinan penyebab:</strong>
                            </p>
                            <ul class="mt-2 text-sm text-blue-700 list-disc list-inside space-y-1">
                                <li>Link sudah kadaluarsa</li>
                                <li>Link tidak lengkap atau rusak</li>
                                <li>Link sudah tidak aktif</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Help Information -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <p class="text-sm text-gray-700 mb-2">
                        <strong>Butuh bantuan?</strong>
                    </p>
                    <p class="text-sm text-gray-600">
                        Silakan hubungi panitia wisuda untuk mendapatkan link undangan yang baru atau informasi lebih lanjut.
                    </p>
                </div>

                <!-- Action Button -->
                <div class="space-y-3">
                    <button 
                        onclick="window.history.back()" 
                        class="w-full inline-flex justify-center items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow transition duration-150 ease-in-out">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </button>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-100 px-8 py-4 text-center">
                <p class="text-xs text-gray-600">
                    &copy; {{ date('Y') }} Sistem Absensi Wisuda Digital
                </p>
            </div>
        </div>
    </div>
</body>
</html>
