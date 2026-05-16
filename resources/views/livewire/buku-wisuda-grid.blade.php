<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @foreach($mahasiswas as $mhs)
        <div class="group bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-blue-200">
            <!-- Photo Container -->
            <div class="relative aspect-[3/4] overflow-hidden bg-gradient-to-br from-blue-50 to-indigo-50">
                @if($mhs->foto_wisuda && file_exists(public_path('storage/graduation-photos/' . $mhs->foto_wisuda)))
                    <img
                        src="{{ asset('storage/graduation-photos/' . $mhs->foto_wisuda) }}"
                        alt="{{ $mhs->nama }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        loading="lazy"
                    >
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                @endif

                <!-- Yudisium Badge -->
                @if($mhs->yudisium)
                    <div class="absolute top-3 right-3">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $this->getYudisiumColor($mhs->yudisium) }} shadow-sm backdrop-blur-sm">
                            <span class="mr-1">{{ $this->getYudisiumIcon($mhs->yudisium) }}</span>
                            {{ $mhs->yudisium }}
                        </span>
                    </div>
                @endif

                <!-- Hover Overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            </div>

            <!-- Info -->
            <div class="p-4">
                <h3 class="font-bold text-gray-900 mb-1 truncate" title="{{ $mhs->nama }}">
                    {{ $mhs->nama }}
                </h3>
                
                <p class="text-sm text-gray-500 mb-2">{{ $mhs->npm }}</p>
                
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 truncate flex-1 mr-2" title="{{ $mhs->program_studi }}">
                        {{ $mhs->program_studi }}
                    </span>
                    <span class="font-bold text-blue-600">{{ number_format($mhs->ipk, 2) }}</span>
                </div>
            </div>
        </div>
    @endforeach
</div>
