<?php
session_start();
// Customer interface - allow access without auth
$role = $_SESSION['role'] ?? 'customer';
// Redirect customers to dashboard landing by default. Use ?view=shop to open shop directly.
if (($role ?? 'customer') !== 'admin') {
    $view = $_GET['view'] ?? '';
    if ($view !== 'shop') {
        header('Location: dashboard.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>POS Warung - Customer</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #667eea;
            --primary-dark: #764ba2;
            --accent: #4caf50;
            --accent-light: #81c784;
            --success: #10b981;
            --danger: #ef4444;
            --bg: #f8f9fa;
            --card: #ffffff;
            --text: #1a1a1a;
            --text-muted: #666666;
            --border: #e0e0e0;
        }
        /* Dashboard hero */
        .hero {
            background: linear-gradient(90deg,#1e824c,#2ecc71);
            color: white;
            border-radius: 20px;
            padding: 50px 40px;
            display: flex;
            gap: 40px;
            align-items: center;
            margin-bottom: 40px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.15);
            width: 100%;
            position: relative;
            overflow: hidden;
            min-height: 300px;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            pointer-events: none;
            animation: pulse 4s ease-in-out infinite;
        }
        .hero .hero-left {
            flex: 1;
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: left;
        }
        .hero h3 {
            margin: 0 0 16px 0;
            font-size: 42px;
            font-weight: 800;
            line-height: 1.2;
            animation: slideInLeft 1s ease-out;
        }
        .hero p {
            margin: 0 0 24px 0;
            color: rgba(255,255,255,0.95);
            font-size: 18px;
            font-weight: 400;
            animation: slideInLeft 1s ease-out 0.2s both;
        }
        .hero .hero-cta {
            padding: 16px 32px;
            border-radius: 12px;
            background: #66bb6a;
            color: white;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.4s ease;
            font-size: 16px;
            animation: slideInLeft 1s ease-out 0.4s both;
        }
        .hero .hero-cta:hover {
            transform: scale(1.08);
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }
        .hero img {
            width: 400px;
            height: 200px;
            object-fit: cover;
            border-radius: 15px;
            position: relative;
            z-index: 2;
            animation: slideInRight 1s ease-out;
        }

        .categories {
            display:flex; gap:24px; justify-content:center; align-items:center; flex-wrap:wrap; padding:40px 0; margin-bottom:40px; max-width:1000px; margin-left:auto; margin-right:auto;
        }
        .cat-card {
            min-width:160px; max-width:180px; background:var(--card); border-radius:16px; padding:24px 16px; box-shadow:0 12px 40px rgba(0,0,0,0.1); display:flex; flex-direction:column; align-items:center; gap:12px; cursor:pointer; transition:all 0.4s ease; border:1px solid rgba(0,0,0,0.08); position:relative; overflow:hidden; animation:fadeIn 0.6s ease-out forwards; opacity:0;
        }
        .cat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: all 0.5s ease;
        }
        .cat-card:hover::before {
            left: 100%;
        }
        .cat-card:hover {
            transform:translateY(-8px) scale(1.05); box-shadow:0 20px 60px rgba(0,0,0,0.15);
        }
        .cat-card img{
            width:64px;
            height:64px;
            border-radius:12px;
            transition:transform 0.3s ease;
        }
        .cat-card:hover img{
            transform:scale(1.1);
        }
        .cat-card .cat-name{
            font-weight:700;
            color:var(--text);
            font-size:16px;
            text-align:center;
            line-height:1.3;
            letter-spacing:0.5px;
        }
        .floating-shapes{position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:0;overflow:hidden}
        .shape{position:absolute;border-radius:50%;background:linear-gradient(45deg, rgba(255,255,255,0.2), rgba(255,255,255,0.1));animation:float 8s ease-in-out infinite;backdrop-filter:blur(1px)}
        .shape:nth-child(1){width:80px;height:80px;top:8%;left:8%;animation-delay:0s}
        .shape:nth-child(2){width:60px;height:60px;top:15%;right:12%;animation-delay:1.5s}
        .shape:nth-child(3){width:100px;height:100px;bottom:15%;left:15%;animation-delay:3s}
        .shape:nth-child(4){width:40px;height:40px;top:50%;right:8%;animation-delay:0.8s}
        .shape:nth-child(5){width:70px;height:70px;bottom:8%;right:20%;animation-delay:2.3s}
        .shape:nth-child(6){width:50px;height:50px;top:25%;left:25%;animation-delay:4s}
        .shape:nth-child(7){width:90px;height:90px;bottom:25%;right:30%;animation-delay:1s}
        .shape:nth-child(8){width:35px;height:35px;top:70%;left:10%;animation-delay:3.5s}
        @keyframes float{0%,100%{transform:translateY(0px) rotate(0deg)}50%{transform:translateY(-20px) rotate(180deg)}}
        @keyframes slideInLeft{from{opacity:0;transform:translateX(-50px)}to{opacity:1;transform:translateX(0)}}
        @keyframes slideInRight{from{opacity:0;transform:translateX(50px)}to{opacity:1;transform:translateX(0)}}
        @keyframes pulse{0%,100%{opacity:0.7}50%{opacity:1}}
        @keyframes fadeIn{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
        * {
            box-sizing: border-box;
            font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial;
        }
        body {
            margin: 0;
            background: linear-gradient(135deg, #1e824c 0%, #2ecc71 100%);
            color: var(--text);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.03)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.03)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.02)"/><circle cx="10" cy="50" r="0.5" fill="rgba(255,255,255,0.02)"/><circle cx="90" cy="30" r="0.5" fill="rgba(255,255,255,0.02)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            pointer-events: none;
            z-index: -1;
        }
        .navbar {
            background: linear-gradient(135deg, #e8f5e8, #f1f8e9);
            color: #2e7d32;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            position: relative;
            z-index: 10;
            border-bottom: 2px solid #c8e6c9;
        }
        .navbar h1 {
            margin: 0;
            font-size: 24px;
        }
        .navbar-right {
            display: flex;
            gap: 12px;
            align-items: center;
        }
        .btn {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
            text-decoration: none;
        }
        .btn-primary {
            background: var(--accent);
            color: white;
        }
        .btn-primary:hover {
            background: var(--accent-light);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(76, 175, 80, 0.3);
        }
        .btn-outline {
            background: transparent;
            color: white;
            border: 2px solid white;
        }
        .btn-outline:hover {
            background: rgba(255,255,255,0.1);
        }
        .container {
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 30px;
            text-align: center;
            position: relative;
            z-index: 1;
        }
        .page-header {
            margin-bottom: 24px;
        }
        .page-header h2 {
            margin: 0;
            font-size: 28px;
            color: var(--text);
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }
        .product-card {
            background: var(--card);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s;
            border: 2px solid transparent;
        }
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
            border-color: var(--primary);
        }
        .product-card .name {
            font-size: 18px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 8px;
        }
        .product-card .code {
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 12px;
        }
        .product-card .price {
            font-size: 24px;
            font-weight: 700;
            color: var(--accent);
            margin-bottom: 8px;
        }
        .product-card .stock {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 12px;
        }
        .product-card .add-btn {
            width: 100%;
            padding: 12px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        .product-card .add-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(102, 126, 234, 0.3);
        }
        .cart-section {
            background: var(--card);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-top: 24px;
            border-left: 4px solid var(--accent);
        }
        .cart-section h3 {
            margin: 0 0 16px 0;
            font-size: 20px;
            color: var(--text);
        }
        .cart-items {
            max-height: 400px;
            overflow-y: auto;
            margin-bottom: 16px;
        }
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            background: var(--bg);
            border-radius: 8px;
            margin-bottom: 8px;
        }
        .cart-item-info {
            flex: 1;
        }
        .cart-item-name {
            font-weight: 600;
            color: var(--text);
        }
        .cart-item-price {
            font-size: 12px;
            color: var(--text-muted);
        }
        .cart-item-qty {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .cart-item-qty input {
            width: 60px;
            padding: 6px;
            border: 1px solid var(--border);
            border-radius: 6px;
        }
        .cart-summary {
            border-top: 2px solid var(--border);
            padding-top: 16px;
            margin-bottom: 16px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .summary-total {
            font-size: 20px;
            font-weight: 700;
            color: var(--accent);
            display: flex;
            justify-content: space-between;
        }
        .cart-actions {
            display: flex;
            gap: 12px;
        }
        .cart-actions button {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-checkout {
            background: var(--success);
            color: white;
        }
        .btn-checkout:hover {
            background: #0d9668;
            transform: translateY(-2px);
        }
        .btn-clear {
            background: var(--border);
            color: var(--text);
        }
        .btn-clear:hover {
            background: #d0d0d0;
        }
        .search-bar {
            margin-bottom: 24px;
            display: flex;
            gap: 12px;
        }
        .search-bar input {
            flex: 1;
            padding: 12px;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
        }
        .search-bar input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        @media (max-width: 768px) {
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
            .navbar {
                flex-direction: column;
                gap: 12px;
            }
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        .modal.active {
            display: flex;
        }
        .modal-content {
            background: var(--card);
            padding: 30px;
            border-radius: 12px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .modal-header {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--text);
        }
        .form-group {
            margin-bottom: 16px;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: var(--text);
            font-size: 14px;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
        }
        .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .modal-footer {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }
        .modal-footer button {
            flex: 1;
            padding: 10px;
        }
        .btn-secondary {
            background: var(--border);
            color: var(--text);
            border: none;
        }
        .btn-secondary:hover {
            background: #d0d0d0;
        }
        .receipt {
            background: white;
            padding: 30px;
            border-radius: 12px;
            max-width: 400px;
            font-family: monospace;
            text-align: center;
            line-height: 1.6;
            color: var(--text);
        }
        .receipt-header {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 16px;
            border-bottom: 2px solid var(--text);
            padding-bottom: 12px;
        }
        .receipt-line {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-bottom: 8px;
        }
        .receipt-divider {
            border-top: 2px dashed var(--border);
            margin: 12px 0;
        }
        .receipt-total {
            font-size: 18px;
            font-weight: 700;
            margin: 16px 0;
        }
        @media (max-width: 768px) {
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
            .navbar {
                flex-direction: column;
                gap: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div style="display:flex;align-items:center;gap:12px"><strong style="color:#2e7d32">🛒 POS Warung - Customer</strong></div>
        <div style="display:flex;gap:12px;align-items:center">
            <a href="dashboard.php" class="btn btn-primary" style="background:#4caf50;color:white;font-weight:600">🏠 Dashboard</a>
            <a href="login.php" class="btn" style="background:#81c784;color:#2e7d32;border:1px solid #4caf50">Login Admin</a>
        </div>
    </div>

    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="container">
        <div class="page-header">
            <h2>Selamat Datang di POS Warung</h2>
            <p style="color: var(--text-muted); margin: 8px 0 0 0;">Pilih produk dan lakukan pemesanan</p>
        </div>

        <!-- Dashboard Hero -->
        <div id="dashboardHero" class="hero">
            <div class="hero-left">
                <h3>Stok Warung Habis? <br><span style="color:#fff7c2;">Restock Cepat di Sini.</span></h3>
                <p>Harga khusus mitra. Pesan sekarang, kirim besok pagi.</p>
                <button class="hero-cta" onclick="gotoShop()">Buat Pesanan Baru</button>
            </div>
        </div>

        <!-- Category Row -->
        <div class="categories" id="categoryRow"></div>

        <div class="search-bar">
            <div id="selectedCategoryPill" style="display:none; margin-right:8px; align-items:center;">
                <span id="selectedCategoryName" style="background:var(--accent); color:#111; padding:6px 10px; border-radius:20px; margin-right:6px;"></span>
                <button class="btn-clear" style="padding:6px 8px;" onclick="clearCategory()">Hapus</button>
            </div>
            <input type="text" id="globalSearch" placeholder="Cari produk..." />
        </div>

        <div class="product-grid" id="productGrid"></div>

        <div class="cart-section">
            <h3>🛍️ Keranjang Belanja</h3>
            <div class="cart-items" id="cartItems"></div>
            <div class="cart-summary">
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span id="subtotal">Rp 0</span>
                </div>
                <div class="summary-row">
                    <span>Diskon:</span>
                    <span id="diskon">Rp 0</span>
                </div>
                <div class="summary-total">
                    <span>Total:</span>
                    <span id="total">Rp 0</span>
                </div>
            </div>
            <div class="cart-actions">
                <button class="btn-checkout" onclick="openCustomerModal()">Pesan Sekarang</button>
                <button class="btn-clear" onclick="clearCart()">Kosongkan</button>
            </div>
        </div>
    </div>

    <!-- Checkout Type Modal -->
    <div id="checkoutTypeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Pilih Tipe Pesanan</div>
            <p style="color: var(--text-muted); margin-bottom: 20px;">Pilih apakah Anda ingin mengisi data untuk pengiriman atau beli langsung di tempat</p>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="chooseDelivery()">📍 Anter (Isi Data)</button>
                <button class="btn btn-secondary" onclick="choosePickup()">🏪 Beli Langsung</button>
            </div>
        </div>
    </div>

    <!-- Customer Data Modal (for delivery) -->
    <div id="customerModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Data Pengiriman</div>
            <form onsubmit="submitOrder(event)">
                <div class="form-group">
                    <label>Nama Lengkap *</label>
                    <input type="text" id="c_name" placeholder="Nama Anda" required>
                </div>
                <div class="form-group">
                    <label>Nomor Telepon *</label>
                    <input type="tel" id="c_phone" placeholder="08xxxxxxxxxx" required>
                </div>
                <div class="form-group">
                    <label>Alamat Pengiriman *</label>
                    <textarea id="c_address" placeholder="Alamat lengkap untuk pengiriman" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label>Metode Pembayaran</label>
                    <select id="payment_method" style="padding:10px;border:2px solid var(--border);border-radius:8px;width:100%;">
                        <option value="TUNAI">Tunai (Bayar di tempat)</option>
                        <option value="TRANSFER">Transfer (Upload bukti setelah pesan)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Transfer Ke</label>
                    <P>
                        Rekening BCA 123-456-7890 POS WARUNG <br>
                        Rekening MANDIRI 987-654-3210 POS WARUNG <br>
                        Rekening OVO 0812-3456-7890 POS WARUNG
                    </P>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeCustomerModal()">Batal</button>
                    <button type="submit" class="btn btn-primary" style="background: var(--success);">Lanjutkan Pesanan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Pickup Confirmation Modal -->
    <div id="pickupModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">✓ Konfirmasi Pembelian</div>
            <p style="color: var(--text-muted); margin-bottom: 20px;">Anda akan membeli langsung di tempat. Pesanan akan diproses segera.</p>
            <div id="pickupSummary" style="background: var(--bg); padding: 15px; border-radius: 8px; margin-bottom: 20px; max-height: 200px; overflow-y: auto;"></div>
            <div class="form-group">
                <label>Nama / Catatan (opsional)</label>
                <input type="text" id="p_name" placeholder="Nama atau catatan khusus">
            </div>
            <div class="form-group">
                <label>Metode Pembayaran</label>
                <select id="payment_method_pickup" style="padding:10px;border:2px solid var(--border);border-radius:8px;width:100%;">
                    <option value="TUNAI">Tunai (Bayar di tempat)</option>
                    <option value="TRANSFER">Transfer (Upload bukti setelah pesan)</option>
                </select>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closePickupModal()">Batal</button>
                <button class="btn btn-primary" style="background: var(--success);" onclick="submitPickupOrder()">Konfirmasi Pesanan</button>
            </div>
        </div>
    </div>

    <!-- Receipt Modal -->
    <div id="receiptModal" class="modal">
        <div class="modal-content">
            <div id="receiptContent"></div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="printReceipt()">🖨️ Cetak</button>
                <button class="btn btn-secondary" onclick="closeReceiptModal(); clearCart();">Selesai</button>
            </div>
        </div>
    </div>

    <!-- Upload Proof Modal -->
    <div id="uploadProofModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Upload Bukti Transfer</div>
            <div class="form-group">
                <input type="file" id="uploadProofInput" accept="image/*,application/pdf" />
            </div>
            <div class="form-group" id="uploadProofPreviewArea" style="display:none;">
                <label>Preview:</label>
                <div id="uploadProofPreview" style="margin-top:8px;"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeUploadModal()">Tutup</button>
                <button class="btn btn-primary" onclick="uploadProof()">Upload</button>
            </div>
        </div>
    </div>

    <script>
        const LS_KEY = 'pos_customer_v1';
        let state = {
            products: [],
            cart: [],
            kas: 0,
            mode: 'TUNAI',
            lastOrder: null,
            pendingUploadOrderId: null,
            selectedCategory: null
        };

        // sample categories to render in dashboard
        
        const categories = [
            {id:1, name: 'Sembako', icon: 'https://img.icons8.com/fluency/96/000000/groceries.png'},
            {id:2, name: 'Minuman', icon: 'https://img.icons8.com/fluency/96/000000/water-bottle.png'},
            {id:3, name: 'Bumbu', icon: 'https://img.icons8.com/fluency/96/000000/seasoning.png'},
            {id:4, name: 'Gas', icon: 'https://img.icons8.com/fluency/96/000000/gas-pump.png'}
        ];

        function renderCategories(){
            const el = document.getElementById('categoryRow');
            if (!el) return;
            el.innerHTML = '';
            categories.forEach((c, index)=>{
                const card = document.createElement('div');
                card.className = 'cat-card';
                card.innerHTML = `<img src="${c.icon}" alt="${c.name}" /><div class="cat-name">${c.name}</div>`;
                card.style.animationDelay = `${index * 0.1}s`;
                card.addEventListener('click', ()=>{
                    // set selected category and render
                    state.selectedCategory = c.name;
                    document.getElementById('selectedCategoryName').innerText = c.name;
                    document.getElementById('selectedCategoryPill').style.display = 'flex';
                    document.getElementById('globalSearch').value = '';
                    renderProducts();
                    window.scrollTo({top: document.getElementById('productGrid').offsetTop - 80, behavior:'smooth'});
                });
                el.appendChild(card);
            });
        }

        function clearCategory(){
            state.selectedCategory = null;
            document.getElementById('selectedCategoryPill').style.display = 'none';
            document.getElementById('selectedCategoryName').innerText = '';
            renderProducts();
        }

        function gotoShop(){
            // hide dashboard hero and scroll to products
            const hero = document.getElementById('dashboardHero');
            if (hero) hero.scrollIntoView({behavior:'smooth'});
            document.getElementById('globalSearch').focus();
        }

        function init() {
            // Load products from database
            fetch('api/products.php?action=list')
                .then(r => r.json())
                .then(data => {
                    if (data.ok && data.products && data.products.length > 0) {
                        // Normalize types coming from the API (MySQL returns strings)
                        state.products = data.products.map(p => ({
                            id: Number(p.id),
                            name: p.name,
                            code: p.code,
                            price: Number(p.price),
                            stock: Number(p.stock),
                            category: p.category,
                            unit: p.unit,
                            image: p.image || ''
                        }));
                    } else {
                        // Fallback to default products
                        state.products = [
                            {id:1, name:'Beras 5kg', code:'BR05', price:65000, stock:20, category:'Beras', unit:'pack'},
                            {id:2, name:'Gula 1kg', code:'GL01', price:15000, stock:50, category:'Bumbu', unit:'pack'},
                            {id:3, name:'Minyak 2L', code:'MK02', price:30000, stock:30, category:'Minyak', unit:'botol'},
                            {id:4, name:'Sarden Kaleng', code:'SD01', price:12000, stock:60, category:'Makanan', unit:'pcs'},
                            {id:5, name:'Rokok A', code:'RK01', price:20000, stock:80, category:'Rokok', unit:'pcs'},
                            {id:6, name:'Kopi 250g', code:'KP25', price:22000, stock:40, category:'Minuman', unit:'pack'}
                        ];
                    }
                    renderAll();
                    renderCategories();
                    // If a category is provided via URL (?cat=...), apply it
                    try {
                        const params = new URLSearchParams(window.location.search);
                        const cat = params.get('cat');
                        if (cat) {
                            state.selectedCategory = decodeURIComponent(cat);
                            document.getElementById('selectedCategoryName').innerText = state.selectedCategory;
                            document.getElementById('selectedCategoryPill').style.display = 'flex';
                            renderProducts();
                        }
                    } catch(e) { /* ignore URL errors */ }
                })
                .catch(err => {
                    console.error('Error loading products:', err);
                    state.products = [
                        {id:1, name:'Beras 5kg', code:'BR05', price:65000, stock:20, category:'Beras', unit:'pack'},
                        {id:2, name:'Gula 1kg', code:'GL01', price:15000, stock:50, category:'Bumbu', unit:'pack'},
                        {id:3, name:'Minyak 2L', code:'MK02', price:30000, stock:30, category:'Minyak', unit:'botol'},
                        {id:4, name:'Sarden Kaleng', code:'SD01', price:12000, stock:60, category:'Makanan', unit:'pcs'},
                        {id:5, name:'Rokok A', code:'RK01', price:20000, stock:80, category:'Rokok', unit:'pcs'},
                        {id:6, name:'Kopi 250g', code:'KP25', price:22000, stock:40, category:'Minuman', unit:'pack'}
                    ];
                    renderAll();
                });
        }

        function saveState() {
            localStorage.setItem(LS_KEY, JSON.stringify(state));
            renderAll();
            renderCategories();
        }

        function format(n) {
            return Number(n).toLocaleString('id-ID');
        }

        function renderProducts() {
            const q = document.getElementById('globalSearch').value.toLowerCase();
            const container = document.getElementById('productGrid');
            container.innerHTML = '';
            
            const list = state.products.filter(p => {
                const matchesQuery = q === '' || p.name.toLowerCase().includes(q) || p.code.toLowerCase().includes(q);
                const matchesCategory = !state.selectedCategory || (p.category && p.category.toLowerCase() === state.selectedCategory.toLowerCase());
                return matchesQuery && matchesCategory;
            });

            list.forEach(p => {
                const el = document.createElement('div');
                el.className = 'product-card';
                const imgHtml = p.image ? `<img src="${p.image}" alt="${p.name}" style="width:100%;height:140px;object-fit:cover;border-radius:6px;margin-bottom:8px;">` : '';
                el.innerHTML = `
                    ${imgHtml}
                    <div class="name">${p.name}</div>
                    <div class="code">Kode: ${p.code}</div>
                    <div class="price">Rp ${format(p.price)}</div>
                    <div class="stock">Stok: ${p.stock} ${p.unit}</div>
                    <button class="add-btn" onclick="addToCart(${p.id})">+ Tambah ke Keranjang</button>
                `;
                container.appendChild(el);
            });
        }

        function addToCart(id) {
            const p = state.products.find(x => x.id === id);
            if (!p || p.stock <= 0) {
                alert('Produk tidak tersedia');
                return;
            }
            const existing = state.cart.find(c => c.id === id);
            if (existing) {
                if (existing.qty < p.stock) {
                    existing.qty++;
                } else {
                    alert('Stok tidak cukup');
                    return;
                }
            } else {
                state.cart.push({id: p.id, name: p.name, price: p.price, qty: 1});
            }
            saveState();
        }

        function renderCart() {
            const c = document.getElementById('cartItems');
            c.innerHTML = '';
            if (state.cart.length === 0) {
                c.innerHTML = '<p style="color: var(--text-muted); text-align: center; padding: 20px;">Keranjang kosong</p>';
                return;
            }
            state.cart.forEach(item => {
                const el = document.createElement('div');
                el.className = 'cart-item';
                el.innerHTML = `
                    <div class="cart-item-info">
                        <div class="cart-item-name">${item.name}</div>
                        <div class="cart-item-price">Rp ${format(item.price)}</div>
                    </div>
                    <div class="cart-item-qty">
                        <input type="number" min="1" value="${item.qty}" onchange="updateQty(${item.id}, this.value)" />
                        <button class="btn-clear" style="padding: 6px 12px;" onclick="removeFromCart(${item.id})">Hapus</button>
                    </div>
                `;
                c.appendChild(el);
            });
            updateTotals();
        }

        function updateQty(id, val) {
            const item = state.cart.find(i => i.id === id);
            if (item) {
                item.qty = Math.max(1, Number(val));
                saveState();
            }
        }

        function removeFromCart(id) {
            state.cart = state.cart.filter(i => i.id !== id);
            saveState();
        }

        function clearCart() {
            if (confirm('Kosongkan keranjang?')) {
                state.cart = [];
                saveState();
            }
        }

        function updateTotals() {
            const sub = state.cart.reduce((s, i) => s + i.price * i.qty, 0);
            document.getElementById('subtotal').innerText = 'Rp ' + format(sub);
            document.getElementById('diskon').innerText = 'Rp 0';
            document.getElementById('total').innerText = 'Rp ' + format(sub);
        }

        function checkout() {
            if (state.cart.length === 0) {
                alert('Keranjang kosong!');
                return;
            }
            openCustomerModal();
        }

        function checkout() {
            if (state.cart.length === 0) {
                alert('Keranjang kosong!');
                return;
            }
            document.getElementById('checkoutTypeModal').classList.add('active');
        }

        function chooseDelivery() {
            document.getElementById('checkoutTypeModal').classList.remove('active');
            openCustomerModal();
        }

        function choosePickup() {
            document.getElementById('checkoutTypeModal').classList.remove('active');
            showPickupSummary();
            document.getElementById('pickupModal').classList.add('active');
        }

        function showPickupSummary() {
            let html = '<strong>Ringkasan Pesanan:</strong><br>';
            state.cart.forEach(item => {
                html += item.name + ' x' + item.qty + ' = Rp ' + format(item.price * item.qty) + '<br>';
            });
            const total = state.cart.reduce((s, i) => s + i.price * i.qty, 0);
            html += '<strong>Total: Rp ' + format(total) + '</strong>';
            document.getElementById('pickupSummary').innerHTML = html;
        }

        function openCustomerModal() {
            if (state.cart.length === 0) {
                alert('Keranjang kosong!');
                return;
            }
            document.getElementById('c_name').value = '';
            document.getElementById('c_phone').value = '';
            document.getElementById('c_address').value = '';
            document.getElementById('customerModal').classList.add('active');
        }

        function closeCustomerModal() {
            document.getElementById('customerModal').classList.remove('active');
        }

        function closePickupModal() {
            document.getElementById('pickupModal').classList.remove('active');
        }

        async function submitPickupOrder() {
            const name = document.getElementById('p_name').value.trim() || 'Pembeli Langsung';
            const total = state.cart.reduce((s, i) => s + i.price * i.qty, 0);
            
            closePickupModal();
            
            const receiptContent = document.getElementById('receiptContent');
            receiptContent.innerHTML = '<div style="text-align: center; padding: 40px;"><p>Memproses pesanan...</p></div>';
            document.getElementById('receiptModal').classList.add('active');

            try {
                const formData = new FormData();
                formData.append('action', 'create');
                formData.append('customer_name', name);
                formData.append('customer_phone', '-');
                formData.append('customer_address', 'Pembeli Langsung (Tidak Diantar)');
                const method = document.getElementById('payment_method_pickup') ? document.getElementById('payment_method_pickup').value : 'TUNAI';
                formData.append('payment_method', method);
                formData.append('items', JSON.stringify(state.cart));
                formData.append('discount', 0);

                const response = await fetch('./api/orders.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.ok) {
                    state.lastOrder = {
                        order_number: result.order_number,
                        order_id: result.order_id,
                        customer_name: name,
                        customer_phone: '-',
                        customer_address: 'Pembeli Langsung',
                        items: state.cart,
                        total: result.total,
                        timestamp: new Date(),
                        payment_method: method,
                        type: 'pickup'
                    };
                    saveState();
                    showReceipt();
                    if (method === 'TRANSFER') {
                        openUploadModal(result.order_id);
                    }
                } else {
                    alert('Gagal membuat pesanan: ' + result.msg);
                    closeReceiptModal();
                }
            } catch (err) {
                alert('Error: ' + err.message);
                console.error(err);
                closeReceiptModal();
            }
        }
        

        // Handler for delivery (customer data) form submission
        async function submitOrder(e) {
            e.preventDefault();

            const name = document.getElementById('c_name').value.trim();
            const phone = document.getElementById('c_phone').value.trim();
            const address = document.getElementById('c_address').value.trim();

            if (!name || !phone) {
                alert('Nama dan nomor telepon harus diisi!');
                return;
            }

            const total = state.cart.reduce((s, i) => s + i.price * i.qty, 0);
            
            closeCustomerModal();
            
            // Show loading
            const receiptContent = document.getElementById('receiptContent');
            receiptContent.innerHTML = '<div style="text-align: center; padding: 40px;"><p>Memproses pesanan...</p></div>';
            document.getElementById('receiptModal').classList.add('active');

            try {
                const formData = new FormData();
                formData.append('action', 'create');
                formData.append('customer_name', name);
                formData.append('customer_phone', phone);
                formData.append('customer_address', address);
                const method = document.getElementById('payment_method') ? document.getElementById('payment_method').value : 'TUNAI';
                formData.append('payment_method', method);
                formData.append('items', JSON.stringify(state.cart));
                formData.append('discount', 0);

                const response = await fetch('./api/orders.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.ok) {
                    const method = document.getElementById('payment_method') ? document.getElementById('payment_method').value : 'TUNAI';
                    state.lastOrder = {
                        order_number: result.order_number,
                        order_id: result.order_id,
                        customer_name: name,
                        customer_phone: phone,
                        customer_address: address,
                        items: state.cart,
                        total: result.total,
                        timestamp: new Date(),
                        payment_method: method
                    };
                    saveState();
                    showReceipt();
                    if (state.lastOrder.payment_method === 'TRANSFER') {
                        openUploadModal(result.order_id);
                    }
                } else {
                    alert('Gagal membuat pesanan: ' + result.msg);
                    closeReceiptModal();
                }
            } catch (err) {
                alert('Error: ' + err.message);
                console.error(err);
                closeReceiptModal();
            }
        }
        

        function showReceipt() {
            const order = state.lastOrder;
            if (!order) return;

            let html = '<div class="receipt">';
            html += '<div class="receipt-header">✓ PESANAN BERHASIL</div>';
            html += '<div class="receipt-line"><span>No. Pesanan:</span><strong>' + order.order_number + '</strong></div>';
            html += '<div class="receipt-line"><span>Tanggal:</span><span>' + new Date(order.timestamp).toLocaleString('id-ID') + '</span></div>';
            html += '<div class="receipt-divider"></div>';
            html += '<div style="text-align: left; margin-bottom: 12px;">';
            html += '<strong>DATA PEMESAN</strong><br>';
            html += 'Nama: ' + order.customer_name + '<br>';
            html += 'Telp: ' + order.customer_phone + '<br>';
            if (order.customer_address) {
                html += 'Alamat: ' + order.customer_address + '<br>';
            }
            html += '</div>';
            html += '<div class="receipt-divider"></div>';
            html += '<div style="text-align: left; margin-bottom: 12px;">';
            html += '<strong>DETAIL PESANAN</strong><br>';
            order.items.forEach(item => {
                html += item.name + ' x' + item.qty + '<br>';
                html += '  Rp ' + format(item.price * item.qty) + '<br>';
            });
            html += '</div>';
            html += '<div class="receipt-divider"></div>';
            html += '<div class="receipt-total">Total: Rp ' + format(order.total) + '</div><br>';
            html += '<strong>KETERANGAN</strong><br>';
            html += '<p>BARANG YG SUDAH DI PESAN, JIKA KELALAIAN BUKAN DARI PIHAK KAMI MAKA TIDAK BISA DI KEMBALIKAN <br> JANGAN LUPA SERTAKAN BUKTI</p>';
            html += '<p style="font-size: 13px;">Terima kasih telah berbelanja di POS Warung</p>';
            html += '</div>';

            document.getElementById('receiptContent').innerHTML = html;
        }

        function closeReceiptModal() {
            document.getElementById('receiptModal').classList.remove('active');
        }

        // Upload proof handlers
        function openUploadModal(orderId) {
            state.pendingUploadOrderId = orderId;
            const inp = document.getElementById('uploadProofInput');
            if (inp) inp.value = '';
            const previewArea = document.getElementById('uploadProofPreviewArea');
            if (previewArea) previewArea.style.display = 'none';
            document.getElementById('uploadProofModal').classList.add('active');
        }

        function closeUploadModal() {
            document.getElementById('uploadProofModal').classList.remove('active');
            state.pendingUploadOrderId = null;
        }

        document.getElementById('uploadProofInput').addEventListener('change', function (ev) {
            const file = ev.target.files[0];
            const preview = document.getElementById('uploadProofPreview');
            const area = document.getElementById('uploadProofPreviewArea');
            if (!file) {
                if (area) area.style.display = 'none';
                if (preview) preview.innerHTML = '';
                return;
            }
            if (file.type.startsWith('image/')) {
                const url = URL.createObjectURL(file);
                preview.innerHTML = `<img src="${url}" style="max-width:100%;height:auto;border-radius:6px;" />`;
                if (area) area.style.display = 'block';
            } else {
                preview.innerHTML = `<div style="padding:8px;border:1px solid var(--border);border-radius:6px;">${file.name}</div>`;
                if (area) area.style.display = 'block';
            }
        });

        async function uploadProof() {
            if (!state.pendingUploadOrderId) {
                alert('Tidak ada pesanan untuk diupload.');
                return;
            }
            const inp = document.getElementById('uploadProofInput');
            if (!inp || !inp.files || inp.files.length === 0) {
                alert('Pilih file bukti transfer terlebih dahulu.');
                return;
            }
            const file = inp.files[0];
            const formData = new FormData();
            formData.append('action', 'upload_proof');
            formData.append('id', state.pendingUploadOrderId);
            formData.append('payment_proof', file);

            try {
                const resp = await fetch('./api/orders.php', { method: 'POST', body: formData });
                const res = await resp.json();
                if (res.ok) {
                    alert('Bukti transfer berhasil diunggah. Terima kasih.');
                    // update local state
                    if (state.lastOrder && state.lastOrder.order_id == state.pendingUploadOrderId) {
                        state.lastOrder.payment_status = 'pending';
                        state.lastOrder.payment_proof = res.payment_proof || null;
                    }
                    saveState();
                    closeUploadModal();
                } else {
                    alert('Gagal mengunggah bukti: ' + (res.msg || 'unknown'));
                }
            } catch (err) {
                console.error(err);
                alert('Error saat mengunggah: ' + err.message);
            }
        }

        function printReceipt() {
            const order = state.lastOrder;
            if (!order) return;

            let html = '<html><head><title>Nota Pembelian</title></head><body>';
            html += '<pre style="font-family: monospace; text-align: center;">';
            html += '==== POS WARUNG ====\n';
            html += 'Nota No: ' + order.order_number + '\n';
            html += new Date(order.timestamp).toLocaleString('id-ID') + '\n\n';
            html += '--- DATA PEMESAN ---\n';
            html += 'Nama: ' + order.customer_name + '\n';
            html += 'Telp: ' + order.customer_phone + '\n';
            if (order.customer_address) {
                html += 'Alamat: ' + order.customer_address + '\n';
            }
            html += '\n--- DETAIL PESANAN ---\n';
            order.items.forEach(item => {
                html += item.name + '\n';
                html += '  x' + item.qty + ' @ Rp ' + format(item.price) + ' = Rp ' + format(item.price * item.qty) + '\n';
            });
            html += '\n================\n';
            html += 'TOTAL: Rp ' + format(order.total) + '\n';
            html += '================\n\n';
            html += 'Terima kasih telah berbelanja!\n';
            html += '</pre></body></html>';

            const w = window.open('', '_blank');
            w.document.write(html);
            w.print();
        }

        function renderAll() {
            renderProducts();
            renderCart();
        }

        document.getElementById('globalSearch').addEventListener('input', renderProducts);

        init();
    </script>
</body>
</html>
