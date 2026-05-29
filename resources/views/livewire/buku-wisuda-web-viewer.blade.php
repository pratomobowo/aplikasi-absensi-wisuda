<div class="min-h-screen bg-gray-100 flex flex-col" x-data="{ sidebarOpen: false }">
    <!-- Header - sticky at top, above everything -->
    <header class="bg-white shadow-sm sticky top-0 z-50 flex-shrink-0">
        <div class="flex items-center px-4 py-2">
            <button @click="sidebarOpen = !sidebarOpen" class="p-2 hover:bg-gray-100 rounded-lg lg:hidden mr-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            
            <!-- Search - full width on mobile -->
            <div class="flex-1">
                <input type="text" 
                       wire:model.live.debounce.300ms="search"
                       placeholder="Cari nama/NPM..."
                       class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>

            <div class="hidden lg:flex text-xs text-gray-500 ml-4">
                {{ $totalPages }} hal
            </div>
        </div>
        <!-- Event name below header on desktop -->
        <div class="hidden lg:block px-4 pb-2 -mt-1">
            <p class="text-xs text-gray-500">{{ $event->name ?? '' }}</p>
        </div>
    </header>

    <!-- Body - sidebar + content side by side -->
    <div class="flex flex-1 overflow-hidden"></parameter>

        <!-- Sidebar Desktop - always visible -->
        <aside class="hidden lg:block w-64 bg-white border-r border-gray-200 overflow-y-auto flex-shrink-0"
               style="height: calc(100vh - 4rem);">
            <div class="p-4">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Daftar Program Studi</h2>
                <nav class="space-y-1">
                    @foreach($prodiList as $prodi)
                        @php
                            $pageNum = $this->getPageNumber($prodi);
                            $studentCount = count($groupedMahasiswas[$prodi] ?? []);
                        @endphp
                        <button 
                            onclick="window.scrollToPage({{ $pageNum }})"
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

        <!-- Sidebar Mobile - slide in/out -->
        <aside x-show="sidebarOpen"
               x-transition:enter="transition transform ease-out duration-300"
               x-transition:enter-start="-translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition transform ease-in duration-200"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="-translate-x-full"
               class="lg:hidden w-64 bg-white border-r border-gray-200 overflow-y-auto fixed inset-y-0 z-40"
               style="top: 4rem; height: calc(100vh - 4rem);">
            <div class="p-4">
                <div class="mb-4 pb-3 border-b border-gray-200">
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Buku Wisuda</p>
                    <p class="text-sm font-semibold text-gray-900 leading-tight">{{ $event->name ?? '' }}</p>
                </div>
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Daftar Program Studi</h2>
                <nav class="space-y-1">
                    @foreach($prodiList as $prodi)
                        @php
                            $pageNum = $this->getPageNumber($prodi);
                            $studentCount = count($groupedMahasiswas[$prodi] ?? []);
                        @endphp
                        <button 
                            @click="sidebarOpen = false; window.scrollToPage({{ $pageNum }})"
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

        <!-- Mobile overlay -->
        <div x-show="sidebarOpen"
             x-transition:enter="transition opacity ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition opacity ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="lg:hidden fixed inset-0 bg-black/50 z-30"
             style="top: 4rem;"
             @click="sidebarOpen = false"></div>

        <!-- Main Content - horizontal scroll like flipbook -->
        <main class="flex-1 overflow-x-auto overflow-y-auto p-1 lg:p-6" id="main-content">
            <div class="flex h-full gap-2 lg:gap-4" style="width: max-content;">
                
                <!-- Initial Pages (Images) - full screen on mobile, proper size on desktop -->
                @foreach($initialPages as $index => $page)
                    <div class="page-section flex-shrink-0 bg-white rounded-lg shadow-sm flex items-center justify-center p-2"
                         id="page-{{ $index }}"
                         style="height: calc(100vh - 4rem); width: calc(100vw - 1rem);">
                        <img src="{{ asset('storage/buku-wisuda/' . $page) }}" 
                             alt="Halaman {{ $index + 1 }}"
                             class="max-h-full max-w-full object-contain">
                    </div>
                @endforeach

                <!-- Student Pages by Prodi - full screen on mobile -->
                @foreach($groupedMahasiswas as $prodi => $students)
                    @php
                        $pageIndex = count($initialPages) + array_search($prodi, array_keys($groupedMahasiswas));
                    @endphp
                    <div class="page-section flex-shrink-0 flex flex-col bg-white rounded-lg shadow-sm overflow-hidden p-2"
                         id="page-{{ $pageIndex }}"
                         style="height: calc(100vh - 4rem); width: calc(100vw - 1rem);">
                        <!-- Header -->
                        <div class="bg-primary-600 text-white px-4 py-2 flex-shrink-0">
                            <h2 class="text-lg font-bold">{{ $prodi }}</h2>
                            <p class="text-primary-100 text-xs">{{ count($students) }} Wisudawan</p>
                        </div>
                        
                        <!-- Student Grid - scrollable -->
                        <div class="p-2 overflow-y-auto flex-1">
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
                                @foreach($students as $student)
                                    <div class="student-card border border-gray-200 rounded-lg p-2 hover:shadow-md transition-shadow {{ $highlightedNpm === $student->npm ? 'ring-2 ring-primary-500 bg-primary-50' : '' }}"
                                         data-npm="{{ $student->npm }}"
                                         data-prodi="{{ $prodi }}">
                                        @if($student->foto_wisuda && Storage::disk('public')->exists('graduation-photos/' . $student->foto_wisuda))
                                            <img src="{{ asset('storage/graduation-photos/' . $student->foto_wisuda) }}" 
                                                 alt="{{ $student->nama }}"
                                                 class="w-full aspect-[3/4] object-cover rounded-lg mb-1 bg-gray-100">
                                        @else
                                            <div class="w-full aspect-[3/4] bg-gray-200 rounded-lg mb-1 flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        <h3 class="font-semibold text-gray-900 text-xs truncate">{{ $student->nama }}</h3>
                                        <p class="text-xs text-gray-500 truncate">{{ $student->npm }}</p>
                                        @if($student->ipk)
                                            <p class="text-xs text-gray-400">IPK: {{ number_format($student->ipk, 2) }}</p>
                                        @endif
                                        @if($student->yudisium)
                                            <p class="text-xs font-medium text-primary-600">{{ $student->yudisium }}</p>
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
    window.scrollToPage = function(pageIndex) {
        const element = document.getElementById('page-' + pageIndex);
        if (element) {
            element.scrollIntoView({ behavior: 'smooth', inline: 'start', block: 'nearest' });
        }
    };

    window.scrollToStudent = function(npm) {
        const element = document.querySelector('[data-npm="' + npm + '"]');
        if (element) {
            element.scrollIntoView({ behavior: 'smooth', block: 'center' });
            element.classList.add('ring-4', 'ring-yellow-400');
            setTimeout(() => {
                element.classList.remove('ring-4', 'ring-yellow-400');
            }, 2000);
        }
    };

    document.addEventListener('livewire:init', () => {
        Livewire.on('scrollToPage', (eventData) => {
            const data = Array.isArray(eventData) ? eventData[0] : eventData;
            window.scrollToPage(data?.pageIndex ?? data);
        });

        Livewire.on('scrollToStudent', (eventData) => {
            const data = Array.isArray(eventData) ? eventData[0] : eventData;
            window.scrollToStudent(data?.npm ?? data);
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

/* Desktop: larger page sizes */
@media (min-width: 1024px) {
    .page-section {
        height: calc(100vh - 8rem) !important;
        width: auto !important;
        max-width: calc((100vh - 8rem) * 0.75) !important;
    }
}
</style>
