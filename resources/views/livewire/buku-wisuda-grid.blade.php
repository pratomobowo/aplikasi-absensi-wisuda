<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    @foreach($mahasiswas as $mhs)
        <div class="group bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-200">
            <!-- Card Layout: Foto Kiri, Data Kanan -->
            <div class="flex flex-col sm:flex-row">
                
                <!-- Kolom 1: Foto (Kiri) - Fixed Size -->
                <div class="relative w-full sm:w-[200px] flex-shrink-0 bg-gradient-to-br from-blue-50 to-indigo-50">
                    <div class="w-full h-[300px] flex items-center justify-center overflow-hidden">
                        @if($mhs->foto_wisuda && file_exists(public_path('storage/graduation-photos/' . $mhs->foto_wisuda)))
                            <img
                                src="{{ asset('storage/graduation-photos/' . $mhs->foto_wisuda) }}"
                                alt="{{ $mhs->nama }}"
                                class="w-full h-full object-cover object-top"
                                loading="lazy"
                            >
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 bg-gray-100">
                                <svg class="w-20 h-20 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="text-sm">Foto tidak tersedia</span>
                            </div>
                        @endif
                    </div>

                    <!-- Badge Yudisium -->
                    @if($mhs->yudisium)
                        <div class="absolute top-4 left-4">
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold border-2 shadow-lg backdrop-blur-md bg-white/95 {{ $this->getYudisiumColor($mhs->yudisium) }}">
                                {{ $this->getYudisiumLabel($mhs->yudisium) }}
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Kolom 2: Data Mahasiswa (Kanan) -->
                <div class="flex-1 p-6 flex flex-col justify-center bg-white min-h-[300px]">
                    <!-- Nama -->
                    <h3 class="text-xl font-bold text-gray-900 mb-5 leading-snug border-b border-gray-100 pb-3">
                        {{ $mhs->nama }}
                    </h3>
                    
                    <!-- Data Grid -->
                    <div class="space-y-3">
                        <!-- NPM -->
                        <div class="flex items-center">
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider w-24 flex-shrink-0">NPM</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $mhs->npm }}</span>
                        </div>

                        <!-- Program Studi -->
                        <div class="flex items-center">
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider w-24 flex-shrink-0">Prodi</span>
                            <span class="text-sm text-gray-700">{{ $mhs->program_studi }}</span>
                        </div>

                        <!-- IPK -->
                        <div class="flex items-center">
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider w-24 flex-shrink-0">IPK</span>
                            <span class="text-lg font-bold text-blue-600">{{ number_format($mhs->ipk, 2) }}</span>
                        </div>

                        <!-- Yudisium -->
                        <div class="flex items-center">
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider w-24 flex-shrink-0">Yudisium</span>
                            <span class="text-sm text-gray-700 font-medium">{{ $mhs->yudisium ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Baris 2: Judul Skripsi (Full Width) -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 mt-0.5">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Judul Skripsi / Thesis</p>
                        <p class="text-sm text-gray-800 leading-relaxed font-medium">
                            {{ $mhs->judul_skripsi ?? 'Belum diisi' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
