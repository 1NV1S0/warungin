<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Meja - Warungin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">

    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Manajemen Meja 🪑</h1>
            <a href="{{ route('admin.dashboard') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 font-bold text-sm">
                ← Kembali ke Dashboard
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- KOLOM KIRI: DAFTAR MEJA (Lebar 2/3) -->
            <div class="md:col-span-2">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-gray-100 bg-gray-50 font-bold text-gray-700">
                        Daftar Meja Warungin
                    </div>
                    
                    @if(session('error'))
                        <div class="m-4 bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <table class="w-full text-left">
                        <thead class="bg-gray-100 text-gray-600 text-xs uppercase">
                            <tr>
                                <th class="p-4">Nomor Meja</th>
                                <th class="p-4 text-center">Kapasitas</th>
                                <th class="p-4 text-center">Status</th>
                                <th class="p-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach($tables as $table)
                            <tr class="hover:bg-gray-50">
                                <td class="p-4 font-bold text-gray-800">{{ $table->table_number }}</td>
                                <td class="p-4 text-center">{{ $table->capacity }} Orang</td>
                                <td class="p-4 text-center">
                                    @if($table->status == 'available')
                                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-bold">Kosong</span>
                                    @elseif($table->status == 'occupied')
                                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs font-bold">Terisi</span>
                                    @else
                                        <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full text-xs font-bold">Reserved</span>
                                    @endif
                                </td>
                                <td class="p-4 text-right">
                                    <form action="{{ route('admin.tables.destroy', $table->id) }}" method="POST" onsubmit="return confirm('Hapus meja ini?')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-500 hover:text-red-700 font-bold text-xs underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- KOLOM KANAN: FORM TAMBAH (Lebar 1/3) -->
            <div>
                <div class="bg-white rounded-xl shadow-sm p-6 sticky top-6">
                    <h3 class="font-bold text-lg text-gray-800 mb-4">Tambah Meja Baru</h3>
                    
                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-200 text-green-700 px-3 py-2 rounded text-sm">
                            ✅ {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.tables.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-600 mb-1">Nomor/Nama Meja</label>
                            <input type="text" name="table_number" placeholder="Contoh: Meja 10 atau VIP A" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-orange-500" required>
                            @error('table_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-600 mb-1">Kapasitas (Orang)</label>
                            <input type="number" name="capacity" value="4" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-orange-500" required>
                        </div>

                        <button type="submit" class="w-full bg-orange-600 text-white font-bold py-2 rounded-lg hover:bg-orange-700 transition shadow-lg shadow-orange-200">
                            + Simpan Meja
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

</body>
</html>