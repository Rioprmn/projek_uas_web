<x-guest-layout>
    <style>
        .auth-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
        }
        .btn-gradient {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            transition: all 0.3s ease;
        }
    </style>

    <div class="auth-card">
        <div class="text-center mb-8">
            <h1 style="font-size: 2.2rem; font-weight: 900; color: #1e293b; letter-spacing: -1px;">BUAT AKUN</h1>
            <div style="width: 40px; height: 4px; background: #10b981; margin: 10px auto; border-radius: 10px;"></div>
        </div>

        <form method="POST" action="{{ route('register') }}" class="grid grid-cols-1 gap-5">
            @csrf
            <div>
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1 mb-2 block">Nama Lengkap</label>
                <input id="name" class="w-full bg-slate-50 border-none ring-1 ring-slate-200 focus:ring-2 focus:ring-emerald-500 rounded-2xl px-5 py-4" type="text" name="name" :value="old('name')" required autofocus placeholder="Contoh: Budi Kasir" />
                <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs" />
            </div>

            <div>
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1 mb-2 block">Email Kerja</label>
                <input id="email" class="w-full bg-slate-50 border-none ring-1 ring-slate-200 focus:ring-2 focus:ring-emerald-500 rounded-2xl px-5 py-4" type="email" name="email" :value="old('email')" required placeholder="email@toko.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1 mb-2 block">Password</label>
                    <input id="password" class="w-full bg-slate-50 border-none ring-1 ring-slate-200 focus:ring-2 focus:ring-emerald-500 rounded-2xl px-5 py-4 text-sm" type="password" name="password" required placeholder="••••••••" />
                </div>
                <div>
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1 mb-2 block">Konfirmasi</label>
                    <input id="password_confirmation" class="w-full bg-slate-50 border-none ring-1 ring-slate-200 focus:ring-2 focus:ring-emerald-500 rounded-2xl px-5 py-4 text-sm" type="password" name="password_confirmation" required placeholder="••••••••" />
                </div>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />

            <button type="submit" class="btn-gradient w-full text-white font-black py-4 rounded-2xl shadow-lg uppercase tracking-widest text-sm mt-4 transition-all">
                Daftar & Mulai
            </button>
        </form>

        <div class="mt-8 text-center">
            <a href="{{ route('login') }}" class="text-sm font-bold text-slate-500 hover:text-emerald-600 transition-all underline decoration-emerald-200 decoration-2 underline-offset-4">Sudah ada akun? Masuk di sini</a>
        </div>
    </div>
</x-guest-layout>