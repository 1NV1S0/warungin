<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Ambil filter tipe (daily, monthly, yearly), default 'daily'
        $type = $request->input('type', 'daily');
        
        // Ambil parameter waktu
        $date = $request->input('date', date('Y-m-d'));
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        // Query Dasar (Status Paid)
        $query = \App\Models\Order::where('status', 'paid');

        // Filter Berdasarkan Tipe
        if ($type == 'daily') {
            $query->whereDate('created_at', $date);
            $label = "Harian ($date)";
        } elseif ($type == 'monthly') {
            $query->whereMonth('created_at', $month)->whereYear('created_at', $year);
            $label = "Bulanan ($month-$year)";
        } elseif ($type == 'yearly') {
            $query->whereYear('created_at', $year);
            $label = "Tahunan ($year)";
        }

        // Eksekusi Query
        $orders = $query->with('items.menu')->orderBy('created_at', 'desc')->get();

        $totalIncome = $orders->sum('total_amount');
        $totalTransactions = $orders->count();

        // Logika AI (Sama seperti sebelumnya)
        $aiAnalysis = null;
        if ($request->has('analyze_ai') && $orders->isNotEmpty()) {
            $aiAnalysis = $this->askGemini($orders, $label);
        }

        return view('admin.reports.index', compact('orders', 'totalIncome', 'totalTransactions', 'type', 'date', 'month', 'year', 'aiAnalysis'));
    }

    // UPDATE JUGA FUNGSI EXPORT (Agar mengikuti filter)
    public function export(Request $request)
    {
        $type = $request->input('type', 'daily');
        $date = $request->input('date', date('Y-m-d'));
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        $query = \App\Models\Order::where('status', 'paid')->with('items.menu');

        if ($type == 'daily') {
            $query->whereDate('created_at', $date);
            $filename = "Laporan_Harian_$date.csv";
        } elseif ($type == 'monthly') {
            $query->whereMonth('created_at', $month)->whereYear('created_at', $year);
            $filename = "Laporan_Bulanan_$month-$year.csv";
        } elseif ($type == 'yearly') {
            $query->whereYear('created_at', $year);
            $filename = "Laporan_Tahunan_$year.csv";
        }

        $orders = $query->orderBy('created_at', 'asc')->get();

        return response()->streamDownload(function () use ($orders) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['No. Order', 'Tanggal', 'Pelanggan', 'Tipe', 'Menu', 'Total']);

            foreach ($orders as $order) {
                $menuList = [];
                foreach ($order->items as $item) {
                    $menuName = $item->menu ? $item->menu->name : 'Menu Dihapus';
                    $menuList[] = $item->quantity . "x " . $menuName;
                }
                
                fputcsv($handle, [
                    '#' . $order->id,
                    $order->created_at->format('Y-m-d H:i'), // Format tanggal lengkap
                    $order->customer_name,
                    $order->order_type,
                    implode(", ", $menuList),
                    $order->total_amount
                ]);
            }
            fputcsv($handle, []);
            fputcsv($handle, ['', '', '', '', 'TOTAL PENDAPATAN', $orders->sum('total_amount')]);
            fclose($handle);
        }, $filename);
    }

    // Fungsi AI (Biarkan tetap sama, saya singkat biar gak kepanjangan)
    private function askGemini($orders, $date)
    {
        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) return "API Key Error";

        $summary = "LAPORAN PENJUALAN HARIAN - WARUNGIN\nTanggal: $date\n";
        $summary .= "Total Transaksi: " . $orders->count() . "\n";
        $summary .= "Total Omzet: Rp " . number_format($orders->sum('total_amount'), 0, ',', '.') . "\nDetail:\n";

        $soldItems = [];
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $name = $item->menu->name;
                if (!isset($soldItems[$name])) $soldItems[$name] = 0;
                $soldItems[$name] += $item->quantity;
            }
        }
        foreach ($soldItems as $name => $qty) {
            $summary .= "- $name: $qty porsi\n";
        }

        $prompt = "Sebagai Konsultan Bisnis F&B, analisis data ini:\n$summary\nBerikan evaluasi menu terlaris/kurang laris, dan 3 strategi konkret singkat.";

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}", [
                'contents' => [['parts' => [['text' => $prompt]]]]
            ]);
            return $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? 'Gagal analisa.';
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }
}