<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warungin - Pesan Makan Mudah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style> 
        body { font-family: 'Poppins', sans-serif; } 
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    <!-- Navbar -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-bold text-orange-600">Warungin.</h1>
                @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'owner', 'cashier']))
                    <a href="{{ route('dashboard') }}" class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded hover:bg-gray-200 border border-gray-200">
                        ← Kembali ke Dapur
                    </a>
                @endif
            </div>

            <!-- Icon Keranjang (Hanya Muncul di Mobile/Tablet Kecil) -->
            <div class="relative lg:hidden">
                @php $totalQty = 0; @endphp
                @if(session('cart'))
                    @foreach(session('cart') as $details)
                        @php $totalQty += $details['quantity']; @endphp
                    @endforeach
                @endif

                <a href="{{ route('view_cart') }}" class="relative group">
                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-[10px] font-bold rounded-full h-5 w-5 flex items-center justify-center border-2 border-white">
                        {{ $totalQty }}
                    </span>
                    <div class="p-2 bg-gray-100 rounded-full group-hover:bg-orange-100 group-hover:text-orange-600 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </a>
            </div>
        </div>
    </nav>

    <!-- Konten Utama -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-24 pt-6">
        
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
            <div>
                <h2 class="text-gray-500 font-medium">Selamat Datang,</h2>
                <h3 class="text-3xl font-bold text-gray-800">Mau makan apa hari ini? 😋</h3>
            </div>
            
            <!-- Tombol Reset Cart (Mobile Only) -->
            @if(session('cart'))
                <a href="{{ route('clear_cart') }}" class="lg:hidden text-sm text-red-500 hover:text-red-700 font-semibold underline decoration-red-500/30 hover:decoration-red-500">
                    Kosongkan Keranjang
                </a>
            @endif
        </div>

        <!-- Alert Sukses -->
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm mb-6 flex justify-between items-center animate-pulse">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-green-700 font-bold">&times;</button>
            </div>
        @endif

        <!-- LAYOUT UTAMA: SPLIT SCREEN (KIRI MENU, KANAN CART) -->
        <div class="flex flex-col lg:flex-row gap-8 items-start">
            
            <!-- KOLOM KIRI: DAFTAR MENU (Lebar 75%) -->
            <div class="w-full lg:w-3/4">
                @foreach($groupedMenus as $category => $menus)
                    <div class="mb-10">
                        <div class="sticky top-[72px] z-30 bg-gray-50/95 backdrop-blur py-2 mb-4 border-b border-gray-200">
                            <h3 class="text-xl font-bold capitalize text-gray-800 flex items-center gap-2">
                                <span class="w-2 h-8 bg-orange-500 rounded-full"></span>
                                {{ $category }}
                            </h3>
                        </div>
                        
                        <!-- Grid System: Responsive -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($menus as $menu)
                            <div class="group bg-white rounded-2xl shadow-sm hover:shadow-md border border-gray-100 transition-all duration-300 flex md:flex-col overflow-hidden h-full relative">
                                <!-- Gambar -->
                                <div class="w-32 h-32 md:w-full md:h-48 bg-gray-200 flex-shrink-0 relative overflow-hidden">
                                    <img src="{{ $menu->image_path ? asset('storage/'.$menu->image_path) : 'https://placehold.co/400x300?text=No+Image' }}" 
                                         alt="{{ $menu->name }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                    
                                    <div class="absolute top-2 right-2 hidden md:block">
                                        @if($menu->stock == 0)
                                            <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full font-bold shadow-sm">Habis</span>
                                        @elseif($menu->stock < 5)
                                            <span class="bg-yellow-400 text-yellow-900 text-xs px-2 py-1 rounded-full font-bold shadow-sm">Sisa {{ $menu->stock }}</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Info -->
                                <div class="flex-1 p-4 flex flex-col justify-between">
                                    <div>
                                        <h4 class="font-bold text-lg text-gray-800 leading-snug group-hover:text-orange-600 transition">{{ $menu->name }}</h4>
                                        <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $menu->description }}</p>
                                    </div>
                                    <div class="flex justify-between items-end mt-4 pt-4 border-t border-gray-50">
                                        <span class="text-orange-600 font-bold text-lg">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                                        @if($menu->stock > 0)
                                            <a href="{{ route('add_to_cart', $menu->id) }}" 
                                               class="bg-orange-100 text-orange-600 w-10 h-10 rounded-full flex items-center justify-center hover:bg-orange-500 hover:text-white transition active:scale-95 shadow-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                            </a>
                                        @else
                                            <button disabled class="bg-gray-100 text-gray-400 w-10 h-10 rounded-full flex items-center justify-center cursor-not-allowed">x</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- KOLOM KANAN: SIDEBAR CART (Lebar 25%) - HANYA MUNCUL DI DESKTOP -->
            <div class="hidden lg:block lg:w-1/4 relative">
                <div class="sticky top-24 bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="bg-gray-900 p-4 text-white flex justify-between items-center">
                        <h3 class="font-bold">Pesanan Aktif</h3>
                        <span class="bg-orange-500 text-xs px-2 py-1 rounded-full">{{ $totalQty }} Item</span>
                    </div>

                    <div class="p-4 max-h-[60vh] overflow-y-auto custom-scrollbar">
                        @if(session('cart'))
                            <div class="space-y-4">
                                @php $totalPrice = 0; @endphp
                                @foreach(session('cart') as $id => $details)
                                    @php $totalPrice += $details['price'] * $details['quantity']; @endphp
                                    <div class="flex gap-3">
                                        <!-- Gambar Kecil -->
                                        <div class="w-12 h-12 bg-gray-100 rounded-lg bg-cover bg-center flex-shrink-0"
                                             style="background-image: url('{{ $details['image'] ? asset('storage/'.$details['image']) : 'https://placehold.co/100x100' }}')">
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-sm font-bold text-gray-700 line-clamp-1">{{ $details['name'] }}</h4>
                                            <div class="flex justify-between items-center mt-1">
                                                <span class="text-xs text-gray-500">{{ $details['quantity'] }} x {{ number_format($details['price'], 0, ',', '.') }}</span>
                                                <span class="text-sm font-bold text-orange-600">Rp {{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- Total & Tombol -->
                            <div class="mt-6 pt-4 border-t border-gray-100">
                                <div class="flex justify-between items-center mb-4">
                                    <span class="text-gray-500">Total Tagihan</span>
                                    <span class="text-xl font-bold text-gray-900">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <a href="{{ route('clear_cart') }}" class="text-center py-2 rounded-lg border border-red-200 text-red-500 text-sm font-bold hover:bg-red-50 transition">
                                        Reset
                                    </a>
                                    <a href="{{ route('view_cart') }}" class="text-center py-2 rounded-lg bg-orange-600 text-white text-sm font-bold hover:bg-orange-700 transition shadow-lg shadow-orange-200">
                                        Bayar →
                                    </a>
                                </div>
                            </div>

                        @else
                            <div class="text-center py-10">
                                <div class="bg-gray-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                </div>
                                <p class="text-gray-400 text-sm">Keranjang masih kosong</p>
                                <p class="text-xs text-gray-300 mt-1">Klik menu di samping untuk pesan</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>

    </main>

    <!-- Floating Footer (HANYA DI MOBILE/TABLET - Hidden di Desktop) -->
    @php 
        $totalPrice = 0;
        if(session('cart')){
            foreach(session('cart') as $item){
                $totalPrice += $item['price'] * $item['quantity'];
            }
        }
    @endphp

    @if($totalQty > 0)
    <div class="lg:hidden fixed bottom-0 left-0 right-0 p-4 z-40 bg-gradient-to-t from-white via-white to-transparent pb-6">
        <div class="bg-gray-900 text-white p-4 rounded-2xl shadow-2xl flex justify-between items-center transform hover:-translate-y-1 transition duration-300 cursor-pointer" onclick="window.location='{{ route('view_cart') }}'">
            <div class="flex items-center gap-4">
                <div class="bg-orange-500 text-white w-10 h-10 rounded-full flex items-center justify-center font-bold">
                    {{ $totalQty }}
                </div>
                <div class="flex flex-col">
                    <span class="text-xs text-gray-400">Total</span>
                    <span class="font-bold text-lg">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="flex items-center gap-2 font-bold text-orange-400 group">
                <span>Keranjang</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:translate-x-1 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </div>
        </div>
    </div>
    @endif

</body>
</html>