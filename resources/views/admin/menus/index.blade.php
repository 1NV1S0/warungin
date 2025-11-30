<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Menu - Warungin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-5xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-700">Manajemen Menu</h1>
            <div class="flex gap-2">
                <a href="{{ route('admin.menus.trash') }}" class="bg-red-100 text-red-600 px-4 py-2 rounded hover:bg-red-200 font-bold text-sm">
                🗑️ Lihat Sampah
                </a>
                <a href="{{ route('admin.dashboard') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Kembali ke Dashboard</a>
                <a href="{{ route('admin.menus.create') }}" class="bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700">+ Tambah Menu</a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
        @endif

        

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 text-sm uppercase">
                        <th class="p-4 border-b">Gambar</th>
                        <th class="p-4 border-b">Nama Menu</th>
                        <th class="p-4 border-b">Kategori</th>
                        <th class="p-4 border-b">Harga</th>
                        <th class="p-4 border-b text-center">Stok</th>
                        <th class="p-4 border-b text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($menus as $menu)
                    <tr class="hover:bg-gray-50">
                        <td class="p-4">
                            @if($menu->image_path)
                                <img src="{{ asset('storage/'.$menu->image_path) }}" class="w-12 h-12 object-cover rounded">
                            @else
                                <span class="text-gray-400 text-xs">No Img</span>
                            @endif
                        </td>
                        <td class="p-4 font-bold">{{ $menu->name }}</td>
                        <td class="p-4"><span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">{{ $menu->category }}</span></td>
                        <td class="p-4">Rp {{ number_format($menu->price, 0, ',', '.') }}</td>
                        <td class="p-4 text-center">
                            <span class="font-bold {{ $menu->stock < 5 ? 'text-red-500' : 'text-green-600' }}">
                                {{ $menu->stock }}
                            </span>
                        </td>
                        <td class="p-4 flex justify-center gap-2">
                            <a href="{{ route('admin.menus.edit', $menu->id) }}" class="text-blue-500 hover:underline">Edit</a>
                            <form action="{{ route('admin.menus.destroy', $menu->id) }}" method="POST" onsubmit="return confirm('Hapus menu ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>