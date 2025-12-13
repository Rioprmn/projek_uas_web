<?php
session_start();
$role = $_SESSION['role'] ?? 'customer';
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Pet Shop - Dashboard</title>
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
        .hero{background:linear-gradient(135deg,#1e824c 0%,#2ecc71 100%);color:white;border-radius:24px;padding:60px 50px;display:flex;gap:50px;align-items:center;margin-bottom:40px;box-shadow:0 20px 60px rgba(0,0,0,0.15);width:100%;position:relative;overflow:hidden;min-height:380px}
        .hero::before{content:'';position:absolute;top:-50%;left:-50%;width:200%;height:200%;background:radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);pointer-events:none;animation:pulse 4s ease-in-out infinite}
        .hero .hero-left{flex:1;position:relative;z-index:2;display:flex;flex-direction:column;justify-content:center;text-align:left}
        .hero h3{margin:0 0 16px 0;font-size:44px;font-weight:800;line-height:1.2;animation:slideInLeft 1s ease-out;text-shadow:0 2px 4px rgba(0,0,0,0.1)}
        .hero p{margin:0 0 24px 0;color:rgba(255,255,255,0.95);font-size:18px;font-weight:400;animation:slideInLeft 1s ease-out 0.2s both;text-shadow:0 1px 2px rgba(0,0,0,0.1)}
        .hero img{width:480px;height:360px;object-fit:cover;border-radius:16px;position:relative;z-index:2;animation:slideInRight 1s ease-out;border:2px solid rgba(255,255,255,0.2);box-shadow:0 15px 40px rgba(0,0,0,0.2)}
        .categories{display:flex;gap:24px;justify-content:center;align-items:center;flex-wrap:wrap;padding:40px 0;margin-bottom:40px;max-width:1000px;margin-left:auto;margin-right:auto}
        .cat-card{min-width:160px;max-width:180px;background:var(--card);border-radius:16px;padding:24px 16px;box-shadow:0 12px 40px rgba(0,0,0,0.1);display:flex;flex-direction:column;align-items:center;gap:12px;cursor:pointer;transition:all 0.4s ease;border:1px solid rgba(0,0,0,0.08);position:relative;overflow:hidden;animation:fadeIn 0.6s ease-out forwards;opacity:0}
        .cat-card::before{content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.2),transparent);transition:all 0.5s ease}
        .cat-card:hover::before{left:100%}
        .cat-card:hover{transform:translateY(-8px) scale(1.05);box-shadow:0 20px 60px rgba(0,0,0,0.15)}
        .cat-card img{width:64px;height:64px;border-radius:12px;transition:transform 0.3s ease}
        .cat-card:hover img{transform:scale(1.1)}
        .icon-container{display:flex;align-items:center;justify-content:center;width:64px;height:64px;font-size:28px;border-radius:12px;background:rgba(76,175,80,0.1);transition:transform 0.3s ease;border:1px solid rgba(76,175,80,0.2)}
        .icon-container img{width:48px;height:48px;object-fit:contain}
        .cat-card:hover .icon-container{transform:scale(1.1)}
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
        <div style="display:flex;align-items:center;gap:12px"><strong style="color:#2e7d32">Pet Shop </strong></div>
        <div style="display:flex;gap:12px;align-items:center">
            <button onclick="showLocationModal()" class="btn" style="background:#ff9800;color:white;font-weight:600;border:1px solid #f57c00">📍 Lokasi Toko</button>
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
                <h3>Semua Kebutuhan Hewan Peliharaan Anda, Dalam Satu Tempat. <br>
                <span style="color:#fff7c2;">Temukan makanan, vitamin, dan perlengkapan hewan terlengkap di sini.</span>
                </h3>
                <p>Lihat Katalog Produk</p>
            </div>
            <div class="hero-right">
                <img src="uploads/bg.png" alt="Pet shop with animals">
            </div>
        </div>

        <div class="categories" id="categoryRow"></div>

        <script>
            const categories = [
                {id:1,name:'Makanan Kucing',icon:'https://img.icons8.com/color/96/000000/cat.png',fallback:'🐱'},
                {id:2,name:'Makanan Anjing',icon:'https://img.icons8.com/color/96/000000/dog.png',fallback:'🐶'},
                {id:3,name:'Makanan Ikan',icon:'https://img.icons8.com/color/96/000000/fish.png',fallback:'🐠'},
                {id:4,name:'Makanan Burung',icon:'https://img.icons8.com/color/96/000000/bird.png',fallback:'🐦'},
                {id:5,name:'Aksesoris Hewan',icon:'https://img.icons8.com/color/96/000000/dog-leash.png',fallback:'🦮'},
                {id:6,name:'Vitamin & Obat',icon:'https://img.icons8.com/color/96/000000/pill.png',fallback:'💊'},
                {id:7,name:'Perawatan Hewan',icon:'https://img.icons8.com/color/96/000000/scissors.png',fallback:'✂️'},
                {id:8,name:'Kandang & Peralatan',icon:'https://img.icons8.com/color/96/000000/cage.png',fallback:'🏠'},
                {id:9,name:'Mainan Hewan',icon:'https://img.icons8.com/color/96/000000/dog-toy.png',fallback:'🧸'},
                {id:10,name:'Snack Hewan',icon:'https://img.icons8.com/color/96/000000/dog-bone.png',fallback:'🦴'}
            ];
            const el = document.getElementById('categoryRow');
            categories.forEach((c, index)=>{
                const card = document.createElement('div');
                card.className = 'cat-card';
                card.innerHTML = `<div class="icon-container"><img src="${c.icon}" alt="${c.name}" onerror="this.parentElement.innerHTML='${c.fallback}';" /></div><div class="cat-name">${c.name}</div>`;
                card.style.animationDelay = `${index * 0.1}s`;
                card.addEventListener('click', ()=>{ location.href = 'customer.php?view=shop&cat='+encodeURIComponent(c.name); });
                el.appendChild(card);
            });
        </script>

        <!-- Location Modal -->
        <div id="locationModal" class="modal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);z-index:10000;justify-content:center;align-items:center;">
            <div class="modal-content" style="background:white;border-radius:16px;max-width:800px;width:90%;max-height:90vh;overflow-y:auto;">
                <div class="modal-header" style="padding:20px;border-bottom:1px solid #eee;font-size:24px;font-weight:700;color:#2e7d32;">📍 Lokasi Toko Pet Shop</div>
                <div style="padding:20px;">
                    <div style="margin-bottom:20px;">
                        <h3 style="color:#4caf50;margin-bottom:10px;">Alamat Toko:</h3>
                        <p style="margin:0;color:#333;">
                            <strong>Pet Shop</strong><br>
                            548 Jl. Raya Ciwidey<br>
                            Pasirjambu, Jawa Barat<br><br>
                            <strong>Jam Operasional:</strong><br>
                            Senin - Minggu: 08:00 - 20:00 WIB<br><br>
                            <strong>Kontak:</strong><br>
                            📞 (021) 1234-5678<br>
                            📱 +62 812-3456-7890
                        </p>
                    </div>
                    <div style="border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.5!2d107.35!3d-7.08!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68c8b8b8b8b8b8b%3A0x1234567890abcdef!2s548%20Jl.%20Raya%20Ciwidey%2C%20Pasirjambu%2C%20Jawa%20Barat!5e0!3m2!1sen!2sid!4v1700000000000!5m2!1sen!2sid" 
                            width="100%" 
                            height="300" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                    <div style="margin-top:15px;text-align:center;">
                        <a href="https://maps.google.com/?q=548+Jl.+Raya+Ciwidey,+Pasirjambu,+Jawa+Barat,+Indonesia" 
                           target="_blank" 
                           style="background:#4caf50;color:white;padding:10px 20px;text-decoration:none;border-radius:8px;display:inline-block;font-weight:600;">
                            🗺️ Buka di Google Maps
                        </a>
                    </div>
                </div>
                <div style="padding:20px;border-top:1px solid #eee;text-align:right;">
                    <button onclick="closeLocationModal()" style="background:#666;color:white;border:none;padding:10px 20px;border-radius:8px;cursor:pointer;">Tutup</button>
                </div>
            </div>
        </div>

        <script>
            function showLocationModal() {
                document.getElementById('locationModal').style.display = 'flex';
            }

            function closeLocationModal() {
                document.getElementById('locationModal').style.display = 'none';
            }
        </script>
    </div>
</body>
</html>
