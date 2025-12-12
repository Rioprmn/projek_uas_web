<?php
session_start();
$role = $_SESSION['role'] ?? 'customer';
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>POS Warung - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root{
            --primary:#667eea; --primary-dark:#764ba2; --bg:#f8f9fa; --card:#fff; --text:#1a1a1a; --border:#e0e0e0;
        }
        *{box-sizing:border-box;font-family:Inter,system-ui, -apple-system,Segoe UI,Roboto,Arial}
        body{margin:0;background:var(--bg);color:var(--text)}
        .navbar{background:linear-gradient(135deg,var(--primary),var(--primary-dark));color:white;padding:16px 24px;display:flex;justify-content:space-between;align-items:center}
        .btn{padding:8px 12px;border-radius:8px;border:none;cursor:pointer}
        .container{max-width:1200px;margin:24px auto;padding:0 20px}
        .hero{background:linear-gradient(90deg,#1e824c,#2ecc71);color:white;border-radius:14px;padding:28px;display:flex;gap:20px;align-items:center;margin-bottom:20px;box-shadow:0 8px 30px rgba(0,0,0,0.08)}
        .hero .hero-left{flex:1}
        .hero h3{margin:0 0 8px 0;font-size:28px}
        .hero p{margin:0 0 12px 0;color:rgba(255,255,255,0.9)}
        .hero .hero-cta{padding:10px 16px;border-radius:10px;background:#ffd166;color:#0b3a2a;font-weight:700;border:none;cursor:pointer}
        .hero img{width:280px;height:120px;object-fit:cover;border-radius:10px}
        .categories{display:flex;gap:12px;overflow:auto;padding:10px 0;margin-bottom:20px}
        .cat-card{min-width:140px;background:var(--card);border-radius:10px;padding:12px;box-shadow:0 6px 18px rgba(0,0,0,0.06);display:flex;flex-direction:column;align-items:center;gap:8px;cursor:pointer}
        .cat-card img{width:56px;height:56px}
        .cat-name{font-weight:700;color:var(--text);font-size:13px}
    </style>
</head>
<body>
    <div class="navbar">
        <div style="display:flex;align-items:center;gap:12px"><strong>POS Warung &amp; Grosir</strong></div>
        <div>
            <?php if(isset($_SESSION['role']) && $_SESSION['role']==='admin'): ?>
                <a href="admin.php" class="btn" style="background:rgba(255,255,255,0.12);color:white">Admin</a>
            <?php else: ?>
                <a href="login.php" class="btn" style="background:rgba(255,255,255,0.12);color:white">Login Admin</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="container">
        <div class="hero">
            <div class="hero-left">
                <h3>Stok Warung Habis? <br><span style="color:#fff7c2;">Restock Cepat di Sini.</span></h3>
                <p>Harga khusus mitra. Pesan sekarang, kirim besok pagi.</p>
                <button class="hero-cta" onclick="location.href='customer.php?view=shop'">Buat Pesanan Baru</button>
            </div>
            <div class="hero-right">
                <img src="https://images.unsplash.com/photo-1542831371-d531d36971e6?w=800&q=60&fit=crop&auto=format" alt="banner">
            </div>
        </div>

        <div class="categories" id="categoryRow"></div>

        <script>
            const categories = [
                {id:1,name:'Sembako',icon:'https://img.icons8.com/fluency/96/000000/groceries.png'},
                {id:2,name:'Minuman',icon:'https://img.icons8.com/fluency/96/000000/water-bottle.png'},
                {id:3,name:'Bumbu Dapur',icon:'https://img.icons8.com/fluency/96/000000/seasoning.png'},
                {id:4,name:'Perlengkapan',icon:'https://img.icons8.com/fluency/96/000000/broom.png'},
                {id:5,name:'Gas & Galon',icon:'https://img.icons8.com/fluency/96/000000/gas-pump.png'}
            ];
            const el = document.getElementById('categoryRow');
            categories.forEach(c=>{
                const card = document.createElement('div');
                card.className = 'cat-card';
                card.innerHTML = `<img src="${c.icon}" alt="${c.name}"><div class="cat-name">${c.name}</div>`;
                card.addEventListener('click', ()=>{ location.href = 'customer.php?view=shop&cat='+encodeURIComponent(c.name); });
                el.appendChild(card);
            });
        </script>
    </div>
</body>
</html>
