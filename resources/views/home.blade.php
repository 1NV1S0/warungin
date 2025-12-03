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
        /* Animasi Toast */
        .toast-enter { transform: translateY(100%); opacity: 0; }
        .toast-enter-active { transform: translateY(0); opacity: 1; transition: all 0.3s ease-out; }
        .toast-exit { transform: translateY(0); opacity: 1; }
        .toast-exit-active { transform: translateY(100%); opacity: 0; transition: all 0.3s ease-in; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    <!-- TOAST NOTIFICATION -->
    <div id="toast-container" class="fixed bottom-24 left-1/2 transform -translate-x-1/2 z-50 transition-all duration-300 pointer-events-none opacity-0 translate-y-10">
        <div class="bg-gray-900 text-white px-6 py-3 rounded-full shadow-2xl flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span id="toast-message" class="font-bold text-sm">Item ditambahkan!</span>
        </div>
    </div>

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

            <!-- Icon Keranjang Mobile -->
            <div class="relative lg:hidden">
                @php $totalQty = 0; @endphp
                @if(session('cart'))
                    @foreach(session('cart') as $details)
                        @php $totalQty += $details['quantity']; @endphp
                    @endforeach
                @endif

                <a href="{{ route('view_cart') }}" class="relative group">
                    <span id="cart-badge-mobile" class="absolute -top-2 -right-2 bg-red-500 text-white text-[10px] font-bold rounded-full h-5 w-5 flex items-center justify-center border-2 border-white {{ $totalQty > 0 ? '' : 'hidden' }}">
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
        
        <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
            <div>
                <h2 class="text-gray-500 font-medium">Selamat Datang,</h2>
                <h3 class="text-3xl font-bold text-gray-800">Mau makan apa hari ini? 😋</h3>
            </div>
            
            @if(session('cart'))
                <a href="{{ route('clear_cart') }}" class="lg:hidden text-sm text-red-500 hover:text-red-700 font-semibold underline decoration-red-500/30 hover:decoration-red-500">
                    Kosongkan Keranjang
                </a>
            @endif
        </div>

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm mb-6 flex justify-between items-center animate-pulse">
                <span>{{ session('error') }}</span>
                <button onclick="this.parentElement.remove()" class="text-red-700 font-bold">&times;</button>
            </div>
        @endif

        <div class="flex flex-col lg:flex-row gap-8 items-start">
            
            <!-- KOLOM KIRI: MENU -->
            <div class="w-full lg:w-3/4">
                @foreach($groupedMenus as $category => $menus)
                    <div class="mb-10">
                        <div class="sticky top-[72px] z-30 bg-gray-50/95 backdrop-blur py-2 mb-4 border-b border-gray-200">
                            <h3 class="text-xl font-bold capitalize text-gray-800 flex items-center gap-2">
                                <span class="w-2 h-8 bg-orange-500 rounded-full"></span>
                                {{ $category }}
                            </h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($menus as $menu)
                            <div class="group bg-white rounded-2xl shadow-sm hover:shadow-md border border-gray-100 transition-all duration-300 flex md:flex-col overflow-hidden h-full relative">
                                
                                <div class="w-32 h-32 md:w-full md:h-48 bg-gray-200 flex-shrink-0 relative overflow-hidden">
                                    <img src="{{ $menu->image_path ? asset('storage/'.$menu->image_path) : 'https://placehold.co/400x300?text=No+Image' }}" 
                                         alt="{{ $menu->name }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                    
                                    <div class="absolute top-2 right-2 hidden md:block">
                                        <span id="stock-badge-desktop-{{ $menu->id }}" class="text-xs px-2 py-1 rounded-full font-bold shadow-sm 
                                            {{ $menu->stock == 0 ? 'bg-red-500 text-white' : ($menu->stock < 5 ? 'bg-yellow-400 text-yellow-900' : 'bg-green-100 text-green-800') }}">
                                            {{ $menu->stock == 0 ? 'Habis' : ($menu->stock < 5 ? 'Sisa ' . $menu->stock : 'Stok: ' . $menu->stock) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex-1 p-4 flex flex-col justify-between">
                                    <div>
                                        <div class="flex justify-between items-start">
                                            <h4 class="font-bold text-lg text-gray-800 leading-snug group-hover:text-orange-600 transition">{{ $menu->name }}</h4>
                                            
                                            <div class="md:hidden">
                                                <span id="stock-badge-mobile-{{ $menu->id }}" class="text-[10px] px-2 py-1 rounded-full font-bold
                                                    {{ $menu->stock == 0 ? 'bg-red-100 text-red-600' : ($menu->stock < 5 ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
                                                    {{ $menu->stock == 0 ? 'Habis' : ($menu->stock < 5 ? 'Sisa ' . $menu->stock : 'Stok ' . $menu->stock) }}
                                                </span>
                                            </div>
                                        </div>
                                        <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $menu->description }}</p>
                                    </div>

                                    <div class="flex justify-between items-end mt-4 pt-4 border-t border-gray-50">
                                        <span class="text-orange-600 font-bold text-lg">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                                        
                                        <!-- Tombol Tambah AJAX -->
                                        <button onclick="addToCart(event, {{ $menu->id }})" 
                                           id="btn-add-{{ $menu->id }}"
                                           class="w-10 h-10 rounded-full flex items-center justify-center transition active:scale-95 shadow-sm
                                           {{ $menu->stock > 0 ? 'bg-orange-100 text-orange-600 hover:bg-orange-500 hover:text-white' : 'bg-gray-100 text-gray-400 cursor-not-allowed pointer-events-none' }}"
                                           {{ $menu->stock <= 0 ? 'disabled' : '' }}>
                                            
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $menu->stock > 0 ? 'M12 4v16m8-8H4' : 'M6 18L18 6M6 6l12 12' }}" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- KOLOM KANAN: SIDEBAR CART -->
            <div class="hidden lg:block lg:w-1/4 relative">
                <div class="sticky top-24 bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="bg-gray-900 p-4 text-white flex justify-between items-center">
                        <h3 class="font-bold">Pesanan Aktif</h3>
                        <span id="cart-badge-desktop" class="bg-orange-500 text-xs px-2 py-1 rounded-full">{{ $totalQty }} Item</span>
                    </div>

                    <!-- PERBAIKAN: ID INI PENTING -->
                    <div id="cart-sidebar-content" class="p-4 max-h-[60vh] overflow-y-auto custom-scrollbar">
                        @if(session('cart'))
                            <div class="space-y-4">
                                @php $totalPrice = 0; @endphp
                                @foreach(session('cart') as $id => $details)
                                    @php $totalPrice += $details['price'] * $details['quantity']; @endphp
                                    <div class="flex gap-3 relative group">
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
                                        
                                        <a href="{{ route('remove_from_cart', $id) }}" class="absolute -right-2 -top-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition shadow-md" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="mt-6 pt-4 border-t border-gray-100">
                                <div class="flex justify-between items-center mb-4">
                                    <span class="text-gray-500">Total Tagihan</span>
                                    <span class="text-xl font-bold text-gray-900">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <a href="{{ route('clear_cart') }}" class="text-center py-2 rounded-lg border border-red-200 text-red-500 text-sm font-bold hover:bg-red-50 transition">Reset</a>
                                    <a href="{{ route('view_cart') }}" class="text-center py-2 rounded-lg bg-orange-600 text-white text-sm font-bold hover:bg-orange-700 transition shadow-lg shadow-orange-200">Bayar →</a>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-10 text-gray-400">Keranjang kosong</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Floating Footer (Mobile) -->
    @if($totalQty > 0)
    <div class="lg:hidden fixed bottom-0 left-0 right-0 p-4 z-40 bg-gradient-to-t from-white via-white to-transparent pb-6">
        <div class="bg-gray-900 text-white p-4 rounded-2xl shadow-2xl flex justify-between items-center cursor-pointer" onclick="window.location='{{ route('view_cart') }}'">
            <div class="flex items-center gap-4">
                <div class="bg-orange-500 text-white w-10 h-10 rounded-full flex items-center justify-center font-bold" id="cart-badge-footer">{{ $totalQty }}</div>
                <div class="flex flex-col">
                    <span class="text-xs text-gray-400">Total</span>
                    <span class="font-bold text-lg">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="flex items-center gap-2 font-bold text-orange-400">
                <span>Keranjang</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
            </div>
        </div>
    </div>
    @endif

    <!-- SCRIPT UTAMA -->
    <script>
        // --- FUNGSI TAMBAH ITEM (+) ---
        async function addToCart(event, id) {
            if(event) event.preventDefault();
            
            // Animasi loading kecil di tombol sidebar (kalau ada)
            // ... (logika loading opsional) ...

            try {
                const response = await fetch(`/add-to-cart/${id}`, {
                    method: 'POST',
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const result = await response.json();

                if (result.success) {
                    updateCartCount(result.total_qty);
                    updateCartSidebar(result.cart, result.total_price);
                    // showToast(result.message); // Opsional: Matikan toast kalau terlalu berisik saat klik +/-
                } else {
                    alert(result.message);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // --- FUNGSI KURANGI ITEM (-) ---
        async function decreaseItem(id) {
            try {
                const response = await fetch(`/decrease-item/${id}`, {
                    method: 'POST',
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const result = await response.json();

                if (result.success) {
                    updateCartCount(result.total_qty);
                    updateCartSidebar(result.cart, result.total_price);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // --- RENDER SIDEBAR ---
        function updateCartSidebar(cart, totalPrice) {
            const container = document.getElementById('cart-sidebar-content');
            if (!container) return; 

            if (!cart || Object.keys(cart).length === 0) {
                container.innerHTML = `<div class="text-center py-10 text-gray-400">Keranjang kosong</div>`;
                return;
            }

            let html = '<div class="space-y-4">';
            const fmt = (val) => new Intl.NumberFormat('id-ID').format(val);

            for (const [id, details] of Object.entries(cart)) {
                const imageUrl = details.image ? `/storage/${details.image}` : 'https://placehold.co/100x100';

                html += `
                    <div class="flex gap-3 relative group items-center">
                        <!-- Gambar -->
                        <div class="w-12 h-12 bg-gray-100 rounded-lg bg-cover bg-center flex-shrink-0"
                             style="background-image: url('${imageUrl}')">
                        </div>
                        
                        <!-- Info & Kontrol Jumlah -->
                        <div class="flex-1">
                            <h4 class="text-sm font-bold text-gray-700 line-clamp-1">${details.name}</h4>
                            
                            <div class="flex justify-between items-center mt-1">
                                <!-- Kontrol Plus Minus -->
                                <div class="flex items-center gap-2 bg-gray-50 rounded-lg p-1">
                                    
                                    <!-- Tombol Kurang (-) -->
                                    <button onclick="decreaseItem(${id})" class="w-5 h-5 flex items-center justify-center bg-white rounded shadow text-gray-600 hover:bg-red-50 hover:text-red-500 font-bold text-xs transition">
                                        -
                                    </button>
                                    
                                    <span class="text-xs font-bold text-gray-700 min-w-[15px] text-center">${details.quantity}</span>
                                    
                                    <!-- Tombol Tambah (+) -->
                                    <button onclick="addToCart(null, ${id})" class="w-5 h-5 flex items-center justify-center bg-white rounded shadow text-gray-600 hover:bg-green-50 hover:text-green-500 font-bold text-xs transition">
                                        +
                                    </button>
                                </div>

                                <span class="text-sm font-bold text-orange-600">Rp ${fmt(details.price * details.quantity)}</span>
                            </div>
                        </div>
                    </div>
                `;
            }
            html += '</div>';

            html += `
                <div class="mt-6 pt-4 border-t border-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-gray-500">Total Tagihan</span>
                        <span class="text-xl font-bold text-gray-900">Rp ${fmt(totalPrice)}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('clear_cart') }}" class="text-center py-2 rounded-lg border border-red-200 text-red-500 text-sm font-bold hover:bg-red-50 transition">Reset</a>
                        <a href="{{ route('view_cart') }}" class="text-center py-2 rounded-lg bg-orange-600 text-white text-sm font-bold hover:bg-orange-700 transition shadow-lg shadow-orange-200">Bayar →</a>
                    </div>
                </div>
            `;

            container.innerHTML = html;
        }

        // ... Sisa fungsi updateCartCount, showToast, updateStocks, updateMenuUI (TETAP SAMA) ...
        function updateCartCount(qty) {
            const badges = [document.getElementById('cart-badge-desktop'), document.getElementById('cart-badge-mobile'), document.getElementById('cart-badge-footer')];
            badges.forEach(el => { if(el) { el.innerText = qty + (el.id === 'cart-badge-desktop' ? ' Item' : ''); el.classList.remove('hidden'); }});
        }

        function showToast(message) {
            const container = document.getElementById('toast-container');
            if(container) {
                const msg = document.getElementById('toast-message');
                msg.innerText = message;
                container.classList.remove('opacity-0', 'translate-y-10', 'pointer-events-none');
                setTimeout(() => { container.classList.add('opacity-0', 'translate-y-10', 'pointer-events-none'); }, 1000); // Toast lebih cepat hilang (1 detik) biar gak ganggu
            }
        }

        async function updateStocks() {
            try {
                const response = await fetch("{{ route('api.menu.stocks') }}");
                const result = await response.json();
                if (result.success) { result.data.forEach(menu => { updateMenuUI(menu.id, menu.stock); }); }
            } catch (error) { console.error("Gagal update stok"); }
        }

        function updateMenuUI(id, stock) {
            const badges = [document.getElementById(`stock-badge-desktop-${id}`), document.getElementById(`stock-badge-mobile-${id}`)];
            badges.forEach(badge => {
                if (badge) {
                    badge.className = "text-xs px-2 py-1 rounded-full font-bold shadow-sm " + (window.innerWidth < 768 ? "text-[10px] " : "");
                    if (stock === 0) {
                        badge.innerText = "Habis"; badge.classList.remove('bg-yellow-400', 'text-yellow-900', 'bg-green-100', 'text-green-800'); badge.classList.add('bg-red-500', 'text-white');
                    } else if (stock < 5) {
                        badge.innerText = "Sisa " + stock; badge.classList.remove('bg-red-500', 'text-white', 'bg-green-100', 'text-green-800'); badge.classList.add('bg-yellow-400', 'text-yellow-900');
                    } else {
                        badge.innerText = "Stok: " + stock; badge.classList.remove('bg-red-500', 'text-white', 'bg-yellow-400', 'text-yellow-900'); badge.classList.add('bg-green-100', 'text-green-800');
                    }
                }
            });
            const btn = document.getElementById(`btn-add-${id}`);
            if (btn) {
                if (stock > 0) {
                    btn.classList.remove('bg-gray-100', 'text-gray-400', 'cursor-not-allowed', 'pointer-events-none');
                    btn.classList.add('bg-orange-100', 'text-orange-600', 'hover:bg-orange-500', 'hover:text-white');
                    btn.setAttribute('onclick', `addToCart(event, ${id})`);
                } else {
                    btn.classList.remove('bg-orange-100', 'text-orange-600', 'hover:bg-orange-500', 'hover:text-white');
                    btn.classList.add('bg-gray-100', 'text-gray-400', 'cursor-not-allowed', 'pointer-events-none');
                    btn.removeAttribute('onclick');
                }
            }
        }

        setInterval(updateStocks, 5000);
    </script>

</body>
</html>