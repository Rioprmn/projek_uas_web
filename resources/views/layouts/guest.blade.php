<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir-Ku | Portal Akses</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-slate-900 overflow-hidden">

    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-emerald-500/30 rounded-full blur-[120px] animate-float"></div>
        <div class="absolute -bottom-[10%] -right-[10%] w-[40%] h-[40%] bg-teal-500/20 rounded-full blur-[120px] animate-float" style="animation-delay: -3s;"></div>
    </div>

    <div class="relative w-full max-w-md px-6">
        <div class="glass-card rounded-[2.5rem] shadow-[0_32px_64px_-15px_rgba(0,0,0,0.3)] p-10">
            {{ $slot }}
        </div>
        
        <p class="text-center mt-8 text-slate-500 text-sm font-medium tracking-wide">
            &copy; 2026 Kasir-Ku POS System. All rights reserved.
        </p>
    </div>

</body>
</html>