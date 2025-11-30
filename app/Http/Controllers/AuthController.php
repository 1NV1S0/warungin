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
    
    // 5. Update Status Pesanan
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();
        
        return redirect()->back()->with('success', 'Status pesanan diperbarui!');
    }

    // 6. Cetak Struk (TAMBAHAN BARU)
    public function printReceipt($id)
    {
        // Ambil data order beserta detail item, meja, dan kasirnya
        $order = \App\Models\Order::with(['items.menu', 'table', 'cashier'])->findOrFail($id);
        
        return view('receipt', compact('order'));
    }
}