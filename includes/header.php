<!-- <?php
// Header include: contains the document head and sidebar
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Pet Shop </title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    :root{
      --bg:#081126; --card:#07132a; --muted:#cbd5e1; --accent:#ff7a18; --accent-2:#7b61ff; --glass: rgba(255,255,255,0.04);
      --accent-3:#06b6d4; --success:#10b981; --danger:#ef4444; --surface:#071226;
    }
    *{box-sizing:border-box;font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,'Helvetica Neue',Arial}
    body{margin:0;background:linear-gradient(180deg,var(--bg),#051028);color:#e6eef6;min-height:100vh}
    .app{display:grid;grid-template-columns:260px 1fr;gap:20px;padding:24px}
    .sidebar{background:linear-gradient(180deg,var(--card),#061126);border-radius:16px;padding:18px;height:calc(100vh - 48px);position:sticky;top:24px}
    .brand{display:flex;gap:12px;align-items:center;margin-bottom:18px}
    .logo{width:44px;height:44px;border-radius:10px;background:linear-gradient(135deg,var(--accent),var(--accent-2));display:flex;align-items:center;justify-content:center;font-weight:800;color:#042;box-shadow:0 8px 20px rgba(139,92,246,0.12)}
    h1{font-size:18px;margin:0}
    .nav{margin-top:12px}
    .nav a{display:flex;gap:12px;padding:10px;border-radius:10px;color:var(--muted);text-decoration:none;margin-bottom:6px}
    .nav a.active, .nav a:hover{background:var(--glass);color:#fff}
    .search{display:flex;gap:8px;margin-top:12px}
    .search input{flex:1;padding:8px;border-radius:10px;border:none;background:transparent;color:inherit;outline:none}

    .main{padding:18px}
    .topbar{display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:18px}
    .card{background:linear-gradient(180deg,rgba(255,255,255,0.02),transparent);padding:14px;border-radius:12px;box-shadow:0 6px 20px rgba(2,6,23,0.6)}
    .grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}
    .product-area{display:grid;grid-template-columns:1fr 420px;gap:16px}
    .products{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}
    .product{padding:12px;border-radius:10px;background:linear-gradient(180deg, rgba(255,255,255,0.01), transparent);cursor:pointer;transition:transform .12s ease;}
    .product:hover{transform:translateY(-6px)}
    .product .title{font-weight:700}
    .product .meta{font-size:12px;color:var(--muted)}

    .cart{width:100%;max-height:70vh;overflow:auto;padding:12px;border-radius:12px}
    .cart-item{display:flex;justify-content:space-between;gap:8px;padding:8px;border-radius:8px;background:rgba(0,0,0,0.16);margin-bottom:8px}
    .btn{display:inline-block;padding:8px 12px;border-radius:10px;background:var(--accent);color:#042;text-decoration:none;font-weight:600;border:none;cursor:pointer}
    .btn.ghost{background:transparent;border:1px solid rgba(255,255,255,0.06);color:var(--muted)}
    .btn.red{background:var(--danger);color:#fff}
    .controls{display:flex;gap:8px;align-items:center}
    .small{font-size:13px;color:var(--muted)}

    .modal{position:fixed;inset:0;background:rgba(2,6,23,0.6);display:flex;align-items:center;justify-content:center;padding:24px}
    .modal-card{width:720px;background:linear-gradient(180deg,#071226, #051022);border-radius:12px;padding:18px}

    footer{margin-top:18px;color:var(--muted);font-size:13px}

    /* responsive */
    @media (max-width:1000px){.app{grid-template-columns:1fr} .sidebar{position:static;height:auto;border-radius:12px} .product-area{grid-template-columns:1fr} .products{grid-template-columns:repeat(2,1fr)} }
    @media (max-width:600px){.products{grid-template-columns:1fr}}
  </style>
</head>
<body>
  <div class="app">
    <aside class="sidebar card">
      <div class="brand"><div class="logo">PW</div><div><h1>POS Warung</h1><div class="small">Prototype — Warung &amp; Grosir</div></div></div>
      <div class="search card" style="padding:8px;margin-top:8px">
        <input id="globalSearch" placeholder="Cari produk... (nama/kode)" />
        <button class="btn ghost admin-only" onclick="openAddProduct()">+ Produk</button>
      </div>
      <nav class="nav">
        <a href="#" class="active" onclick="show('pos')">POS &amp; Kasir</a>
        <a href="#" onclick="showProducts()">Produk</a>
        <a href="#" onclick="show('laporan')">Laporan</a>
        <a href="#" onclick="show('member')">Member &amp; Diskon</a>
        <a href="#" onclick="show('setting')">Pengaturan</a>
      </nav>
      <div style="margin-top:12px">
        <div class="small">Saldo Kas</div>
        <div style="font-weight:700;font-size:18px;margin-top:6px">Rp <span id="kas">0</span></div>
      </div>
      <footer>
        v1.0 • Offline-ready • Simpan otomatis
      </footer>
    </aside>

    <main class="main">
      <div class="topbar">
        <div style="display:flex;gap:12px;align-items:center">
          <h2 id="pageTitle">POS & Kasir</h2>
          <div class="small card" style="padding:8px">Mode: <strong id="mode">TUNAI</strong></div>
        </div>
        <div class="controls">
          <select id="storeSwitch" onchange="changeStore(this.value)" class="ghost" style="padding:8px;border-radius:8px;background:transparent;color:inherit">
            <option value="warung">Warung (Kecil)</option>
            <option value="grosir">Grosir (Besar)</option>
          </select>
          <button class="btn" onclick="exportCSV()">Export CSV</button>
          <button class="btn ghost" onclick="clearData()">Reset</button>
          <button id="authBtn" class="btn ghost" onclick="openLogin()">Login Admin</button>
        </div>
      </div> -->
