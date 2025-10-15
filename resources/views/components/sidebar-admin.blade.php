@php
$navigations = [
'home' => '🏠 Beranda',
'master' => [
'text'=>'📦 Master Data',
'nav'=>[
'product' => '📋 Produk',
'product-categories' => '🏷️ Kategori',
'supplier' => '🏭 Supplier',
'member' => '👥 Kelola Member',
],
],
'transaksi' => [
'text'=>'💳 Transaksi',
'nav'=>[
'items-in' => '📥 Barang Masuk',
'items-out' => '📤 Barang Keluar',
'transaction' => '🆕 Penjualan',
],
],
'laporan' => [
'text'=>'📑 Laporan',
'nav'=> [
'report' => '📑 Laporan Periode',
'trend' => '📑 Trend Produk',
'stock-report' => '📋 Stok per Produk',
],
],
'team' => '👥 Kelola Team',
'admin' => '👥 Kelola Admin',
];

$active = '';
foreach($navigations as $key => $value) {
if(is_string($value)){
if(request()->routeIs($key.'*')) {
$active = $key;
break;
}
}else{
foreach($value['nav'] as $nav => $text){
if(request()->routeIs($nav.'*')) {
$active = $key;
break 2; // keluar dari 2 loop
}
}
}
}
@endphp

<aside
    class="fixed top-0 h-full w-64 bg-white shadow-xl flex flex-col transition-all duration-300"
    x-data="{ active: '{{ $active }}' }" :class="{'left-0':sidebarHide , '-left-full':sidebarHide== false}">
    <!-- Header -->
    <div class="flex items-center gap-3 p-6 border-b border-gray-200 bg-white">
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

        <!-- Gradient Text -->
        <h1 class="text-2xl font-extrabold tracking-wide bg-gradient-to-r from-pink-500 to-purple-600 bg-clip-text text-transparent">
            MAMAKU
        </h1>
    </div>

    <!-- user info -->
    <div class="flex flex-col items-center gap-3 w-full p-6 border-b border-gray-200">
        @if(Auth::user()->photo == null)
        <div class="w-12 h-12 rounded-full bg-pink-200 flex items-center justify-center text-xl font-bold text-pink-600">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
        </div>
        @else
        <img src="/file/{{  Auth::user()->photo }}" alt="User Photo" class="w-12 h-12 rounded-full object-cover">
        @endif
        <div class="flex flex-col items-center">
            <div class="flex items-center gap-x-1">
                <p class="font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                <p class="text-sm text-white bg-pink-400 px-2 py-0.5 rounded-full">{{ Auth::user()->level }}</p>
            </div>
            <p class="text-sm text-gray-500 mb-0.5">{{ Auth::user()->email }}</p>
            <p class="text-sm text-gray-500 mb-2">{{ Auth::user()->team->name }}</p>
            <a href="{{ route('profile.index') }}" class="px-3.5 py-1 border border-pink-400 bg-white text-sm rounded-xl hover:bg-pink-400 hover:text-white transition">Lihat Profil</a>
        </div>
    </div>

    <!-- Menu -->
    <nav class="flex-1 p-4 space-y-4 overflow-y-auto">
        @foreach($navigations as $key => $value)
        @if(gettype($value) === 'string')
        <!-- Dashboard -->
        <a href="{{ route($key . '.index') }}"
            class="flex items-center px-3 py-2 rounded-lg {{ request()->routeIs($key . '.*')?'bg-pink-600 text-white':'text-gray-700' }} hover:bg-pink-50 hover:text-pink-600 transition">
            {{ $value }}
        </a>
        @else
        <!-- Master Data -->
        <div>
            <button @click="active = active === '{{ $key }}' ? '' : '{{ $key }}'"
                class="flex justify-between items-center w-full px-3 py-2 rounded-lg text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">
                <span>{{ $value['text'] }}</span>
                <svg :class="{ 'rotate-180': active === '{{ $key }}' }" xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <ul
                x-show="active === '{{ $key }}'"
                x-collapse
                class="mt-2 space-y-1 pl-4 overflow-hidden">
                @foreach($value['nav'] as $nav => $text)
                <li>
                    <a href="{{ route($nav . '.index') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs($nav . '.*')?'bg-pink-600 text-white':'text-gray-700' }} hover:bg-pink-50 hover:text-pink-600">
                        {{ $text }}
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
        @endif
        @endforeach
        <!-- Keluar -->
        <button type="button" @click="logoutModal = true"
            class="flex items-center px-3 py-2 rounded-lg text-gray-700 hover:bg-pink-50 hover:text-pink-600 transition">
            🚪 Keluar
        </button>

    </nav>
</aside>
