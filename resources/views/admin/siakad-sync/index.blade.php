@extends('layouts.admin')

@section('title', 'Sync SIAKAD')

@section('content')
    <div class="space-y-6">
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

        <!-- Tabs -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <button onclick="switchTab('bulk')" id="tab-bulk" class="tab-btn px-6 py-4 text-sm font-medium border-b-2 border-primary-500 text-primary-600">
                        Sync Massal
                    </button>
                    <button onclick="switchTab('single')" id="tab-single" class="tab-btn px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                        Sync per NIM
                    </button>
                </nav>
            </div>

            <!-- Tab: Sync Massal -->
            <div id="content-bulk" class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Ambil Data dari SIAKAD</h2>
            
            <form action="{{ route('admin.siakad-sync.preview') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div class="flex flex-col md:flex-row gap-4 items-start md:items-end">
                        <div class="w-full md:w-56">
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
                            <button type="submit" class="w-full md:w-auto px-6 py-2.5 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700">
                                Preview Data
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            </div>

            <!-- Tab: Sync per NIM -->
            <div id="content-single" class="p-6 hidden">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Sync Data per NIM</h2>
                <p class="text-sm text-gray-600 mb-4">Cocok untuk test download foto atau update data satu mahasiswa saja.</p>

                <form action="{{ route('admin.siakad-sync.sync-single') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="flex flex-col md:flex-row gap-4 items-start md:items-end">
                        <div class="w-full md:w-80">
                            <label class="block text-sm font-medium text-gray-700 mb-1">NPM / NIM *</label>
                            <input type="text" name="nim" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
                                   placeholder="Contoh: 2112217019">
                            <p class="text-xs text-gray-500 mt-1">Masukkan NPM mahasiswa yang ingin di-sync</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="download_photo" value="1" checked 
                                       class="h-4 w-4 text-primary-600 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Download foto</span>
                            </label>
                        </div>
                        <div>
                            <button type="submit" 
                                    class="w-full md:w-auto px-6 py-2.5 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700"
                                    onclick="this.disabled=true; this.innerHTML='Memproses...'; this.form.submit();">
                                Sync Sekarang
                            </button>
                        </div>
                    </div>
                </form>

                @if(session('sync_single_result'))
                    @php $result = session('sync_single_result'); @endphp
                    <div class="mt-6 p-4 rounded-lg {{ $result['success'] ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
                        <h3 class="font-semibold {{ $result['success'] ? 'text-green-800' : 'text-red-800' }} mb-2">
                            {{ $result['success'] ? '✅ Sync Berhasil' : '❌ Sync Gagal' }}
                        </h3>
                        <div class="text-sm space-y-1 {{ $result['success'] ? 'text-green-700' : 'text-red-700' }}">
                            <p><strong>NIM:</strong> {{ $result['nim'] }}</p>
                            <p><strong>Nama:</strong> {{ $result['nama'] ?? '-' }}</p>
                            <p><strong>Status:</strong> {{ $result['message'] }}</p>
                            @if(isset($result['photo_downloaded']))
                                <p><strong>Foto:</strong> {{ $result['photo_downloaded'] ? '✅ Berhasil didownload' : '❌ Gagal didownload' }}</p>
                            @endif
                            @if(isset($result['error']))
                                <p><strong>Error:</strong> {{ $result['error'] }}</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
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

        <!-- Info Card -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="text-sm text-blue-800">
                    <p class="font-semibold mb-1">Cara Kerja Sync:</p>
                    <ul class="list-disc list-inside space-y-1">
                        <li>Preview akan menampilkan sampel 20 data dari SIAKAD</li>
                        <li>Saat sync, data mahasiswa akan di-import/update otomatis</li>
                        <li>Foto akan di-download otomatis dari server SEVIMA</li>
                        <li>Jika timeout, proses akan auto-retry hingga 3 kali</li>
                        <li>Progress dapat dipantau secara real-time</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
    function switchTab(tab) {
        // Hide all contents
        document.getElementById('content-bulk').classList.add('hidden');
        document.getElementById('content-single').classList.add('hidden');
        
        // Reset all tabs
        document.getElementById('tab-bulk').classList.remove('border-primary-500', 'text-primary-600');
        document.getElementById('tab-bulk').classList.add('border-transparent', 'text-gray-500');
        document.getElementById('tab-single').classList.remove('border-primary-500', 'text-primary-600');
        document.getElementById('tab-single').classList.add('border-transparent', 'text-gray-500');
        
        // Show selected
        document.getElementById('content-' + tab).classList.remove('hidden');
        document.getElementById('tab-' + tab).classList.remove('border-transparent', 'text-gray-500');
        document.getElementById('tab-' + tab).classList.add('border-primary-500', 'text-primary-600');
    }
</script>
@endpush

@endsection
