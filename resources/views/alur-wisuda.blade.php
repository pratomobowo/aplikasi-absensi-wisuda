@extends('layouts.public')

@section('title', 'Alur Wisuda - ' . config('app.name', 'Sistem Absensi Wisuda'))

@push('styles')
    <style>
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
@endpush

@section('content')
    <!-- Main Content -->
    <main>
        <!-- Page Header -->
        <section class="relative bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-700 pt-32 pb-20 md:pt-40 md:pb-28 overflow-hidden">
            <!-- Decorative elements -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-400 rounded-full mix-blend-multiply filter blur-xl opacity-30"></div>
                <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-indigo-400 rounded-full mix-blend-multiply filter blur-xl opacity-30"></div>
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-purple-400 rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
            </div>

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-white text-sm font-medium mb-6 md:mb-8 border border-white/20">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    Panduan Wisuda
                </div>
                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold text-white mb-4 md:mb-6 leading-tight">
                    Alur Wisuda
                </h1>
                <p class="text-lg sm:text-xl md:text-2xl text-blue-50 max-w-3xl mx-auto leading-relaxed">
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
@endsection
