<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));

        // Ambil data penjualan
        $orders = Order::whereDate('created_at', $date)
                       ->where('status', 'paid')
                       ->with('items.menu')
                       ->orderBy('created_at', 'desc')
                       ->get();

        $totalIncome = $orders->sum('total_amount');
        $totalTransactions = $orders->count();

        // Siapkan data untuk view
        $aiAnalysis = null;
        
        // JIKA tombol "Analisa dengan AI" ditekan
        if ($request->has('analyze_ai') && $orders->isNotEmpty()) {
            $aiAnalysis = $this->askGemini($orders, $date);
        }

        return view('admin.reports.index', compact('orders', 'totalIncome', 'totalTransactions', 'date', 'aiAnalysis'));
    }

    // --- FUNGSI BARU: EXPORT EXCEL (CSV) ---
    public function export(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));

        // Ambil data yang sama
        $orders = Order::whereDate('created_at', $date)
                       ->where('status', 'paid')
                       ->with('items.menu')
                       ->orderBy('created_at', 'asc') // Urutkan dari pagi ke malam
                       ->get();

        $filename = "Laporan_Warungin_$date.csv";

        // Buat stream download
        return response()->streamDownload(function () use ($orders, $date) {
            $handle = fopen('php://output', 'w');

            // 1. Tulis Judul Kolom (Header)
            fputcsv($handle, [
                'No. Order',
                'Jam',
                'Nama Pelanggan',
                'Tipe Pesanan',
                'Menu Dipesan',
                'Total Harga'
            ]);

            // 2. Isi Datanya
            foreach ($orders as $order) {
                // Rangkum menu jadi satu string (misal: "2x Nasi Goreng, 1x Es Teh")
                $menuList = [];
                foreach ($order->items as $item) {
                    $menuList[] = $item->quantity . "x " . $item->menu->name;
                }
                $menuString = implode(", ", $menuList);

                fputcsv($handle, [
                    '#' . $order->id,
                    $order->created_at->format('H:i'),
                    $order->customer_name,
                    strtoupper($order->order_type), // DINE_IN / TAKE_AWAY
                    $menuString,
                    $order->total_amount // Format angka biasa biar bisa dijumlah di Excel
                ]);
            }

            // 3. Tulis Total di baris paling bawah
            fputcsv($handle, []); // Baris kosong
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