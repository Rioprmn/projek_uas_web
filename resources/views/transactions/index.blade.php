@extends('layouts.main')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0;">Laporan Transaksi</h2>
        <a href="{{ route('transactions.create') }}" style="background: #2563eb; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none;">+ Transaksi Baru</a>
    </div>

    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8fafc; text-align: left;">
                <th style="padding: 12px; border-bottom: 2px solid #ddd;">No. Nota</th>
                <th style="padding: 12px; border-bottom: 2px solid #ddd;">Tarikh</th>
                <th style="padding: 12px; border-bottom: 2px solid #ddd;">Total Bayar</th>
                <th style="padding: 12px; border-bottom: 2px solid #ddd; text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $trx)
            <tr>
                <td style="padding: 12px; border-bottom: 1px solid #eee;">#TRX-{{ $trx->id }}</td>
                <td style="padding: 12px; border-bottom: 1px solid #eee;">{{ $trx->created_at->format('d M Y H:i') }}</td>
                <td style="padding: 12px; border-bottom: 1px solid #eee;">Rp {{ number_format($trx->total_price) }}</td>
                <td style="padding: 12px; border-bottom: 1px solid #eee; text-align: center;">
                    <a href="{{ route('transactions.show', $trx->id) }}" style="color: #2563eb; text-decoration: none; font-weight: bold;">Lihat Nota</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection