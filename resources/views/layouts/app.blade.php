@extends('layouts.main')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0;">Daftar Barang</h2>
        <a href="/products/create" style="background: #2563eb; color: white; padding: 8px 14px; text-decoration: none; border-radius: 4px;">
            + Tambah Barang
        </a>
    </div>

    <table border="1" cellpadding="10" style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead style="background: #f8fafc;">
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->category->name }}</td>
                <td>Rp {{ number_format($item->price) }}</td>
                <td>{{ $item->stock }}</td>
                <td>
                    <a href="/products/{{ $item->id }}/edit" style="color: #2563eb;">Edit</a> | 
                    <form action="/products/{{ $item->id }}" method="POST" style="display:inline">
                        @csrf @method('DELETE')
                        <button type="submit" style="background:none; border:none; color:red; cursor:pointer; padding:0;" onclick="return confirm('Yakin hapus?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; color: #64748b;">Belum ada data barang.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection