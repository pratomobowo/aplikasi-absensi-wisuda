@extends('layouts.admin')

@section('title', 'Data Konsumsi')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Data Konsumsi</h1>
        </div>

        <!-- Filters -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <form method="GET" class="flex items-end space-x-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama mahasiswa, NPM"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="w-40">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="konsumsi_diterima" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">Semua</option>
                        <option value="1" {{ request('konsumsi_diterima') === '1' ? 'selected' : '' }}>Sudah Diterima</option>
                        <option value="0" {{ request('konsumsi_diterima') === '0' ? 'selected' : '' }}>Belum Diterima</option>
                    </select>
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
                <a href="{{ route('admin.konsumsi.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800">Reset</a>
            </form>
        </div>

        <!-- Bulk Actions -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <form id="bulk-form" method="POST" class="flex items-center space-x-3">
                @csrf
                <button type="button" onclick="bulkAction('{{ route('admin.konsumsi.bulk-mark-received') }}')" 
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Tandai Sudah Diterima
                </button>
                <button type="button" onclick="bulkAction('{{ route('admin.konsumsi.bulk-mark-not-received') }}')" 
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Tandai Belum Diterima
                </button>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            <input type="checkbox" id="select-all" class="h-4 w-4 text-primary-600 border-gray-300 rounded">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mahasiswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NPM</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu Scan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Scan Oleh</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($tickets as $ticket)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <input type="checkbox" name="ids[]" value="{{ $ticket->id }}" class="row-checkbox h-4 w-4 text-primary-600 border-gray-300 rounded">
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $ticket->mahasiswa->nama ?? 'Unknown' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $ticket->mahasiswa->npm ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @if($ticket->konsumsi_diterima)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Sudah Diterima</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Belum Diterima</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $ticket->konsumsi_at ? $ticket->konsumsi_at->format('d M Y H:i:s') : '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $ticket->konsumsiRecord->first()?->scannedBy->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm">
                                <form action="{{ route('admin.konsumsi.toggle', $ticket) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-primary-600 hover:text-primary-800">
                                        {{ $ticket->konsumsi_diterima ? 'Tandai Belum' : 'Tandai Sudah' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">Tidak ada data konsumsi</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $tickets->links() }}
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('select-all').addEventListener('change', function() {
            document.querySelectorAll('.row-checkbox').forEach(cb => {
                cb.checked = this.checked;
            });
        });

        function bulkAction(url) {
            const form = document.getElementById('bulk-form');
            const checked = document.querySelectorAll('.row-checkbox:checked');
            
            if (checked.length === 0) {
                alert('Pilih minimal satu data.');
                return;
            }

            if (!confirm('Yakin ingin melakukan aksi ini?')) {
                return;
            }

            // Remove existing hidden inputs
            form.querySelectorAll('input[type="hidden"]').forEach(el => el.remove());

            checked.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                form.appendChild(input);
            });

            form.action = url;
            form.submit();
        }
    </script>
    @endpush
@endsection