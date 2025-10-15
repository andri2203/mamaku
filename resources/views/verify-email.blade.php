<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- favicon from svg "favicon.svg" -->
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">

    <title>Verifikasi Email</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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

            <!-- Header -->
            <header class="mb-6">
                <div class="flex flex-col justify-center items-center gap-2 pb-6 border-b border-gray-200">
                    <!-- SVG Icon: Inventory Rack -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"
                        class="w-14 h-14">
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
                    <h1 class="text-xl font-extrabold tracking-wide bg-gradient-to-r from-pink-500 to-purple-600 bg-clip-text text-transparent">
                        UMKM Manajemen
                    </h1>
                </div>
            </header>

            <!-- Title -->
            <h2 class="text-xl font-semibold mb-4 text-center text-gray-800">Verifikasi Email</h2>
            <p class="text-gray-600 text-center mb-6 text-sm">
                Kami telah mengirimkan kode verifikasi ke email Anda. Silakan masukkan kode tersebut di bawah ini untuk melanjutkan.
            </p>

            <!-- Form -->
            <form method="POST" action="{{ route('verify.email.post') }}" @submit="loading = true">
                @csrf

                <!-- Kode Verifikasi -->
                <div class="mb-6">
                    <label for="verification_code" class="block text-gray-700 font-medium mb-2">Kode Verifikasi</label>
                    <input id="verification_code" type="text" name="verification_code" required autofocus
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-400 @error('verification_code') border-red-500 @enderror">
                    @error('verification_code')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Button -->
                <button type="submit"
                    class="w-full bg-gradient-to-r from-pink-500 to-purple-600 text-white py-3 rounded-xl font-semibold shadow-lg hover:opacity-90 transition">
                    Verifikasi
                </button>
            </form>

            <!-- Resend Link -->
            <div class="mt-6 text-center text-sm">
                <p class="text-gray-600">
                    Tidak menerima email?
                    <a href="{{ route('verify.email.resend') }}" class="text-pink-600 hover:underline font-medium">Kirim ulang kode</a>
                </p>
            </div>
        </div>
    </div>

</body>

</html>
