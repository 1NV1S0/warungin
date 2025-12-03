<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    // 1. Tampilkan Daftar Meja
    public function index()
    {
        $tables = Table::orderBy('table_number', 'asc')->get();
        return view('admin.tables.index', compact('tables'));
    }

    // 2. Tambah Meja Baru
    public function store(Request $request)
    {
        $request->validate([
            'table_number' => 'required|unique:tables,table_number',
            'capacity' => 'required|integer|min:1',
        ], [
            'table_number.unique' => 'Nomor meja ini sudah ada.',
        ]);

        Table::create([
            'table_number' => $request->table_number,
            'capacity' => $request->capacity,
            'status' => 'available' // Default kosong
        ]);

        return redirect()->back()->with('success', 'Meja berhasil ditambahkan!');
    }

    // 3. Hapus Meja
    public function destroy(Table $table)
    {
        // Cek apakah meja sedang dipakai?
        if ($table->status == 'occupied') {
            return redirect()->back()->with('error', 'Gagal! Meja sedang dipakai pelanggan.');
        }

        $table->delete();
        return redirect()->back()->with('success', 'Meja berhasil dihapus!');
    }
}