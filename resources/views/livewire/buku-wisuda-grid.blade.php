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
                                <span class="mr-1">{{ $this->getYudisiumIcon($mhs->yudisium) }}</span>
                                {{ $mhs->yudisium }}
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Info Section - Right Side -->
                <div class="flex-1 p-5 flex flex-col justify-between">
                    <div>
                        <!-- Name -->
                        <h3 class="text-xl font-bold text-gray-900 mb-3 break-words" title="{{ $mhs->nama }}">
                            {{ $mhs->nama }}
                        </h3>
                        
                        <!-- Info Grid -->
                        <div class="space-y-2.5">
                            <!-- NPM -->
                            <div class="flex items-start">
                                <div class="w-24 flex-shrink-0">
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">NPM</span>
                                </div>
                                <div class="flex-1">
                                    <span class="text-sm font-medium text-gray-900">{{ $mhs->npm }}</span>
                                </div>
                            </div>

                            <!-- Program Studi -->
                            <div class="flex items-start">
                                <div class="w-24 flex-shrink-0">
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Prodi</span>
                                </div>
                                <div class="flex-1">
                                    <span class="text-sm text-gray-700">{{ $mhs->program_studi }}</span>
                                </div>
                            </div>

                            <!-- IPK -->
                            <div class="flex items-start">
                                <div class="w-24 flex-shrink-0">
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">IPK</span>
                                </div>
                                <div class="flex-1">
                                    <span class="text-lg font-bold text-blue-600">{{ number_format($mhs->ipk, 2) }}</span>
                                </div>
                            </div>

                            <!-- Yudisium (if no badge on photo) -->
                            @if(!$mhs->yudisium)
                                <div class="flex items-start">
                                    <div class="w-24 flex-shrink-0">
                                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Yudisium</span>
                                    </div>
                                    <div class="flex-1">
                                        <span class="text-sm text-gray-500">-</span>
                                    </div>
                                </div>
                            @endif
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
