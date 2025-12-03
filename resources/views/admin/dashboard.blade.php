<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Warungin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

    <!-- Navbar -->
    <nav class="bg-white shadow px-6 py-4 flex justify-between items-center sticky top-0 z-50">
        <h1 class="text-xl font-bold text-gray-800">Kantor Warungin 🏢</h1>
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-500">Halo, {{ Auth::user()->name }}</span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="bg-red-50 text-red-600 px-3 py-1 rounded text-sm font-bold hover:bg-red-100">Logout</button>
            </form>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto p-6">
        
        <!-- Kartu Statistik Hari Ini -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Omzet -->
            <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-green-500">
                <p class="text-gray-500 text-sm uppercase font-bold">Omzet Hari Ini</p>
                <h2 class="text-3xl font-bold text-gray-800 mt-2">Rp {{ number_format($todaysIncome, 0, ',', '.') }}</h2>
            </div>
            
            <!-- Total Order -->
            <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-blue-500">
                <p class="text-gray-500 text-sm uppercase font-bold">Total Transaksi</p>
                <h2 class="text-3xl font-bold text-gray-800 mt-2">{{ $todaysOrders }} <span class="text-sm font-normal text-gray-400">pesanan</span></h2>
            </div>

            <!-- Peringatan Stok -->
            <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-red-500">
                <p class="text-gray-500 text-sm uppercase font-bold">Stok Menipis</p>
                <h2 class="text-3xl font-bold text-red-600 mt-2">{{ $lowStockMenus->count() }} <span class="text-sm font-normal text-gray-400">menu</span></h2>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Kolom Kiri: Menu Cepat & Stok -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Menu Cepat -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-700 mb-4">Akses Cepat</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <a href="{{ route('admin.menus.index') }}" class="p-4 bg-orange-50 rounded-xl text-center hover:bg-orange-100 transition border border-orange-100">
                            <div class="text-2xl mb-2">🍔</div>
                            <div class="font-bold text-orange-700 text-sm">Kelola Menu</div>
                        </a>
                        <a href="{{ route('admin.reports.index') }}" class="p-4 bg-purple-50 rounded-xl text-center hover:bg-purple-100 transition border border-purple-100">
                            <div class="text-2xl mb-2">📊</div>
                            <div class="font-bold text-purple-700 text-sm">Laporan Keuangan</div>
                        </a>

                        <!-- TAMBAHAN BARU: KELOLA MEJA -->
                        <a href="{{ route('admin.tables.index') }}" class="p-4 bg-green-50 rounded-xl text-center hover:bg-green-100 transition border border-green-100">
                            <div class="text-2xl mb-2">🪑</div>
                            <div class="font-bold text-green-700 text-sm">Kelola Meja</div>
                        </a>

                        <a href="{{ route('dashboard') }}" class="p-4 bg-blue-50 rounded-xl text-center hover:bg-blue-100 transition border border-blue-100">
                            <div class="text-2xl mb-2">👨‍🍳</div>
                            <div class="font-bold text-blue-700 text-sm">Lihat Dapur</div>
                        </a>
                    </div>
                </div>

                <!-- Alert Stok Menipis -->
                @if($lowStockMenus->isNotEmpty())
                <div class="bg-red-50 rounded-xl p-6 border border-red-100">
                    <h3 class="font-bold text-red-700 mb-3 flex items-center gap-2">
                        ⚠️ Perhatian: Stok Menipis!
                    </h3>
                    <div class="bg-white rounded-lg overflow-hidden border border-red-100">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-red-50 text-red-800">
                                <tr>
                                    <th class="p-3">Nama Menu</th>
                                    <th class="p-3">Sisa Stok</th>
                                    <th class="p-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lowStockMenus as $menu)
                                <tr class="border-b last:border-0">
                                    <td class="p-3 font-medium">{{ $menu->name }}</td>
                                    <td class="p-3 font-bold text-red-600">{{ $menu->stock }}</td>
                                    <td class="p-3 text-right">
                                        <a href="{{ route('admin.menus.edit', $menu->id) }}" class="text-blue-600 hover:underline">Restock</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

            </div>

            <!-- Kolom Kanan: Aktivitas Terbaru -->
            <div>
                <div class="bg-white rounded-xl shadow-sm p-6 h-full">
                    <h3 class="font-bold text-gray-700 mb-4">Transaksi Terakhir</h3>
                    <div class="space-y-4">
                        @foreach($recentOrders as $order)
                        <div class="flex items-center justify-between pb-3 border-b border-gray-100 last:border-0">
                            <div>
                                <p class="font-bold text-sm text-gray-800">{{ $order->customer_name }}</p>
                                <p class="text-xs text-gray-400">{{ $order->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-sm">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                <span class="text-[10px] px-2 py-0.5 rounded-full 
                                    {{ $order->status == 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ $order->status }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                        
                        <div class="pt-2 text-center">
                            <a href="{{ route('admin.reports.index') }}" class="text-xs text-blue-500 hover:underline">Lihat Semua Transaksi →</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
</body>
</html>