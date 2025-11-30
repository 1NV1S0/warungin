<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - Warungin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Tambah Markdown Parser sederhana biar tulisan AI rapi -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    
    <nav class="bg-white shadow px-6 py-4 flex justify-between items-center mb-6">
        <h1 class="text-xl font-bold text-orange-600">Laporan Keuangan 📊</h1>
        <a href="{{ route('admin.dashboard') }}" class="bg-gray-500 text-white px-4 py-2 rounded text-sm hover:bg-gray-600">Kembali ke Dashboard</a>
    </nav>

    <div class="max-w-5xl mx-auto px-4 pb-10">
        
        <!-- Filter & Summary -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
            <form action="{{ route('admin.reports.index') }}" method="GET" class="flex items-center gap-3 w-full md:w-auto">
                <label class="font-bold text-gray-600">Pilih Tanggal:</label>
                <input type="date" name="date" value="{{ $date }}" 
                       class="border rounded-lg p-2 focus:ring-2 focus:ring-orange-500"
                       onchange="this.form.submit()">
            </form>

            <div class="flex items-center gap-6 w-full md:w-auto justify-between md:justify-end">
                <!-- Tombol Request AI -->
                @if(!$orders->isEmpty())

                <!-- Tombol Export Excel (BARU) -->
                    <a href="{{ route('admin.reports.export', ['date' => $date]) }}" 
                       class="bg-green-600 text-white px-4 py-2 rounded-lg font-bold shadow hover:bg-green-700 flex items-center gap-2 transition transform active:scale-95 text-sm"
                       title="Download Excel">
                        📥 Export Excel
                    </a>

                <form action="{{ route('admin.reports.index') }}" method="GET">
                    <input type="hidden" name="date" value="{{ $date }}">
                    <input type="hidden" name="analyze_ai" value="1">
                    <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg font-bold shadow hover:bg-purple-700 flex items-center gap-2 transition transform active:scale-95">
                        ✨ Analisa AI
                    </button>
                </form>
                @endif

                <div class="text-right">
                    <p class="text-xs text-gray-400 uppercase">Omzet Tanggal Ini</p>
                    <p class="text-2xl font-bold text-green-600">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Bagian Hasil AI (Muncul cuma kalau ada hasil) -->
        @if(isset($aiAnalysis))
        <div class="bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200 rounded-xl p-6 mb-6 shadow-sm">
            <h3 class="font-bold text-purple-800 text-lg mb-2 flex items-center gap-2">
                🤖 Analisa Warungin AI
            </h3>
            <div class="prose prose-purple max-w-none text-gray-700 text-sm leading-relaxed" id="ai-content">
                <!-- Konten akan dirender oleh JS di bawah -->
            </div>
            <script>
                document.getElementById('ai-content').innerHTML = marked.parse(`{!! addslashes($aiAnalysis) !!}`);
            </script>
        </div>
        @endif

        <!-- Tabel Transaksi -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            @if($orders->isEmpty())
                <div class="p-10 text-center text-gray-400">
                    <p>Tidak ada transaksi lunas pada tanggal ini.</p>
                </div>
            @else
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="p-4">Jam</th>
                            <th class="p-4">Pelanggan</th>
                            <th class="p-4">Tipe</th>
                            <th class="p-4">Menu Dipesan</th>
                            <th class="p-4 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 text-sm font-mono text-gray-500">
                                {{ $order->created_at->format('H:i') }}
                            </td>
                            <td class="p-4">
                                <div class="font-bold">{{ $order->customer_name }}</div>
                                <div class="text-xs text-gray-400">{{ $order->customer_phone }}</div>
                            </td>
                            <td class="p-4">
                                @if($order->order_type == 'dine_in')
                                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">Dine In</span>
                                @elseif($order->order_type == 'take_away')
                                    <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">Take Away</span>
                                @else
                                    <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded">Booking</span>
                                @endif
                            </td>
                            <td class="p-4 text-sm text-gray-600">
                                <ul class="list-disc pl-4">
                                    @foreach($order->items as $item)
                                        <li>{{ $item->quantity }}x {{ $item->menu->name }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="p-4 text-right font-bold text-gray-700">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</body>
</html>