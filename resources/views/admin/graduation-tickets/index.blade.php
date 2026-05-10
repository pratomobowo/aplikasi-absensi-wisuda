@extends('layouts.admin')

@section('title', 'Tiket Wisuda')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Tiket Wisuda</h1>
            <a href="{{ route('admin.graduation-tickets.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                + Tambah Tiket
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <form method="GET" class="flex items-end space-x-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama mahasiswa, NPM"
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
                <div class="w-40">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Distribusi</label>
                    <select name="is_distributed" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">Semua</option>
                        <option value="1" {{ request('is_distributed') === '1' ? 'selected' : '' }}>Sudah</option>
                        <option value="0" {{ request('is_distributed') === '0' ? 'selected' : '' }}>Belum</option>
                    </select>
                </div>
                <div class="w-40">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kehadiran</label>
                    <select name="attendance_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">Semua</option>
                        <option value="all_attended" {{ request('attendance_status') === 'all_attended' ? 'selected' : '' }}>Semua Hadir</option>
                        <option value="partial_attended" {{ request('attendance_status') === 'partial_attended' ? 'selected' : '' }}>Sebagian</option>
                        <option value="not_attended" {{ request('attendance_status') === 'not_attended' ? 'selected' : '' }}>Belum Hadir</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">Filter</button>
                <a href="{{ route('admin.graduation-tickets.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800">Reset</a>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mahasiswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NPM</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acara</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Terdistribusi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kehadiran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($tickets as $ticket)
                        @php
                            $attendanceStatus = $ticket->getAttendanceStatus();
                            $statusText = ($attendanceStatus['mahasiswa'] ? '1' : '0') . '/' . 
                                          ($attendanceStatus['pendamping1'] ? '1' : '0') . '/' . 
                                          ($attendanceStatus['pendamping2'] ? '1' : '0');
                            $allAttended = $attendanceStatus['mahasiswa'] && $attendanceStatus['pendamping1'] && $attendanceStatus['pendamping2'];
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $ticket->mahasiswa->nama ?? 'Unknown' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $ticket->mahasiswa->npm ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $ticket->graduationEvent->name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @if($ticket->is_distributed)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Ya</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Tidak</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $allAttended ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm space-x-2">
                                <a href="{{ route('admin.graduation-tickets.show', $ticket) }}" class="text-primary-600 hover:text-primary-800">Detail</a>
                                <a href="{{ route('invitation.show', $ticket->magic_link_token) }}" target="_blank" class="text-blue-600 hover:text-blue-800">Lihat</a>
                                <form action="{{ route('admin.graduation-tickets.destroy', $ticket) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Hapus tiket ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">Tidak ada data tiket</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $tickets->links() }}
        </div>
    </div>
@endsection