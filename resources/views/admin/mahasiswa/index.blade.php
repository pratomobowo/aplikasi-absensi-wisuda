@extends('layouts.admin')

@section('title', 'List Wisudawan')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">List Wisudawan</h1>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.mahasiswa.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    + Tambah Mahasiswa
                </a>
                <a href="{{ route('admin.mahasiswa.export') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Export Excel
                </a>
            </div>
        </div>

        <!-- Import Section -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <form action="{{ route('admin.mahasiswa.import') }}" method="POST" enctype="multipart/form-data" class="flex items-end space-x-4">
                @csrf
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Import Data Mahasiswa</label>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Import</button>
                <a href="{{ route('admin.mahasiswa.template') }}" class="px-4 py-2 text-primary-600 hover:text-primary-700">Download Template</a>
            </form>
        </div>

        <!-- Filters -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <form method="GET" class="flex items-end space-x-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="NPM, Nama, Email, Program Studi"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="w-64">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Acara Wisuda</label>
                    <select name="graduation_event_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">Semua Acara Aktif</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{ request('graduation_event_id') == $event->id ? 'selected' : '' }}>
                                {{ $event->name }} ({{ $event->date->format('d M Y') }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="w-64">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Program Studi</label>
                    <select name="program_studi" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">Semua Program Studi</option>
                        @foreach($programStudis as $ps)
                            <option value="{{ $ps }}" {{ request('program_studi') == $ps ? 'selected' : '' }}>{{ $ps }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">Filter</button>
                <a href="{{ route('admin.mahasiswa.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800">Reset</a>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NPM</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Program Studi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IPK</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Yudisium</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acara Wisuda</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Foto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($mahasiswas as $mahasiswa)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $mahasiswa->npm }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $mahasiswa->nama }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $mahasiswa->program_studi }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ number_format($mahasiswa->ipk, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $mahasiswa->yudisium ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                @if($mahasiswa->graduationTickets->isNotEmpty())
                                    @foreach($mahasiswa->graduationTickets as $ticket)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mb-1">
                                            {{ $ticket->graduationEvent->name ?? 'Unknown' }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-xs text-gray-400">Belum ada tiket</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($mahasiswa->foto_wisuda)
                                    <img src="{{ $mahasiswa->foto_wisuda_url }}" alt="Foto" class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <span class="text-xs text-gray-400">Belum upload</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm space-x-2">
                                <a href="{{ route('admin.mahasiswa.edit', $mahasiswa) }}" class="text-primary-600 hover:text-primary-800">Edit</a>
                                <form action="{{ route('admin.mahasiswa.reset-password', $mahasiswa) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-yellow-600 hover:text-yellow-800" onclick="return confirm('Reset password ke NPM?')">Reset Password</button>
                                </form>
                                <form action="{{ route('admin.mahasiswa.destroy', $mahasiswa) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Hapus mahasiswa ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-sm text-gray-500">Tidak ada data wisudawan untuk acara ini</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $mahasiswas->links() }}
        </div>
    </div>
@endsection