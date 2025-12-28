<!DOCTYPE html>
<html>
<head>
    <title>Nota Transaksi #{{ $transaction->id }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .invoice-box { max-width: 300px; margin: auto; padding: 10px; border: 1px solid #eee; }
        .text-center { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { border-bottom: 1px dashed #ddd; text-align: left; padding: 5px 0; }
        td { padding: 5px 0; }
        .total { border-top: 1px dashed #ddd; font-weight: bold; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px; cursor: pointer;">Cetak Nota</button>
        <a href="{{ route('transactions.create') }}" style="padding: 10px; text-decoration: none; color: blue;">Kembali ke Kasir</a>
    </div>

    <div class="invoice-box">
        <div class="text-center">
            <h3 style="margin-bottom: 5px;">NAMA TOKO KASIR</h3>
            <p style="margin-top: 0;">
                Nota: #TRX-{{ $transaction->id }}<br>
                Kasir: {{ $transaction->user->name ?? 'Admin' }}<br>
                {{ $transaction->created_at->format('d/m/Y H:i') }}
            </p>
        </div>

        
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                {{-- Gunakan $transaction->items bukan $transaction->details --}}
                @foreach($transaction->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>Rp {{ number_format($item->subtotal) }}</td>
                </tr>
                @endforeach
                
                <tr class="total">
                    <td colspan="2">TOTAL</td>
                    <td>Rp {{ number_format($transaction->total) }}</td>
                </tr>
            </tbody>
        </table>
        
        <p class="text-center" style="margin-top: 20px;">-- Terima Kasih --</p>
    </div>
</body>
</html>