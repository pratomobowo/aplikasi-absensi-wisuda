@extends('layouts.admin')

@section('title', 'Sync SIAKAD')

@section('content')
    <div class="space-y-6">
        <!-- Header dengan Tab -->
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <a href="{{ route('admin.siakad-sync.index') }}" 
                   class="border-primary-500 text-primary-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                    </svg>
                    Sync Data
                </a>
                <a href="{{ route('admin.siakad-sync.photo') }}" 
                   class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Download Foto
                </a>
            </nav>
        </div>
        
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Sync Data SIAKAD</h1>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Wisudawan</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_mahasiswa']) }}</p>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Dengan Foto</p>
                        <p class="text-3xl font-bold text-green-600 mt-1">{{ number_format($stats['with_photo']) }}</p>
                    </div>
                    <div class="p-3 bg-green-50 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Tanpa Foto</p>
                        <p class="text-3xl font-bold text-red-600 mt-1">{{ number_format($stats['without_photo']) }}</p>
                    </div>
                    <div class="p-3 bg-red-50 rounded-lg">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sync Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Ambil Data dari SIAKAD</h2>
            
            <form action="{{ route('admin.siakad-sync.preview') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Periode Akademik *</label>
                        <select name="periode" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                            <option value="">Pilih Periode</option>
                            <option value="20251">2025 Gelombang 1 (20251)</option>
                            <option value="20252">2025 Gelombang 2 (20252)</option>
                            <option value="20241">2024 Gelombang 1 (20241)</option>
                            <option value="20242">2024 Gelombang 2 (20242)</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Periode akademik sesuai format SIAKAD Sevima (e.g. 20251 = Tahun 2025, Semester/Gelombang 1)</p>
                    </div>
                    <div>
                        <button type="submit" class="w-full px-6 py-2.5 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700">
                            Preview Data
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Program Studi Stats -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Distribusi per Program Studi</h2>
            </div>
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Program Studi</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-1/2">Distribusi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($stats['by_prodi'] as $prodi)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $prodi->program_studi }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 text-right">{{ $prodi->total }}</td>
                            <td class="px-6 py-4">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-primary-600 h-2 rounded-full" style="width: {{ $stats['total_mahasiswa'] > 0 ? ($prodi->total / $stats['total_mahasiswa'] * 100) : 0 }}%"></div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-500">Belum ada data wisudawan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection