<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Pesanan - Warungin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-50 text-gray-800">

    <!-- Header Sederhana -->
    <div class="bg-white shadow-sm p-4 sticky top-0 z-50">
        <div class="max-w-md mx-auto flex items-center gap-3">
            <a href="{{ route('home') }}" class="text-gray-600 hover:text-orange-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="text-xl font-bold">Konfirmasi Pesanan</h1>
        </div>
    </div>

    <main class="max-w-md mx-auto px-4 py-6 pb-24">

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if(session('cart'))
            <!-- Daftar Menu -->
            <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
                <h3 class="font-bold text-gray-500 mb-3 text-sm">ITEM DIPESAN</h3>
                @php $total = 0; @endphp
                @foreach(session('cart') as $id => $details)
                    @php $total += $details['price'] * $details['quantity'] @endphp
                    <div class="flex justify-between items-center py-3 border-b border-gray-100 last:border-0">
                        <div class="flex gap-3 items-center">
                            <div class="bg-orange-100 text-orange-600 font-bold w-8 h-8 rounded flex items-center justify-center text-sm">
                                {{ $details['quantity'] }}x
                            </div>
                            <div>
                                <h4 class="font-bold text-sm">{{ $details['name'] }}</h4>
                                <p class="text-xs text-gray-400">@ Rp {{ number_format($details['price'], 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <p class="font-bold text-sm">Rp {{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }}</p>
                    </div>
                @endforeach
                <div class="mt-4 flex justify-between items-center pt-2">
                    <span class="font-bold text-gray-600">Total Harga</span>
                    <span class="font-bold text-xl text-orange-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Form Data Diri -->
            <form action="{{ route('checkout') }}" method="POST" class="bg-white rounded-xl shadow-sm p-4">
                @csrf
                <h3 class="font-bold text-gray-500 mb-4 text-sm">DATA PEMESAN</h3>

                <div class="mb-3">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Lengkap</label>
                    <input type="text" name="customer_name" required placeholder="Contoh: Budi Santoso"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 p-2 border">
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">No. WhatsApp / HP</label>
                    <input type="number" name="customer_phone" required placeholder="0812xxxxx"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 p-2 border">
                </div>

                <!-- Pilihan Tipe Pesanan -->
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Mau makan gimana?</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="order_type" value="dine_in" class="peer sr-only" onchange="toggleOrderType()">
                            <div class="rounded-lg border border-gray-300 p-3 text-center hover:bg-gray-50 peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-checked:text-orange-600 transition">
                                <span class="block text-sm font-bold">Makan Sini</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="order_type" value="take_away" class="peer sr-only" onchange="toggleOrderType()">
                            <div class="rounded-lg border border-gray-300 p-3 text-center hover:bg-gray-50 peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-checked:text-orange-600 transition">
                                <span class="block text-sm font-bold">Bungkus</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="order_type" value="booking" class="peer sr-only" onchange="toggleOrderType()">
                            <div class="rounded-lg border border-gray-300 p-3 text-center hover:bg-gray-50 peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-checked:text-orange-600 transition">
                                <span class="block text-sm font-bold">Booking</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Input Khusus Dine In (Pilih Meja) -->
                <div id="dineInInput" class="hidden mb-4 bg-gray-50 p-3 rounded-lg border border-dashed border-gray-300">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pilih Meja</label>
                    <select name="table_id" class="w-full border-gray-300 rounded-lg p-2 border">
                        <option value="">-- Pilih Meja Kosong --</option>
                        @foreach($tables as $table)
                            <option value="{{ $table->id }}">{{ $table->table_number }} ({{ $table->capacity }} org)</option>
                        @endforeach
                    </select>
                </div>

                <!-- Input Khusus Booking (Pilih Jam) -->
                <div id="bookingInput" class="hidden mb-4 bg-gray-50 p-3 rounded-lg border border-dashed border-gray-300">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Mau datang jam berapa?</label>
                    <input type="datetime-local" name="booking_time" 
                           class="w-full border-gray-300 rounded-lg p-2 border">
                </div>

                <button type="submit" class="w-full bg-orange-600 text-white font-bold py-3 rounded-xl shadow-lg hover:bg-orange-700 transition">
                    Pesan Sekarang
                </button>
            </form>

        @else
            <!-- Tampilan Kalau Keranjang Kosong -->
            <div class="text-center py-20">
                <div class="bg-gray-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-600">Keranjang Kosong</h2>
                <p class="text-gray-400 mb-6">Kamu belum memilih menu apapun.</p>
                <a href="{{ route('home') }}" class="inline-block bg-orange-500 text-white px-6 py-2 rounded-full font-bold">Mulai Pesan</a>
            </div>
        @endif

    </main>

    <!-- Script Sederhana untuk Ganti Form -->
    <script>
        function toggleOrderType() {
            const types = document.getElementsByName('order_type');
            let selectedValue;
            for (const type of types) {
                if (type.checked) {
                    selectedValue = type.value;
                    break;
                }
            }

            const dineInInput = document.getElementById('dineInInput');
            const bookingInput = document.getElementById('bookingInput');

            // Reset
            dineInInput.classList.add('hidden');
            bookingInput.classList.add('hidden');

            if (selectedValue === 'dine_in') {
                dineInInput.classList.remove('hidden');
            } else if (selectedValue === 'booking') {
                bookingInput.classList.remove('hidden');
            }
        }
    </script>
</body>
</html>