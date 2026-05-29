<div class="min-h-screen bg-gray-100 flex flex-col">
    <!-- Header - sticky at top, above everything -->
    <header class="bg-white shadow-sm sticky top-0 z-50 flex-shrink-0">
        <div class="flex items-center justify-between px-4 py-2">
            <div class="flex items-center space-x-3">
                <button wire:click="toggleSidebar" class="p-2 hover:bg-gray-100 rounded-lg lg:hidden">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <h1 class="text-base font-bold text-gray-900">Buku Wisuda</h1>
            </div>
            
            <!-- Search - simplified on mobile -->
            <div class="flex-1 max-w-xs mx-2">
                <input type="text" 
                       wire:model.live.debounce.300ms="search"
                       placeholder="Cari nama/NPM..."
                       class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>

            <div class="text-xs text-gray-500">
                {{ $totalPages }} hal
            </div>
        </div>
    </header>

    <!-- Body - sidebar + content side by side -->
    <div class="flex flex-1 overflow-hidden">
        <!-- Sidebar - below header on mobile, full height on desktop -->
        <aside class="{{ $sidebarOpen ? 'block' : 'hidden' }} lg:block w-64 bg-white border-r border-gray-200 overflow-y-auto flex-shrink-0 fixed lg:static inset-0 z-40 lg:z-auto pt-16 lg:pt-0"
               style="top: 4rem; height: calc(100vh - 4rem);">
            <div class="p-4">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Daftar Program Studi</h2>
                <nav class="space-y-1">
                    @foreach($prodiList as $prodi)
                        @php
                            $pageNum = $this->getPageNumber($prodi);
                            $studentCount = count($groupedMahasiswas[$prodi] ?? []);
                        @endphp
                        <button 
                            wire:click="scrollToPage({{ $pageNum }})"
                            class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors group text-gray-700"
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium truncate">{{ $prodi }}</span>
                                <span class="text-xs text-gray-500 group-hover:text-gray-700">{{ $studentCount }}</span>
                            </div>
                            <div class="text-xs text-gray-400 mt-0.5">Hal. {{ $pageNum + 1 }}</div>
                        </button>
                    @endforeach
                </nav>
            </div>
        </aside>

        <!-- Mobile overlay - behind sidebar, above content -->
        @if($sidebarOpen)
            <div class="fixed inset-0 bg-black/50 z-30 lg:hidden" style="top: 4rem;" wire:click="toggleSidebar"></div>
        @endif

        <!-- Main Content - horizontal scroll like flipbook -->
        <main class="flex-1 overflow-x-auto overflow-y-auto p-4 lg:p-6" id="main-content">
            <div class="flex h-full gap-4" style="width: max-content;">
                
                <!-- Initial Pages (Images) - same height for all -->
                @foreach($initialPages as $index => $page)
                    <div class="page-section flex-shrink-0 bg-white rounded-lg shadow-sm p-3"
                         id="page-{{ $index }}"
                         style="height: calc(100vh - 8rem); width: auto;">
                        <img src="{{ asset('storage/buku-wisuda/' . $page) }}" 
                             alt="Halaman {{ $index + 1 }}"
                             class="h-full w-auto">
                    </div>
                @endforeach

                <!-- Student Pages by Prodi -->
                @foreach($groupedMahasiswas as $prodi => $students)
                    @php
                        $pageIndex = count($initialPages) + array_search($prodi, array_keys($groupedMahasiswas));
                    @endphp
                    <div class="page-section flex-shrink-0 h-full flex flex-col bg-white rounded-lg shadow-sm overflow-hidden"
                         id="page-{{ $pageIndex }}"
                         style="height: calc(100vh - 8rem); width: auto; max-width: calc((100vh - 8rem) * 0.75);">
                        <!-- Header -->
                        <div class="bg-primary-600 text-white px-4 py-3 flex-shrink-0">
                            <h2 class="text-lg font-bold">{{ $prodi }}</h2>
                            <p class="text-primary-100 text-xs">{{ count($students) }} Wisudawan</p>
                        </div>
                        
                        <!-- Student Grid - scrollable -->
                        <div class="p-3 overflow-y-auto flex-1">
                            <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($students as $student)
                                    <div class="student-card border border-gray-200 rounded-lg p-3 hover:shadow-md transition-shadow {{ $highlightedNpm === $student->npm ? 'ring-2 ring-primary-500 bg-primary-50' : '' }}"
                                         data-npm="{{ $student->npm }}"
                                         data-prodi="{{ $prodi }}">
                                        @if($student->foto_wisuda && Storage::disk('public')->exists('graduation-photos/' . $student->foto_wisuda))
                                            <img src="{{ asset('storage/graduation-photos/' . $student->foto_wisuda) }}" 
                                                 alt="{{ $student->nama }}"
                                                 class="w-full aspect-[3/4] object-cover rounded-lg mb-2 bg-gray-100">
                                        @else
                                            <div class="w-full aspect-[3/4] bg-gray-200 rounded-lg mb-2 flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        <h3 class="font-semibold text-gray-900 text-xs">{{ $student->nama }}</h3>
                                        <p class="text-xs text-gray-600 mb-1">NPM: {{ $student->npm }}</p>
                                        @if($student->yudisium)
                                            <p class="text-xs text-primary-600 font-medium">{{ $student->yudisium }}</p>
                                        @endif
                                        @if($student->judul_skripsi)
                                            <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ strip_tags($student->judul_skripsi) }}</p>
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
    function scrollToPage(pageIndex) {
        const element = document.getElementById('page-' + pageIndex);
        if (element) {
            element.scrollIntoView({ behavior: 'smooth', inline: 'start', block: 'nearest' });
        }
    }

    function scrollToStudent(npm) {
        const element = document.querySelector('[data-npm="' + npm + '"]');
        if (element) {
            element.scrollIntoView({ behavior: 'smooth', block: 'center' });
            element.classList.add('ring-4', 'ring-yellow-400');
            setTimeout(() => {
                element.classList.remove('ring-4', 'ring-yellow-400');
            }, 2000);
        }
    }

    document.addEventListener('livewire:init', () => {
        Livewire.on('scrollToPage', (eventData) => {
            const data = Array.isArray(eventData) ? eventData[0] : eventData;
            scrollToPage(data?.pageIndex ?? data);
        });

        Livewire.on('scrollToStudent', (eventData) => {
            const data = Array.isArray(eventData) ? eventData[0] : eventData;
            scrollToStudent(data?.npm ?? data);
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
