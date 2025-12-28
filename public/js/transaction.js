document.addEventListener('DOMContentLoaded', function () {
    const product = document.getElementById('product');
    const qty = document.getElementById('qty');
    const subtotal = document.getElementById('subtotal');

    function hitung() {
        const selected = product.options[product.selectedIndex];
        const price = selected.dataset.price;
        const stock = selected.dataset.stock;

        qty.max = stock;

        if (qty.value > stock) {
            qty.value = stock;
        }

        subtotal.value = 'Rp ' + (price * qty.value).toLocaleString('id-ID');
    }

    product.addEventListener('change', hitung);
    qty.addEventListener('input', hitung);
    hitung();
});
