@extends('layouts.main') {{-- Pastikan mengarah ke layouts.main --}}

@section('content')
<div class="card">
    <h2 style="margin-top: 0;">Dashboard Utama</h2>
    <p>Selamat datang, <strong>{{ Auth::user()->name }}</strong>! Berikut adalah ringkasan sistem kasir kamu hari ini:</p>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 20px;">
        <div style="background: #eff6ff; padding: 20px; border-radius: 8px; border-left: 5px solid #3b82f6;">
            <small style="color: #64748b; font-weight: bold;">TOTAL PRODUK</small>
            <h3 style="margin: 5px 0; font-size: 1.5rem;">{{ \App\Models\Product::count() }}</h3>
            <a href="/products" style="color: #3b82f6; text-decoration: none; font-size: 0.8rem;">Lihat Detail →</a>
        </div>

        <div style="background: #f0fdf4; padding: 20px; border-radius: 8px; border-left: 5px solid #22c55e;">
            <small style="color: #64748b; font-weight: bold;">TRANSAKSI HARI INI</small>
            <h3 style="margin: 5px 0; font-size: 1.5rem;">{{ \App\Models\Transaction::whereDate('created_at', today())->count() }}</h3>
            <a href="/transactions" style="color: #22c55e; text-decoration: none; font-size: 0.8rem;">Lihat Laporan →</a>
        </div>
    </div>

    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
        <h4>Menu Cepat:</h4>
        <div style="display: flex; gap: 10px;">
            <a href="/transactions/create" style="background: #2563eb; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none;">+ Transaksi Baru</a>
            <a href="/products/create" style="background: #64748b; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none;">Tambah Produk</a>
        </div>
    </div>
</div>
@endsection