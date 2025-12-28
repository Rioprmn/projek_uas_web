<x-app-layout>
    <h2>Edit Barang</h2>

    <form method="POST" action="/products/{{ $product->id }}">
        @csrf
        @method('PUT')

        <div>
            <label>Kategori</label>
            <select name="category_id" required>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                        {{ $product->category_id == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label>Nama Barang</label>
            <input type="text" name="name" value="{{ $product->name }}" required>
        </div>

        <div>
            <label>Harga</label>
            <input type="number" name="price" value="{{ $product->price }}" required>
        </div>

        <div>
            <label>Stok</label>
            <input type="number" name="stock" value="{{ $product->stock }}" required>
        </div>

        <button type="submit">Update</button>
    </form>
</x-app-layout>
