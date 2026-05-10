@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="space-y-8">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Mahasiswa</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_mahasiswa']) }}</p>
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
                        <p class="text-sm font-medium text-gray-600">Acara Aktif</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['active_event'] }}</p>
                    </div>
                    <div class="p-3 bg-green-50 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Tiket</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_tickets']) }}</p>
                    </div>
                    <div class="p-3 bg-purple-50 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Kehadiran Hari Ini</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['today_attendance']) }}</p>
                    </div>
                    <div class="p-3 bg-orange-50 rounded-lg">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Secondary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <p class="text-sm font-medium text-gray-600 mb-2">Distribusi Tiket</p>
                <div class="flex items-end space-x-2">
                    <span class="text-3xl font-bold text-gray-900">{{ $stats['distributed_tickets'] }}</span>
                    <span class="text-sm text-gray-500 mb-1">/ {{ $stats['total_tickets'] }}</span>
                </div>
                <div class="mt-3 w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-primary-600 h-2 rounded-full" style="width: {{ $stats['total_tickets'] > 0 ? ($stats['distributed_tickets'] / $stats['total_tickets'] * 100) : 0 }}%"></div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <p class="text-sm font-medium text-gray-600 mb-2">Konsumsi</p>
                <div class="flex items-end space-x-2">
                    <span class="text-3xl font-bold text-green-600">{{ $stats['konsumsi_received'] }}</span>
                    <span class="text-sm text-gray-500 mb-1">diterima</span>
                </div>
                <p class="text-sm text-gray-500 mt-1">{{ $stats['konsumsi_pending'] }} belum diterima</p>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <p class="text-sm font-medium text-gray-600 mb-2">Foto Wisuda</p>
                <div class="flex items-end space-x-2">
                    <span class="text-3xl font-bold text-primary-600">{{ $stats['mahasiswa_with_photos'] }}</span>
                    <span class="text-sm text-gray-500 mb-1">/uploaded</span>
                </div>
                <p class="text-sm text-red-500 mt-1">{{ $stats['mahasiswa_without_photos'] }} belum upload</p>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Kehadiran Terbaru</h2>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($recentAttendances as $attendance)
                        <div class="p-4 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $attendance->graduationTicket->mahasiswa->nama ?? 'Unknown' }}</p>
                                <p class="text-xs text-gray-500">{{ ucfirst($attendance->role) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-900">{{ $attendance->scannedBy->name ?? 'System' }}</p>
                                <p class="text-xs text-gray-500">{{ $attendance->scanned_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-sm text-gray-500">Belum ada data kehadiran</div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Konsumsi Terbaru</h2>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($recentKonsumsi as $ticket)
                        <div class="p-4 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $ticket->mahasiswa->nama ?? 'Unknown' }}</p>
                                <p class="text-xs text-gray-500">{{ $ticket->mahasiswa->npm ?? '-' }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Diterima
                                </span>
                                <p class="text-xs text-gray-500 mt-1">{{ $ticket->konsumsi_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-sm text-gray-500">Belum ada data konsumsi</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection