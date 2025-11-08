<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center px-4 py-12">
    <!-- Modal Container -->
    <div class="w-full max-w-md">
        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-8 py-8">
                <div class="flex items-center justify-center mb-4">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white text-center mb-2">Ubah Password</h1>
                <p class="text-blue-100 text-center text-sm">Login pertama Anda. Silakan ubah password untuk keamanan</p>
            </div>

            <!-- Form Content -->
            <div class="px-8 py-8">
                <!-- Info Alert -->
                <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 rounded">
                    <p class="text-sm text-blue-800">
                        <strong>Penting:</strong> Gunakan password yang kuat dan mudah Anda ingat. Password harus minimal 8 karakter.
                    </p>
                </div>

                <form wire:submit.prevent="changePassword" class="space-y-5">
                    <!-- Current Password -->
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password Saat Ini
                        </label>
                        <input
                            type="password"
                            id="current_password"
                            wire:model="current_password"
                            placeholder="Masukkan password Anda saat ini"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('current_password') border-red-500 @enderror"
                        />
                        @error('current_password')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password Baru
                        </label>
                        <input
                            type="password"
                            id="new_password"
                            wire:model="new_password"
                            placeholder="Masukkan password baru (minimal 8 karakter)"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('new_password') border-red-500 @enderror"
                        />
                        @error('new_password')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                            Konfirmasi Password
                        </label>
                        <input
                            type="password"
                            id="confirm_password"
                            wire:model="confirm_password"
                            placeholder="Konfirmasi password baru Anda"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('confirm_password') border-red-500 @enderror"
                        />
                        @error('confirm_password')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Message Display -->
                    @if($message)
                        <div class="p-4 rounded-lg @if($message_type === 'success') bg-green-50 border border-green-200 @else bg-red-50 border border-red-200 @endif">
                            <p class="text-sm @if($message_type === 'success') text-green-800 @else text-red-800 @endif">
                                {{ $message }}
                            </p>
                        </div>
                    @endif

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-semibold py-3 rounded-lg transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed mt-6"
                    >
                        <span wire:loading.remove>
                            Ubah Password
                        </span>
                        <span wire:loading>
                            <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                </form>
                <!-- Logout Button -->
                <form action="{{ route('student.logout') }}" method="POST" class="mt-6">
                    @csrf
                    <button
                        type="submit"
                        class="w-full px-4 py-2 text-gray-600 text-sm font-medium border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition duration-200"
                    >
                        Keluar / Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript for redirect after password change -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('passwordChanged', () => {
                console.log('[PasswordChange] Event received, redirecting to dashboard in 2 seconds...');
                setTimeout(() => {
                    console.log('[PasswordChange] Redirecting to:', '{{ route("student.dashboard") }}');
                    window.location.href = '{{ route("student.dashboard") }}';
                }, 2000);
            });
        });
    </script>
</div>
