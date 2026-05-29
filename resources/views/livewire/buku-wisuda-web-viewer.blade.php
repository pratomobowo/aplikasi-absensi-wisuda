<div class="min-h-screen bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="flex items-center justify-between px-4 py-3">
            <div class="flex items-center space-x-4">
                <button wire:click="toggleSidebar" class="p-2 hover:bg-gray-100 rounded-lg lg:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <div>
                    <h1 class="text-lg font-bold text-gray-900">Buku Wisuda</h1>
                    <p class="text-sm text-gray-600">{{ $event->name ?? '' }}</p>
                </div>
            </div>
            
            <!-- Search -->
            <div class="flex-1 max-w-md mx-4">
                <div class="relative">
                    <input type="text" 
                           wire:model.live.debounce.300ms="search"
                           placeholder="Cari nama atau NPM..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    @if($search)
                        <button wire:click="clearSearch" class="absolute right-3 top-2.5">
                            <svg class="w-5 h-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    @endif
                </div>
            </div>

            <div class="text-sm text-gray-500 hidden sm:block">
                {{ $totalPages }} halaman
            </div>
        </div>
    </header>

    <div class="flex">
        <!-- Sidebar -->
        <aside class="{{ $sidebarOpen ? 'block' : 'hidden' }} lg:block w-64 bg-white border-r border-gray-200 lg:sticky lg:top-16 lg:h-[calc(100vh-4rem)] lg:overflow-y-auto fixed inset-y-0 left-0 z-40 pt-16 lg:pt-0">
            <div class="p-4">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Daftar Program Studi</h2>
                <nav class="space-y-1">
                    @foreach($prodiList as $prodi)
                        @php
                            $pageNum = $this->getPageNumber($prodi);
                            $studentCount = count($groupedMahasiswas[$prodi] ?? []);
                        @endphp
                        <button 
                            wire:click="scrollToPage({{ $pageNum - 1 }})"
                            class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors group text-gray-700"
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium truncate">{{ $prodi }}</span>
                                <span class="text-xs text-gray-500 group-hover:text-gray-700">{{ $studentCount }}</span>
                            </div>
                            <div class="text-xs text-gray-400 mt-0.5">Hal. {{ $pageNum }}</div>
                        </button>
                    @endforeach
                </nav>
            </div>
        </aside>

        <!-- Mobile overlay -->
        @if($sidebarOpen)
            <div class="fixed inset-0 bg-black/50 z-30 lg:hidden" wire:click="toggleSidebar"></div>
        @endif

        <!-- Main Content -->
        <main class="flex-1 p-4 lg:p-8" id="main-content">
            <div class="max-w-5xl mx-auto space-y-8">
                
                <!-- Initial Pages (Images) -->
                @foreach($initialPages as $index => $page)
                    <div class="page-section bg-white rounded-xl shadow-sm overflow-hidden" id="page-{{ $index }}">
                        <img src="{{ asset('storage/buku-wisuda/' . $page) }}" 
                             alt="Halaman {{ $index + 1 }}"
                             class="w-full h-auto">
                        <div class="p-2 bg-gray-50 text-center text-xs text-gray-500">
                            Halaman {{ $index + 1 }}
                        </div>
                    </div>
                @endforeach

                <!-- Student Pages by Prodi -->
                @foreach($groupedMahasiswas as $prodi => $students)
                    @php
                        $pageIndex = count($initialPages) + array_search($prodi, array_keys($groupedMahasiswas));
                    @endphp
                    <div class="page-section bg-white rounded-xl shadow-sm overflow-hidden" id="page-{{ $pageIndex }}">
                        <!-- Header -->
                        <div class="bg-primary-600 text-white px-6 py-4">
                            <h2 class="text-xl font-bold">{{ $prodi }}</h2>
                            <p class="text-primary-100 text-sm">{{ count($students) }} Wisudawan</p>
                        </div>
                        
                        <!-- Student Grid -->
                        <div class="p-4 lg:p-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($students as $student)
                                    <div class="student-card border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow {{ $highlightedNpm === $student->npm ? 'ring-2 ring-primary-500 bg-primary-50' : '' }}"
                                         data-npm="{{ $student->npm }}"
                                         data-prodi="{{ $prodi }}">
                                        @if($student->foto_wisuda && Storage::disk('public')->exists('graduation-photos/' . $student->foto_wisuda))
                                            <img src="{{ asset('storage/graduation-photos/' . $student->foto_wisuda) }}" 
                                                 alt="{{ $student->nama }}"
                                                 class="w-full aspect-[3/4] object-cover rounded-lg mb-3 bg-gray-100">
                                        @else
                                            <div class="w-full aspect-[3/4] bg-gray-200 rounded-lg mb-3 flex items-center justify-center">
                                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        <h3 class="font-semibold text-gray-900 text-sm">{{ $student->nama }}</h3>
                                        <p class="text-xs text-gray-600 mb-1">NPM: {{ $student->npm }}</p>
                                        @if($student->yudisium)
                                            <p class="text-xs text-primary-600 font-medium mb-1">{{ $student->yudisium }}</p>
                                        @endif
                                        @if($student->judul_skripsi)
                                            <p class="text-xs text-gray-500 mt-2 line-clamp-2">{{ strip_tags($student->judul_skripsi) }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        </main>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('scrollToPage', (event) => {
            const pageIndex = event.pageIndex;
            const element = document.getElementById('page-' + pageIndex);
            if (element) {
                element.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });

        Livewire.on('scrollToStudent', (event) => {
            const npm = event.npm;
            const element = document.querySelector('[data-npm="' + npm + '"]');
            if (element) {
                element.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    });
</script>
@endpush

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
