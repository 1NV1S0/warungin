<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. Data Hari Ini
        $today = Carbon::today();
        
        $todaysIncome = Order::whereDate('created_at', $today)
                             ->where('status', 'paid')
                             ->sum('total_amount');
                             
        $todaysOrders = Order::whereDate('created_at', $today)->count();

        // 2. Cek Stok Kritis (Menu yang sisa dikit)
        $lowStockMenus = Menu::where('stock', '<', 5)
                             ->where('is_available', true)
                             ->get();

        // 3. Pesanan Terbaru (5 transaksi terakhir)
        $recentOrders = Order::orderBy('created_at', 'desc')->take(5)->get();

        return view('admin.dashboard', compact('todaysIncome', 'todaysOrders', 'lowStockMenus', 'recentOrders'));
    }
}