<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class AuthController extends Controller
{
    // 1. Tampilkan Form Login
    public function showLogin()
    {
        return view('login');
    }

    // 2. Proses Login (LOGIKA REDIRECT ADA DI SINI)
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // --- CEK ROLE & ARAHKAN KE TEMPAT YANG BENAR ---
            
            $role = Auth::user()->role;

            // Jika Bos atau Admin -> Masuk ke Kantor (Dashboard Admin)
            if ($role === 'admin' || $role === 'owner') {
                return redirect()->route('admin.dashboard');
            }

            // Jika Kasir -> Masuk ke Dapur (Dashboard Kasir)
            // Defaultnya ke dashboard biasa
            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah!',
        ]);
    }

    // 3. Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    // 4. Dashboard Kasir (Menampilkan Pesanan)
    public function dashboard()
    {
        $orders = Order::whereNotIn('status', ['paid', 'cancelled'])
                       ->orderBy('created_at', 'desc')
                       ->with(['items.menu', 'table']) 
                       ->get();

        return view('dashboard', compact('orders'));
    }
    
    // 5. Update Status Pesanan (DENGAN LOGIKA MEJA OTOMATIS)
    public function updateStatus(Request $request, $id)
    {
        $order = Order::with('table')->findOrFail($id);
        $oldStatus = $order->status;
        $newStatus = $request->status;

        // Update status order
        $order->status = $newStatus;
        $order->save();

        // --- LOGIKA LEPAS MEJA ---
        // Jika meja ada (cek objeknya langsung) DAN status baru adalah 'paid' atau 'cancelled'
        // Maka meja harus dikosongkan kembali (available)
        // PERBAIKAN: Tambahkan () pada ->table() saat update
        // Ini memaksa Laravel memanggil Relasi (Query Builder), bukan properti nama tabel
        if ($order->table && in_array($newStatus, ['paid', 'cancelled'])) {
            $order->table()->update(['status' => 'available']);
        }
        
        // Catatan: Kalau statusnya masih 'confirmed' atau 'served', meja TETAP 'occupied'
        // -------------------------
        
        return redirect()->back()->with('success', "Status pesanan diperbarui menjadi $newStatus!");
    }


    // 6. Cetak Struk (TAMBAHAN BARU)
    public function printReceipt($id)
    {
        // Ambil data order beserta detail item, meja, dan kasirnya
        $order = \App\Models\Order::with(['items.menu', 'table', 'cashier'])->findOrFail($id);
        
        return view('receipt', compact('order'));
    }

    // 7. API untuk AJAX (Ambil data pesanan terbaru)
    public function getNewOrders()
    {
        // Ambil pesanan yang belum selesai (pending, confirmed, served)
        // Urutkan dari yang terbaru
        $orders = \App\Models\Order::whereNotIn('status', ['paid', 'cancelled'])
                       ->orderBy('created_at', 'desc')
                       ->with(['items.menu', 'table']) // Load relasi biar lengkap
                       ->get();

        // Kembalikan dalam format JSON
        return response()->json([
            'success' => true,
            'orders' => $orders
        ]);
    }
}