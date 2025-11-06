<x-layouts.public>
    <x-slot name="title">QnA - {{ config('app.name', 'Sistem Absensi Wisuda') }}</x-slot>
    
    <!-- Main Content -->
    <main class="pt-20">
        <!-- Page Header -->
        <section class="relative bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-700 pt-32 pb-20 overflow-hidden">
            <!-- Decorative elements -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-400 rounded-full mix-blend-multiply filter blur-xl opacity-30"></div>
                <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-indigo-400 rounded-full mix-blend-multiply filter blur-xl opacity-30"></div>
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-purple-400 rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
            </div>
            
            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-white text-sm font-medium mb-6 border border-white/20">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Pertanyaan & Jawaban
                </div>
                <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold text-white mb-6 leading-tight">
                    QnA Wisuda
                </h1>
                <p class="text-lg sm:text-xl text-blue-50 max-w-3xl mx-auto leading-relaxed">
                    Temukan jawaban atas pertanyaan yang sering diajukan seputar wisuda
                </p>
            </div>
        </section>
        
        <!-- Content Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 gap-8">
                <!-- FAQ Section -->
                <div>
                    <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Pertanyaan yang Sering Diajukan (FAQ)</h2>
                        
                        <div class="space-y-4" x-data="{ openFaq: null }">
                            <!-- FAQ 1 -->
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <button 
                                    @click="openFaq = openFaq === 1 ? null : 1"
                                    class="w-full px-6 py-4 text-left bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center"
                                >
                                    <span class="font-semibold text-gray-900">Bagaimana cara mendaftar wisuda?</span>
                                    <svg 
                                        class="w-5 h-5 text-gray-500 transform transition-transform duration-200"
                                        :class="{ 'rotate-180': openFaq === 1 }"
                                        fill="none" 
                                        stroke="currentColor" 
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div 
                                    x-show="openFaq === 1"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                                    x-transition:enter-end="opacity-100 transform translate-y-0"
                                    class="px-6 py-4 bg-white"
                                    style="display: none;"
                                >
                                    <p class="text-gray-600 leading-relaxed">
                                        Pendaftaran wisuda dilakukan melalui sistem akademik universitas. Pastikan Anda telah memenuhi semua persyaratan akademik dan administrasi, kemudian login ke portal akademik dan ikuti petunjuk pendaftaran wisuda.
                                    </p>
                                </div>
                            </div>
                            
                            <!-- FAQ 2 -->
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <button 
                                    @click="openFaq = openFaq === 2 ? null : 2"
                                    class="w-full px-6 py-4 text-left bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center"
                                >
                                    <span class="font-semibold text-gray-900">Apa saja persyaratan untuk mengikuti wisuda?</span>
                                    <svg 
                                        class="w-5 h-5 text-gray-500 transform transition-transform duration-200"
                                        :class="{ 'rotate-180': openFaq === 2 }"
                                        fill="none" 
                                        stroke="currentColor" 
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div 
                                    x-show="openFaq === 2"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                                    x-transition:enter-end="opacity-100 transform translate-y-0"
                                    class="px-6 py-4 bg-white"
                                    style="display: none;"
                                >
                                    <p class="text-gray-600 leading-relaxed">
                                        Persyaratan wisuda meliputi: telah menyelesaikan seluruh mata kuliah, IPK memenuhi standar kelulusan, tidak memiliki tunggakan administrasi, telah menyelesaikan tugas akhir/skripsi, dan melengkapi berkas-berkas administrasi yang diperlukan.
                                    </p>
                                </div>
                            </div>
                            
                            <!-- FAQ 3 -->
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <button 
                                    @click="openFaq = openFaq === 3 ? null : 3"
                                    class="w-full px-6 py-4 text-left bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center"
                                >
                                    <span class="font-semibold text-gray-900">Bagaimana cara mendapatkan undangan wisuda digital?</span>
                                    <svg 
                                        class="w-5 h-5 text-gray-500 transform transition-transform duration-200"
                                        :class="{ 'rotate-180': openFaq === 3 }"
                                        fill="none" 
                                        stroke="currentColor" 
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div 
                                    x-show="openFaq === 3"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                                    x-transition:enter-end="opacity-100 transform translate-y-0"
                                    class="px-6 py-4 bg-white"
                                    style="display: none;"
                                >
                                    <p class="text-gray-600 leading-relaxed">
                                        Undangan wisuda digital akan dikirimkan melalui email setelah Anda menyelesaikan proses pendaftaran dan pembayaran. Undangan berisi QR Code yang dapat dibagikan kepada keluarga dan tamu undangan Anda.
                                    </p>
                                </div>
                            </div>
                            
                            <!-- FAQ 4 -->
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <button 
                                    @click="openFaq = openFaq === 4 ? null : 4"
                                    class="w-full px-6 py-4 text-left bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center"
                                >
                                    <span class="font-semibold text-gray-900">Berapa jumlah tamu yang bisa saya undang?</span>
                                    <svg 
                                        class="w-5 h-5 text-gray-500 transform transition-transform duration-200"
                                        :class="{ 'rotate-180': openFaq === 4 }"
                                        fill="none" 
                                        stroke="currentColor" 
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div 
                                    x-show="openFaq === 4"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                                    x-transition:enter-end="opacity-100 transform translate-y-0"
                                    class="px-6 py-4 bg-white"
                                    style="display: none;"
                                >
                                    <p class="text-gray-600 leading-relaxed">
                                        Setiap wisudawan dapat mengundang maksimal 4 tamu. Pastikan tamu Anda membawa undangan digital dengan QR Code untuk proses check-in di hari pelaksanaan wisuda.
                                    </p>
                                </div>
                            </div>
                            
                            <!-- FAQ 5 -->
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <button 
                                    @click="openFaq = openFaq === 5 ? null : 5"
                                    class="w-full px-6 py-4 text-left bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center"
                                >
                                    <span class="font-semibold text-gray-900">Kapan jadwal gladi bersih wisuda?</span>
                                    <svg 
                                        class="w-5 h-5 text-gray-500 transform transition-transform duration-200"
                                        :class="{ 'rotate-180': openFaq === 5 }"
                                        fill="none" 
                                        stroke="currentColor" 
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div 
                                    x-show="openFaq === 5"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                                    x-transition:enter-end="opacity-100 transform translate-y-0"
                                    class="px-6 py-4 bg-white"
                                    style="display: none;"
                                >
                                    <p class="text-gray-600 leading-relaxed">
                                        Jadwal gladi bersih akan diinformasikan melalui email dan portal akademik. Biasanya dilaksanakan 1-2 hari sebelum hari pelaksanaan wisuda. Kehadiran pada gladi bersih bersifat wajib.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</x-layouts.public>
