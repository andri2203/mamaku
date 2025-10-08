<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- favicon from svg "favicon.svg" -->
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">

    <title>Lupa Password</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>

<body>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-pink-100 via-purple-100 to-pink-200" x-data="{ loading: true }" x-init="setTimeout(() => loading = false, 1000)">
        <!-- Loading Overlay -->
        <div x-show="loading" class="fixed inset-0 flex items-center justify-center bg-gray-900/50 z-50">
            <div class="flex flex-col items-center">
                <svg class="animate-spin h-10 w-10 text-pink-500 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
                <span class="text-white font-semibold text-lg">Loading...</span>
            </div>
        </div>
        <div class="backdrop-blur-md bg-white/70 border border-white/30 p-8 rounded-2xl shadow-2xl w-full max-w-md">
            <!-- Success Messege -->
            @if (session('success'))
            <div class="mb-4 p-4 bg-grenn-100 border border-grenn-400 text-grenn-700 rounded-lg">
                {{ session('success') }}
            </div>
            @endif
            <!-- Info Messege -->
            @if (session('info'))
            <div class="mb-4 p-4 bg-blue-100 border border-blue-400 text-blue-700 rounded-lg">
                {{ session('info') }}
            </div>
            @endif
            <!-- Error Messege -->
            @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
            @endif

            <!-- Header (TIDAK DIUBAH) -->
            <header class="mb-4">
                <div class="flex justify-center items-center gap-3 p-4 border-b border-gray-200">
                    <!-- SVG Icon: Inventory Rack -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"
                        class="w-10 h-10">
                        <defs>
                            <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#ec4899;stop-opacity:1" />
                                <stop offset="100%" style="stop-color:#8b5cf6;stop-opacity:1" />
                            </linearGradient>
                        </defs>
                        <rect x="8" y="8" width="48" height="48" rx="4" ry="4" fill="url(#grad1)" />
                        <g stroke="white" stroke-width="2" fill="none">
                            <rect x="16" y="16" width="12" height="12" rx="2" />
                            <rect x="36" y="16" width="12" height="12" rx="2" />
                            <rect x="16" y="36" width="12" height="12" rx="2" />
                            <rect x="36" y="36" width="12" height="12" rx="2" />
                        </g>
                    </svg>
                    <h1 class="text-3xl font-extrabold tracking-wide bg-gradient-to-r from-pink-500 to-purple-600 bg-clip-text text-transparent">
                        MAMAKU
                    </h1>
                </div>
            </header>

            <!-- Title -->
            <h2 class="text-xl font-semibold text-center text-gray-800">Reset Password Anda</h2>

            <!-- Form -->
            <form method="POST" action="{{ route('password.reset.post') }}" @submit="loading = true">
                @csrf
                @method('PUT')

                @if($reset_code_type == 'email_send')
                <p class="mb-4 text-gray-600 text-sm text-center">Kami mengirimkan kode verifikasi ke email anda untuk reset password anda</p>
                @endif

                @if($reset_code_type == 'two_factor_recovery_codes')
                <p class="mb-4 text-gray-600 text-sm text-center">Mohon isi Kode Pemulihan sebagai kode verifikasi untuk reset password anda</p>
                @endif

                <!-- Kode Verifikasi -->
                <div class="mb-4">
                    <label for="verification_code" class="block text-gray-700 font-medium mb-2">Kode Verifikasi</label>
                    <input id="verification_code" type="text" name="verification_code" required autofocus
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-400 @error('verification_code') border-red-500 @enderror">
                    @error('verification_code')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="block text-gray-700 font-medium mb-2">Password Baru</label>
                    <input id="password" type="password" name="password" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-400 @error('password') border-red-500 @enderror">
                    @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Confirmation-->
                <div class="mb-4">
                    <label for="password_confirmation" class="block text-gray-700 font-medium mb-2">Konfirmasi Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-400 @error('password_confirmation') border-red-500 @enderror">
                    @error('password_confirmation')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Button -->
                <button type="submit"
                    class="w-full bg-gradient-to-r from-pink-500 to-purple-600 text-white py-3 mb-4 rounded-xl font-semibold shadow-lg hover:opacity-90 transition">
                    Reset Password
                </button>

                <a href="{{ route('login') }}" @click="loading= true" class="inline-flex items-center gap-x-2 text-pink-600 hover:text-pink-800 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                    </svg>
                    Kembali ke Login
                </a>
            </form>
        </div>
    </div>

</body>

</html>
