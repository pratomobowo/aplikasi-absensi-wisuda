@php
    $itemsPerPage = 2;
    $totalItems = $mahasiswas->count();
    $totalPages = ceil($totalItems / $itemsPerPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    
    $startIndex = ($currentPage - 1) * $itemsPerPage;
    $currentItems = $mahasiswas->slice($startIndex, $itemsPerPage);
@endphp

<div class="flipbook-container">
    <!-- Navigation Top -->
    <div class="flex items-center justify-between mb-6">
        <button
            wire:click="previousPage"
            @disabled($currentPage <= 1)
            class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Sebelumnya
        </button>

        <div class="flex items-center gap-2">
            <span class="text-sm text-gray-600">Halaman</span>
            <input
                type="number"
                wire:model.live="currentPage"
                min="1"
                max="{{ $totalPages }}"
                class="w-16 px-2 py-1 border border-gray-300 rounded text-center text-sm"
            >
            <span class="text-sm text-gray-600">dari {{ $totalPages }}</span>
        </div>

        <button
            wire:click="nextPage"
            @disabled($currentPage >= $totalPages)
            class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center"
        >
            Selanjutnya
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>
    </div>

    <!-- Flipbook -->
    <div class="flipbook relative bg-gray-100 rounded-xl shadow-2xl overflow-hidden" style="min-height: 600px;">
        <!-- Book Shadow Effect -->
        <div class="absolute inset-0 bg-gradient-to-r from-gray-300 via-transparent to-gray-300 opacity-30 pointer-events-none"></div>
        
        <div class="flex flex-col md:flex-row h-full">
            @foreach($currentItems as $index => $mhs)
                <div class="flex-1 p-6 md:p-8 {{ $index === 0 ? 'border-b md:border-b-0 md:border-r border-gray-300' : '' }}">
                    <!-- Page Number -->
                    <div class="text-center text-xs text-gray-400 mb-4">
                        {{ ($currentPage - 1) * $itemsPerPage + $index + 1 }}
                    </div>

                    <div class="max-w-sm mx-auto">
                        <!-- Photo -->
                        <div class="relative mb-6">
                            <div class="aspect-[3/4] bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl overflow-hidden shadow-lg">
                                @if($mhs->foto_wisuda && file_exists(public_path('storage/graduation-photos/' . $mhs->foto_wisuda)))
                                    <img
                                        src="{{ asset('storage/graduation-photos/' . $mhs->foto_wisuda) }}"
                                        alt="{{ $mhs->nama }}"
                                        class="w-full h-full object-cover"
                                        loading="lazy"
                                    >
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-20 h-20 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Yudisium Badge -->
                            @if($mhs->yudisium)
                                <div class="absolute -bottom-3 left-1/2 transform -translate-x-1/2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border {{ $this->getYudisiumColor($mhs->yudisium) }} shadow-sm">
                                        <span class="mr-1">{{ $this->getYudisiumIcon($mhs->yudisium) }}</span>
                                        {{ $mhs->yudisium }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Info -->
                        <div class="text-center space-y-3">
                            <h3 class="text-xl font-bold text-gray-900">{{ $mhs->nama }}</h3>
                            
                            <div class="space-y-1 text-sm">
                                <p class="text-gray-600">
                                    <span class="font-medium">NPM:</span> {{ $mhs->npm }}
                                </p>
                                <p class="text-gray-600">
                                    <span class="font-medium">Prodi:</span> {{ $mhs->program_studi }}
                                </p>
                                <p class="text-gray-600">
                                    <span class="font-medium">IPK:</span> <span class="font-bold text-blue-600">{{ number_format($mhs->ipk, 2) }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Fill empty slot if odd number -->
            @if($currentItems->count() < $itemsPerPage && $currentPage === $totalPages)
                <div class="flex-1 p-6 md:p-8 hidden md:flex items-center justify-center">
                    <div class="text-center text-gray-400">
                        <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <p class="text-sm">End of Book</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Navigation Bottom (Mobile) -->
    <div class="flex md:hidden items-center justify-between mt-6">
        <button
            wire:click="previousPage"
            @disabled($currentPage <= 1)
            class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
            ← Sebelumnya
        </button>

        <span class="text-sm text-gray-600">{{ $currentPage }} / {{ $totalPages }}</span>

        <button
            wire:click="nextPage"
            @disabled($currentPage >= $totalPages)
            class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
            Selanjutnya →
        </button>
    </div>
</div>
