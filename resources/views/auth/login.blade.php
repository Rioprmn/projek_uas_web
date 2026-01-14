<x-guest-layout>
    <style>
        /* Style tambahan untuk efek premium */
        .auth-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
        }
        .input-group:focus-within label { color: #10b981; }
        .btn-gradient {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            transition: all 0.3s ease;
        }
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.4);
        }
    </style>

    <div class="auth-card">
        <div class="text-center mb-10">
            <h1 style="font-size: 2.5rem; font-weight: 900; background: linear-gradient(to right, #10b981, #064e3b); -webkit-background-clip: text; -webkit-text-fill-color: transparent; letter-spacing: -1px;">
                KASIR-KU
            </h1>
            <p style="color: #64748b; font-weight: 500; margin-top: 5px;">Manajemen Toko & Penjualan</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf
            <div class="input-group">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1 mb-2 block">Akses Email</label>
                <input id="email" class="w-full bg-slate-50 border-none ring-1 ring-slate-200 focus:ring-2 focus:ring-emerald-500 rounded-2xl px-5 py-4 transition-all" type="email" name="email" :value="old('email')" required autofocus placeholder="nama@toko.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs" />
            </div>

            <div class="input-group">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1 mb-2 block">Kata Sandi</label>
                <input id="password" class="w-full bg-slate-50 border-none ring-1 ring-slate-200 focus:ring-2 focus:ring-emerald-500 rounded-2xl px-5 py-4 transition-all" type="password" name="password" required placeholder="••••••••" />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs" />
            </div>

            <div class="flex items-center justify-between px-1">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" class="w-5 h-5 rounded-lg border-slate-300 text-emerald-600 focus:ring-emerald-500" name="remember">
                    <span class="ml-3 text-sm text-slate-600 font-medium">Ingat Saya</span>
                </label>
                <a href="{{ route('password.request') }}" class="text-sm font-bold text-emerald-600 hover:text-emerald-700">Lupa?</a>
            </div>

            <button type="submit" class="btn-gradient w-full text-white font-black py-4 rounded-2xl shadow-lg uppercase tracking-widest text-sm">
                Masuk Sekarang
            </button>
        </form>

        {{-- <div class="mt-10 text-center">
            <span class="text-slate-400 text-sm">Belum punya akses?</span>
            <a href="{{ route('register') }}" class="ml-1 text-sm font-black text-slate-800 hover:text-emerald-600 border-b-2 border-emerald-200 hover:border-emerald-500 transition-all">Daftar Akun</a>
        </div> --}}
    </div>
</x-guest-layout>