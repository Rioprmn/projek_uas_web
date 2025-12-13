<?php
session_start();
require_once __DIR__ . '/config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    
    if ($user === ADMIN_USER && $pass === ADMIN_PASS) {
        $_SESSION['role'] = 'admin';
        $_SESSION['username'] = $user;
        header('Location: admin.php');
        exit;
    } else {
        $error = 'Username atau password salah!';
    }
}

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin.php');
    } else {
        header('Location: customer.php');
    }
    exit;
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>POS Warung - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto; }
        body {
            margin: 0;
            background: linear-gradient(135deg, #1e824c 0%, #2ecc71 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
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
        .login-container {
            background: white;
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 25px 80px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 450px;
            position: relative;
            z-index: 1;
            animation: slideInUp 0.8s ease-out;
        }
        .logo-section {
            text-align: center;
            margin-bottom: 40px;
        }
        .logo {
            width: 100px;
            height: 100px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #4caf50, #2ecc71);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            font-weight: 800;
            color: white;
            box-shadow: 0 12px 30px rgba(76, 175, 80, 0.3);
            animation: bounceIn 1s ease-out 0.2s both;
        }
        h1 {
            margin: 0;
            font-size: 32px;
            color: #1a1a1a;
            font-weight: 700;
            animation: fadeIn 1s ease-out 0.4s both;
        }
        .subtitle {
            color: #666;
            font-size: 16px;
            margin-top: 8px;
            animation: fadeIn 1s ease-out 0.6s both;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        input:focus {
            outline: none;
            border-color: #4caf50;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        }
        .error {
            background: #ffebee;
            border: 1px solid #f44336;
            color: #c62828;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            animation: shake 0.5s ease-in-out;
        }
        .login-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #4caf50, #2ecc71);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
            animation: slideInUp 1s ease-out 0.8s both;
        }
        .login-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(76, 175, 80, 0.4);
        }
        .customer-link {
            text-align: center;
            margin-top: 30px;
            animation: fadeIn 1s ease-out 1s both;
        }
        .customer-link a {
            color: #4caf50;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: color 0.3s ease;
        }
        .customer-link a:hover {
            color: #2ecc71;
            text-decoration: underline;
        }
        .floating-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }
        .shape {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(45deg, rgba(255,255,255,0.2), rgba(255,255,255,0.1));
            animation: float 8s ease-in-out infinite;
            backdrop-filter: blur(1px);
        }
        .shape:nth-child(1) { width: 80px; height: 80px; top: 8%; left: 8%; animation-delay: 0s; }
        .shape:nth-child(2) { width: 60px; height: 60px; top: 15%; right: 12%; animation-delay: 1.5s; }
        .shape:nth-child(3) { width: 100px; height: 100px; bottom: 15%; left: 15%; animation-delay: 3s; }
        .shape:nth-child(4) { width: 40px; height: 40px; top: 50%; right: 8%; animation-delay: 0.8s; }
        .shape:nth-child(5) { width: 70px; height: 70px; bottom: 8%; right: 20%; animation-delay: 2.3s; }
        .shape:nth-child(6) { width: 50px; height: 50px; top: 25%; left: 25%; animation-delay: 4s; }
        .shape:nth-child(7) { width: 90px; height: 90px; bottom: 25%; right: 30%; animation-delay: 1s; }
        .shape:nth-child(8) { width: 35px; height: 35px; top: 70%; left: 10%; animation-delay: 3.5s; }
        @keyframes float { 0%, 100% { transform: translateY(0px) rotate(0deg); } 50% { transform: translateY(-20px) rotate(180deg); } }
        @keyframes slideInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes bounceIn { 0% { opacity: 0; transform: scale(0.3); } 50% { opacity: 1; transform: scale(1.05); } 70% { transform: scale(0.9); } 100% { opacity: 1; transform: scale(1); } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes shake { 0%, 100% { transform: translateX(0); } 10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); } 20%, 40%, 60%, 80% { transform: translateX(5px); } }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo-section">
            <div class="logo">PS</div>
            <h1>Pet Shop</h1>
            <div class="subtitle">Login ke Sistem</div>
        </div>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Masukkan username" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="login-btn">Login</button>
        </form>

        <div class="customer-link">
            <p style="margin: 0 0 12px 0; color: #666; font-size: 14px;">Bukan admin?</p>
            <a href="customer.php">Akses sebagai Customer</a>
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

</body>
</html>
