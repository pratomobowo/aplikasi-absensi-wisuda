@extends('layouts.admin')

@section('title', 'Progress Download Foto - SIAKAD')

@section('content')
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="text-center">
            <h1 class="text-2xl font-bold text-gray-900">Download Foto Wisudawan</h1>
            <p class="text-gray-600 mt-2">Proses berjalan di background. Anda dapat menutup halaman ini.</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            <!-- Progress Bar -->
            <div class="mb-6">
                <div class="flex justify-between mb-2">
                    <span id="status-text" class="text-sm font-medium text-gray-700">Memulai...</span>
                    <span id="percentage-text" class="text-sm font-medium text-gray-700">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div id="progress-bar" class="bg-primary-600 h-4 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
            </div>

            <!-- Stats -->
            <div id="stats-container" class="grid grid-cols-2 md:grid-cols-3 gap-4 hidden">
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <p class="text-2xl font-bold text-green-600" id="stat-downloaded">0</p>
                    <p class="text-xs text-gray-600">Berhasil</p>
                </div>
                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <p class="text-2xl font-bold text-yellow-600" id="stat-skipped">0</p>
                    <p class="text-xs text-gray-600">Dilewati</p>
                </div>
                <div class="text-center p-4 bg-red-50 rounded-lg">
                    <p class="text-2xl font-bold text-red-600" id="stat-failed">0</p>
                    <p class="text-xs text-gray-600">Gagal</p>
                </div>
            </div>

            <!-- Counter -->
            <div class="text-center mt-4">
                <p class="text-sm text-gray-600">
                    <span id="current-count">0</span> / <span id="total-count">0</span> mahasiswa
                </p>
            </div>

            <!-- Failed List -->
            <div id="failed-list-container" class="hidden mt-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Daftar Gagal:</h3>
                <div id="failed-list" class="space-y-2 max-h-60 overflow-y-auto">
                    <!-- Failed items will be inserted here -->
                </div>
            </div>

            <!-- Completed Message -->
            <div id="completed-message" class="hidden mt-6 text-center">
                <div class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-lg">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Download selesai!
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.siakad-sync.photo') }}" class="text-primary-600 hover:text-primary-800">
                        Kembali ke halaman download foto →
                    </a>
                </div>
            </div>

            <!-- Error Message -->
            <div id="error-message" class="hidden mt-6 text-center">
                <div class="inline-flex items-center px-4 py-2 bg-red-100 text-red-800 rounded-lg">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span id="error-text">Terjadi kesalahan</span>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const jobId = '{{ $jobId }}';
        let isCompleted = false;

        function updateProgress() {
            if (isCompleted) return;

            fetch(`{{ route('admin.siakad-sync.photo-progress', ':job_id') }}`.replace(':job_id', jobId), {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Update progress bar
                document.getElementById('progress-bar').style.width = data.percentage + '%';
                document.getElementById('percentage-text').textContent = data.percentage + '%';
                document.getElementById('status-text').textContent = data.status;
                document.getElementById('current-count').textContent = data.current;
                document.getElementById('total-count').textContent = data.total;

                // Update stats
                if (data.stats) {
                    document.getElementById('stats-container').classList.remove('hidden');
                    document.getElementById('stat-downloaded').textContent = data.stats.downloaded || 0;
                    document.getElementById('stat-skipped').textContent = data.stats.skipped || 0;
                    document.getElementById('stat-failed').textContent = data.stats.failed || 0;

                    // Show failed list
                    if (data.stats.failedList && data.stats.failedList.length > 0) {
                        const failedListContainer = document.getElementById('failed-list-container');
                        const failedList = document.getElementById('failed-list');
                        failedListContainer.classList.remove('hidden');
                        
                        failedList.innerHTML = data.stats.failedList.map(item => `
                            <div class="flex items-center justify-between p-2 bg-red-50 rounded text-sm">
                                <span class="font-medium text-red-800">${item.npm}</span>
                                <span class="text-red-600">${item.reason}</span>
                            </div>
                        `).join('');
                    }
                }

                // Check if completed
                if (data.status === 'Completed') {
                    isCompleted = true;
                    document.getElementById('completed-message').classList.remove('hidden');
                } else if (data.status === 'Failed') {
                    isCompleted = true;
                    document.getElementById('error-message').classList.remove('hidden');
                    document.getElementById('error-text').textContent = data.error || 'Terjadi kesalahan';
                }

                // Continue polling
                if (!isCompleted) {
                    setTimeout(updateProgress, 1000);
                }
            })
            .catch(error => {
                console.error('Error fetching progress:', error);
                setTimeout(updateProgress, 2000);
            });
        }

        // Start polling
        updateProgress();
    </script>
    @endpush
@endsection
