<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kasir - Warungin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta http-equiv="refresh" content="30"> <!-- Refresh otomatis tiap 30 detik buat cek order baru -->
</head>
<body class="bg-gray-100 min-h-screen">

    <!-- Navbar Admin -->
    <nav class="bg-white shadow px-6 py-4 flex justify-between items-center sticky top-0 z-50">
        <div class="flex items-center gap-4">
            <h1 class="text-xl font-bold text-orange-600">Dapur Warungin 👨‍🍳</h1>
            <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">Kasir Mode</span>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="text-red-500 hover:text-red-700 font-bold text-sm">Logout</button>
        </form>
    </nav>

    <main class="p-6 max-w-7xl mx-auto">
        
        <!-- Statistik Singkat -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                <h3 class="text-blue-500 font-bold text-xs uppercase">Pesanan Pending</h3>
                <p class="text-2xl font-bold">{{ $orders->where('status', 'pending')->count() }}</p>
            </div>
            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                <h3 class="text-yellow-600 font-bold text-xs uppercase">Sedang Dimasak</h3>
                <p class="text-2xl font-bold">{{ $orders->where('status', 'confirmed')->count() }}</p>
            </div>
            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                <h3 class="text-green-600 font-bold text-xs uppercase">Siap Saji</h3>
                <p class="text-2xl font-bold">{{ $orders->where('status', 'served')->count() }}</p>
            </div>
        </div>

        <h2 class="font-bold text-gray-700 mb-4 text-lg">Daftar Pesanan Masuk</h2>

        @if($orders->isEmpty())
            <div class="text-center py-20 bg-white rounded-xl shadow-sm">
                <p class="text-gray-400">Belum ada pesanan aktif saat ini.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($orders as $order)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative">
                    
                    <!-- Label Status di Pojok -->
                    <div class="absolute top-0 right-0 px-3 py-1 text-xs font-bold rounded-bl-xl
                        {{ $order->status == 'pending' ? 'bg-red-500 text-white' : '' }}
                        {{ $order->status == 'confirmed' ? 'bg-yellow-400 text-yellow-900' : '' }}
                        {{ $order->status == 'served' ? 'bg-green-500 text-white' : '' }}">
                        {{ strtoupper($order->status) }}
                    </div>

                    <div class="p-5">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-bold text-lg">{{ $order->customer_name }}</h3>
                                <p class="text-sm text-gray-500">
                                    {{ $order->order_type == 'dine_in' ? '🍽️ Dine In' : ($order->order_type == 'take_away' ? '🥡 Take Away' : '📅 Booking') }}
                                    @if($order->table)
                                        - <span class="font-bold text-gray-800">{{ $order->table->table_number }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <!-- List Item -->
                        <div class="bg-gray-50 rounded p-3 mb-4 text-sm max-h-40 overflow-y-auto">
                            @foreach($order->items as $item)
                            <div class="flex justify-between py-1 border-b border-gray-200 last:border-0">
                                <span>{{ $item->quantity }}x {{ $item->menu->name }}</span>
                                <span class="text-gray-500 font-mono">{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                            <div class="flex justify-between font-bold mt-2 pt-2 border-t border-gray-300">
                                <span>Total</span>
                                <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Tombol Aksi Kasir -->
                        <form action="{{ route('order.update', $order->id) }}" method="POST" class="flex gap-2">
                            @csrf
                            
                            @if($order->status == 'pending')
                                <button name="status" value="confirmed" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-bold text-sm transition">
                                    ✅ Terima & Masak
                                </button>
                                <button name="status" value="cancelled" class="px-3 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg font-bold text-sm transition" onclick="return confirm('Yakin tolak pesanan?')">
                                    ❌
                                </button>
                            
                            @elseif($order->status == 'confirmed')
                                <button name="status" value="served" class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white py-2 rounded-lg font-bold text-sm transition">
                                    🔔 Siap Disajikan
                                </button>
                            
                            @elseif($order->status == 'served')
                                <button name="status" value="paid" class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg font-bold text-sm transition">
                                    💰 Sudah Bayar (Selesai)
                                </button>
                            @endif

                            <!-- TAMBAHAN BARU: Tombol Print -->
                            <!-- Muncul kalau status BUKAN pending -->
                            @if($order->status != 'pending')
                                <a href="{{ route('order.print', $order->id) }}" target="_blank" class="ml-2 px-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-bold text-sm flex items-center justify-center transition" title="Cetak Struk">
                                    🖨️
                                </a>
                            @endif

                        </form>
                    </div>
                    <div class="bg-gray-50 px-5 py-2 text-xs text-gray-400 flex justify-between">
                        <span>{{ $order->created_at->diffForHumans() }}</span>
                        <span>#{{ substr($order->id, 0, 8) }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </main>
</body>
</html>