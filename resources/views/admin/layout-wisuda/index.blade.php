@extends('layouts.admin')

@section('title', 'Layout Wisuda')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Layout Wisuda</h1>
                <p class="text-sm text-gray-600 mt-1">Upload dan kelola layout PDF untuk halaman panduan wisuda</p>
            </div>
        </div>

        <!-- Current Layout -->
        @if($layout)
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $layout->title }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ $layout->original_filename }}</p>
                        <div class="flex items-center gap-4 mt-3 text-sm text-gray-600">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                {{ $layout->human_file_size }}
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ $layout->updated_at->format('d M Y H:i') }}
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ $layout->url }}" target="_blank"
                           class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                            Lihat PDF
                        </a>
                        <form action="{{ route('admin.layout-wisuda.destroy', $layout) }}" method="POST" class="inline"
                              onsubmit="return confirm('Hapus layout ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                <p class="text-yellow-800">Belum ada layout wisuda diupload.</p>
            </div>
        @endif

        <!-- Upload Form -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Upload Layout Baru</h3>
            <form action="{{ route('admin.layout-wisuda.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul</label>
                        <input type="text" name="title" value="{{ $layout->title ?? 'Layout Wisuda XXIII' }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
                               placeholder="Contoh: Layout Wisuda XXIII">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">File PDF</label>
                        <input type="file" name="pdf" accept=".pdf"
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-6 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                        <p class="text-sm text-gray-500 mt-2">Format: PDF. Maks 500MB.</p>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                        {{ $layout ? 'Ganti Layout' : 'Upload Layout' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
