<?php
session_start();
require_once __DIR__ . '/config.php';

// Protect admin area
if (($_SESSION['role'] ?? null) !== 'admin') {
    header('Location: login.php');
    exit;
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>POS Warung - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #667eea;
            --primary-dark: #764ba2;
            --accent: #ff7a18;
            --accent-light: #ffb84d;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --bg: #f8f9fa;
            --card: #ffffff;
            --text: #1a1a1a;
            --text-muted: #666666;
            --border: #e0e0e0;
        }
        * {
            box-sizing: border-box;
            font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial;
        }
        body {
            margin: 0;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }
        .navbar {
            background: linear-gradient(135deg, #1e293b, #334155);
            color: white;
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
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
        .user-info {
            color: rgba(255,255,255,0.8);
            font-size: 14px;
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
            box-shadow: 0 6px 12px rgba(255, 122, 24, 0.3);
        }
        .btn-outline {
            background: transparent;
            color: white;
            border: 2px solid rgba(255,255,255,0.3);
        }
        .btn-outline:hover {
            background: rgba(255,255,255,0.1);
            border-color: white;
        }
        .btn-small {
            padding: 6px 12px;
            font-size: 12px;
        }
        .btn-danger {
            background: var(--danger);
            color: white;
        }
        .btn-danger:hover {
            background: #dc2626;
        }
        .btn-success {
            background: var(--success);
            color: white;
        }
        .btn-success:hover {
            background: #0d9668;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 24px;
        }
        .tabs {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
            border-bottom: 2px solid var(--border);
        }
        .tab {
            padding: 12px 20px;
            background: transparent;
            border: none;
            cursor: pointer;
            font-weight: 600;
            color: var(--text-muted);
            font-size: 14px;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }
        .tab.active {
            color: var(--accent);
            border-bottom-color: var(--accent);
        }
        .tab:hover {
            color: var(--text);
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .page-header h2 {
            margin: 0;
            font-size: 28px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }
        .stat-card {
            background: var(--card);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-left: 4px solid var(--primary);
        }
        .stat-label {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 8px;
        }
        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--text);
        }
        .table-container {
            background: var(--card);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            text-align: left;
            padding: 12px;
            background: var(--bg);
            border-bottom: 2px solid var(--border);
            font-weight: 600;
            color: var(--text);
        }
        td {
            padding: 12px;
            border-bottom: 1px solid var(--border);
        }
        tr:hover {
            background: var(--bg);
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
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }
        .badge-warning {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }
        .badge-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .tabs {
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>⚙️ POS Warung - Admin Panel</h1>
        <div class="navbar-right">
            <div class="user-info">Admin: <?= htmlspecialchars($_SESSION['username'] ?? 'admin') ?></div>
            <a href="logout.php" class="btn btn-outline btn-small">Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="tabs">
            <button class="tab active" onclick="switchTab('products')">📦 Produk</button>
            <button class="tab" onclick="switchTab('orders')">📋 Pesanan</button>
            <button class="tab" onclick="switchTab('reports')">📊 Laporan</button>
            <button class="tab" onclick="switchTab('monthly')">📈 Laporan Bulanan</button>
        </div>

        <!-- PRODUCTS TAB -->
        <div id="products" class="tab-content active">
            <div class="page-header">
                <h2>Manajemen Produk</h2>
                <button class="btn btn-primary" onclick="openAddProductModal()">+ Tambah Produk</button>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Gambar</th>
                            <th>Nama Produk</th>
                            <th>Kode</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Kategori</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="productsTable"></tbody>
                </table>
            </div>
        </div>

        <!-- ORDERS TAB -->
        <div id="orders" class="tab-content">
            <div class="page-header">
                    <h2>Pesanan Masuk</h2>
                    <div style="margin-left: 12px; display:inline-block">
                        <button class="btn btn-danger" onclick="resetOrders()">Reset Pesanan</button>
                    </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Pesanan</div>
                    <div class="stat-value" id="totalOrders">0</div>
                </div>
                <div class="stat-card" style="border-left-color: var(--success);">
                    <div class="stat-label">Total Penjualan</div>
                    <div class="stat-value" id="totalSales">Rp 0</div>
                </div>
                <div class="stat-card" style="border-left-color: var(--accent);">
                    <div class="stat-label">Rata-rata Order</div>
                    <div class="stat-value" id="avgOrder">Rp 0</div>
                </div>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No. Pesanan</th>
                            <th>Nama Customer</th>
                            <th>Telepon</th>
                            <th>Total</th>
                            <th>Jumlah Item</th>
                            <th>Tanggal</th>
                            <th>Metode Pembayaran</th>
                            <th>Status</th>
                            <th>Bukti TF</th>
                            <th>Pesanan</th>
                            <th>Konfirmasi</th>
                            <th>Batal</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTable"></tbody>
                </table>
            </div>
        </div>

        <!-- REPORTS TAB -->
        <div id="reports" class="tab-content">
            <div class="page-header">
                <h2>Laporan & Analitik</h2>
                <div style="display: flex; gap: 12px;">
                    <button class="btn btn-primary" onclick="loadReport('daily')" id="btnDaily" style="background: var(--accent);">📅 Hari Ini</button>
                    <button class="btn btn-primary" onclick="loadReport('weekly')">📆 Minggu Ini</button>
                    <button class="btn btn-primary" onclick="loadReport('monthly')">📋 Bulan Ini</button>
                    <button class="btn btn-primary" onclick="loadReport('all')">📊 Semua</button>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card" style="border-left-color: var(--accent);">
                    <div class="stat-label">Total Produk</div>
                    <div class="stat-value" id="totalProducts">0</div>
                </div>
                <div class="stat-card" style="border-left-color: var(--warning);">
                    <div class="stat-label">Stok Rendah</div>
                    <div class="stat-value" id="lowStock">0</div>
                </div>
                <div class="stat-card" style="border-left-color: var(--success);">
                    <div class="stat-label" id="periodLabel">Total Pendapatan Hari Ini</div>
                    <div class="stat-value" id="totalRevenue">Rp 0</div>
                </div>
                <div class="stat-card" style="border-left-color: var(--primary);">
                    <div class="stat-label" id="avgLabel">Rata-rata Order Hari Ini</div>
                    <div class="stat-value" id="avgRevenue">Rp 0</div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                <div class="table-container">
                    <h3 style="margin: 0 0 16px 0;">Daftar Stok Rendah (< 20 unit)</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Stok</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="lowStockTable"></tbody>
                    </table>
                </div>

                <div class="table-container">
                    <h3 style="margin: 0 0 16px 0;">Produk Terlaris</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Terjual</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody id="topProductsTable"></tbody>
                    </table>
                </div>
            </div>

            <div class="table-container" style="margin-top: 20px;">
                <h3 style="margin: 0 0 16px 0;" id="breakdownTitle">Penjualan Per Hari</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jumlah Order</th>
                            <th>Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody id="dailyBreakdownTable"></tbody>
                </table>
            </div>
        </div>

        <!-- MONTHLY REPORTS TAB -->
        <div id="monthly" class="tab-content">
            <div class="page-header">
                <h2>Laporan Penjualan Bulanan</h2>
                <button class="btn btn-primary" onclick="generateMonthlyReport()">📄 Buat Laporan Bulan Ini</button>
            </div>

            <div class="table-container">
                <h3 style="margin: 0 0 16px 0;">Daftar Laporan Bulanan</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Bulan</th>
                            <th>Total Pesanan</th>
                            <th>Total Penjualan</th>
                            <th>Total Item</th>
                            <th>Rata-rata Order</th>
                            <th>Produk Terlaris</th>
                            <th>Tanggal Laporan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="monthlyReportsTable"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add/Edit Product Modal -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <div class="modal-header" id="modalTitle">Tambah Produk</div>
            <div class="form-group">
                <label>Nama Produk</label>
                <input type="text" id="p_name" placeholder="Nama produk">
            </div>
            <div class="form-group">
                <label>Kode / SKU</label>
                <input type="text" id="p_code" placeholder="Kode produk">
            </div>
            <div class="form-group">
                <label>Harga (Rp)</label>
                <input type="number" id="p_price" placeholder="Harga">
            </div>
            <div class="form-group">
                <label>Stok</label>
                <input type="number" id="p_stock" placeholder="Jumlah stok">
            </div>
            <div class="form-group">
                <label>Kategori</label>
                <input type="text" id="p_category" placeholder="Kategori">
            </div>
            <div class="form-group">
                <label>Satuan</label>
                <input type="text" id="p_unit" placeholder="pcs, kg, botol, dll">
            </div>
            <div class="form-group">
                <label>Gambar (URL)</label>
                <input type="text" id="p_image" placeholder="https://example.com/image.jpg">
            </div>
            <div class="form-group">
                <label>Atau Unggah Gambar</label>
                <input type="file" id="p_image_file" accept="image/*">
                <div id="p_image_preview" style="margin-top:8px;"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeProductModal()">Batal</button>
                <button class="btn btn-primary" onclick="saveProduct()">Simpan</button>
            </div>
        </div>
    </div>

    <!-- View Payment Proof Modal -->
    <div id="proofModal" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">Bukti Pembayaran Transfer</div>
            <div style="margin: 20px 0;">
                <p id="proofOrderInfo" style="margin: 0 0 12px 0; color: var(--text-muted); font-size: 14px;"></p>
                <div id="proofPreview" style="text-align: center; min-height: 200px; background: #f5f5f5; border-radius: 8px; display: flex; align-items: center; justify-content: center; overflow: auto;"></div>
            </div>
            <div style="display: flex; gap: 12px; margin-top: 20px;">
                <button class="btn btn-primary" id="confirmPaymentBtn" onclick="confirmPayment()" style="flex: 1;">✓ Konfirmasi Pembayaran</button>
                <button class="btn btn-outline" onclick="closeProofModal()" style="flex: 1;">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        const ADMIN_LS_KEY = 'pos_admin_v1';
        let adminState = {
            products: [],
            orders: [],
            editingId: null
        };

        function init() {
            // Load products from database instead of localStorage
            loadProductsFromDB();
            
            // Auto-generate monthly report for current month if it doesn't exist
            checkAndGenerateMonthlyReport();
        }

        function loadProductsFromDB() {
            fetch('api/products.php?action=list')
                .then(r => r.json())
                .then(data => {
                    if (data.ok && data.products && data.products.length > 0) {
                        // Normalize types (id, price, stock) from DB and include image
                        adminState.products = data.products.map(p => ({
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
                        // Fallback to default products if database is empty
                        adminState.products = [
                            {id:1, name:'Beras 5kg', code:'BR05', price:65000, stock:20, category:'Beras', unit:'pack'},
                            {id:2, name:'Gula 1kg', code:'GL01', price:15000, stock:50, category:'Bumbu', unit:'pack'},
                            {id:3, name:'Minyak 2L', code:'MK02', price:30000, stock:30, category:'Minyak', unit:'botol'},
                            {id:4, name:'Sarden Kaleng', code:'SD01', price:12000, stock:60, category:'Makanan', unit:'pcs'},
                            {id:5, name:'Rokok A', code:'RK01', price:20000, stock:80, category:'Rokok', unit:'pcs'},
                            {id:6, name:'Kopi 250g', code:'KP25', price:22000, stock:40, category:'Minuman', unit:'pack'}
                        ];
                        // Save default products to database
                        adminState.products.forEach(p => {
                            saveProductToDB(p);
                        });
                    }
                    renderAll();
                })
                .catch(err => {
                    console.error('Error loading products:', err);
                    // Fallback
                    adminState.products = [
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

        function checkAndGenerateMonthlyReport() {
            const now = new Date();
            const month = now.getMonth() + 1;
            const year = now.getFullYear();
            
            fetch(`api/reports.php?action=get&year=${year}&month=${month}`)
                .then(r => r.json())
                .then(data => {
                    if (!data.ok) {
                        // Report doesn't exist for current month, auto-generate it
                        const formData = new FormData();
                        formData.append('month', month);
                        formData.append('year', year);
                        
                        return fetch('api/reports.php?action=generate', {
                            method: 'POST',
                            body: formData
                        });
                    }
                })
                .then(r => r && r.json())
                .then(data => {
                    if (data && data.ok) {
                        console.log('Monthly report auto-generated');
                        loadMonthlyReports();
                    }
                })
                .catch(err => console.log('Auto-check completed (report may already exist)'));
        }


        function saveAdminState() {
            // Save all products to database
            adminState.products.forEach(p => {
                saveProductToDB(p);
            });
            renderAll();
        }

        function saveProductToDB(product) {
            const formData = new FormData();
            formData.append('id', product.id);
            formData.append('name', product.name);
            formData.append('code', product.code);
            formData.append('price', product.price);
            formData.append('stock', product.stock);
            formData.append('category', product.category);
            formData.append('unit', product.unit);
            formData.append('image', product.image || '');

            fetch('api/products.php?action=create', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .catch(err => console.error('Error saving product:', err));
        }

        function deleteProductFromDB(id) {
            const formData = new FormData();
            formData.append('id', id);

            return fetch('api/products.php?action=delete', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json());
        }

        function format(n) {
            return Number(n).toLocaleString('id-ID');
        }

        function switchTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab').forEach(el => el.classList.remove('active'));
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }

        function renderProducts() {
            const table = document.getElementById('productsTable');
            table.innerHTML = '';
            adminState.products.forEach(p => {
                const row = document.createElement('tr');
                const imgHtml = p.image ? `<img src="${p.image}" alt="${p.name}" style="width:48px;height:48px;object-fit:cover;border-radius:4px;">` : '<div style="width:48px;height:48px;background:#f0f0f0;border-radius:4px;display:flex;align-items:center;justify-content:center;color:#999">no</div>';
                row.innerHTML = `
                    <td>${imgHtml}</td>
                    <td><strong>${p.name}</strong></td>
                    <td>${p.code}</td>
                    <td>Rp ${format(p.price)}</td>
                    <td>
                        <span class="badge ${p.stock < 20 ? 'badge-warning' : 'badge-success'}">
                            ${p.stock} ${p.unit}
                        </span>
                    </td>
                    <td>${p.category}</td>
                    <td>
                        <button class="btn btn-primary btn-small" onclick="editProduct(${p.id})">Edit</button>
                        <button class="btn btn-danger btn-small" onclick="deleteProduct(${p.id})">Hapus</button>
                    </td>
                `;
                table.appendChild(row);
            });
        }

        function openAddProductModal() {
            adminState.editingId = null;
            document.getElementById('modalTitle').innerText = 'Tambah Produk Baru';
            document.getElementById('p_name').value = '';
            document.getElementById('p_code').value = '';
            document.getElementById('p_price').value = '';
            document.getElementById('p_stock').value = '';
            document.getElementById('p_category').value = '';
            document.getElementById('p_unit').value = '';
            document.getElementById('p_image').value = '';
            const fileEl = document.getElementById('p_image_file'); if (fileEl) fileEl.value = '';
            const prev = document.getElementById('p_image_preview'); if (prev) prev.innerHTML = '';
            document.getElementById('productModal').classList.add('active');
        }

        function closeProductModal() {
            document.getElementById('productModal').classList.remove('active');
        }

        function editProduct(id) {
            const p = adminState.products.find(x => x.id === id);
            if (!p) return;
            adminState.editingId = id;
            document.getElementById('modalTitle').innerText = 'Edit Produk';
            document.getElementById('p_name').value = p.name;
            document.getElementById('p_code').value = p.code;
            document.getElementById('p_price').value = p.price;
            document.getElementById('p_stock').value = p.stock;
            document.getElementById('p_category').value = p.category;
            document.getElementById('p_unit').value = p.unit;
            document.getElementById('p_image').value = p.image || '';
            const fileEl2 = document.getElementById('p_image_file'); if (fileEl2) fileEl2.value = '';
            const prev2 = document.getElementById('p_image_preview'); if (prev2) prev2.innerHTML = p.image ? `<img src="${p.image}" style="max-width:120px;border-radius:6px;">` : '';
            document.getElementById('productModal').classList.add('active');
        }

        function saveProduct() {
            const p = {
                id: adminState.editingId || Date.now(),
                name: document.getElementById('p_name').value || 'Produk',
                code: document.getElementById('p_code').value || 'SKU' + Date.now(),
                price: Number(document.getElementById('p_price').value) || 0,
                stock: Number(document.getElementById('p_stock').value) || 0,
                category: document.getElementById('p_category').value || 'Umum',
                unit: document.getElementById('p_unit').value || 'pcs',
                image: document.getElementById('p_image').value || ''
            };

            // If a file is selected, upload it along with product data
            const fileEl = document.getElementById('p_image_file');
            if (fileEl && fileEl.files && fileEl.files[0]) {
                const fd = new FormData();
                fd.append('id', p.id);
                fd.append('name', p.name);
                fd.append('code', p.code);
                fd.append('price', p.price);
                fd.append('stock', p.stock);
                fd.append('category', p.category);
                fd.append('unit', p.unit);
                // prefer file upload; leave 'image' empty so server uses uploaded file
                fd.append('image_file', fileEl.files[0]);

                fetch('api/products.php?action=create', { method: 'POST', body: fd })
                    .then(r => r.json())
                    .then(res => {
                        if (res.ok) {
                            p.image = res.image || p.image;
                            if (adminState.editingId) {
                                adminState.products = adminState.products.map(x => x.id === adminState.editingId ? p : x);
                            } else {
                                adminState.products.push(p);
                            }
                            closeProductModal();
                            renderAll();
                        } else {
                            alert('Gagal menyimpan produk: ' + (res.msg || 'Unknown'));
                        }
                    })
                    .catch(err => { console.error(err); alert('Error saat mengunggah gambar'); });
                return;
            }

            // No file -> use existing flow (save URL or blank)
            if (adminState.editingId) {
                adminState.products = adminState.products.map(x => x.id === adminState.editingId ? p : x);
            } else {
                adminState.products.push(p);
            }

            // Persist single product immediately (uses image URL field)
            saveProductToDB(p);
            closeProductModal();
            renderAll();
        }

        function deleteProduct(id) {
            if (confirm('Hapus produk ini?')) {
                // Delete from database first
                deleteProductFromDB(id).then(result => {
                    if (result.ok) {
                        // Then remove from state
                        adminState.products = adminState.products.filter(p => p.id !== id);
                        renderProducts();
                    } else {
                        alert('Gagal menghapus produk: ' + (result.msg || 'Unknown error'));
                    }
                });
            }
        }

        function renderOrders() {
            const table = document.getElementById('ordersTable');
            table.innerHTML = '';

            // Fetch orders from API
            fetch('./api/orders.php?action=list')
                .then(res => res.json())
                .then(data => {
                    if (data.ok && data.orders.length > 0) {
                        let totalOrders = data.orders.length;
                        let totalSales = 0;
                        let avgOrder = 0;

                        data.orders.forEach(order => {
                            totalSales += order.final_amount;
                            const row = document.createElement('tr');
                            const itemCount = order.items ? order.items.length : 0;
                            const status = order.status || 'pending';
                            const statusBadge = status === 'pending' ? `<span class="badge badge-warning">${status}</span>` : (status === 'completed' ? `<span class="badge badge-success">${status}</span>` : `<span class="badge">${status}</span>`);
                            row.innerHTML = `
                                <td><strong>${order.order_number}</strong></td>
                                <td>${order.customer_name}</td>
                                <td>${order.customer_phone}</td>
                                <td>Rp ${format(order.final_amount)}</td>
                                <td>${itemCount} item</td>
                                <td>${new Date(order.created_at).toLocaleString('id-ID')}</td>
                                <td>${order.payment_method || 'TUNAI'}</td>
                                <td>${statusBadge}</td>
                                <td>
                                    ${order.payment_proof ? `<button class="btn btn-primary btn-small" onclick="viewProof(${order.id}, '${order.payment_proof}', '${order.order_number}')">Lihat</button>` : '-'}
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-small" onclick="viewOrderDetail(${order.id})">Lihat</button>
                                </td>
                                <td>
                                    <button class="btn btn-success btn-small" onclick="updateOrderStatus(${order.id}, 'completed')">Selesai</button>
                                </td>
                                <td>
                                    <button class="btn btn-danger btn-small" onclick="updateOrderStatus(${order.id}, 'cancelled')">Batal</button>
                                </td>
                            `;
                            table.appendChild(row);
                        });

                        avgOrder = Math.round(totalSales / totalOrders);

                        document.getElementById('totalOrders').innerText = totalOrders;
                        document.getElementById('totalSales').innerText = 'Rp ' + format(totalSales);
                        document.getElementById('avgOrder').innerText = 'Rp ' + format(avgOrder);
                    } else {
                        table.innerHTML = '<tr><td colspan="10" style="text-align: center; color: var(--text-muted);">Belum ada pesanan</td></tr>';
                    }
                })
                .catch(err => {
                    console.error('Error fetching orders:', err);
                    table.innerHTML = '<tr><td colspan="7" style="text-align: center; color: var(--danger);">Error loading orders</td></tr>';
                });
        }

        function viewOrderDetail(orderId) {
            fetch('./api/orders.php?action=get&id=' + orderId)
                .then(res => res.json())
                .then(data => {
                    if (data.ok) {
                        const order = data.order;
                        let detail = 'No. Pesanan: ' + order.order_number + '\n\n';
                        detail += 'DATA CUSTOMER\n';
                        detail += 'Nama: ' + order.customer_name + '\n';
                        detail += 'Telepon: ' + order.customer_phone + '\n';
                        detail += 'Alamat: ' + (order.customer_address || '-') + '\n\n';
                        detail += 'DETAIL PESANAN\n';
                        order.items.forEach(item => {
                            detail += item.product_name + ' x' + item.quantity + ' = Rp ' + format(item.subtotal) + '\n';
                        });
                        detail += '\nTotal: Rp ' + format(order.final_amount);
                        alert(detail);
                    }
                })
                .catch(err => console.error(err));
        }

        function updateOrderStatus(orderId, status) {
            if (!confirm('Ubah status pesanan ke "' + status + '"?')) return;
            const fd = new FormData();
            fd.append('id', orderId);
            fd.append('status', status);

            fetch('./api/orders.php?action=update_status', { method: 'POST', body: fd })
                .then(res => res.json())
                .then(data => {
                    if (data.ok) {
                        renderOrders();
                    } else {
                        alert('Gagal mengubah status: ' + (data.msg || 'Unknown'));
                    }
                })
                .catch(err => { console.error(err); alert('Error saat mengubah status'); });
        }

        let currentProofOrderId = null;

        function viewProof(orderId, proofPath, orderNumber) {
            currentProofOrderId = orderId;
            const preview = document.getElementById('proofPreview');
            const info = document.getElementById('proofOrderInfo');
            info.innerText = 'Pesanan: ' + orderNumber + ' | Path: ' + proofPath;
            
            const ext = proofPath.toLowerCase().split('.').pop();
            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                preview.innerHTML = `<img src="${proofPath}" style="max-width: 100%; max-height: 400px; border-radius: 6px;">`;
            } else if (ext === 'pdf') {
                preview.innerHTML = `<iframe src="${proofPath}" style="width: 100%; height: 400px; border: none; border-radius: 6px;"></iframe>`;
            } else {
                preview.innerHTML = `<div style="padding: 20px; text-align: center;"><p>File: ${proofPath}</p><a href="${proofPath}" target="_blank" class="btn btn-primary">Buka File</a></div>`;
            }
            
            document.getElementById('proofModal').classList.add('active');
        }

        function closeProofModal() {
            document.getElementById('proofModal').classList.remove('active');
            currentProofOrderId = null;
        }

        function confirmPayment() {
            if (!currentProofOrderId) {
                alert('Pesanan tidak ditemukan');
                return;
            }
            if (!confirm('Konfirmasi pembayaran untuk pesanan ini?')) return;

            const fd = new FormData();
            fd.append('id', currentProofOrderId);

            fetch('./api/orders.php?action=confirm_payment', { method: 'POST', body: fd })
                .then(res => res.json())
                .then(data => {
                    if (data.ok) {
                        alert('Pembayaran berhasil dikonfirmasi');
                        closeProofModal();
                        renderOrders();
                    } else {
                        alert('Gagal mengonfirmasi pembayaran: ' + (data.msg || 'Unknown'));
                    }
                })
                .catch(err => { console.error(err); alert('Error saat mengonfirmasi pembayaran'); });
        }

        function resetOrders() {
            if (!confirm('Yakin ingin menghapus SEMUA pesanan dan item pesanan? Tindakan ini TIDAK bisa dikembalikan.')) return;
            fetch('./api/orders.php?action=reset', { method: 'POST' })
                .then(res => res.json())
                .then(data => {
                    if (data.ok) {
                        alert('Semua pesanan berhasil dihapus');
                        renderOrders();
                        renderReports();
                    } else {
                        alert('Gagal mereset pesanan: ' + (data.msg || 'Unknown'));
                    }
                })
                .catch(err => { console.error(err); alert('Error saat mereset pesanan'); });
        }

        function renderReports() {
            loadReport('daily');
        }

        let currentReportPeriod = 'daily';

        function loadReport(period) {
            currentReportPeriod = period;

            // Update button styles
            document.querySelectorAll('#reports .btn').forEach(btn => {
                btn.style.background = 'transparent';
                btn.style.color = 'var(--text-muted)';
            });

            const periodLabels = {
                'daily': 'Hari Ini',
                'weekly': 'Minggu Ini',
                'monthly': 'Bulan Ini',
                'all': 'Sepanjang Waktu'
            };

            const periodLabel = periodLabels[period];

            document.getElementById('periodLabel').innerText = 'Total Pendapatan ' + periodLabel;
            document.getElementById('avgLabel').innerText = 'Rata-rata Order ' + periodLabel;

            const breakdownTitles = {
                'daily': 'Riwayat Per Jam',
                'weekly': 'Penjualan Per Hari (Minggu Ini)',
                'monthly': 'Penjualan Per Hari (Bulan Ini)',
                'all': 'Penjualan Per Hari'
            };
            document.getElementById('breakdownTitle').innerText = breakdownTitles[period];

            const lowStockItems = adminState.products.filter(p => p.stock < 20);
            document.getElementById('totalProducts').innerText = adminState.products.length;
            document.getElementById('lowStock').innerText = lowStockItems.length;

            renderLowStock();
            renderTopProducts();

            // Fetch report data
            fetch('./api/orders.php?action=stats&period=' + period)
                .then(res => res.json())
                .then(data => {
                    if (data.ok) {
                        const summary = data.summary;
                        document.getElementById('totalRevenue').innerText = 'Rp ' + format(summary.total_revenue || 0);
                        document.getElementById('avgRevenue').innerText = 'Rp ' + format(summary.avg_order_value || 0);

                        renderDailyBreakdown(data.daily);
                    }
                })
                .catch(err => console.error(err));
        }

        function renderLowStock() {
            const lowStockItems = adminState.products.filter(p => p.stock < 20);
            const table = document.getElementById('lowStockTable');
            table.innerHTML = '';
            if (lowStockItems.length === 0) {
                table.innerHTML = '<tr><td colspan="3" style="text-align: center; color: var(--text-muted);">Semua stok normal</td></tr>';
            } else {
                lowStockItems.forEach(p => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td><strong>${p.name}</strong></td>
                        <td>${p.stock} ${p.unit}</td>
                        <td><span class="badge badge-warning">Perlu Restock</span></td>
                    `;
                    table.appendChild(row);
                });
            }
        }

        function renderTopProducts() {
            fetch('./api/orders.php?action=list')
                .then(res => res.json())
                .then(data => {
                    if (data.ok) {
                        const productSales = {};
                        data.orders.forEach(order => {
                            if (order.items) {
                                order.items.forEach(item => {
                                    if (!productSales[item.product_name]) {
                                        productSales[item.product_name] = {qty: 0, total: 0};
                                    }
                                    productSales[item.product_name].qty += item.quantity;
                                    productSales[item.product_name].total += item.subtotal;
                                });
                            }
                        });

                        const topProducts = Object.entries(productSales)
                            .sort((a, b) => b[1].qty - a[1].qty)
                            .slice(0, 10);

                        const topTable = document.getElementById('topProductsTable');
                        topTable.innerHTML = '';
                        if (topProducts.length === 0) {
                            topTable.innerHTML = '<tr><td colspan="3" style="text-align: center; color: var(--text-muted);">Tidak ada penjualan</td></tr>';
                        } else {
                            topProducts.forEach(([name, sales]) => {
                                const row = document.createElement('tr');
                                row.innerHTML = `
                                    <td><strong>${name}</strong></td>
                                    <td>${sales.qty} unit</td>
                                    <td>Rp ${format(sales.total)}</td>
                                `;
                                topTable.appendChild(row);
                            });
                        }
                    }
                })
                .catch(err => console.error(err));
        }

        function renderDailyBreakdown(daily) {
            const table = document.getElementById('dailyBreakdownTable');
            table.innerHTML = '';
            if (!daily || daily.length === 0) {
                table.innerHTML = '<tr><td colspan="3" style="text-align: center; color: var(--text-muted);">Tidak ada data penjualan</td></tr>';
            } else {
                daily.forEach(day => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${new Date(day.order_date).toLocaleDateString('id-ID')}</td>
                        <td>${day.daily_orders} pesanan</td>
                        <td><strong>Rp ${format(day.daily_revenue || 0)}</strong></td>
                    `;
                    table.appendChild(row);
                });
            }
        }

        function loadMonthlyReports() {
            fetch('api/reports.php?action=list')
                .then(r => r.json())
                .then(data => {
                    if (data.ok) {
                        renderMonthlyReports(data.reports || []);
                    }
                })
                .catch(err => console.error('Error loading monthly reports:', err));
        }

        function renderMonthlyReports(reports) {
            const table = document.getElementById('monthlyReportsTable');
            table.innerHTML = '';
            if (!reports || reports.length === 0) {
                table.innerHTML = '<tr><td colspan="8" style="text-align: center; color: var(--text-muted);">Belum ada laporan bulanan</td></tr>';
                return;
            }
            
            const monthNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                              'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            
            reports.forEach(report => {
                const row = document.createElement('tr');
                const monthName = monthNames[report.month] || 'Bulan ' + report.month;
                const generatedAt = new Date(report.generated_at).toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                row.innerHTML = `
                    <td><strong>${monthName} ${report.year}</strong></td>
                    <td>${report.total_orders} pesanan</td>
                    <td>Rp ${format(report.total_revenue)}</td>
                    <td>${report.total_items} unit</td>
                    <td>Rp ${format(report.avg_order_value)}</td>
                    <td>${report.top_product || 'N/A'}</td>
                    <td><small>${generatedAt}</small></td>
                    <td>
                        <button class="btn btn-primary btn-small" onclick="viewMonthlyReport(${report.year}, ${report.month})">Lihat</button>
                        <button class="btn btn-outline btn-small" onclick="exportMonthlyReport(${report.year}, ${report.month})" style="background: var(--accent); border: none; color: white;">CSV</button>
                        <button class="btn btn-outline btn-small" onclick="exportMonthlyReportExcel(${report.year}, ${report.month})" style="background: #10b981; border: none; color: white;">Excel</button>
                    </td>
                `;
                table.appendChild(row);
            });
        }

        function generateMonthlyReport() {
            const now = new Date();
            const month = now.getMonth() + 1;
            const year = now.getFullYear();
            
            if (confirm('Buat laporan penjualan untuk ' + (monthNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'])[month] + ' ' + year + '?')) {
                const formData = new FormData();
                formData.append('month', month);
                formData.append('year', year);
                
                fetch('api/reports.php?action=generate', {
                    method: 'POST',
                    body: formData
                })
                .then(r => r.json())
                .then(data => {
                    if (data.ok) {
                        alert('Laporan berhasil dibuat!');
                        loadMonthlyReports();
                    } else {
                        alert('Gagal membuat laporan: ' + (data.msg || 'Unknown error'));
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    alert('Gagal membuat laporan');
                });
            }
        }

        function viewMonthlyReport(year, month) {
            fetch(`api/reports.php?action=get&year=${year}&month=${month}`)
                .then(r => r.json())
                .then(data => {
                    if (data.ok) {
                        const report = data.report;
                        const reportData = typeof report.report_data === 'string' ? JSON.parse(report.report_data) : report.report_data;
                        const monthNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                          'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        
                        let html = `
                            <h3>Laporan Penjualan ${monthNames[month]} ${year}</h3>
                            <table style="width: 100%; border-collapse: collapse; margin-top: 16px;">
                                <tr style="background: #f0f0f0;">
                                    <td style="padding: 8px; border: 1px solid #ddd;"><strong>Total Pesanan</strong></td>
                                    <td style="padding: 8px; border: 1px solid #ddd;">${report.total_orders} pesanan</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px; border: 1px solid #ddd;"><strong>Total Penjualan</strong></td>
                                    <td style="padding: 8px; border: 1px solid #ddd;">Rp ${format(report.total_revenue)}</td>
                                </tr>
                                <tr style="background: #f0f0f0;">
                                    <td style="padding: 8px; border: 1px solid #ddd;"><strong>Total Item Terjual</strong></td>
                                    <td style="padding: 8px; border: 1px solid #ddd;">${report.total_items} unit</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px; border: 1px solid #ddd;"><strong>Rata-rata Order</strong></td>
                                    <td style="padding: 8px; border: 1px solid #ddd;">Rp ${format(report.avg_order_value)}</td>
                                </tr>
                                <tr style="background: #f0f0f0;">
                                    <td style="padding: 8px; border: 1px solid #ddd;"><strong>Produk Terlaris</strong></td>
                                    <td style="padding: 8px; border: 1px solid #ddd;">${report.top_product} (${report.top_product_qty} unit)</td>
                                </tr>
                            </table>
                        `;
                        
                        if (reportData.product_sales && Object.keys(reportData.product_sales).length > 0) {
                            html += '<h4 style="margin-top: 20px;">Penjualan Per Produk</h4><table style="width: 100%; border-collapse: collapse; margin-top: 8px;">';
                            Object.entries(reportData.product_sales).forEach(([product, qty]) => {
                                html += `<tr><td style="padding: 8px; border: 1px solid #ddd;">${product}</td><td style="padding: 8px; border: 1px solid #ddd; text-align: right;">${qty} unit</td></tr>`;
                            });
                            html += '</table>';
                        }
                        
                        alert(html);
                    } else {
                        alert('Laporan tidak ditemukan');
                    }
                })
                .catch(err => console.error('Error:', err));
        }

        function exportMonthlyReport(year, month) {
            window.location.href = `api/reports.php?action=export&year=${year}&month=${month}`;
        }

        function exportMonthlyReportExcel(year, month) {
            window.location.href = `api/reports.php?action=export_excel&year=${year}&month=${month}`;
        }

        function renderAll() {
            renderProducts();
            renderOrders();
            renderReports();
            loadMonthlyReports();
        }

        init();
    </script>
</body>
</html>
