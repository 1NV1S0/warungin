<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tong Sampah Menu - Warungin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6 min-h-screen">
    <div class="max-w-5xl mx-auto">
        
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-700 flex items-center gap-2">
                    <span>🗑️</span> Tong Sampah Menu
                </h1>
                <p class="text-sm text-gray-500">Daftar menu yang telah dihapus (diarsipkan).</p>
            </div>
            <a href="{{ route('admin.menus.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition shadow-sm font-bold text-sm">
                ← Kembali ke Daftar Menu
            </a>
        </div>

        <!-- Tabel Data -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
            <table class="w-full text-left border-collapse">
                <thead class="bg-red-50 text-red-700 text-xs uppercase font-bold tracking-wider">
                    <tr>
                        <th class="p-4 border-b border-red-100">Nama Menu</th>
                        <th class="p-4 border-b border-red-100">Kategori</th>
                        <th class="p-4 border-b border-red-100">Dihapus Pada</th>
                        <th class="p-4 border-b border-red-100 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($menus as $menu)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4 font-bold text-gray-600">
                            {{ $menu->name }}
                        </td>
                        <td class="p-4">
                            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs">
                                {{ ucfirst($menu->category) }}
                            </span>
                        </td>
                        <td class="p-4 text-gray-500">
                            {{ $menu->deleted_at->format('d M Y, H:i') }} WIB
                        </td>
                        <td class="p-4 text-center">
                            <!-- Tombol Restore -->
                            <a href="{{ route('admin.menus.restore', $menu->id) }}" 
                               onclick="return confirm('Yakin ingin mengembalikan menu ini?')"
                               class="inline-flex items-center gap-1 bg-green-100 text-green-700 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-green-200 border border-green-200 transition">
                                ♻️ Pulihkan
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-10 text-center flex flex-col items-center justify-center text-gray-400">
                            <span class="text-4xl mb-2">✨</span>
                            <p class="font-medium">Tong sampah bersih! Tidak ada menu yang dihapus.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
    </div>
</body>
</html>