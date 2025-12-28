<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function create()
    {
        $products = Product::all();
        return view('transactions.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1'
        ]);

        DB::transaction(function () use ($request) {
            $product = Product::findOrFail($request->product_id);
            $qty = $request->qty;
            $subtotal = $product->price * $qty;

            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'total' => $subtotal
            ]);

            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $product->id,
                'price' => $product->price,
                'qty' => $qty,
                'subtotal' => $subtotal
            ]);
        });

        return redirect()->route('transactions.create')
            ->with('success', 'Transaksi berhasil disimpan');
    }

    public function addToCart(Request $request)
    {
    $request->validate([
        'product_id' => 'required|exists:products,id',
        'qty' => 'required|integer|min:1'
    ]);

    $product = Product::findOrFail($request->product_id);
    $cart = session()->get('cart', []);

    $qtyInCart = $cart[$product->id]['qty'] ?? 0;
    $totalQty = $qtyInCart + $request->qty;

    if ($totalQty > $product->stock) {
        return back()->with('error', 'Stok tidak mencukupi');
    }

    // SIMPAN KE CART
    $cart[$product->id] = [
        'name' => $product->name,
        'price' => $product->price,
        'qty' => $totalQty,
    ];

    session()->put('cart', $cart);

    return back()->with('success', 'Produk ditambahkan ke cart');
    }

    public function updateCart(Request $request)
    {
    $request->validate([
        'product_id' => 'required',
        'qty' => 'required|integer|min:1'
    ]);

    $cart = session()->get('cart', []);

    if (!isset($cart[$request->product_id])) {
        return back()->with('error', 'Produk tidak ditemukan di cart');
    }

    $product = Product::findOrFail($request->product_id);

    // 🔒 VALIDASI STOK
    if ($request->qty > $product->stock) {
        return back()->with('error', 'Qty melebihi stok');
    }

    // UPDATE QTY
    $cart[$request->product_id]['qty'] = $request->qty;

    session()->put('cart', $cart);

    return back()->with('success', 'Qty berhasil diupdate');
    }


    public function removeFromCart(Request $request)
    {
    $cart = session()->get('cart', []);
    unset($cart[$request->product_id]);
    session()->put('cart', $cart);

    return back();
    }

    public function checkout()
    {
        $cart = session()->get('cart');

        if (!$cart || count($cart) === 0) {
            return back()->with('error', 'Cart kosong');
        }

        // Variabel untuk menampung ID transaksi agar bisa dipanggil setelah closure
        $transactionId = DB::transaction(function () use ($cart) {
            $total = 0;

            foreach ($cart as $productId => $item) {
                $product = Product::lockForUpdate()->findOrFail($productId);
                if ($product->stock < $item['qty']) {
                    throw new \Exception("Stok {$product->name} tidak cukup");
                }
                $total += $product->price * $item['qty'];
            }

            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'total' => $total // Pastikan nama kolom di DB 'total' atau 'total_price'
            ]);

            foreach ($cart as $productId => $item) {
                $product = Product::findOrFail($productId);
                
                // Pastikan relasi di model Transaction bernama 'details' atau 'items'
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $productId,
                    'price' => $product->price,
                    'qty' => $item['qty'],
                    'subtotal' => $product->price * $item['qty']
                ]);

                $product->decrement('stock', $item['qty']);
            }
            
            return $transaction->id;
        });

        session()->forget('cart');

        // REKOMENDASI: Langsung arahkan ke nota setelah bayar
        return redirect()->route('transactions.show', $transactionId)
            ->with('success', 'Transaksi berhasil!');
    }

    public function index()
    {
        
        $transactions = Transaction::with('items.product')->latest()->get();
        return view('transactions.index', compact('transactions'));
    }

    public function show($id)
    {
        $transaction = Transaction::with('items.product', 'user')->findOrFail($id);
        return view('transactions.invoice', compact('transaction'));
    }



}
