@extends('layouts.admin')

@section('title', 'Preview Data SIAKAD')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('admin.siakad-sync.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">&larr; Kembali</a>
                <h1 class="text-2xl font-bold text-gray-900">Preview Data SIAKAD</h1>
            </div>
        </div>

        <!-- Summary -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <p class="text-sm text-gray-600">Periode</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $periode }}</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-600">Total Data</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $totalData }}</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-600">Sudah Ada</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $existingCount }}</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-600">Baru</p>
                    <p class="text-2xl font-bold text-green-600">{{ $totalData - $existingCount }}</p>
                </div>
            </div>
        </div>

        <!-- Preview Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Preview Data ({{ count($previewData) }} dari {{ $totalData }})</h2>
            </div>
            
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIM/NPM</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Program Studi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IPK</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Yudisium</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($previewData as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item['nim'] }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $item['nama'] }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $item['program_studi'] }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ number_format($item['ipk'], 2) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $item['yudisium'] ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @if($item['exists'])
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Update {{ $item['has_photo'] ? '+Foto' : '' }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Baru
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Action Buttons -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form action="{{ route('admin.siakad-sync.sync') }}" method="POST">
                @csrf
                <input type="hidden" name="periode" value="{{ $periode }}">
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <label class="flex items-center">
                            <input type="checkbox" name="skip_foto" value="1" class="h-4 w-4 text-primary-600 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Lewati download foto (lebih cepat)</span>
                        </label>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('admin.siakad-sync.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">Batal</a>
                        <button type="submit" class="px-6 py-2.5 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700" onclick="return confirm('Yakin menyimpan {{ $totalData }} data ke database?')">
                            Simpan ke Database ({{ $totalData }} data)
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection