@extends('layouts.public')

@section('title', 'Alur Wisuda - ' . config('app.name', 'Sistem Absensi Wisuda'))

@push('styles')
<style>
    .gradient-text {
        background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
</style>
@endpush

@section('content')
<main>
    <!-- Page Header -->
    <section class="relative bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-700 pt-32 pb-20 md:pt-40 md:pb-28 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-400 rounded-full mix-blend-multiply filter blur-xl opacity-30"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-indigo-400 rounded-full mix-blend-multiply filter blur-xl opacity-30"></div>
        </div>
        <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="flex items-center justify-center gap-2 mb-4">
                <div class="w-2 h-2 rounded-full bg-amber-400"></div>
                <span class="text-xs font-bold tracking-widest uppercase text-blue-100">Universitas Sangga Buana YPKP</span>
                <div class="w-2 h-2 rounded-full bg-amber-400"></div>
            </div>
            <div class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-white text-sm font-semibold mb-6 border border-white/20">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                Alur Acara Resmi
            </div>
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold text-white mb-4 leading-tight">
                Wisuda <span class="text-amber-300">XXIII</span>
            </h1>
            <div class="flex flex-wrap items-center justify-center gap-3 text-sm">
                <span class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-full px-4 py-2 text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    30 Mei 2026 · Bandung
                </span>
                <span class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-full px-4 py-2 text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422A12.08 12.08 0 0118.5 19.5H5.5a12.08 12.08 0 01.34-8.922L12 14z"></path></svg>
                    Magister · Sarjana · Ahli Madya
                </span>
                <span class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-full px-4 py-2 text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-4a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    Gelombang I TA 2025/2026
                </span>
            </div>
        </div>
    </section>

    <!-- Timeline Content -->
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <!-- Section 1: Persiapan & Pembukaan -->
        <div class="flex items-center gap-3 mb-6">
            <div class="flex-1 h-px bg-gradient-to-r from-transparent via-blue-200 to-transparent"></div>
            <span class="flex items-center gap-2 bg-white border border-blue-100 rounded-full px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-blue-700 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                Persiapan & Pembukaan
            </span>
            <div class="flex-1 h-px bg-gradient-to-r from-transparent via-blue-200 to-transparent"></div>
        </div>
        <div class="space-y-3 mb-10">
            @include('alur-wisuda.partials.item', ['num' => 1, 'title' => 'Persiapan', 'sub' => 'Wisudawan dan tamu memasuki area acara'])
            @include('alur-wisuda.partials.item', ['num' => 2, 'title' => 'Pembukaan MC & Pembacaan Tata Tertib', 'sub' => 'MC membuka rangkaian upacara wisuda'])
            @include('alur-wisuda.partials.item', ['num' => 3, 'title' => 'Senat Memasuki Ruang Upacara', 'sub' => 'Prosesi masuk senat akademik'])
        </div>

        <!-- Section 2: Upacara Nasional & Keagamaan -->
        <div class="flex items-center gap-3 mb-6">
            <div class="flex-1 h-px bg-gradient-to-r from-transparent via-red-200 to-transparent"></div>
            <span class="flex items-center gap-2 bg-white border border-red-100 rounded-full px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-red-700 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-red-600"></span>
                Upacara Nasional & Keagamaan
            </span>
            <div class="flex-1 h-px bg-gradient-to-r from-transparent via-red-200 to-transparent"></div>
        </div>
        <div class="space-y-3 mb-10">
            @include('alur-wisuda.partials.item', ['num' => 4, 'title' => 'Menyanyikan Lagu Indonesia Raya & Mengheningkan Cipta', 'sub' => 'Penghormatan kepada negara'])
            @include('alur-wisuda.partials.item', ['num' => 5, 'title' => 'Pembacaan Ayat Suci Al-Qur\'an', 'sub' => 'Pembukaan secara spiritual'])
        </div>

        <!-- Section 3: Sidang Wisuda & Sambutan -->
        <div class="flex items-center gap-3 mb-6">
            <div class="flex-1 h-px bg-gradient-to-r from-transparent via-indigo-200 to-transparent"></div>
            <span class="flex items-center gap-2 bg-white border border-indigo-100 rounded-full px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-indigo-700 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                Sidang Wisuda & Sambutan
            </span>
            <div class="flex-1 h-px bg-gradient-to-r from-transparent via-indigo-200 to-transparent"></div>
        </div>
        <div class="space-y-3 mb-10">
            @include('alur-wisuda.partials.item', ['num' => 6, 'title' => 'Pembukaan Sidang Wisuda', 'sub' => 'Resmi dimulainya sidang wisuda'])
            @include('alur-wisuda.partials.item', ['num' => 7, 'title' => 'Pidato Rektor', 'sub' => 'Sambutan dan pesan dari pimpinan universitas'])
            @include('alur-wisuda.partials.item', ['num' => 8, 'title' => 'Sambutan Kepala LLDikti Wilayah IV', 'sub' => 'Pesan dari otoritas pendidikan tinggi'])
            @include('alur-wisuda.partials.item', ['num' => 9, 'title' => 'Pembacaan SK Wisudawan', 'sub' => 'Surat keputusan wisudawan dibacakan'])
        </div>

        <!-- Section 4: Prosesi Pelantikan & Penganugerahan (Highlighted) -->
        <div class="flex items-center gap-3 mb-6">
            <div class="flex-1 h-px bg-gradient-to-r from-transparent via-amber-200 to-transparent"></div>
            <span class="flex items-center gap-2 bg-gradient-to-r from-amber-50 to-yellow-50 border border-amber-200 rounded-full px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-amber-700 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                Prosesi Pelantikan & Penganugerahan ★
            </span>
            <div class="flex-1 h-px bg-gradient-to-r from-transparent via-amber-200 to-transparent"></div>
        </div>
        <div class="space-y-3 mb-10">
            @include('alur-wisuda.partials.item', ['num' => 10, 'title' => 'Pelantikan Wisudawan Terbaik', 'sub' => 'Penghargaan bagi wisudawan dengan prestasi terbaik', 'highlight' => true, 'star' => true])
            @include('alur-wisuda.partials.item', ['num' => 11, 'title' => 'Prosesi Pelantikan Wisudawan Serentak', 'sub' => 'Seluruh wisudawan dilantik bersama-sama', 'highlight' => true])
            @include('alur-wisuda.partials.item', ['num' => 12, 'title' => 'Prosesi Penganugerahan Gelar', 'sub' => 'Penyerahan ijazah dan pemindahan tali toga', 'highlight' => true])
        </div>

        <!-- Section 5: Janji Alumni & Apresiasi -->
        <div class="flex items-center gap-3 mb-6">
            <div class="flex-1 h-px bg-gradient-to-r from-transparent via-teal-200 to-transparent"></div>
            <span class="flex items-center gap-2 bg-white border border-teal-100 rounded-full px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-teal-700 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-teal-600"></span>
                Janji Alumni & Apresiasi
            </span>
            <div class="flex-1 h-px bg-gradient-to-r from-transparent via-teal-200 to-transparent"></div>
        </div>
        <div class="space-y-3 mb-10">
            @include('alur-wisuda.partials.item', ['num' => 13, 'title' => 'Pembacaan Janji Alumni', 'sub' => 'Ikrar alumni Universitas Sangga Buana YPKP'])
            @include('alur-wisuda.partials.item', ['num' => 14, 'title' => 'Penyerahan Bunga', 'sub' => 'Momen apresiasi wisudawan'])
            @include('alur-wisuda.partials.item', ['num' => 15, 'title' => 'Sambutan Wisudawan', 'sub' => 'Perwakilan wisudawan menyampaikan kesan dan pesan'])
            @include('alur-wisuda.partials.item', ['num' => 16, 'title' => 'Sambutan Orang Tua Wisudawan', 'sub' => 'Perwakilan orang tua menyampaikan sambutan'])
        </div>

        <!-- Section 6: Penutupan -->
        <div class="flex items-center gap-3 mb-6">
            <div class="flex-1 h-px bg-gradient-to-r from-transparent via-gray-300 to-transparent"></div>
            <span class="flex items-center gap-2 bg-white border border-gray-200 rounded-full px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-gray-600 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-gray-500"></span>
                Penutupan
            </span>
            <div class="flex-1 h-px bg-gradient-to-r from-transparent via-gray-300 to-transparent"></div>
        </div>
        <div class="space-y-3 mb-10">
            @include('alur-wisuda.partials.item', ['num' => 17, 'title' => 'Pembacaan Doa', 'sub' => 'Penutupan secara spiritual'])
            @include('alur-wisuda.partials.item', ['num' => 18, 'title' => 'Senat Meninggalkan Ruangan', 'sub' => 'Prosesi keluar senat akademik'])
            @include('alur-wisuda.partials.item', ['num' => 19, 'title' => 'Penutupan MC', 'sub' => 'Rangkaian acara wisuda resmi selesai'])
        </div>

        <!-- Footer Banner -->
        <div class="mt-12 bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 rounded-2xl p-6 flex items-center gap-5 relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-32 h-32 rounded-full bg-white/5"></div>
            <div class="absolute -bottom-12 -left-8 w-40 h-40 rounded-full bg-white/5"></div>
            <div class="w-14 h-14 bg-white/15 rounded-xl flex items-center justify-center text-2xl relative z-10 flex-shrink-0">
                🎓
            </div>
            <div class="relative z-10">
                <h3 class="text-white font-bold text-lg mb-1">Selamat kepada seluruh Wisudawan/Wisudawati!</h3>
                <p class="text-blue-100 text-sm leading-relaxed">Wisuda XXIII · Universitas Sangga Buana YPKP<br>Gelombang I · Tahun Akademik 2025/2026 · Bandung</p>
                <div class="flex gap-1.5 mt-3">
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                    <span class="w-2 h-2 rounded-full bg-white/30"></span>
                    <span class="w-2 h-2 rounded-full bg-white/30"></span>
                </div>
            </div>
        </div>

        <p class="text-center text-xs text-gray-400 mt-8">E-Wisuda · Universitas Sangga Buana YPKP · ewisuda.usbypkp.ac.id</p>
    </div>
</main>
@endsection
