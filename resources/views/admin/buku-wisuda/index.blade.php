@extends('layouts.admin')

@section('title', 'Buku Wisuda')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Buku Wisuda</h1>
            <a href="{{ route('admin.buku-wisuda.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                + Upload Buku
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <form method="GET" class="flex items-end space-x-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama file, slug"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="w-48">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Acara</label>
                    <select name="graduation_event_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">Semua Acara</option>
                        @foreach($events as $id => $name)
                            <option value="{{ $id }}" {{ request('graduation_event_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">Filter</button>
                <a href="{{ route('admin.buku-wisuda.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800">Reset</a>
            </form>
        </div>

        <!-- Events Without Buku Wisuda -->
        @if($eventsWithoutBuku->count() > 0)
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-yellow-900 mb-3">Acara Belum Memiliki Buku Wisuda</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($eventsWithoutBuku as $event)
                        <div class="bg-white p-4 rounded-lg border border-yellow-200">
                            <p class="font-medium text-gray-900">{{ $event->name }}</p>
                            <p class="text-sm text-gray-600 mb-3">{{ $event->date->format('d M Y') }}</p>
                            <a href="{{ route('admin.buku-wisuda.preview', $event) }}" 
                               class="inline-flex items-center px-3 py-1.5 bg-primary-600 text-white text-sm rounded hover:bg-primary-700">
                                Preview & Generate
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acara</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama File</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ukuran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Downloads</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($bukuWisudas as $buku)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $buku->graduationEvent->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 truncate max-w-xs">{{ $buku->filename }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($buku->status === 'draft') bg-gray-100 text-gray-800
                                    @elseif($buku->status === 'generated') bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800
                                    @endif">
                                    {{ ucfirst($buku->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $buku->getHumanFileSize() }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $buku->download_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm space-x-2">
                                <a href="{{ route('admin.buku-wisuda.preview', $buku->graduation_event_id) }}" class="text-blue-600 hover:text-blue-800">Preview</a>
                                <a href="{{ route('buku-wisuda.admin-viewer', $buku->slug) }}" target="_blank" class="text-primary-600 hover:text-primary-800">Lihat</a>
                                <a href="{{ route('admin.buku-wisuda.edit', $buku) }}" class="text-primary-600 hover:text-primary-800">Edit</a>
                                <form action="{{ route('admin.buku-wisuda.destroy', $buku) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Hapus buku ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">Tidak ada data buku wisuda</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $bukuWisudas->links() }}
        </div>
    </div>
@endsection