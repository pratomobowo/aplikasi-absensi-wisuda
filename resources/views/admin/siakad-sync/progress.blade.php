@extends('layouts.admin')

@section('title', 'Progress Sync - SIAKAD')

@section('content')
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="text-center">
            <h1 class="text-2xl font-bold text-gray-900">Sinkronisasi Data</h1>
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
            <div id="stats-container" class="grid grid-cols-2 md:grid-cols-4 gap-4 hidden">
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <p class="text-2xl font-bold text-green-600" id="stat-created">0</p>
                    <p class="text-xs text-gray-600">Baru</p>
                </div>
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <p class="text-2xl font-bold text-blue-600" id="stat-updated">0</p>
                    <p class="text-xs text-gray-600">Diupdate</p>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <p class="text-2xl font-bold text-purple-600" id="stat-photo">0</p>
                    <p class="text-xs text-gray-600">Foto</p>
                </div>
                <div class="text-center p-4 bg-red-50 rounded-lg">
                    <p class="text-2xl font-bold text-red-600" id="stat-failed">0</p>
                    <p class="text-xs text-gray-600">Gagal</p>
                </div>
            </div>

            <!-- Counter -->
            <div class="text-center mt-4">
                <p class="text-sm text-gray-600">
                    <span id="current-count">0</span> / <span id="total-count">0</span> data
                </p>
            </div>

            <!-- Completed Message -->
            <div id="completed-message" class="hidden mt-6 text-center">
                <div class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-lg">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Sync selesai!
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.siakad-sync.index') }}" class="text-primary-600 hover:text-primary-800">
                        Kembali ke halaman sync &rarr;
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

            <!-- Real-time Logs (Terminal Style) -->
            <div class="mt-6">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-semibold text-gray-700">Log Sinkronisasi</h3>
                    <button onclick="document.getElementById('log-container').classList.toggle('max-h-96'); document.getElementById('log-container').classList.toggle('max-h-[600px]');" class="text-xs text-primary-600 hover:text-primary-800">
                        Toggle Height
                    </button>
                </div>
                <div id="log-container" class="bg-gray-900 rounded-lg p-4 font-mono text-xs max-h-96 overflow-y-auto scroll-smooth">
                    <div id="logs-content" class="space-y-0.5">
                        <span class="text-gray-500">[INFO] Menunggu proses dimulai...</span>
                    </div>
                    <div id="log-cursor" class="inline-block w-2 h-4 bg-green-400 animate-pulse ml-1"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const jobId = '{{ $jobId }}';
        let isCompleted = false;

        let lastLogCount = 0;

        function formatLogMessage(message) {
            // Color code based on message type
            if (message.includes('[ERROR]') || message.includes('✗')) {
                return `<span class="text-red-400">${escapeHtml(message)}</span>`;
            } else if (message.includes('[WARN]')) {
                return `<span class="text-yellow-400">${escapeHtml(message)}</span>`;
            } else if (message.includes('[CREATE]') || message.includes('[DONE]') || message.includes('✓')) {
                return `<span class="text-green-400">${escapeHtml(message)}</span>`;
            } else if (message.includes('[PHOTO]')) {
                return `<span class="text-blue-400">${escapeHtml(message)}</span>`;
            } else if (message.includes('[INFO]') || message.includes('[SUMMARY]')) {
                return `<span class="text-cyan-400">${escapeHtml(message)}</span>`;
            } else if (message.includes('[PROCESS]')) {
                return `<span class="text-gray-300">${escapeHtml(message)}</span>`;
            } else if (message.includes('[FATAL]')) {
                return `<span class="text-red-500 font-bold">${escapeHtml(message)}</span>`;
            }
            return `<span class="text-gray-400">${escapeHtml(message)}</span>`;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function updateProgress() {
            if (isCompleted) return;

            fetch(`{{ route('admin.siakad-sync.progress', ':job_id') }}`.replace(':job_id', jobId), {
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
                    document.getElementById('stat-created').textContent = data.stats.created || 0;
                    document.getElementById('stat-updated').textContent = data.stats.updated || 0;
                    document.getElementById('stat-photo').textContent = data.stats.photo_downloaded || 0;
                    document.getElementById('stat-failed').textContent = data.stats.failed || 0;
                }

                // Update logs - only append new ones
                if (data.logs && data.logs.length > lastLogCount) {
                    const logsContainer = document.getElementById('logs-content');
                    const newLogs = data.logs.slice(lastLogCount);
                    
                    newLogs.forEach(log => {
                        const logLine = document.createElement('div');
                        logLine.className = 'whitespace-pre-wrap break-all';
                        logLine.innerHTML = formatLogMessage(log);
                        logsContainer.appendChild(logLine);
                    });
                    
                    lastLogCount = data.logs.length;
                    
                    // Auto-scroll to bottom
                    const logContainer = document.getElementById('log-container');
                    logContainer.scrollTop = logContainer.scrollHeight;
                }

                // Check if completed
                if (data.status === 'Completed') {
                    isCompleted = true;
                    document.getElementById('completed-message').classList.remove('hidden');
                    document.getElementById('log-cursor').classList.add('hidden');
                } else if (data.status === 'Failed') {
                    isCompleted = true;
                    document.getElementById('error-message').classList.remove('hidden');
                    document.getElementById('error-text').textContent = data.error || 'Terjadi kesalahan';
                    document.getElementById('log-cursor').classList.add('hidden');
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