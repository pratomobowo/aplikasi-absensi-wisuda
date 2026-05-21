<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    @foreach($mahasiswas as $mhs)
        <div class="group bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-200">
            <!-- Layout: Foto Kiri, Data Kanan -->
            <div class="flex flex-col sm:flex-row">
                
                <!-- Kolom Kiri: Foto Portrait -->
                <div class="w-full sm:w-48 flex-shrink-0 bg-gray-100">
                    <div class="h-80 w-full relative">
                        @if($mhs->foto_wisuda && file_exists(public_path('storage/graduation-photos/' . $mhs->foto_wisuda)))
                            <img
                                src="{{ asset('storage/graduation-photos/' . $mhs->foto_wisuda) }}"
                                alt="{{ $mhs->nama }}"
                                class="w-full h-full object-cover object-top"
                                loading="lazy"
                            >
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-16 h-16 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="text-sm">Foto tidak tersedia</span>
                            </div>
                        @endif
                        
                        <!-- Badge Yudisium -->
                        @if($mhs->yudisium)
                            <div class="absolute top-3 left-3">
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold border shadow-md bg-white {{ $this->getYudisiumColor($mhs->yudisium) }}">
                                    {{ $this->getYudisiumLabel($mhs->yudisium) }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Kolom Kanan: Data Mahasiswa -->
                <div class="flex-1 p-5 flex flex-col justify-center">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100">
                        {{ $mhs->nama }}
                    </h3>
                    
                    <div class="space-y-2">
                        <div class="flex">
                            <span class="text-xs font-bold text-gray-500 uppercase w-20">NPM</span>
                            <span class="text-sm text-gray-900">{{ $mhs->npm }}</span>
                        </div>

                        <div class="flex">
                            <span class="text-xs font-bold text-gray-500 uppercase w-20">Prodi</span>
                            <span class="text-sm text-gray-700">{{ $mhs->program_studi }}</span>
                        </div>

                        <div class="flex">
                            <span class="text-xs font-bold text-gray-500 uppercase w-20">IPK</span>
                            <span class="text-base font-bold text-blue-600">{{ number_format($mhs->ipk, 2) }}</span>
                        </div>

                        <div class="flex">
                            <span class="text-xs font-bold text-gray-500 uppercase w-20">Yudisium</span>
                            <span class="text-sm text-gray-700">{{ $mhs->yudisium ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Baris Bawah: Judul Skripsi -->
            <div class="px-5 py-3 bg-gray-50 border-t border-gray-200">
                <div class="flex items-start gap-2">
                    <div class="w-6 h-6 rounded bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase mb-0.5">Judul Skripsi</p>
                        <p class="text-sm text-gray-800">{{ $mhs->judul_skripsi ?? 'Belum diisi' }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
