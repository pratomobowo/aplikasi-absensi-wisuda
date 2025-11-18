@extends('layouts.public')

@section('title', '{{ $title ?? "Coming Soon" }} - E-Wisuda Universitas Sangga Buana')

@section('content')
    <!-- Coming Soon Section -->
    <section class="min-h-screen flex items-center justify-center px-4 py-20 sm:px-6 lg:px-8 pt-32">
        <div class="max-w-md w-full text-center">
            <!-- Content -->
            <div class="bg-white rounded-2xl shadow-xl p-8 sm:p-10">
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                    Coming Soon
                </h1>

                <p class="text-xl font-semibold text-blue-600 mb-4">
                    {{ $title ?? 'Fitur Baru' }}
                </p>

                <p class="text-gray-600 text-lg mb-8 leading-relaxed">
                    {{ $description ?? 'Kami sedang mempersiapkan sesuatu yang luar biasa untuk Anda. Harap menunggu...' }}
                </p>

                <!-- Optional message -->
                @if(isset($message))
                <div class="bg-blue-50 border-l-4 border-blue-600 p-4 mb-8 rounded">
                    <p class="text-blue-800 text-sm">
                        {{ $message }}
                    </p>
                </div>
                @endif

                <!-- Back button -->
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ url('/') }}"
                       class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-150 ease-in-out">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
