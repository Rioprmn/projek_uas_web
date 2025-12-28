@extends('layouts.main')

@section('content')
<div style="display: grid; grid-template-columns: 350px 1fr; gap: 20px; align-items: start; padding: 20px;">
    
    <div class="card" style="position: sticky; top: 20px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h3 style="margin-top: 0; border-bottom: 2px solid #f4f6f8; padding-bottom: 10px;">Transaksi Kasir</h3>

        {{-- ALERT NOTIFIKASI --}}
        @if(session('success'))
            <div style="background: #dcfce7; color: #16a34a; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 0.9rem;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background: #fee2e2; color: #ef4444; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 0.9rem;">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('cart.add') }}" method="POST">
            @csrf
            <div style="margin-bottom: 15px;">
                <label style="font-weight: bold; display: block; margin-bottom: 5px;">Cari Produk</label>
                <select name="product_id" style="width: 100%; padding: 10px; border-radius: 4px; border: 1px solid #ddd; background: white;" required>
                    <option value="">-- Pilih Produk --</option>
                    {{-- CEK APAKAH VARIABEL PRODUCTS ADA --}}
                    @if(isset($products) && $products->count() > 0)
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                                {{ $product->name }} (Stok: {{ $product->stock }}) - Rp{{ number_format($product->price) }}
                            </option>
                        @endforeach
                    @else
                        <option value="" disabled>Data produk kosong di database</option>
                    @endif
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="font-weight: bold; display: block; margin-bottom: 5px;">Jumlah (Qty)</label>
                <input type="number" name="qty" min="1" value="1" style="width: 100%; padding: 10px; border-radius: 4px; border: 1px solid #ddd;" required>
            </div>

            <button type="submit" style="width: 100%; background: #2563eb; color: white; padding: 12px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">
                + Tambah ke Keranjang
            </button>
        </form>
    </div>

    <div class="card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h3 style="margin-top: 0; border-bottom: 2px solid #f4f6f8; padding-bottom: 10px;">Daftar Belanja</h3>
        
        @if(session('cart') && count(session('cart')) > 0)
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8fafc; text-align: left;">
                        <th style="padding: 12px; border-bottom: 2px solid #ddd;">Produk</th>
                        <th style="padding: 12px; border-bottom: 2px solid #ddd;">Harga</th>
                        <th style="padding: 12px; border-bottom: 2px solid #ddd; width: 120px;">Qty</th>
                        <th style="padding: 12px; border-bottom: 2px solid #ddd;">Subtotal</th>
                        <th style="padding: 12px; border-bottom: 2px solid #ddd; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = 0; @endphp
                    @foreach(session('cart') as $id => $item)
                        @php 
                            $subtotal = $item['price'] * $item['qty']; 
                            $total += $subtotal;
                        @endphp
                        <tr>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;">{{ $item['name'] }}</td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;">Rp {{ number_format($item['price']) }}</td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;">
                                <form action="{{ route('cart.update') }}" method="POST" style="display:flex; gap: 4px;">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $id }}">
                                    <input type="number" name="qty" value="{{ $item['qty'] }}" min="1" style="width: 50px; padding: 5px;">
                                    <button type="submit" style="background: #64748b; color: white; border: none; font-size: 10px; padding: 5px; cursor: pointer; border-radius: 3px;">Update</button>
                                </form>
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;">Rp {{ number_format($subtotal) }}</td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee; text-align: center;">
                                <form action="{{ route('cart.remove') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $id }}">
                                    <button type="submit" style="background:none; border:none; color:#ef4444; cursor:pointer; font-weight: bold;">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    <tr style="background: #f8fafc;">
                        <td colspan="3" style="padding: 15px; text-align: right; font-weight: bold; font-size: 1.1rem;">Total Bayar:</td>
                        <td colspan="2" style="padding: 15px; font-weight: bold; color: #2563eb; font-size: 1.1rem;">Rp {{ number_format($total) }}</td>
                    </tr>
                </tbody>
            </table>

            <div style="margin-top: 20px;">
                <form action="{{ route('transactions.checkout') }}" method="POST">
                    @csrf
                    <button type="submit" style="width: 100%; background: #16a34a; color: white; padding: 15px; font-size: 1.1rem; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;" onclick="return confirm('Proses pembayaran sekarang?')">
                        Selesaikan Transaksi & Cetak Nota
                    </button>
                </form>
            </div>
        @else
            <div style="padding: 60px; text-align: center; color: #94a3b8;">
                <div style="font-size: 3rem; margin-bottom: 10px;">🛒</div>
                <p>Keranjang kosong. Pilih produk "Mie Goreng" di kiri untuk memulai.</p>
            </div>
        @endif
    </div>
</div>
@endsection