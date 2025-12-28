@extends('layouts.main')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin: 0; font-size: 1.25rem; font-weight: bold;">Daftar Barang</h3>
        <a href="/products/create" style="background: #2563eb; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px; font-weight: bold;">
            + Tambah Produk
        </a>
    </div>

    <table style="width: 100%; border-collapse: collapse; background: white;">
        <thead>
            <tr style="background: #f3f4f6; text-align: left;">
                <th style="padding: 12px; border: 1px solid #e5e7eb;">No</th>
                <th style="padding: 12px; border: 1px solid #e5e7eb;">Nama</th>
                <th style="padding: 12px; border: 1px solid #e5e7eb;">Kategori</th>
                <th style="padding: 12px; border: 1px solid #e5e7eb;">Harga</th>
                <th style="padding: 12px; border: 1px solid #e5e7eb;">Stok</th>
                <th style="padding: 12px; border: 1px solid #e5e7eb; text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
            <tr>
                <td style="padding: 12px; border: 1px solid #e5e7eb; text-align: center;">{{ $loop->iteration }}</td>
                <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $product->name }}</td>
                <td style="padding: 12px; border: 1px solid #e5e7eb;">{{ $product->category->name }}</td>
                <td style="padding: 12px; border: 1px solid #e5e7eb;">Rp {{ number_format($product->price) }}</td>
                <td style="padding: 12px; border: 1px solid #e5e7eb; text-align: center;">{{ $product->stock }}</td>
                <td style="padding: 12px; border: 1px solid #e5e7eb; text-align: center;">
                    <a href="/products/{{ $product->id }}/edit" style="color: #2563eb; text-decoration: none; margin-right: 10px;">Edit</a>
                    <form action="/products/{{ $product->id }}" method="POST" style="display: inline;">
                        @csrf @method('DELETE')
                        <button type="submit" style="color: #ef4444; background: none; border: none; cursor: pointer; padding: 0;" onclick="return confirm('Yakin hapus?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="padding: 20px; border: 1px solid #e5e7eb; text-align: center; color: #6b7280;">Data masih kosong.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection