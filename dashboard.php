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
        body{margin:0;background:linear-gradient(135deg, #1e824c 0%, #2ecc71 100%);color:var(--text);min-height:100vh;position:relative;overflow-x:hidden}
        body::before{content:'';position:absolute;top:0;left:0;right:0;bottom:0;background:url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.03)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.03)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.02)"/><circle cx="10" cy="50" r="0.5" fill="rgba(255,255,255,0.02)"/><circle cx="90" cy="30" r="0.5" fill="rgba(255,255,255,0.02)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');pointer-events:none;z-index:-1}
        .navbar{background:linear-gradient(135deg, #e8f5e8, #f1f8e9);color:#2e7d32;padding:20px 30px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 4px 20px rgba(0,0,0,0.08);position:relative;z-index:10;border-bottom:2px solid #c8e6c9}
        .navbar strong{font-size:24px;font-weight:800;letter-spacing:1px}
        .btn{padding:8px 12px;border-radius:8px;border:none;cursor:pointer}
        .container{max-width:1400px;margin:40px auto;padding:0 30px;text-align:center;position:relative;z-index:1}
        .hero{background:linear-gradient(90deg,#1e824c,#2ecc71);color:white;border-radius:20px;padding:50px 40px;display:flex;gap:40px;align-items:center;margin-bottom:40px;box-shadow:0 15px 50px rgba(0,0,0,0.15);width:100%;position:relative;overflow:hidden;min-height:300px}
        .hero::before{content:'';position:absolute;top:-50%;left:-50%;width:200%;height:200%;background:radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);pointer-events:none;animation:pulse 4s ease-in-out infinite}
        .hero .hero-left{flex:1;position:relative;z-index:2;display:flex;flex-direction:column;justify-content:center;text-align:left}
        .hero h3{margin:0 0 16px 0;font-size:42px;font-weight:800;line-height:1.2;animation:slideInLeft 1s ease-out}
        .hero p{margin:0 0 24px 0;color:rgba(255,255,255,0.95);font-size:18px;font-weight:400;animation:slideInLeft 1s ease-out 0.2s both}
        .hero .hero-cta{padding:16px 32px;border-radius:12px;background:#66bb6a;color:white;font-weight:700;border:none;cursor:pointer;transition:all 0.4s ease;font-size:16px;animation:slideInLeft 1s ease-out 0.4s both}
        .hero .hero-cta:hover{transform:scale(1.08);box-shadow:0 8px 25px rgba(0,0,0,0.3)}
        .hero img{width:400px;height:200px;object-fit:cover;border-radius:15px;position:relative;z-index:2;animation:slideInRight 1s ease-out}
        .categories{display:flex;gap:24px;justify-content:center;align-items:center;flex-wrap:wrap;padding:40px 0;margin-bottom:40px;max-width:1000px;margin-left:auto;margin-right:auto}
        .cat-card{min-width:160px;max-width:180px;background:var(--card);border-radius:16px;padding:24px 16px;box-shadow:0 12px 40px rgba(0,0,0,0.1);display:flex;flex-direction:column;align-items:center;gap:12px;cursor:pointer;transition:all 0.4s ease;border:1px solid rgba(0,0,0,0.08);position:relative;overflow:hidden;animation:fadeIn 0.6s ease-out forwards;opacity:0}
        .cat-card::before{content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.2),transparent);transition:all 0.5s ease}
        .cat-card:hover::before{left:100%}
        .cat-card:hover{transform:translateY(-8px) scale(1.05);box-shadow:0 20px 60px rgba(0,0,0,0.15)}
        .cat-card img{width:64px;height:64px;border-radius:12px;transition:transform 0.3s ease}
        .cat-card:hover img{transform:scale(1.1)}
        .cat-name{font-weight:700;color:var(--text);font-size:16px;text-align:center;line-height:1.3;letter-spacing:0.5px}
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
    </style>
</head>
<body>
    <div class="navbar">
        <div style="display:flex;align-items:center;gap:12px"><strong style="color:#2e7d32">POS Warung &amp; Grosir</strong></div>
        <div style="display:flex;gap:12px;align-items:center">
            <a href="customer.php?view=shop" class="btn btn-primary" style="background:#4caf50;color:white;font-weight:600">🚀 Buat Pesanan Baru</a>
            <?php if(isset($_SESSION['role']) && $_SESSION['role']==='admin'): ?>
                <a href="admin.php" class="btn" style="background:#66bb6a;color:#fff;border:1px solid #4caf50">Admin</a>
            <?php else: ?>
                <a href="login.php" class="btn" style="background:#81c784;color:#2e7d32;border:1px solid #4caf50">Login Admin</a>
            <?php endif; ?>
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
        <div class="hero">
            <div class="hero-left">
                <h3>Stok Warung Habis? <br><span style="color:#fff7c2;text-shadow:0 2px 10px rgba(255,247,194,0.5);">Restock Cepat di Sini.</span></h3>
                <p>Harga khusus mitra. Pesan sekarang, kirim besok pagi.</p>
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
                {id:3,name:'Bumbu',icon:'https://img.icons8.com/fluency/96/000000/seasoning.png'},
                {id:4,name:'Gas',icon:'https://img.icons8.com/fluency/96/000000/gas-pump.png'}
            ];
            const el = document.getElementById('categoryRow');
            categories.forEach((c, index)=>{
                const card = document.createElement('div');
                card.className = 'cat-card';
                card.innerHTML = `<img src="${c.icon}" alt="${c.name}"><div class="cat-name">${c.name}</div>`;
                card.style.animationDelay = `${index * 0.1}s`;
                card.addEventListener('click', ()=>{ location.href = 'customer.php?view=shop&cat='+encodeURIComponent(c.name); });
                el.appendChild(card);
            });
        </script>
    </div>
</body>
</html>
