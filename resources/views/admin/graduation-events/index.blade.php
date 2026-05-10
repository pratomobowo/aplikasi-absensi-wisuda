@extends('layouts.admin')

@section('title', 'Acara Wisuda')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Acara Wisuda</h1>
            <a href="{{ route('admin.graduation-events.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                + Tambah Acara
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <form method="GET" class="flex items-end space-x-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama acara"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="w-48">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Event</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">Semua Status</option>
                        <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>Akan Datang</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai (Arsip)</option>
                    </select>
                </div>
                <div class="w-40">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dari</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="w-40">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sampai</label>
                    <input type="date" name="date_until" value="{{ request('date_until') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">Filter</button>
                <a href="{{ route('admin.graduation-events.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800">Reset</a>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Acara</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lokasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tiket</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($events as $event)
                        @php
                            $statusColors = [
                                'upcoming' => 'bg-blue-100 text-blue-800',
                                'active' => 'bg-green-100 text-green-800',
                                'completed' => 'bg-gray-100 text-gray-800',
                            ];
                            $statusLabels = [
                                'upcoming' => 'Akan Datang',
                                'active' => 'Aktif',
                                'completed' => 'Selesai',
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50 {{ $event->status === 'completed' ? 'opacity-60' : '' }}">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $event->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $event->date->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $event->time->format('H:i') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $event->location_name }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$event->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusLabels[$event->status] ?? $event->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $event->graduation_tickets_count }}</td>
                            <td class="px-6 py-4 text-sm space-x-2">
                                @if($event->status !== 'active')
                                    <form action="{{ route('admin.graduation-events.set-active', $event) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-800">Set Aktif</button>
                                    </form>
                                @endif
                                
                                <!-- Change Status Dropdown -->
                                <div class="relative inline-block text-left" x-data="{ open: false }">
                                    <button @click="open = !open" type="button" class="text-primary-600 hover:text-primary-800">
                                        Ubah Status
                                    </button>
                                    <div x-show="open" @click.away="open = false" 
                                         class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                        <div class="py-1">
                                            <form action="{{ route('admin.graduation-events.set-status', $event) }}" method="POST" class="block">
                                                @csrf
                                                <input type="hidden" name="status" value="upcoming">
                                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ $event->status === 'upcoming' ? 'bg-blue-50 text-blue-700' : '' }}">
                                                    Akan Datang
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.graduation-events.set-status', $event) }}" method="POST" class="block">
                                                @csrf
                                                <input type="hidden" name="status" value="active">
                                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ $event->status === 'active' ? 'bg-green-50 text-green-700' : '' }}">
                                                    Aktif
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.graduation-events.set-status', $event) }}" method="POST" class="block">
                                                @csrf
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 {{ $event->status === 'completed' ? 'bg-red-50' : '' }}" onclick="return confirm('Yakin menandai acara ini sebagai selesai? Data akan diarsipkan.')">
                                                    Selesai (Arsip)
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                
                                <form action="{{ route('admin.graduation-events.generate-tickets', $event) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-blue-600 hover:text-blue-800">Generate Tiket</button>
                                </form>
                                <a href="{{ route('admin.graduation-events.export-tickets', $event) }}" class="text-green-600 hover:text-green-800">Export</a>
                                <a href="{{ route('admin.graduation-events.edit', $event) }}" class="text-primary-600 hover:text-primary-800">Edit</a>
                                <form action="{{ route('admin.graduation-events.destroy', $event) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Hapus acara ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">Tidak ada data acara</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $events->links() }}
        </div>
    </div>
@endsection