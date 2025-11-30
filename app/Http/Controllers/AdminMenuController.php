<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminMenuController extends Controller
{
    // 1. Tampilkan Daftar Menu
    public function index()
    {
        $menus = Menu::orderBy('created_at', 'desc')->get();
        return view('admin.menus.index', compact('menus'));
    }

    // 2. Tampilkan Form Tambah Menu
    public function create()
    {
        return view('admin.menus.create');
    }

    // 3. Simpan Menu Baru (DENGAN VALIDASI)
    public function store(Request $request)
    {
        // Validasi di awal
        $request->validate([
            // unique:menus,name artinya: Cek tabel 'menus', kolom 'name' harus unik
            'name' => 'required|unique:menus,name', 
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'category' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            // Pesan Error Custom (Opsional, biar lebih ramah)
            'name.unique' => 'Ups! Nama menu ini sudah ada. Mohon gunakan nama lain.',
        ]);

        // Kalau lolos validasi, kode di bawah ini baru jalan
        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('menu-images', 'public');
        }

        Menu::create($data);

        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil ditambahkan!');
    }

    // 4. Tampilkan Form Edit
    public function edit(Menu $menu)
    {
        return view('admin.menus.edit', compact('menu'));
    }

    // 5. Update Menu (DENGAN VALIDASI JUGA)
    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            // unique:menus,name,ID_MENU artinya: Cek unik, TAPI abaikan menu ini sendiri
            // (Supaya kalau cuma edit harga tapi namanya tetap, tidak dianggap error)
            'name' => 'required|unique:menus,name,' . $menu->id,
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'name.unique' => 'Nama menu ini sudah dipakai menu lain.',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        if ($request->hasFile('image')) {
            if ($menu->image_path) {
                Storage::disk('public')->delete($menu->image_path);
            }
            $data['image_path'] = $request->file('image')->store('menu-images', 'public');
        }

        $menu->update($data);

        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil diupdate!');
    }

    // 6. Hapus Menu
    public function destroy(Menu $menu)
    {
        // Pakai soft delete yang sebelumnya kita bahas
        $menu->delete();
        return redirect()->back()->with('success', 'Menu berhasil dihapus (diarsipkan)!');
    }

    // 7. Lihat Menu yang Dihapus (Tong Sampah)
    public function trash()
    {
        // onlyTrashed() hanya mengambil data yang sudah dihapus (soft delete)
        $menus = Menu::onlyTrashed()->get();
        return view('admin.menus.trash', compact('menus'));
    }

    // 8. Kembalikan Menu (Restore)
    public function restore($id)
    {
        // Cari menu di tong sampah berdasarkan ID
        // withTrashed() artinya cari di semua data (termasuk yang dihapus)
        $menu = Menu::withTrashed()->findOrFail($id);
        
        // Kembalikan ke daftar aktif
        $menu->restore();

        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil dipulihkan!');
    }

}