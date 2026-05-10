@extends('layouts.admin')

@section('title', 'Kehadiran')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Kehadiran</h1>
        </div>

        <!-- Filters -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <form method="GET" class="flex items-end space-x-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari Mahasiswa</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama mahasiswa"
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select name="role" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">Semua</option>
                        <option value="mahasiswa" {{ request('role') === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                        <option value="pendamping1" {{ request('role') === 'pendamping1' ? 'selected' : '' }}>Pendamping 1</option>
                        <option value="pendamping2" {{ request('role') === 'pendamping2' ? 'selected' : '' }}>Pendamping 2</option>
                    </select>
                </div>
                <div class="w-40">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                    <input type="date" name="scanned_from" value="{{ request('scanned_from') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="w-40">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                    <input type="date" name="scanned_until" value="{{ request('scanned_until') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">Filter</button>
                <a href="{{ route('admin.attendance.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800">Reset</a>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mahasiswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acara</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu Scan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Scanner</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($attendances as $attendance)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $attendance->graduationTicket->mahasiswa->nama ?? 'Unknown' }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $roleColors = [
                                        'mahasiswa' => 'bg-blue-100 text-blue-800',
                                        'pendamping1' => 'bg-green-100 text-green-800',
                                        'pendamping2' => 'bg-yellow-100 text-yellow-800',
                                    ];
                                    $roleLabels = [
                                        'mahasiswa' => 'Mahasiswa',
                                        'pendamping1' => 'Pendamping 1',
                                        'pendamping2' => 'Pendamping 2',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $roleColors[$attendance->role] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $roleLabels[$attendance->role] ?? $attendance->role }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $attendance->graduationTicket->graduationEvent->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $attendance->scanned_at->format('d M Y H:i:s') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $attendance->scannedBy->name ?? 'System' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">Tidak ada data kehadiran</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $attendances->links() }}
        </div>
    </div>
@endsection