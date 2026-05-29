@extends('layouts.admin')

@section('title', 'Preview Buku Wisuda - ' . $event->name)

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Preview Buku Wisuda</h1>
                <p class="text-sm text-gray-600 mt-1">{{ $event->name }} - {{ $event->date->format('d M Y') }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.buku-wisuda.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                    Kembali
                </a>
                @if($bukuWisuda && $bukuWisuda->status === 'generated')
                    <form action="{{ route('admin.buku-wisuda.publish', $bukuWisuda) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            Publish Buku
                        </button>
                    </form>
                @endif
                <form action="{{ route('admin.buku-wisuda.generate', $event) }}" method="POST" class="inline" id="generateForm">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all"
                            onclick="if(!confirm('{{ $bukuWisuda ? 'Regenerate PDF akan menimpa PDF yang sudah ada. Lanjutkan?' : 'Generate PDF untuk event ini?' }}')) { event.preventDefault(); return false; }"
                            id="generateBtn">
                        <span id="btnText">{{ $bukuWisuda ? 'Regenerate PDF' : 'Generate PDF' }}</span>
                        <span id="btnLoading" class="hidden">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memproses...
                        </span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Status Card -->
        @if($bukuWisuda)
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Status Buku Wisuda</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Status: 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($bukuWisuda->status === 'draft') bg-gray-100 text-gray-800
                                @elseif($bukuWisuda->status === 'generated') bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800
                                @endif">
                                {{ ucfirst($bukuWisuda->status) }}
                            </span>
                        </p>
                        @if($bukuWisuda->generated_at)
                            <p class="text-sm text-gray-600">Digenerate pada: {{ $bukuWisuda->generated_at->format('d M Y H:i') }}</p>
                        @endif
                        @if($bukuWisuda->generated_by)
                            <p class="text-sm text-gray-600">Oleh: {{ $bukuWisuda->generated_by }}</p>
                        @endif
                    </div>
                    @if($bukuWisuda->status !== 'draft')
                        <a href="{{ route('buku-wisuda.admin-viewer', $bukuWisuda->slug) }}" target="_blank"
                           class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                            Lihat PDF
                        </a>
                    @endif
                </div>
            </div>
        @endif

        <!-- Preview Stats -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistik Data</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-primary-50 p-4 rounded-lg">
                    <p class="text-sm text-primary-600 font-medium">Total Wisudawan</p>
                    <p class="text-2xl font-bold text-primary-900">{{ $total }}</p>
                </div>
                @php
                    $withPhotoCount = 0;
                    $withoutPhotoCount = 0;
                    foreach ($mahasiswa as $mhs) {
                        if ($mhs->hasFotoWisuda()) {
                            $withPhotoCount++;
                        } else {
                            $withoutPhotoCount++;
                        }
                    }
                @endphp
                <div class="bg-green-50 p-4 rounded-lg">
                    <p class="text-sm text-green-600 font-medium">Dengan Foto</p>
                    <p class="text-2xl font-bold text-green-900">{{ $withPhotoCount }}</p>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <p class="text-sm text-yellow-600 font-medium">Tanpa Foto</p>
                    <p class="text-2xl font-bold text-yellow-900">{{ $withoutPhotoCount }}</p>
                </div>
                <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="text-sm text-blue-600 font-medium">Dengan Judul</p>
                    <p class="text-2xl font-bold text-blue-900">{{ $mahasiswa->whereNotNull('judul_skripsi')->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Upload Halaman Awal Buku -->
        @if($bukuWisuda)
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Halaman Awal Buku</h3>
                        <p class="text-sm text-gray-600 mt-1">Upload gambar PNG/WEBP untuk halaman awal buku wisuda (bisa upload banyak sekaligus)</p>
                    </div>
                    @if($bukuWisuda && $bukuWisuda->initial_pages)
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                            {{ count($bukuWisuda->initial_pages) }} halaman
                        </span>
                    @endif
                </div>

                <!-- Current Pages -->
                @if($bukuWisuda && $bukuWisuda->initial_pages && count($bukuWisuda->initial_pages) > 0)
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-sm font-medium text-gray-700">{{ count($bukuWisuda->initial_pages) }} Halaman</h4>
                            <form action="{{ route('admin.buku-wisuda.delete-all-initial-pages', $bukuWisuda->id) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Hapus semua {{ count($bukuWisuda->initial_pages) }} halaman awal?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 bg-red-100 text-red-700 rounded-lg text-xs font-medium hover:bg-red-200 transition-colors">
                                    Hapus Semua
                                </button>
                            </form>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach($bukuWisuda->initial_pages as $index => $page)
                                <div class="relative flex flex-col items-center">
                                    <div class="relative w-16 h-20 border border-gray-200 rounded bg-gray-50 overflow-hidden">
                                        <img src="{{ asset('storage/buku-wisuda/' . $page) }}"
                                             alt="Hal. {{ $index + 1 }}"
                                             class="w-full h-full object-cover">
                                        <form action="{{ route('admin.buku-wisuda.delete-initial-page', $bukuWisuda->id) }}" method="POST" class="absolute top-0 right-0 m-0.5">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="filename" value="{{ $page }}">
                                            <button type="submit" class="w-4 h-4 bg-red-500 text-white rounded-full flex items-center justify-center text-xs leading-none hover:bg-red-700"
                                                    onclick="return confirm('Hapus?')">
                                                ×
                                            </button>
                                        </form>
                                    </div>
                                    <span class="text-xs text-gray-500 mt-0.5 truncate max-w-16">{{ $loop->iteration }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Upload Form -->
                <form action="{{ route('admin.buku-wisuda.upload-initial-pages', $bukuWisuda->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-primary-400 transition-colors">
                        <input type="file"
                               name="initial_pages[]"
                               id="initial_pages"
                               accept="image/png,image/webp,image/jpeg"
                               multiple
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-6 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                        <p class="text-sm text-gray-500 mt-3">Format: PNG, WEBP, JPG. Maks 500MB per file. Pilih banyak file sekaligus.</p>
                        <p class="text-xs text-gray-400 mt-1">Contoh: Pilih file 1.png, 2.webp, 3.png dst sekaligus untuk upload berurut.</p>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-gray-200 mt-4">
                        <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                            Upload Halaman
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
@push('scripts')
<script>
    document.getElementById('generateForm').addEventListener('submit', function(e) {
        const btn = document.getElementById('generateBtn');
        const btnText = document.getElementById('btnText');
        const btnLoading = document.getElementById('btnLoading');
        
        btn.disabled = true;
        btnText.classList.add('hidden');
        btnLoading.classList.remove('hidden');
    });
</script>
@endpush

@endsection