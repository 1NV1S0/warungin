<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Menu Baru</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-lg">
        <h2 class="text-xl font-bold mb-4">Tambah Menu Baru</h2>

        <!-- Cek apakah ada error validasi -->
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Penting: enctype="multipart/form-data" wajib ada buat upload file -->
        <form action="{{ route('admin.menus.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-bold mb-1">Nama Menu</label>
                <input type="text" name="name" class="w-full border rounded p-2" required>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-bold mb-1">Harga (Rp)</label>
                    <input type="number" name="price" class="w-full border rounded p-2" required>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Stok Awal</label>
                    <input type="number" name="stock" class="w-full border rounded p-2" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold mb-1">Kategori</label>
                <select name="category" class="w-full border rounded p-2">
                    <option value="makanan">Makanan</option>
                    <option value="minuman">Minuman</option>
                    <option value="snack">Snack</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold mb-1">Deskripsi</label>
                <textarea name="description" class="w-full border rounded p-2 h-20"></textarea>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold mb-1">Foto Menu</label>
                <input type="file" name="image" class="w-full text-sm text-gray-500">
            </div>

            <div class="flex gap-2">
                <a href="{{ route('admin.menus.index') }}" class="flex-1 bg-gray-200 text-center py-2 rounded text-gray-700 font-bold">Batal</a>
                <button type="submit" class="flex-1 bg-orange-600 text-white py-2 rounded font-bold hover:bg-orange-700">Simpan Menu</button>
            </div>
        </form>
    </div>
</body>
</html>