@extends('layouts.admin')

@section('title', 'Download Foto - SIAKAD Sync')

@section('content')
    <div class="space-y-6">
        <!-- Header dengan Tab -->
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <a href="{{ route('admin.siakad-sync.index') }}" 
                   class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                    </svg>
                    Sync Data
                </a>
                <a href="{{ route('admin.siakad-sync.photo') }}" 
                   class="border-primary-500 text-primary-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Download Foto
                </a>
            </nav>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 rounded-lg bg-blue-100">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Mahasiswa</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_mahasiswa'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 rounded-lg bg-green-100">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Sudah Ada Foto</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['with_photo'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 rounded-lg bg-red-100">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Belum Ada Foto</p>
                        <p class="text-2xl font-bold text-red-600">{{ $stats['without_photo'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Download -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Download Foto</h2>
            
            <form action="{{ route('admin.siakad-sync.photo.download') }}" method="POST" class="space-y-4">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Filter NPM (Opsional)</label>
                        <input type="text" name="npm" placeholder="Contoh: 2114218001" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <p class="text-xs text-gray-500 mt-1">Kosongkan untuk download semua</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Program Studi (Opsional)</label>
                        <select name="program_studi" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                            <option value="">Semua Program Studi</option>
                            @foreach($programStudiList as $prodi)
                                <option value="{{ $prodi }}">{{ $prodi }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center">
                    <label class="flex items-center">
                        <input type="checkbox" name="download_all" value="1" class="h-4 w-4 text-primary-600 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-700">Download ulang semua (termasuk yang sudah ada foto)</span>
                    </label>
                </div>

                <div class="flex justify-end">
                    <button type="submit" 
                            class="px-6 py-2.5 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700"
                            onclick="return confirm('Yakin download foto? Proses ini memerlukan waktu.')"> 
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Download Foto
                    </button>
                </div>
            </form>
        </div>

        <!-- Preview Foto -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Preview Foto</h2>
            
            <div class="flex gap-4 mb-4">
                <input type="text" id="preview-npm" placeholder="Masukkan NPM" 
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                <button onclick="previewPhoto()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    Cek Foto
                </button>
            </div>

            <div id="preview-result" class="hidden">
                <div class="border rounded-lg p-4 text-center">
                    <img id="preview-image" src="" alt="Preview" class="mx-auto max-w-xs rounded-lg shadow-md">
                    <p id="preview-status" class="mt-2 text-sm font-medium"></p>
                </div>
            </div>
        </div>

        <!-- Tabel per Prodi -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Status Foto per Program Studi</h2>
            </div>
            
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Program Studi</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Ada Foto</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Belum Ada</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">% Lengkap</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($stats['by_prodi'] as $prodi)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $prodi->program_studi }}</td>
                            <td class="px-6 py-4 text-center text-sm text-gray-900">{{ $prodi->total }}</td>
                            <td class="px-6 py-4 text-center text-sm text-green-600 font-medium">{{ $prodi->with_photo }}</td>
                            <td class="px-6 py-4 text-center text-sm text-red-600 font-medium">{{ $prodi->without_photo }}</td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $percentage = $prodi->total > 0 ? round(($prodi->with_photo / $prodi->total) * 100, 1) : 0;
                                @endphp
                                <div class="flex items-center justify-center">
                                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-primary-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600">{{ $percentage }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script>
        function previewPhoto() {
            const npm = document.getElementById('preview-npm').value;
            if (!npm) {
                alert('Masukkan NPM terlebih dahulu');
                return;
            }

            const resultDiv = document.getElementById('preview-result');
            const image = document.getElementById('preview-image');
            const status = document.getElementById('preview-status');

            resultDiv.classList.remove('hidden');
            status.textContent = 'Memuat...';
            status.className = 'mt-2 text-sm font-medium text-gray-600';

            fetch('{{ route('admin.siakad-sync.photo.preview') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ npm: npm })
            })
            .then(response => response.json())
            .then(data => {
                image.src = data.url;
                image.onerror = function() {
                    status.textContent = 'Foto tidak ditemukan di server';
                    status.className = 'mt-2 text-sm font-medium text-red-600';
                };
                image.onload = function() {
                    status.textContent = data.exists ? 'Foto tersedia ✓' : 'Foto tidak ditemukan';
                    status.className = data.exists 
                        ? 'mt-2 text-sm font-medium text-green-600' 
                        : 'mt-2 text-sm font-medium text-red-600';
                };
            })
            .catch(error => {
                status.textContent = 'Error: ' + error.message;
                status.className = 'mt-2 text-sm font-medium text-red-600';
            });
        }
    </script>
    @endpush
@endsection