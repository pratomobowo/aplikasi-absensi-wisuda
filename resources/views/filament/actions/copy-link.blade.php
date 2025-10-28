<div class="space-y-4">
    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Magic Link:</p>
        <div class="flex items-center gap-2">
            <input 
                type="text" 
                value="{{ $url }}" 
                id="magic-link-input"
                readonly
                class="flex-1 text-sm bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded px-3 py-2"
            />
            <button 
                type="button"
                onclick="copyToClipboard()"
                class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded text-sm font-medium"
            >
                Copy
            </button>
        </div>
    </div>
    
    <script>
        function copyToClipboard() {
            const input = document.getElementById('magic-link-input');
            input.select();
            input.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(input.value);
            
            // Show success notification
            window.dispatchEvent(new CustomEvent('notify', {
                detail: {
                    message: 'Link berhasil disalin ke clipboard!',
                    type: 'success'
                }
            }));
        }
    </script>
</div>
