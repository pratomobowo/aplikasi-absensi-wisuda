<div class="flex items-stretch bg-white rounded-xl border {{ $highlight ?? false ? 'border-amber-300 shadow-amber-100 shadow-lg ring-2 ring-amber-100' : 'border-gray-100 shadow-gray-100 shadow-sm' }} overflow-hidden hover:shadow-md transition-all duration-200 group">
    <div class="w-14 flex-shrink-0 bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center text-white font-bold text-lg relative">
        {{ $num }}
        @if($star ?? false)
            <span class="absolute bottom-1.5 text-amber-300 text-xs">★</span>
        @endif
    </div>
    <div class="flex-1 px-4 py-3 flex flex-col justify-center border-l border-gray-100">
        <h3 class="font-semibold text-gray-900 text-sm leading-snug">{{ $title }}</h3>
        <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ $sub }}</p>
    </div>
</div>
