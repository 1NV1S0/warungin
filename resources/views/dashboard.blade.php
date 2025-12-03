<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kasir - Warungin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Hapus meta refresh, kita pakai JS -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100 min-h-screen">

    <!-- Audio Notifikasi -->
    <audio id="notifSound" src="{{ asset('audio/sfx.mp3') }}" preload="auto"></audio>

    <!-- Navbar Admin -->
    <nav class="bg-white shadow px-6 py-4 flex justify-between items-center sticky top-0 z-50">
        <div class="flex items-center gap-4">
            <h1 class="text-xl font-bold text-orange-600">Dapur Warungin 👨‍🍳</h1>
            
            <a href="{{ route('home') }}" target="_blank" class="bg-orange-100 text-orange-600 hover:bg-orange-200 text-xs font-bold px-3 py-2 rounded-lg flex items-center gap-1 transition">
                + Input Pesanan Baru
            </a>

            <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">Kasir Mode</span>
            
            <!-- Indikator Live -->
            <div class="flex items-center gap-2 px-3 py-1 bg-gray-50 rounded-full border border-gray-200">
                <span class="relative flex h-3 w-3">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                </span>
                <span class="text-xs text-gray-500 font-mono">Live Update</span>
            </div>
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
                <p class="text-2xl font-bold" id="count-pending">0</p>
            </div>
            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                <h3 class="text-yellow-600 font-bold text-xs uppercase">Sedang Dimasak</h3>
                <p class="text-2xl font-bold" id="count-confirmed">0</p>
            </div>
            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                <h3 class="text-green-600 font-bold text-xs uppercase">Siap Saji</h3>
                <p class="text-2xl font-bold" id="count-served">0</p>
            </div>
        </div>

        <h2 class="font-bold text-gray-700 mb-4 text-lg">Daftar Pesanan Masuk</h2>

        <!-- Container Kartu Pesanan -->
        <div id="orders-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Kartu pesanan akan dimasukkan di sini oleh JavaScript -->
            <div class="col-span-full text-center py-20 text-gray-400 animate-pulse">
                Memuat data pesanan...
            </div>
        </div>

    </main>

    <!-- SCRIPT AJAX UTAMA -->
    <script>
        let previousPendingCount = 0;
        let isFirstLoad = true; // <--- VARIABLE BARU: Penanda load pertama
        const audio = document.getElementById("notifSound");

        const formatRupiah = (number) => {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
        }

        const formatDate = (dateString) => {
            if(!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) + ', ' + 
                   date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        }

        async function fetchOrders() {
            try {
                const response = await fetch("{{ route('api.orders') }}");
                const data = await response.json();

                if (data.success) {
                    renderOrders(data.orders);
                    updateStats(data.orders);
                    checkNewOrders(data.orders);
                }
            } catch (error) {
                console.error("Koneksi error:", error);
            }
        }

        function updateStats(orders) {
            document.getElementById('count-pending').innerText = orders.filter(o => o.status === 'pending').length;
            document.getElementById('count-confirmed').innerText = orders.filter(o => o.status === 'confirmed').length;
            document.getElementById('count-served').innerText = orders.filter(o => o.status === 'served').length;
        }

        // --- UPDATE LOGIKA BUNYI DI SINI ---
        function checkNewOrders(orders) {
            const currentPendingCount = orders.filter(o => o.status === 'pending').length;
            
            // Jika ini adalah pengecekan pertama setelah refresh/buka halaman
            if (isFirstLoad) {
                isFirstLoad = false; // Matikan status first load
                previousPendingCount = currentPendingCount; // Simpan jumlah saat ini
                return; // JANGAN BUNYI DULU
            }

            // Jika jumlah pending BERTAMBAH (artinya beneran ada yang baru masuk)
            if (currentPendingCount > previousPendingCount) {
                audio.play().catch(e => console.log("Audio perlu interaksi user dulu"));
            }
            
            // Update jumlah terakhir
            previousPendingCount = currentPendingCount;
        }

        function renderOrders(orders) {
            const container = document.getElementById('orders-container');
            
            if (orders.length === 0) {
                container.innerHTML = `<div class="col-span-full text-center py-20 bg-white rounded-xl shadow-sm text-gray-400">Belum ada pesanan aktif.</div>`;
                return;
            }

            let html = '';
            
            orders.forEach(order => {
                let statusColor = 'bg-gray-500';
                let actionButtons = '';

                if (order.status === 'pending') {
                    statusColor = 'bg-red-500 text-white';
                    actionButtons = `
                        <form action="/order/${order.id}/update" method="POST" class="flex gap-2 flex-1">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <button name="status" value="confirmed" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-bold text-sm transition shadow-sm">✅ Terima</button>
                            <button name="status" value="cancelled" class="px-3 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg font-bold text-sm transition" onclick="return confirm('Tolak?')">❌</button>
                        </form>`;
                } else if (order.status === 'confirmed') {
                    statusColor = 'bg-yellow-400 text-yellow-900';
                    actionButtons = `
                        <form action="/order/${order.id}/update" method="POST" class="flex gap-2 flex-1">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <button name="status" value="served" class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white py-2 rounded-lg font-bold text-sm transition shadow-sm">🔔 Siap Saji</button>
                        </form>`;
                } else if (order.status === 'served') {
                    statusColor = 'bg-green-500 text-white';
                    actionButtons = `
                        <form action="/order/${order.id}/update" method="POST" class="flex gap-2 flex-1">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <button name="status" value="paid" class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg font-bold text-sm transition shadow-sm">💰 Bayar</button>
                        </form>`;
                }

                let typeInfo = '';
                if(order.order_type === 'dine_in') typeInfo = '🍽️ Dine In';
                else if(order.order_type === 'take_away') typeInfo = '🥡 Take Away';
                else typeInfo = '📅 Booking';

                if(order.table) typeInfo += ` - <span class="font-bold text-gray-800">${order.table.table_number}</span>`;

                let bookingHtml = '';
                if (order.order_type === 'booking') {
                    if (order.booking_time) {
                        bookingHtml += `<div class="mt-1 bg-blue-50 border border-blue-100 text-blue-600 px-2 py-1 rounded text-xs inline-block font-bold">⏰ ${formatDate(order.booking_time)}</div>`;
                    }
                    if (order.customer_phone) {
                        bookingHtml += `
                        <div class="mt-1">
                            <a href="https://wa.me/62${order.customer_phone.replace(/^0/, '')}" target="_blank" class="bg-green-50 border border-green-100 text-green-600 px-2 py-1 rounded text-xs inline-flex items-center gap-1 font-bold hover:bg-green-100">
                                📱 ${order.customer_phone}
                            </a>
                        </div>`;
                    }
                }

                let itemsHtml = '';
                order.items.forEach(item => {
                    itemsHtml += `
                    <div class="flex justify-between py-1 border-b border-gray-200 last:border-0">
                        <span>${item.quantity}x ${item.menu ? item.menu.name : 'Item dihapus'}</span>
                        <span class="text-gray-500 font-mono">${formatRupiah(item.subtotal)}</span>
                    </div>`;
                });

                let printBtn = '';
                if(order.status !== 'pending') {
                    printBtn = `<a href="/order/${order.id}/print" target="_blank" class="ml-2 w-10 h-10 bg-gray-700 hover:bg-gray-800 text-white rounded-lg flex items-center justify-center transition shadow-sm">🖨️</a>`;
                }

                html += `
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative fade-in">
                    <div class="absolute top-0 right-0 px-3 py-1 text-xs font-bold rounded-bl-xl ${statusColor}">
                        ${order.status.toUpperCase()}
                    </div>
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-bold text-lg">${order.customer_name}</h3>
                                <p class="text-sm text-gray-500">${typeInfo}</p>
                                ${bookingHtml}
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded p-3 mb-4 text-sm max-h-40 overflow-y-auto">
                            ${itemsHtml}
                            <div class="flex justify-between font-bold mt-2 pt-2 border-t border-gray-300">
                                <span>Total</span>
                                <span>${formatRupiah(order.total_amount)}</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between mt-4">
                            ${actionButtons}
                            ${printBtn}
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-2 text-xs text-gray-400 flex justify-between border-t border-gray-100">
                        <span>#${order.id}</span>
                        <span>Update: ${new Date(order.updated_at).toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'})}</span>
                    </div>
                </div>`;
            });

            container.innerHTML = html;
        }

        fetchOrders();
        setInterval(fetchOrders, 5000);
    </script>
</body>
</html>