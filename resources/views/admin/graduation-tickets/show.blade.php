@extends('layouts.admin')

@section('title', 'Detail Tiket')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center mb-6">
            <a href="{{ route('admin.graduation-tickets.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">&larr; Kembali</a>
            <h1 class="text-2xl font-bold text-gray-900">Detail Tiket</h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Mahasiswa Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Mahasiswa</h2>
                <div class="flex items-start gap-4 mb-4">
                    @if($graduationTicket->mahasiswa->foto_wisuda && \Illuminate\Support\Facades\Storage::disk('public')->exists('graduation-photos/' . $graduationTicket->mahasiswa->foto_wisuda))
                        <img src="{{ $graduationTicket->mahasiswa->foto_wisuda_url }}" alt="Foto {{ $graduationTicket->mahasiswa->nama }}" class="w-24 h-24 rounded-xl object-cover shadow-sm">
                    @else
                        <div class="w-24 h-24 rounded-xl bg-gray-200 flex items-center justify-center shadow-sm">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                    @endif
                    <div class="flex-1 space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Nama</span>
                            <span class="text-sm font-medium text-gray-900">{{ $graduationTicket->mahasiswa->nama }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">NPM</span>
                            <span class="text-sm font-medium text-gray-900">{{ $graduationTicket->mahasiswa->npm }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Program Studi</span>
                            <span class="text-sm font-medium text-gray-900">{{ $graduationTicket->mahasiswa->program_studi }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Event Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Acara</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Nama Acara</span>
                        <span class="text-sm font-medium text-gray-900">{{ $graduationTicket->graduationEvent->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Tanggal</span>
                        <span class="text-sm font-medium text-gray-900">{{ $graduationTicket->graduationEvent->date->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Waktu</span>
                        <span class="text-sm font-medium text-gray-900">{{ $graduationTicket->graduationEvent->time->format('H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Lokasi</span>
                        <span class="text-sm font-medium text-gray-900">{{ $graduationTicket->graduationEvent->location_name }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- QR Code -->
        <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">QR Code Absensi & Konsumsi</h2>
            <div class="flex justify-center">
                <div class="text-center max-w-sm">
                    <p class="text-sm font-medium text-gray-700 mb-2">Wisudawan</p>
                    @php $qrService = app(\App\Services\QRCodeService::class); @endphp
                    <img src="{{ $qrService->generateQRCode($graduationTicket->qr_token_mahasiswa) }}" alt="QR Mahasiswa" class="mx-auto w-64 h-64">
                    <p class="text-xs text-gray-500 mt-2">Scan pagi untuk absensi, scan sore untuk konsumsi</p>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Aksi</h2>
            <div class="flex flex-wrap gap-3">
                <button onclick="copyToClipboard('{{ route('invitation.show', $graduationTicket->magic_link_token) }}')" 
                        class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    Copy Link
                </button>
                <a href="https://wa.me/?text={{ urlencode('Halo ' . $graduationTicket->mahasiswa->nama . "\n\nSelamat! Anda telah terdaftar untuk mengikuti " . $graduationTicket->graduationEvent->name . ".\n\nSilakan akses undangan digital Anda melalui link berikut:\n" . route('invitation.show', $graduationTicket->magic_link_token) . "\n\nTunjukkan QR code pada halaman undangan saat acara berlangsung.\n\nTerima kasih.") }}" 
                   target="_blank"
                   class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Kirim WhatsApp
                </a>
                <a href="{{ route('invitation.show', $graduationTicket->magic_link_token) }}" target="_blank"
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Lihat Undangan
                </a>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Link berhasil disalin!');
            });
        }
    </script>
    @endpush
@endsection