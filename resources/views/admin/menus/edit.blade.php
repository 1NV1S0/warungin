<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-lg">
        <h2 class="text-xl font-bold mb-4">Edit Menu: {{ $menu->name }}</h2>

        <form action="{{ route('admin.menus.update', $menu->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT') <!-- Penting untuk Update -->
            
            <div class="mb-4">
                <label class="block text-sm font-bold mb-1">Nama Menu</label>
                <input type="text" name="name" value="{{ $menu->name }}" class="w-full border rounded p-2" required>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-bold mb-1">Harga (Rp)</label>
                    <input type="number" name="price" value="{{ $menu->price }}" class="w-full border rounded p-2" required>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Stok</label>
                    <input type="number" name="stock" value="{{ $menu->stock }}" class="w-full border rounded p-2" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold mb-1">Kategori</label>
                <select name="category" class="w-full border rounded p-2">
                    <option value="makanan" {{ $menu->category == 'makanan' ? 'selected' : '' }}>Makanan</option>
                    <option value="minuman" {{ $menu->category == 'minuman' ? 'selected' : '' }}>Minuman</option>
                    <option value="snack" {{ $menu->category == 'snack' ? 'selected' : '' }}>Snack</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold mb-1">Deskripsi</label>
                <textarea name="description" class="w-full border rounded p-2 h-20">{{ $menu->description }}</textarea>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold mb-1">Ganti Foto (Opsional)</label>
                <input type="file" name="image" class="w-full text-sm text-gray-500 mb-2">
                @if($menu->image_path)
                    <p class="text-xs text-gray-400">Foto saat ini:</p>
                    <img src="{{ asset('storage/'.$menu->image_path) }}" class="h-20 rounded border mt-1">
                @endif
            </div>

            <div class="flex gap-2">
                <a href="{{ route('admin.menus.index') }}" class="flex-1 bg-gray-200 text-center py-2 rounded text-gray-700 font-bold">Batal</a>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded font-bold hover:bg-blue-700">Update Menu</button>
            </div>
        </form>
    </div>
</body>
</html>