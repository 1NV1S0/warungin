<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pesanan #{{ $order->id }}</title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; font-size: 12px; max-width: 300px; margin: 0 auto; padding: 10px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .border-bottom { border-bottom: 1px dashed #000; padding-bottom: 5px; margin-bottom: 5px; }
        .flex { display: flex; justify-content: space-between; }
        .no-print { display: none; }
        
        /* Tombol Print hanya muncul di layar, hilang saat diprint */
        @media print {
            .no-print { display: none; }
        }
        .btn-print { background: #000; color: #fff; border: none; padding: 10px; width: 100%; cursor: pointer; margin-bottom: 20px; }
    </style>
</head>
<body> <!-- onload="window.print()" agar otomatis print pas dibuka -->

    <button onclick="window.print()" class="btn-print no-print">🖨️ Cetak Struk</button>

    <div class="text-center">
        <h2 style="margin:0;">WARUNGIN</h2>
        <p style="margin:5px 0;">Jl. Koding No. 123, Internet</p>
    </div>

    <div class="border-bottom"></div>

    <div>
        No: #{{ $order->id }}<br>
        Tgl: {{ $order->created_at->format('d/m/Y H:i') }}<br>
        Kasir: {{ $order->cashier ? $order->cashier->name : '-' }}<br>
        Pelanggan: {{ $order->customer_name }}
    </div>

    <div class="border-bottom"></div>

    @foreach($order->items as $item)
    <div style="margin-bottom: 5px;">
        <div class="bold">{{ $item->menu->name }}</div>
        <div class="flex">
            <span>{{ $item->quantity }} x {{ number_format($item->price_at_time, 0, ',', '.') }}</span>
            <span>{{ number_format($item->subtotal, 0, ',', '.') }}</span>
        </div>
    </div>
    @endforeach

    <div class="border-bottom"></div>

    <div class="flex bold" style="font-size: 14px;">
        <span>TOTAL</span>
        <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
    </div>

    <div class="border-bottom"></div>
    
    <div class="text-center" style="margin-top: 10px;">
        Terima Kasih!<br>
        Simpan struk ini sebagai bukti pembayaran.
    </div>

</body>
</html>