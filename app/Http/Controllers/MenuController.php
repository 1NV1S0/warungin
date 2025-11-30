<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table; 

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::where('is_available', true)
                    ->orderBy('category', 'asc')
                    ->get();

        $groupedMenus = $menus->groupBy('category');

        return view('home', compact('groupedMenus'));
    }

    // FUNGSI BARU: Tambah ke Keranjang
    public function addToCart($id)
    {
        $menu = Menu::findOrFail($id);

        // Ambil keranjang saat ini dari session (kalau kosong, buat array baru)
        $cart = session()->get('cart', []);

        // Cek: Apakah menu ini sudah ada di keranjang?
        if(isset($cart[$id])) {
            // Kalau sudah ada, tambah jumlahnya (quantity + 1)
            $cart[$id]['quantity']++;
        } else {
            // Kalau belum ada, masukkan sebagai item baru
            $cart[$id] = [
                "name" => $menu->name,
                "quantity" => 1,
                "price" => $menu->price,
                "image" => $menu->image_path
            ];
        }

        // Simpan kembali ke session
        session()->put('cart', $cart);

        // Kembali ke halaman menu dengan pesan sukses
        return redirect()->back()->with('success', 'Menu berhasil ditambahkan!');
    }

    // FUNGSI BARU: Kosongkan Keranjang (Reset)
    public function clearCart()
    {
        session()->forget('cart');
        return redirect()->back();
    }

    // --- FUNGSI BARU DI BAWAH INI ---

    // 1. Tampilkan Halaman Keranjang
    public function viewCart()
    {
        // Ambil data meja yang kosong buat pilihan Dine In
        $tables = Table::where('status', 'available')->get();
        return view('cart', compact('tables'));
    }

    // 2. Proses Checkout (Simpan ke Database)
    public function checkout(Request $request)
    {
        // Validasi Input
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:15',
            'order_type' => 'required|in:dine_in,take_away,booking',
            'booking_time' => 'required_if:order_type,booking', // Wajib kalau booking
            'table_id' => 'required_if:order_type,dine_in', // Wajib kalau makan di tempat
        ]);

        $cart = session('cart');

        // Cek kalau keranjang kosong
        if(!$cart) {
            return redirect()->back()->with('error', 'Keranjang kosong!');
        }

        // Hitung Total
        $totalAmount = 0;
        foreach($cart as $id => $details) {
            $totalAmount += $details['price'] * $details['quantity'];
        }

        // Mulai Transaksi Database (Biar aman, kalau error satu batal semua)
        DB::beginTransaction();

        try {
            // 1. Buat Order Baru
            $order = Order::create([
                'guest_token' => session()->getId(), // Pakai ID Session browser sebagai token
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'table_id' => $request->table_id, // Nullable kalau take away
                'total_amount' => $totalAmount,
                'order_type' => $request->order_type,
                'status' => 'pending', // Status awal Pending (tunggu konfirmasi kasir)
                'booking_time' => $request->booking_time,
            ]);

            // 2. Masukkan Item ke order_items & Kurangi Stok
            foreach($cart as $id => $details) {
                $menu = Menu::find($id);
                
                // Cek stok lagi biar aman
                if($menu->stock < $details['quantity']) {
                    DB::rollBack(); // Batalkan semua
                    return redirect()->back()->with('error', 'Stok ' . $menu->name . ' tidak mencukupi!');
                }

                // Simpan Item
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $id,
                    'quantity' => $details['quantity'],
                    'price_at_time' => $details['price'],
                    'subtotal' => $details['price'] * $details['quantity']
                ]);

                // Kurangi Stok Menu
                $menu->decrement('stock', $details['quantity']);
            }

            DB::commit(); // Simpan permanen

            // 3. Bersihkan Keranjang
            session()->forget('cart');

            // Redirect ke halaman sukses (nanti kita buat)
            return redirect()->route('home')->with('success', 'Pesanan Berhasil Dibuat! Silakan tunggu konfirmasi.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}

