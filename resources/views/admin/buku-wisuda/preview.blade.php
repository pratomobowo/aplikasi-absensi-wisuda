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
                <form action="{{ route('admin.buku-wisuda.generate', $event) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700"
                            @if($bukuWisuda && $bukuWisuda->status === 'published') disabled @endif>
                        {{ $bukuWisuda ? 'Regenerate PDF' : 'Generate PDF' }}
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
                <div class="bg-green-50 p-4 rounded-lg">
                    <p class="text-sm text-green-600 font-medium">Dengan Foto</p>
                    <p class="text-2xl font-bold text-green-900">{{ $mahasiswa->whereNotNull('foto_wisuda')->count() }}</p>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <p class="text-sm text-yellow-600 font-medium">Tanpa Foto</p>
                    <p class="text-2xl font-bold text-yellow-900">{{ $mahasiswa->whereNull('foto_wisuda')->count() }}</p>
                </div>
                <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="text-sm text-blue-600 font-medium">Dengan Judul</p>
                    <p class="text-2xl font-bold text-blue-900">{{ $mahasiswa->whereNotNull('judul_skripsi')->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Preview List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Preview Data Wisudawan</h3>
                <p class="text-sm text-gray-600 mt-1">Berikut adalah data yang akan dimasukkan ke dalam buku wisuda</p>
            </div>
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Foto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NPM</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Program Studi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul Skripsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($mahasiswa as $index => $mhs)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                @if($mhs->foto_wisuda)
                                    <img src="{{ $mhs->getFotoWisudaUrlAttribute() }}" 
                                         alt="{{ $mhs->nama }}"
                                         class="w-12 h-16 object-cover rounded border border-gray-200">
                                @else
                                    <div class="w-12 h-16 bg-gray-200 rounded flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $mhs->nama }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $mhs->npm }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $mhs->program_studi ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 max-w-md truncate">{{ $mhs->judul_skripsi ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                                Tidak ada data wisudawan untuk acara ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection