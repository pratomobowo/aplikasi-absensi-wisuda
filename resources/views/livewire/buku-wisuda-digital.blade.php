<div>
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 pt-32 pb-12 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-400 rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-indigo-400 rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        </div>
        
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-white text-sm font-medium mb-4 border border-white/20">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    Buku Wisuda Digital
                </div>
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-white mb-3 leading-tight">
                    {{ $event->name ?? 'Buku Wisuda' }}
                </h1>
                @if($event)
                    <p class="text-lg text-blue-100">
                        {{ $event->date->format('d F Y') }}
                    </p>
                @endif
            </div>
        </div>
    </section>

    <!-- Controls Bar -->
    <div class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col lg:flex-row gap-4 items-center justify-between">
                <!-- Search -->
                <div class="relative w-full lg:w-96">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari nama, NPM, atau prodi..."
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                    >
                    <svg class="absolute left-3 top-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                <!-- Filters -->
                <div class="flex flex-wrap gap-3 items-center w-full lg:w-auto">
                    <!-- Program Studi Filter -->
                    <select
                        wire:model.live="selectedProdi"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm bg-white"
                    >
                        <option value="">Semua Prodi</option>
                        @foreach($prodiList as $prodi)
                            <option value="{{ $prodi }}">{{ $prodi }}</option>
                        @endforeach
                    </select>

                    <!-- Download PDF Button -->
                    @if($bukuWisuda)
                        <a
                            href="{{ route('buku-wisuda.download', $bukuWisuda->slug) }}"
                            class="px-4 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors inline-flex items-center"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Download PDF
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Content Area -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($mahasiswas->count() > 0)
            <!-- Results Count -->
            <div class="mb-6 text-sm text-gray-600">
                Menampilkan <span class="font-semibold">{{ $mahasiswas->count() }}</span> wisudawan
                @if($search)
                    untuk pencarian "<span class="font-semibold">{{ $search }}</span>"
                @endif
                @if($selectedProdi)
                    dari prodi <span class="font-semibold">{{ $selectedProdi }}</span>
                @endif
            </div>

            @include('livewire.buku-wisuda-grid')
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak ada data</h3>
                <p class="text-gray-600 max-w-md mx-auto">
                    @if($search || $selectedProdi)
                        Tidak ditemukan wisudawan dengan filter yang dipilih. Coba ubah filter atau kata kunci.
                    @else
                        Belum ada data wisudawan untuk periode ini.
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>
