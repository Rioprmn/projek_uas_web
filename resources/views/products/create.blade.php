@extends('layouts.main')

@section('content')
<div class="card">
    <h2 style="margin-top: 0; border-bottom: 2px solid #f4f6f8; padding-bottom: 10px;">Tambah Barang Baru</h2>

    <form method="POST" action="/products" style="max-width: 500px;">
        @csrf

        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Kategori</label>
            <select name="category_id" style="width: 100%; padding: 10px; border-radius: 4px; border: 1px solid #ddd;" required>
                <option value="">-- Pilih Kategori --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Nama Barang</label>
            <input type="text" name="name" placeholder="Masukan Nama Barang" style="width: 100%; padding: 10px; border-radius: 4px; border: 1px solid #ddd;" required>
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Harga (Rp)</label>
            <input type="number" name="price" placeholder="0" style="width: 100%; padding: 10px; border-radius: 4px; border: 1px solid #ddd;" required>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Stok Awal</label>
            <input type="number" name="stock" placeholder="0" style="width: 100%; padding: 10px; border-radius: 4px; border: 1px solid #ddd;" required>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" style="background: #2563eb; color: white; padding: 10px 20px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">
                Simpan Produk
            </button>
            <a href="/products" style="background: #64748b; color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none; font-size: 0.9rem;">Batal</a>
        </div>
    </form>
</div>
@endsection