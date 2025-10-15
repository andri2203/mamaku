<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- favicon from svg "favicon.svg" -->
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">

    <title>UMKM Manajemen - {{ $title }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <!-- Layout Dashboard with Sidebar & not to big padding navbar -->
    <div class="min-h-screen flex bg-gradient-to-br from-pink-100 via-purple-100 to-pink-200" x-data="{ logoutModal: false, loading: true, sidebarHide: true }" x-init="setTimeout(() => loading = false, 1000)">
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
        <!-- Include Sidebar From Blade Components/sidebar-admin -->
        @include('components.sidebar-admin')

        <!-- Main Content -->
        <div class="flex-1 p-6 transition-all duration-300" :class="{'ml-64':sidebarHide, 'ml-0':sidebarHide==false}">
            <nav class="inline-flex items-center justify-between mb-6 ">
                <header class="inline-flex">
                    <button class="outline-0 bg-pink-300 hover:bg-pink-500 p-2 me-2 rounded-lg cursor-pointer" type="button" @click="sidebarHide = !sidebarHide">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-3">
                            <path fill-rule="evenodd" d="M3 5.25a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 5.25Zm0 4.5A.75.75 0 0 1 3.75 9h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 9.75Zm0 4.5a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75a.75.75 0 0 1-.75-.75Zm0 4.5a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <h1 class="text-3xl font-extrabold tracking-wide bg-gradient-to-r from-pink-500 to-purple-600 bg-clip-text text-transparent">@yield('title')</h1>
                </header>

                <!-- Menu Goes here -->
            </nav>
            @if (session('success'))
            <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-800">
                {{ session('success') }}
            </div>
            @endif
            @if (session('error'))
            <div class="mb-4 p-4 rounded-lg bg-red-100 text-red-800">
                {{ session('error') }}
            </div>
            @endif
            <!-- Content Section -->
            <!-- yield content from child views -->
            @yield('content')
        </div>

        <!-- Modal with form -->
        <div x-show="logoutModal == true" class="fixed inset-0 flex items-center justify-center bg-black/50"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md"
                @click.away="logoutModal = false"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-90">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Konfirmasi Keluar</h2>
                <p class="mb-6 text-gray-600">Apakah Anda yakin ingin keluar?</p>
                <div class="flex justify-end space-x-4">
                    <button @click="logoutModal = false" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Batal</button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600 transition">Keluar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
