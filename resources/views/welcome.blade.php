<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Digital - PT SALTTEK DUMPANG JAYA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-[#F8FAFC] antialiased">

    <div class="min-h-screen flex items-center justify-center p-6 relative overflow-hidden">
        {{-- Efek Dekorasi Background --}}
        <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/3 w-96 h-96 bg-blue-200 rounded-full blur-[120px] opacity-50"></div>
        <div class="absolute bottom-0 left-0 translate-y-1/2 -translate-x-1/3 w-96 h-96 bg-indigo-200 rounded-full blur-[120px] opacity-50"></div>

        <div class="relative z-10 w-full max-w-md">
            <div class="bg-white rounded-[2.5rem] shadow-[0_20px_50px_rgba(8,112,184,0.12)] border border-blue-50/50 p-10 text-center">
                
                {{-- Logo Section --}}
                <div class="w-20 h-20 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-3xl mx-auto mb-8 flex items-center justify-center shadow-2xl shadow-blue-200">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A10.003 10.003 0 0012 3c1.708 0 3.305.429 4.708 1.186M15.5 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>

                {{-- Branding Section --}}
                <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight leading-tight uppercase">
                    PT SALTTEK <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">DUMPANG JAYA</span>
                </h1>

                {{-- Deskripsi Profesional (Tulisan KKN sudah dihapus) --}}
                <p class="mt-4 text-slate-500 text-sm font-medium leading-relaxed">
                    Sistem Monitoring Absensi Digital <br>& Manajemen Karyawan Terintegrasi.
                </p>

                {{-- Button Section --}}
                <div class="mt-10 space-y-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="flex items-center justify-center w-full bg-slate-900 hover:bg-black text-white font-bold py-4 rounded-2xl transition-all active:scale-95 shadow-lg shadow-slate-200">
                                MASUK KE DASHBOARD
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="flex items-center justify-center w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-2xl transition-all shadow-lg shadow-blue-200 active:scale-95 uppercase text-xs tracking-widest">
                                LOGIN SEKARANG
                            </a>

                            @if (Route::has('register'))
                                <div class="flex items-center gap-4 my-6">
                                    <div class="h-px bg-slate-100 flex-1"></div>
                                    <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">Atau</span>
                                    <div class="h-px bg-slate-100 flex-1"></div>
                                </div>

                                <a href="{{ route('register') }}" class="flex items-center justify-center w-full bg-white border-2 border-slate-100 hover:border-blue-100 text-slate-600 font-bold py-4 rounded-2xl transition-all active:scale-95 text-sm">
                                    Daftar Akun Baru
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>

                {{-- Footer Section --}}
                <div class="mt-12 pt-8 border-t border-slate-50">
                    <p class="text-[9px] font-black text-slate-300 uppercase tracking-[0.4em]">
                        Medan, Sumatera Utara • 2026
                    </p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>