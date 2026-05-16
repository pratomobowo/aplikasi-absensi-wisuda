<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    @foreach($mahasiswas as $mhs)
        <div class="group bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden border border-gray-100 hover:border-blue-200">
            <div class="flex flex-col sm:flex-row">
                <!-- Photo Section - Left Side -->
                <div class="relative w-full sm:w-48 md:w-56 flex-shrink-0">
                    <div class="aspect-[3/4] sm:aspect-auto sm:h-full bg-gradient-to-br from-blue-50 to-indigo-50">
                        @if($mhs->foto_wisuda && file_exists(public_path('storage/graduation-photos/' . $mhs->foto_wisuda)))
                            <img
                                src="{{ asset('storage/graduation-photos/' . $mhs->foto_wisuda) }}"
                                alt="{{ $mhs->nama }}"
                                class="w-full h-full object-cover"
                                loading="lazy"
                            >
                        @else
                            <div class="w-full h-full min-h-[200px] flex items-center justify-center">
                                <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Yudisium Badge on Photo -->
                    @if($mhs->yudisium)
                        <div class="absolute top-3 left-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $this->getYudisiumColor($mhs->yudisium) }} shadow-sm backdrop-blur-sm bg-white/90">
                                {{ $this->getYudisiumLabel($mhs->yudisium) }}
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Info Section - Right Side -->
                <div class="flex-1 p-5 flex flex-col justify-between">
                    <div class="space-y-3">
                        <!-- Name -->
                        <h3 class="text-lg font-bold text-gray-900 leading-tight" title="{{ $mhs->nama }}">
                            {{ $mhs->nama }}
                        </h3>
                        
                        <!-- Info Grid -->
                        <div class="space-y-2">
                            <!-- NPM -->
                            <div class="flex items-start gap-2">
                                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider w-20 flex-shrink-0">NPM</span>
                                <span class="text-sm text-gray-900">{{ $mhs->npm }}</span>
                            </div>

                            <!-- Program Studi -->
                            <div class="flex items-start gap-2">
                                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider w-20 flex-shrink-0">Prodi</span>
                                <span class="text-sm text-gray-700">{{ $mhs->program_studi }}</span>
                            </div>

                            <!-- IPK -->
                            <div class="flex items-start gap-2">
                                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider w-20 flex-shrink-0">IPK</span>
                                <span class="text-base font-bold text-blue-600">{{ number_format($mhs->ipk, 2) }}</span>
                            </div>

                            <!-- Yudisium -->
                            <div class="flex items-start gap-2">
                                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider w-20 flex-shrink-0">Yudisium</span>
                                <span class="text-sm text-gray-700">{{ $mhs->yudisium ?? '-' }}</span>
                            </div>

                            <!-- Judul Skripsi -->
                            <div class="flex items-start gap-2">
                                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider w-20 flex-shrink-0">Skripsi</span>
                                <span class="text-sm text-gray-700 leading-relaxed">{{ $mhs->judul_skripsi ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Info -->
                    <div class="mt-4 pt-3 border-t border-gray-100">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400">
                                Wisudawan
                            </span>
                            <span class="text-xs text-gray-400">
                                {{ $loop->iteration }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
