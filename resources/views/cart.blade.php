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

        <!-- ALERT ERROR UMUM -->
        @if(session('error'))
            <div class="bg-red-500 text-white px-4 py-4 rounded-xl shadow-lg mb-6 flex items-start gap-3 animate-pulse">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <h4 class="font-bold text-lg">Gagal Memesan!</h4>
                    <p class="text-sm">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- ALERT SUKSES -->
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm mb-6 flex justify-between items-center">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-green-700 font-bold">&times;</button>
            </div>
        @endif

        @if(session('cart'))
            <!-- Daftar Menu -->
            <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-bold text-gray-500 text-sm">ITEM DIPESAN</h3>
                    <a href="{{ route('clear_cart') }}" class="text-xs text-red-500 underline hover:text-red-700">Hapus Semua</a>
                </div>

                @php $total = 0; @endphp
                @foreach(session('cart') as $id => $details)
                    @php $total += $details['price'] * $details['quantity']; @endphp
                    
                    <div class="flex justify-between items-center py-3 border-b border-gray-100 last:border-0 group">
                        <div class="flex gap-3 items-center">
                            <!-- Gambar Kecil -->
                            <div class="w-12 h-12 bg-gray-100 rounded-lg bg-cover bg-center flex-shrink-0"
                                 style="background-image: url('{{ $details['image'] ? asset('storage/'.$details['image']) : 'https://placehold.co/100x100' }}')">
                            </div>
                            
                            <div>
                                <h4 class="font-bold text-sm line-clamp-1">{{ $details['name'] }}</h4>
                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                    <span class="bg-gray-100 px-2 py-0.5 rounded text-gray-700 font-bold">{{ $details['quantity'] }}x</span>
                                    <span>@ Rp {{ number_format($details['price'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <p class="font-bold text-sm text-gray-700">Rp {{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }}</p>
                            
                            <!-- TOMBOL HAPUS PER ITEM -->
                            <a href="{{ route('remove_from_cart', $id) }}" class="p-2 bg-red-50 rounded-full text-red-500 hover:bg-red-100 transition" title="Hapus Item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
                
                <div class="mt-4 flex justify-between items-center pt-2 border-t border-gray-100">
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

                <!-- Input No HP (Muncul Jika Booking) -->
                <div class="mb-4 hidden" id="phoneInputContainer">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">No. WhatsApp / HP (Wajib untuk Booking)</label>
                    <input type="number" name="customer_phone" id="phoneInput" placeholder="0812xxxxx"
                           class="w-full border rounded-lg p-2 border @error('customer_phone') border-red-500 bg-red-50 @enderror">
                    
                    @error('customer_phone')
                        <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                    @enderror
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
                    <select name="table_id" class="w-full border-gray-300 rounded-lg p-2 border @error('table_id') border-red-500 bg-red-50 @enderror">
                        <option value="">-- Pilih Meja Kosong --</option>
                        @foreach($tables as $table)
                            <option value="{{ $table->id }}">{{ $table->table_number }} ({{ $table->capacity }} org)</option>
                        @endforeach
                    </select>
                    
                    @error('table_id')
                        <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Input Khusus Booking (Pilih Jam) -->
                <div id="bookingInput" class="hidden mb-4 bg-gray-50 p-3 rounded-lg border border-dashed border-gray-300">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Mau datang jam berapa?</label>
                    <input type="datetime-local" name="booking_time" 
                           class="w-full border-gray-300 rounded-lg p-2 border @error('booking_time') border-red-500 bg-red-50 @enderror">
                    
                    @error('booking_time')
                        <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                    @enderror
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

    <!-- Script Logika Tampilan -->
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
            const phoneInputContainer = document.getElementById('phoneInputContainer');
            const phoneInputField = document.getElementById('phoneInput');

            // Reset
            dineInInput.classList.add('hidden');
            bookingInput.classList.add('hidden');
            phoneInputContainer.classList.add('hidden');
            phoneInputField.required = false;

            if (selectedValue === 'dine_in') {
                dineInInput.classList.remove('hidden');
            } else if (selectedValue === 'booking') {
                bookingInput.classList.remove('hidden');
                phoneInputContainer.classList.remove('hidden');
                phoneInputField.required = true;
            }
        }
    </script>
</body>
</html>