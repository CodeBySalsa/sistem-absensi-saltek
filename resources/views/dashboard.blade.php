<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full">
            {{-- Judul responsif memanjang lurus ke samping tanpa patah kebawah --}}
            <h2 class="font-bold text-xs sm:text-xl md:text-2xl text-slate-800 leading-tight tracking-tighter uppercase whitespace-nowrap overflow-hidden text-ellipsis max-w-[190px] sm:max-w-none">
                @if(Auth::user()->role == 'admin')
                    {{ __('Dashboard Admin PT Salttek Dumpang Jaya') }}
                @else
                    {{ __('Dashboard Karyawan PT Salttek Dumpang Jaya') }}
                @endif
            </h2>
            <div class="flex items-center gap-1 md:gap-3 shrink-0">
                <div id="countdown-area" class="text-[8px] md:text-xs font-bold text-white bg-slate-900 px-2 py-1 md:px-4 md:py-2 rounded-xl shadow-lg border border-slate-700 whitespace-nowrap">
                    <span class="opacity-80 uppercase tracking-widest mr-1 text-[7px] md:text-[10px]">Batas Absen:</span>
                    <span id="timer" class="font-mono text-rose-500">--:--:--</span>
                </div>
                
                <div id="realtime-clock" class="text-[9px] md:text-sm font-black text-indigo-600 bg-white px-2 py-1 md:px-4 md:py-2 rounded-xl border border-slate-100 shadow-sm font-mono whitespace-nowrap">
                    --:--:--
                </div>
            </div>
        </div>
    </x-slot>

    {{-- Script Leaflet & SweetAlert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <style>
        .swal2-popup { border-radius: 24px !important; }
        .animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        #map-preview { height: 200px; width: 100%; border-radius: 1.5rem; z-index: 1; }

        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .glass-stat {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
        .hero-premium {
            background: linear-gradient(135deg, #1e1b4b 0%, #4338ca 100%);
            position: relative;
            box-shadow: 0 15px 35px rgba(67, 56, 202, 0.1);
            min-height: 140px;
        }
        .hero-premium::before {
            content: '';
            position: absolute;
            top: -20%;
            right: -5%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
            border-radius: 50%;
        }
        /* CSS Optimalisasi Mobile HP (Mencegah Melorot Kebawah) */
        @media (max-width: 767px) {
            .mobile-title { font-size: 10px !important; font-weight: 800 !important; }
            .mobile-sub { font-size: 7px !important; }
            .mobile-badge { font-size: 6px !important; padding: 2px 4px !important; }
            .mobile-table-text { font-size: 7.5px !important; }
            .mobile-padding { padding: 6px 4px !important; }
            .mobile-truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 80px; }
            .force-no-p { padding: 8px 6px !important; border-radius: 1rem !important; }
            .force-tight-gap { gap: 4px !important; }
            .force-icon-size { width: 22px !important; height: 22px !important; }
        }
    </style>

    <div class="py-6 md:py-10 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- LOGIKA DETEKSI HARI MINGGU --}}
            @php
                $hariIniObj = \Carbon\Carbon::now('Asia/Jakarta');
                $isMinggu = $hariIniObj->isSunday();
            @endphp

            {{-- Flash Message --}}
            @if(session('success'))
                <script>Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", showConfirmButton: false, timer: 2000 });</script>
            @endif
            @if(session('error'))
                <script>Swal.fire({ icon: 'error', title: 'Gagal!', text: "{{ session('error') }}", showConfirmButton: true });</script>
            @endif

            {{-- 1. HERO BANNER KARYAWAN --}}
            @if(Auth::user()->role === 'karyawan')
                <div class="mb-6 hero-premium live-badge rounded-[1.5rem] md:rounded-[2rem] p-5 md:p-8 text-white relative overflow-hidden flex flex-row justify-between items-center gap-2 animate-fade-in">
                    <div class="relative z-10 w-7/12 pb-1 md:pb-0">
                        <div class="inline-flex items-center gap-1 bg-white/10 px-2 py-1 rounded-full text-[6px] md:text-[9px] font-black uppercase tracking-[0.15em] border border-white/10 backdrop-blur-md mb-2 md:mb-4 whitespace-nowrap">
                            <span class="flex h-1 w-1 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-1 w-1 bg-emerald-500"></span>
                            </span>
                            Presence System • PT Salttek Dumpang Jaya
                        </div>
                        <h1 class="text-xs md:text-3xl lg:text-4xl font-black tracking-tight leading-tight uppercase italic mobile-title">HELLO, <span class="text-indigo-300">{{ Auth::user()->name }}!</span></h1>
                        
                        <div class="flex gap-1.5 md:gap-3 mt-2 md:mt-4">
                            <div class="glass-stat px-2 py-1 md:px-4 md:py-2 rounded-xl md:rounded-2xl flex flex-col items-center min-w-[45px] md:min-w-[70px] cursor-pointer transition-all duration-300 hover:scale-110 active:scale-95 shadow-sm hover:shadow-white/10">
                                <span class="text-[6px] md:text-[10px] font-bold text-indigo-200 uppercase">Hadir</span>
                                <span class="text-xs md:text-lg font-black leading-none mt-0.5 md:mt-1">{{ $totalHadir ?? 0 }}</span>
                            </div>
                            <div class="glass-stat px-2 py-1 md:px-4 md:py-2 rounded-xl md:rounded-2xl flex flex-col items-center min-w-[45px] md:min-w-[70px] cursor-pointer transition-all duration-300 hover:scale-110 active:scale-95 shadow-sm hover:shadow-white/10">
                                <span class="text-[6px] md:text-[10px] font-bold text-indigo-200 uppercase">Izin</span>
                                <span class="text-xs md:text-lg font-black leading-none mt-0.5 md:mt-1">{{ $ringkasanStatistik->total_izin ?? 0 }}</span>
                            </div>
                            <div class="glass-stat px-2 py-1 md:px-4 md:py-2 rounded-xl md:rounded-2xl flex flex-col items-center min-w-[45px] md:min-w-[70px] cursor-pointer transition-all duration-300 hover:scale-110 active:scale-95 shadow-sm hover:shadow-white/10">
                                <span class="text-[6px] md:text-[10px] font-bold text-indigo-200 uppercase">Sakit</span>
                                <span class="text-xs md:text-lg font-black leading-none mt-0.5 md:mt-1">{{ $ringkasanStatistik->total_sakit ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="glass-card p-2 md:p-5 rounded-xl md:rounded-[1.5rem] flex items-center gap-2 md:gap-5 w-5/12 md:w-auto shadow-xl border-white/20 shrink-0 cursor-pointer transition-all duration-300 hover:scale-105 hover:-translate-y-0.5 active:scale-95">
                        <div class="w-7 h-7 md:w-12 md:h-12 rounded-lg md:rounded-xl flex items-center justify-center text-sm md:text-2xl shadow-inner shrink-0
                            @if($isMinggu) bg-slate-100 @elseif($cekAbsensi && $cekAbsensi->status == 'Terlambat') bg-rose-100 @elseif($cekAbsensi) bg-emerald-50 @else bg-amber-50 @endif">
                            @if($isMinggu) ☕ @elseif($cekAbsensi && $cekAbsensi->status == 'Terlambat') ⏰ @elseif($cekAbsensi && $cekAbsensi->jam_keluar) 🏁 @elseif($cekAbsensi) ✅ @else ⏳ @endif
                        </div>
                        <div class="min-w-0">
                            <p class="text-[6px] md:text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5 italic leading-none truncate">Real-time Status</p>
                            <h4 class="text-[8px] md:text-md font-black {{ ($isMinggu) ? 'text-slate-500' : (($cekAbsensi && $cekAbsensi->status == 'Terlambat') ? 'text-rose-600' : 'text-slate-800') }} uppercase leading-tight italic truncate">
                                @if($isMinggu) HARI LIBUR @elseif($cekAbsensi) {{ $cekAbsensi->status }} @else Belum Presensi @endif
                            </h4>
                            <p class="text-[6px] md:text-[10px] text-indigo-600 font-bold mt-0.5 leading-none truncate font-mono">
                                 @if($isMinggu) Istirahat @elseif($cekAbsensi) Log: {{ \Carbon\Carbon::parse($cekAbsensi->jam_masuk)->format('H:i') }} WIB @else Silakan Absensi @endif
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            

            {{-- 3. ADMIN PANEL --}}
            @if(Auth::user()->role == 'admin')
                <div class="mb-6 bg-slate-900 rounded-[1.5rem] md:rounded-[3rem] p-1 shadow-2xl relative overflow-hidden group">
                    <div class="relative z-10 bg-slate-900 rounded-[1.4rem] md:rounded-[2.9rem] p-4 md:p-10">
                        <div class="flex flex-col lg:flex-row justify-between items-stretch lg:items-center gap-4 md:gap-8">
                            
                            <div class="space-y-1.5 md:space-y-2">
                                <h3 class="text-[7px] md:text-[11px] font-black tracking-[0.2em] md:tracking-[0.3em] uppercase text-indigo-400/80 italic leading-none">Control Center PT Salttek</h3>
                                <h1 class="text-sm md:text-4xl font-black tracking-tighter text-white uppercase italic leading-tight">HALO, <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">{{ Auth::user()->name }}</span></h1>
                                <div class="flex flex-row gap-2 mt-2 md:mt-6">
                                    <a href="{{ route('karyawan.index') }}" class="px-2.5 py-1.5 md:px-5 md:py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-[6px] md:text-xs font-black uppercase tracking-wider rounded-lg md:rounded-xl transition-all shadow-lg flex items-center gap-1 whitespace-nowrap"><span>👥</span> DATA KARYAWAN</a>
                                    <a href="{{ route('karyawan.create') }}" class="px-2.5 py-1.5 md:px-5 md:py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-[6px] md:text-xs font-black uppercase tracking-wider rounded-lg md:rounded-xl transition-all shadow-lg flex items-center gap-1 whitespace-nowrap"><span>➕</span> TAMBAH KARYAWAN</a>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-4 gap-1.5 md:gap-6 mt-2 lg:mt-0 flex-1 md:flex-none">
    <div class="bg-slate-800/40 border border-white/5 p-2 md:p-6 rounded-xl md:rounded-[2rem] flex flex-col items-center text-center hover:scale-105 transition-all duration-300 ease-in-out cursor-pointer">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3 md:w-6 md:h-6 text-slate-400 mb-1">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
        </svg>
        <h3 class="text-xs md:text-3xl font-black text-white tracking-tighter leading-none">{{ $totalKaryawan ?? 0 }}</h3>
        <p class="text-[5px] md:text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1">ANGGOTA</p>
    </div>

    <div class="bg-emerald-500/10 border border-emerald-500/20 p-2 md:p-6 rounded-xl md:rounded-[2rem] flex flex-col items-center text-center hover:scale-105 transition-all duration-300 ease-in-out cursor-pointer">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3 h-3 md:w-6 md:h-6 text-emerald-400 mb-1">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>
        <h3 class="text-xs md:text-3xl font-black text-emerald-400 tracking-tighter leading-none">{{ $hadirHariIni ?? 0 }}</h3>
        <p class="text-[5px] md:text-[10px] font-bold text-emerald-500/70 uppercase tracking-wider mt-1">HADIR</p>
    </div>

    <div class="bg-amber-50/10 border border-amber-500/20 p-2 md:p-6 rounded-xl md:rounded-[2rem] flex flex-col items-center text-center hover:scale-105 transition-all duration-300 ease-in-out cursor-pointer">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3 md:w-6 md:h-6 text-amber-400 mb-1">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
        </svg>
        <h3 class="text-xs md:text-3xl font-black text-amber-400 tracking-tighter leading-none">{{ $totalIzin ?? 0 }}</h3>
        <p class="text-[5px] md:text-[10px] font-bold text-amber-500/70 uppercase tracking-wider mt-1">IZIN</p>
    </div>

    <div class="bg-rose-50/10 border border-rose-500/20 p-2 md:p-6 rounded-xl md:rounded-[2rem] flex flex-col items-center text-center hover:scale-105 transition-all duration-300 ease-in-out cursor-pointer">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3 md:w-6 md:h-6 text-rose-400 mb-1">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
        </svg>
        <h3 class="text-xs md:text-3xl font-black text-rose-400 tracking-tighter leading-none">{{ $totalSakit ?? 0 }}</h3>
        <p class="text-[5px] md:text-[10px] font-bold text-rose-500/70 uppercase tracking-wider mt-1">SAKIT</p>
    </div>
</div>
   </div>
         </div>
        </div>
            @endif

      {{-- WRAPPER CARD --}}
      @if(Auth::user()->role === 'karyawan')
<div class="grid grid-cols-2 gap-3 mt-6 items-stretch">

    {{-- CARD 1 --}}
    <div class="relative overflow-hidden rounded-[1.5rem]
        {{ !$cekAbsensi 
            ? 'bg-gradient-to-br from-amber-50 to-orange-100/80 border border-amber-200/60' 
            : 'bg-gradient-to-br from-emerald-50 to-teal-100/80 border border-emerald-200/60' }}
        p-4 shadow-sm
        transition-all duration-300
        hover:scale-[1.03]
        active:scale-95
        hover:shadow-xl
        cursor-pointer
        flex flex-col justify-center items-center text-center h-full">

        @if(!$cekAbsensi)

            <p class="text-[9px] sm:text-[10px] font-black text-amber-600 uppercase tracking-widest mb-2">
                INFO KEHADIRAN
            </p>

            <form id="formIzinSakit"
                action="{{ route('absensi.izinSakit') }}"
                method="POST"
                class="w-full space-y-2">

                @csrf

                <input type="hidden" name="status" id="status_input">

                <input type="text"
                    name="keterangan"
                    placeholder="Alasan..."
                    required
                    class="w-full text-[10px] sm:text-xs font-bold border border-amber-200 rounded-xl py-2 px-3 text-center outline-none focus:ring-2 focus:ring-amber-300 bg-white">

                <div class="grid grid-cols-2 gap-2">

                    <button type="button"
                        onclick="event.stopPropagation(); konfirmasiStatus('Izin')"
                        class="bg-amber-400 text-white font-black py-2 rounded-xl text-[10px] hover:bg-amber-500 transition-all duration-300 active:scale-95 flex items-center justify-center gap-1 shadow-md">
                        <span>📅</span> IZIN
                    </button>

                    <button type="button"
                        onclick="event.stopPropagation(); konfirmasiStatus('Sakit')"
                        class="bg-rose-500 text-white font-black py-2 rounded-xl text-[10px] hover:bg-rose-600 transition-all duration-300 active:scale-95 flex items-center justify-center gap-1 shadow-md">
                        <span>🌡️</span> SAKIT
                    </button>

                </div>
            </form>

        @else

            <div class="flex flex-col items-center justify-center h-full">

                <div class="text-4xl sm:text-5xl mb-2 animate-pulse">
                    @if($cekAbsensi->status == 'Izin')
                        📝
                    @elseif($cekAbsensi->status == 'Sakit')
                        🤒
                    @elseif($cekAbsensi->status == 'Hadir')
                        ✅
                    @else
                        ✨
                    @endif
                </div>

                <h4 class="text-xs sm:text-sm font-black
                    {{ in_array($cekAbsensi->status, ['Izin', 'Sakit']) 
                        ? 'text-amber-700' 
                        : 'text-emerald-700' }}
                    uppercase">
                    {{ $cekAbsensi->status == 'Hadir' ? 'DATA AMAN' : $cekAbsensi->status }}
                </h4>

            </div>

        @endif
    </div>

    {{-- CARD 2 --}}
    <div class="rounded-[1.5rem] shadow-lg overflow-hidden
        transition-all duration-300
        hover:scale-[1.03]
        active:scale-95
        hover:shadow-xl
        cursor-pointer
        h-full">

        @if($isMinggu)

            <div class="bg-slate-800 text-white h-full flex flex-col items-center justify-center p-4 text-center min-h-[190px]">
                <span class="text-4xl mb-2">⏱️</span>

                <h4 class="font-black uppercase text-xs sm:text-sm tracking-widest">
                    SYSTEM OFF
                </h4>
            </div>

        @else

            @php
                $jamSekarang = \Carbon\Carbon::now('Asia/Jakarta')->hour;
            @endphp

            @if(!$cekAbsensi)

                <div class="bg-gradient-to-br from-emerald-500 to-teal-600 text-white h-full min-h-[190px] p-4 flex flex-col items-center justify-center text-center"
                    onclick="handleAbsensi()">

                    <div class="text-5xl sm:text-6xl mb-3 animate-bounce">
                        🚀
                    </div>

                    <h4 class="font-black uppercase text-xs sm:text-sm tracking-widest">
                        KLIK PRESENSI
                    </h4>

                </div>

            @elseif(in_array($cekAbsensi->status, ['Izin', 'Sakit']))

                <div class="bg-gray-200 text-gray-600 h-full min-h-[190px] p-4 flex flex-col items-center justify-center text-center">

                    <div class="text-5xl mb-3">
                        🏠
                    </div>

                    <h4 class="font-black uppercase text-xs sm:text-sm tracking-tight">
                        STATUS: {{ $cekAbsensi->status }}
                    </h4>

                </div>

            @else

                <div class="h-full min-h-[190px] flex flex-col items-center justify-center text-center p-4
                    {{ $jamSekarang < 17 
                        ? 'bg-rose-100 text-rose-700' 
                        : 'bg-blue-600 text-white' }}"
                    onclick="handlePulang({{ $jamSekarang }})">

                    <div class="text-5xl sm:text-6xl mb-3">
                        🏠
                    </div>

                    <h4 class="font-black uppercase text-xs sm:text-sm tracking-tight">
                        {{ $jamSekarang < 17 ? 'BELUM WAKTUNYA' : 'KLIK PULANG' }}
                    </h4>

                </div>

            @endif

        @endif

    </div>

</div>
</div>
@endif

{{-- 5. TABEL UTAMA: LOG MINGGUAN --}}
<div class="mt-8 w-full max-w-7xl mx-auto bg-indigo-50 rounded-[2.5rem] shadow-xl border border-indigo-100 overflow-hidden">

    <div class="p-4 md:p-8 border-b border-indigo-100 flex items-center gap-2 md:gap-3">
        <div class="w-6 h-6 md:w-10 md:h-10 bg-indigo-600 text-white rounded-2xl flex items-center justify-center shadow-lg text-xs md:text-base">📋</div>
        <h3 class="font-black text-slate-800 uppercase tracking-tight text-xs md:text-lg">
            @if(Auth::user()->role == 'admin') LOG AKTIVITAS HARI INI @else LOG AKTIVITAS MINGGU INI @endif
        </h3>
    </div>

    <div class="w-full overflow-x-auto">
        <table class="w-full text-left md:text-sm">

            {{-- PERUBAHAN DISINI: Warna kembali ke gelap (slate-900) dan ditambahkan radius atas --}}
            <thead class="bg-slate-900 text-white rounded-t-[2.5rem]">
                <tr>
                    <th class="p-3 md:p-4 text-[7px] md:text-[10px] font-black uppercase tracking-widest text-left rounded-tl-[2.5rem]">
                        {{ Auth::user()->role == 'admin' ? 'Nama Karyawan' : 'Hari / Tanggal' }}
                    </th>
                    <th class="p-3 md:p-4 text-[7px] md:text-[10px] font-black uppercase tracking-widest text-center">Jam Masuk</th>
                    <th class="p-3 md:p-4 text-[7px] md:text-[10px] font-black uppercase tracking-widest text-center">Jam Pulang</th>
                    <th class="p-3 md:p-4 text-[7px] md:text-[10px] font-black uppercase tracking-widest text-center">Status</th>
                    <th class="p-3 md:p-4 text-[7px] md:text-[10px] font-black uppercase tracking-widest text-center rounded-tr-[2.5rem]">Keterangan</th>
                </tr>
            </thead>
            
            <tbody class="divide-y divide-slate-200/70">
                {{-- Isi TBody tetap sama sesuai data Anda --}}
                @if(Auth::user()->role == 'admin')
                    @forelse($absensiHariIni as $absen)
                    <tr class="odd:bg-slate-100/80 even:bg-indigo-50 hover:bg-indigo-100 transition-colors duration-200">
                        <td class="p-4 font-bold text-slate-800 text-[8px] md:text-sm">{{ $absen->karyawan->nama_lengkap ?? $absen->user->name }}</td>
                        <td class="p-4 text-center font-mono font-bold text-blue-600 text-[7px] md:text-sm">{{ $absen->jam_masuk ?? '--:--' }}</td>
                        <td class="p-4 text-center font-mono font-bold text-emerald-600 text-[7px] md:text-sm">{{ $absen->jam_keluar ?? '--:--' }}</td>
                        <td class="p-4 text-center">
                            <span class="px-2 py-1 rounded-full text-[6px] md:text-[9px] font-black uppercase {{ in_array($absen->status,['Terlambat','Sakit']) ? 'bg-rose-100 text-rose-600' : ($absen->status == 'Hadir' ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600') }}">
                                {{ $absen->status }}
                            </span>
                        </td>
                        <td class="p-4 text-center italic text-gray-600 text-[7px] md:text-xs">{{ $absen->keterangan ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="p-8 text-center text-slate-400 italic">Belum ada aktivitas hari ini.</td></tr>
                    @endforelse
                @else
                
                    {{-- KARYAWAN --}}
                    @forelse($absensis as $log)

                    <tr class="odd:bg-slate-100/80 even:bg-indigo-50 hover:bg-indigo-100 transition-colors duration-200">

                        <td class="mobile-padding md:p-4 font-bold text-slate-800 text-[8px] md:text-sm break-words">

                            {{-- HARI + TANGGAL --}}
                            @php
                                $tanggal = \Carbon\Carbon::parse($log->tanggal);
                            @endphp

                            <div class="font-bold">
                                {{ $tanggal->locale('id')->translatedFormat('l') }}
                            </div>

                            <div class="text-gray-500 text-[7px] md:text-xs">
                                {{ $tanggal->translatedFormat('d F Y') }}
                            </div>

                        </td>

                        <td class="mobile-padding md:p-4 text-center font-mono text-[7px] md:text-sm font-bold text-blue-600">
                            {{ $log->jam_masuk ?? '--:--' }}
                        </td>

                        <td class="mobile-padding md:p-4 text-center font-mono text-[7px] md:text-sm font-bold text-emerald-600">
                            {{ $log->jam_keluar ?? '--:--' }}
                        </td>

                        <td class="mobile-padding md:p-4 text-center">
                            <span class="px-2 py-1 rounded-full text-[6px] md:text-[9px] font-black uppercase
                            {{ $log->status == 'Terlambat'
                                ? 'bg-rose-100 text-rose-600'
                                : ($log->status == 'Hadir'
                                    ? 'bg-emerald-100 text-emerald-600'
                                    : 'bg-amber-100 text-amber-600') }}">
                                {{ $log->status }}
                            </span>
                        </td>

                        <td class="mobile-padding md:p-4 text-center text-[7px] md:text-xs italic text-gray-600 break-words">
                            {{ $log->keterangan ?? '-' }}
                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-400 italic">
                            Belum ada aktivitas minggu ini.
                        </td>
                    </tr>

                    @endforelse

                @endif

            </tbody>

        </table>
    </div>

</div>

{{-- 6. REKAPITULASI BULANAN --}}
@if(Auth::user()->role == 'admin')
    <div class="mt-8 bg-indigo-50 rounded-xl md:rounded-[2.5rem] shadow-xl border border-indigo-100 overflow-hidden mb-10 animate-fade-in w-full">
        <div class="p-4 md:p-8 border-b border-indigo-100/70 flex items-center gap-2 md:gap-3">
            <div class="w-6 h-6 md:w-10 md:h-10 bg-indigo-950 text-white rounded-lg flex items-center justify-center shadow-lg text-xs md:text-base">📊</div>
            <h3 class="font-black text-slate-800 uppercase tracking-tight text-xs md:text-lg">
                REKAPITULASI ABSENSI KARYAWAN - {{ strtoupper($namaBulan ?? '') }} {{ date('Y') }}
            </h3>
        </div>
        <div class="w-full overflow-hidden rounded-t-xl md:rounded-t-[2rem]">
            <table class="w-full text-left mobile-table-text md:text-sm border-collapse">
                <thead>
                    <tr class="bg-slate-900 text-white">
                        <th class="mobile-padding md:p-6 text-[7px] md:text-[10px] font-black uppercase tracking-widest text-left w-4/12 md:w-3/12 pl-5 md:pl-8">Nama Karyawan</th>
                        <th class="mobile-padding md:p-6 text-[7px] md:text-[10px] font-black uppercase tracking-widest text-center w-2/12 md:w-3/12 text-emerald-400">Hadir</th>
                        <th class="mobile-padding md:p-6 text-[7px] md:text-[10px] font-black uppercase tracking-widest text-center w-2/12 md:w-3/12 text-blue-400">Izin</th>
                        <th class="mobile-padding md:p-6 text-[7px] md:text-[10px] font-black uppercase tracking-widest text-center w-2/12 md:w-3/12 text-rose-400">Sakit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/70">
                    @foreach($rekapBulanan ?? [] as $rekap)
                        <tr class="odd:bg-slate-100/80 even:bg-indigo-50/50 hover:bg-indigo-100 transition-all duration-300 ease-out hover:scale-[1.01] hover:shadow-md hover:relative hover:z-10 cursor-pointer group">
                            <td class="mobile-padding md:p-6 font-bold text-slate-800 uppercase text-left mobile-truncate tracking-wide group-hover:text-indigo-900 transition-colors pl-5 md:pl-8">
                                {{ $rekap->nama_lengkap }}
                            </td>
                            <td class="mobile-padding md:p-6 text-center">
                                <span class="text-emerald-600 font-black font-mono text-[11px] md:text-sm group-hover:scale-110 transition-transform inline-block duration-300">
                                    {{ $rekap->total_hadir }}
                                </span>
                            </td>
                            <td class="mobile-padding md:p-6 text-center">
                                <span class="text-blue-600 font-black font-mono text-[11px] md:text-sm group-hover:scale-110 transition-transform inline-block duration-300">
                                    {{ $rekap->total_izin }}
                                </span>
                            </td>
                            <td class="mobile-padding md:p-6 text-center">
                                <span class="text-rose-600 font-black font-mono text-[11px] md:text-sm group-hover:scale-110 transition-transform inline-block duration-300">
                                    {{ $rekap->total_sakit }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

   {{-- MODAL ABSENSI --}}
<div id="absensiModal" class="fixed inset-0 z-[999] hidden">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeAbsensiModal()"></div>

    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div id="modalContent"
            class="relative bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl p-8 transform translate-y-full transition-transform duration-500 border border-white/20">

            <button type="button"
                onclick="closeAbsensiModal()"
                class="absolute top-5 right-6 text-slate-400 hover:text-rose-500 transition-all text-3xl font-bold">
                ✕
            </button>

            <div class="text-center mb-6">
                <h2 class="text-2xl font-black text-slate-800 uppercase tracking-tight mb-4 italic">
                    Konfirmasi Lokasi
                </h2>

                <div style="position: relative;"
                    class="rounded-3xl overflow-hidden border-4 border-slate-50">

                    <div id="map-preview" style="height: 200px; width: 100%;"></div>

                    <button type="button"
                        onclick="manualRecenter()"
                        class="absolute bottom-4 right-4 z-[1000] bg-white px-3 py-2 rounded-xl text-[10px] font-black shadow-lg border border-slate-100 text-indigo-600 uppercase">
                        📍 FOKUS
                    </button>
                </div>
            </div>

            <form id="formUtamaAbsensi"
    action="{{ route('absensi.store') }}"
    method="POST">

    @csrf

    <input type="hidden" name="latitude" id="lat">
    <input type="hidden" name="longitude" id="lng">

    <button type="button"
        onclick="kirimFormAbsen()">
        Kirim Presensi 🚀
    </button>

</form>

        </div>
    </div>
</div>

   <script>
    // =========================
    // VARIABEL GLOBAL
    // =========================

    let map;
    let userMarker;
    let currentLat, currentLng;

    const KANTOR_LAT = 3.506926236674939;
    const KANTOR_LNG =  98.66095682422633;
    const MAX_RADIUS = 20;

    // =========================
    // HITUNG JARAK (HAVERSINE)
    // =========================

    function calculateDistance(lat1, lon1, lat2, lon2) {

        const R = 6371000;

        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;

        const a =
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(lat1 * Math.PI / 180) *
            Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLon / 2) *
            Math.sin(dLon / 2);

        return R * (2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a)));
    }

    // =========================
    // HANDLE ABSENSI
    // =========================

    function handleAbsensi() {

        Swal.fire({
            title: 'Mendeteksi lokasi...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        if (!navigator.geolocation) {

            Swal.fire({
                icon: 'error',
                title: 'Tidak Didukung',
                text: 'Browser tidak mendukung GPS.'
            });

            return;
        }

        const geoOptions = {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        };

        navigator.geolocation.getCurrentPosition(

            function(position) {

                // Isi variabel global
                currentLat = position.coords.latitude;
                currentLng = position.coords.longitude;

                const jarak = calculateDistance(
                    currentLat,
                    currentLng,
                    KANTOR_LAT,
                    KANTOR_LNG
                );

                // Kalau di luar radius
                if (jarak > MAX_RADIUS) {

                    Swal.fire({
                        icon: 'error',
                        title: 'Di Luar Jangkauan!',
                        text: `Jarak Anda ${Math.round(jarak)} meter dari kantor.`,
                    });

                    return;
                }

                // Isi hidden input
                document.getElementById('lat').value = currentLat;
                document.getElementById('lng').value = currentLng;

                Swal.close();

                // Tampilkan modal
                const modal = document.getElementById('absensiModal');
                const content = document.getElementById('modalContent');

                modal.classList.remove('hidden');

                setTimeout(() => {
                    content.classList.remove('translate-y-full');
                }, 50);

                // Load map
                initMap(currentLat, currentLng);

            },

            function(error) {

                Swal.fire({
                    icon: 'error',
                    title: 'GPS Gagal',
                    text: 'Pastikan izin lokasi diaktifkan.'
                });

            },

            geoOptions
        );
    }

    // =========================
    // INIT MAP
    // =========================

    function initMap(lat, lng) {

        setTimeout(() => {

            if (!map) {

                map = L.map('map-preview').setView([lat, lng], 18);

                L.tileLayer(
                    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'
                ).addTo(map);

                // Radius kantor
                L.circle([KANTOR_LAT, KANTOR_LNG], {
                    radius: MAX_RADIUS,
                    color: '#4338ca',
                    fillColor: '#4338ca',
                    fillOpacity: 0.15
                }).addTo(map);

                // Marker kantor
                L.marker([KANTOR_LAT, KANTOR_LNG])
                    .addTo(map)
                    .bindPopup('Kantor');

            } else {

                map.setView([lat, lng], 18);
            }

            map.invalidateSize();

            // Marker user
            if (!userMarker) {

                userMarker = L.marker([lat, lng])
                    .addTo(map)
                    .bindPopup('Lokasi Anda');

            } else {

                userMarker.setLatLng([lat, lng]);
            }

        }, 300);
    }

    // =========================
    // RECENTER MAP
    // =========================

    function manualRecenter() {

        if (map && currentLat && currentLng) {

            map.setView([currentLat, currentLng], 18);
        }
    }

    // =========================
    // CLOSE MODAL
    // =========================

    function closeAbsensiModal() {

        const modal = document.getElementById('absensiModal');
        const content = document.getElementById('modalContent');

        content.classList.add('translate-y-full');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // =========================
    // SUBMIT FORM ABSENSI
    // =========================

    function kirimFormAbsen() {

        document.getElementById('lat').value = currentLat;
        document.getElementById('lng').value = currentLng;

        Swal.fire({
            title: 'Mengirim absensi...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        document.getElementById('formUtamaAbsensi').submit();
    }

    // =========================
    // KONFIRMASI IZIN / SAKIT
    // =========================

   function konfirmasiStatus(status) {
    // Mencari input berdasarkan atribut 'name'
    const ketInput = document.querySelector('input[name="keterangan"]').value;

    if (!ketInput.trim()) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Silakan isi kolom keterangan/alasan terlebih dahulu.'
        });
        return;
    }

    Swal.fire({
        title: `Ajukan ${status}?`,
        text: `Apakah Anda yakin ingin mengajukan keterangan ${status} hari ini?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4338ca',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Ajukan!'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('status_input').value = status;
            document.getElementById('formIzinSakit').submit();
        }
    });
}

    // =========================
    // HANDLE PULANG
    // =========================

    function handlePulang(jam) {

        if (jam < 17) {

            Swal.fire(
                'Info',
                'Belum waktunya pulang, minimal jam 17:00!',
                'warning'
            );

        } else {

            handleAbsensi();
        }
    }
</script>

   @if(Auth::user()->role === 'karyawan')
    <div class="fixed bottom-5 right-5 z-[9999] flex flex-col items-end gap-2 pointer-events-none group animate-fade-in">
        
        <div id="robot-bubble" class="pointer-events-auto bg-slate-900 text-white text-[10px] md:text-xs font-bold px-4 py-2.5 rounded-2xl shadow-2xl border border-slate-700/50 max-w-[180px] md:max-w-[240px] relative transition-all duration-500 transform scale-100 opacity-100 mb-1 tracking-wide line-clamp-3">
            <span id="robot-text">Halo! Loading... 🤖</span>
            <div class="absolute bottom-[-6px] right-7 w-3 h-3 bg-slate-900 border-r border-b border-slate-700/50 transform rotate-45"></div>
        </div>

        <div class="pointer-events-auto cursor-pointer relative mr-3" onclick="triggerRobotGiggle()">
            <div class="robot-body transition-transform duration-300 active:scale-90">
                <div class="w-1 h-3 bg-slate-400 mx-auto rounded-full relative">
                    <div id="robot-antenna-glow" class="absolute top-0 left-1/2 transform -translate-x-1/2 w-2 h-2 bg-cyan-400 rounded-full shadow-[0_0_8px_#22d3ee] animate-pulse"></div>
                </div>
                <div class="w-12 h-10 md:w-14 md:h-12 bg-gradient-to-b from-slate-100 to-slate-300 rounded-[1.2rem] shadow-xl border border-white flex items-center justify-center p-1.5 relative overflow-hidden">
                    <div class="w-full h-full bg-slate-950 rounded-[0.6rem] flex items-center justify-center gap-1.5 relative">
                        <div id="eye-left" class="robot-eye w-2.5 h-2.5 bg-cyan-400 rounded-full shadow-[0_0_6px_#22d3ee] transition-all duration-500"></div>
                        <div id="eye-right" class="robot-eye w-2.5 h-2.5 bg-cyan-400 rounded-full shadow-[0_0_6px_#22d3ee] transition-all duration-500"></div>
                    </div>
                </div>
                <div class="w-8 h-5 bg-gradient-to-b from-slate-200 to-slate-400 mx-auto rounded-b-xl border-x border-b border-white shadow-md flex justify-center items-center">
                    <div class="w-3 h-1.5 bg-indigo-500 rounded-full animate-pulse"></div>
                </div>
            </div>
        </div>
    </div>


    <style>
        .robot-body { animation: robotFloat 3s ease-in-out infinite; }
        @keyframes robotFloat {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-6px) rotate(1deg); }
        }

        /* 👁️ KUMPULAN STYLE EKSPRESI MATA (DIPICU LEWAT JAVASCRIPT) */
        
        /* 1. Mata Normal + Efek Berkedip Otomatis */
        .expr-normal { border-radius: 50% !important; height: 10px !important; width: 10px !important; clip-path: none !important; animation: robotBlink 4s infinite; }
        @keyframes robotBlink {
            0%, 90%, 100% { transform: scaleY(1); }
            95% { transform: scaleY(0.1); }
        }

        /* 2. Mata Sedih/Lemas (Sakit & Izin) - Melengkung ke Bawah */
        .expr-sad { 
            width: 11px !important; height: 10px !important; 
            background: #60a5fa !important; /* Warna biru sayu */
            box-shadow: 0 0 6px #60a5fa !important;
            border-radius: 40% 40% 0 0 !important;
            clip-path: ellipse(100% 55% at 50% 0%) !important;
            animation: lemasWobble 2s ease-in-out infinite;
        }
        @keyframes lemasWobble { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(0.5px); } }

        /* 3. Mata Senang/Gembira (Sukses Absen) - Melengkung Senyum ke Atas */
        .expr-happy {
            width: 11px !important; height: 8px !important;
            background: #34d399 !important; /* Warna hijau sukses */
            box-shadow: 0 0 8px #34d399 !important;
            border-radius: 50% 50% 0 0 !important;
            clip-path: polygon(0% 100%, 0% 40%, 15% 10%, 50% 0%, 85% 10%, 100% 40%, 100% 100%, 80% 100%, 50% 30%, 20% 100%) !important;
            animation: happyJump 0.6s ease-in-out infinite alternate;
        }
        @keyframes happyJump { from { transform: translateY(0); } to { transform: translateY(-1px); } }

        /* 4. Mata Melirik Curiga/Bingung (Belum Absen Pulang) */
        .expr-glance {
            width: 6px !important; height: 10px !important;
            border-radius: 30% !important;
            transform: translateX(-2px) !important; /* Melirik ke kiri arah teks */
            animation: glanceAlert 1s infinite alternate;
        }
        @keyframes glanceAlert { from { opacity: 0.8; } to { opacity: 1; } }

        /* Efek Geli/Ketawa pas diklik */
        .robot-giggle { animation: giggle 0.5s ease-in-out; }
        @keyframes giggle {
            0%, 100% { transform: scale(1); }
            25% { transform: scale(1.1) translateX(-4px); }
            75% { transform: scale(1.1) translateX(4px); }
        }
    </style>

  <script>
        const robotText = document.getElementById("robot-text");
        const robotBubble = document.getElementById("robot-bubble");
        const eyeLeft = document.getElementById("eye-left");
        const eyeRight = document.getElementById("eye-right");
        const antenna = document.getElementById("robot-antenna-glow");

        // Data dari Laravel
        const isMinggu = @json($isMinggu);
        const cekAbsensi = @json($cekAbsensi);

        const pesanBerkala = [
            "Semangat kerjanya yaa! 🚀",
            "Jangan lupa tetap tersenyum hari ini! 😊",
            "Aku selalu siap bantu kamu! 🤖",
            "Kerja kerasmu luar biasa! 💪",
            "Semoga hari ini produktif! ✨"
        ];

        // FUNGSI UTAMA EKSPRESI - DIPERBAIKI AGAR MATA TIDAK HILANG
        function terapkanEkspresi(ekspresi) {
            // Hapus semua kelas ekspresi, TAPI JANGAN HAPUS 'robot-eye'
            const kelasEkspresi = ["expr-sad", "expr-happy", "expr-glance", "expr-normal"];
            [eyeLeft, eyeRight].forEach(eye => {
                kelasEkspresi.forEach(cls => eye.classList.remove(cls));
            });

            // Tentukan ekspresi dan warna antena
            let kelasBaru = "expr-normal"; // Default selalu ada expr-normal untuk kedipan
            let warnaAntena = "#22d3ee";
            let shadowAntena = "0 0 8px #22d3ee";

            if (ekspresi === "sad") {
                kelasBaru = "expr-sad";
                warnaAntena = "#ef4444"; shadowAntena = "0 0 8px #ef4444";
            } else if (ekspresi === "happy") {
                kelasBaru = "expr-happy";
                warnaAntena = "#10b981"; shadowAntena = "0 0 8px #10b981";
            } else if (ekspresi === "glance") {
                kelasBaru = "expr-glance";
                warnaAntena = "#f59e0b"; shadowAntena = "0 0 8px #f59e0b";
            }

            // Terapkan kelas ekspresi + pastikan expr-normal selalu ada
            eyeLeft.classList.add(kelasBaru, "expr-normal");
            eyeRight.classList.add(kelasBaru, "expr-normal");
            antenna.style.backgroundColor = warnaAntena;
            antenna.style.boxShadow = shadowAntena;
        }

        function hideBubble() {
            robotBubble.classList.remove("scale-100", "opacity-100");
            robotBubble.classList.add("scale-0", "opacity-0");
            terapkanEkspresi("normal");
        }

        function showBubble(teks = null) {
            if (teks) robotText.innerHTML = teks;
            robotBubble.classList.remove("scale-0", "opacity-0");
            robotBubble.classList.add("scale-100", "opacity-100");
            setTimeout(hideBubble, 4000);
        }

        document.addEventListener("DOMContentLoaded", function() {
            let teksAwal = "";
            let ekspresi = "normal";

            if (isMinggu) { teksAwal = "Yeeayy, hari Minggu waktunya libur! 🏡"; ekspresi = "happy"; }
            else if (!cekAbsensi) { teksAwal = "Haloo! Jangan lupa absen masuk yaa! 🚀"; ekspresi = "normal"; }
            else if (cekAbsensi.status === 'Izin') { teksAwal = "Semoga urusanmu lancar hari ini yaa! ✋"; ekspresi = "sad"; }
            else if (cekAbsensi.status === 'Sakit') { teksAwal = "Gws yaa, istirahat yang cukup! 🤒"; ekspresi = "sad"; }
            else { teksAwal = "Semangat bekerja hari ini yaa! 💪"; ekspresi = "happy"; }

            terapkanEkspresi(ekspresi);
            showBubble(teksAwal);

            setInterval(() => {
                let pesanAcak = pesanBerkala[Math.floor(Math.random() * pesanBerkala.length)];
                terapkanEkspresi("happy");
                showBubble(pesanAcak);
            }, 15000);
        });

        function triggerRobotGiggle() {
            const body = document.querySelector('.robot-body');
            body.classList.add('robot-giggle');
            setTimeout(() => { body.classList.remove('robot-giggle'); }, 500);
            terapkanEkspresi("happy");
            showBubble("Aku selalu siap membantu kamu! 🤖");
        }
    </script>
    @endif

 @if(Auth::user()->role == 'admin')
    <div class="fixed bottom-5 right-5 z-[9999] flex flex-col items-end gap-2 pointer-events-none group animate-fade-in">
        
        <div id="robot-bubble-admin" class="pointer-events-auto bg-slate-900 text-white text-[10px] md:text-xs font-bold px-4 py-2.5 rounded-2xl shadow-2xl border border-indigo-500/50 max-w-[200px] md:max-w-[260px] relative transition-all duration-500 transform scale-100 opacity-100 mb-1 tracking-wide">
            <span id="robot-text-admin">Siap memantau performa hari ini, Admin? 📊</span>
            <div class="absolute bottom-[-6px] right-7 w-3 h-3 bg-slate-900 border-r border-b border-indigo-500/50 transform rotate-45"></div>
        </div>

        <div class="pointer-events-auto cursor-pointer relative mr-3" onclick="triggerAdminGiggle()">
            <div class="robot-body transition-transform duration-300 active:scale-90">
                <div class="w-1 h-3 bg-indigo-400 mx-auto rounded-full relative">
                    <div id="admin-antenna-glow" class="absolute top-0 left-1/2 transform -translate-x-1/2 w-2 h-2 bg-indigo-400 rounded-full shadow-[0_0_8px_#818cf8] animate-pulse"></div>
                </div>
                <div class="w-12 h-10 md:w-14 md:h-12 bg-gradient-to-b from-indigo-100 to-indigo-300 rounded-[1.2rem] shadow-xl border border-white flex items-center justify-center p-1.5 relative overflow-hidden">
                    <div class="w-full h-full bg-slate-950 rounded-[0.6rem] flex items-center justify-center gap-1.5 relative">
                        <div id="eye-left-admin" class="robot-eye w-2.5 h-2.5 bg-indigo-400 rounded-full shadow-[0_0_6px_#818cf8] transition-all duration-500"></div>
                        <div id="eye-right-admin" class="robot-eye w-2.5 h-2.5 bg-indigo-400 rounded-full shadow-[0_0_6px_#818cf8] transition-all duration-500"></div>
                    </div>
                </div>
                <div class="w-8 h-5 bg-gradient-to-b from-indigo-200 to-indigo-400 mx-auto rounded-b-xl border-x border-b border-white shadow-md flex justify-center items-center">
                    <div class="w-3 h-1.5 bg-cyan-500 rounded-full animate-pulse"></div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <style>
        .robot-body { animation: robotFloat 3s ease-in-out infinite; }
        @keyframes robotFloat {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-6px) rotate(1deg); }
        }

        /* 👁️ KUMPULAN STYLE EKSPRESI MATA (DIPICU LEWAT JAVASCRIPT) */
        
        /* 1. Mata Normal + Efek Berkedip Otomatis */
        .expr-normal { border-radius: 50% !important; height: 10px !important; width: 10px !important; clip-path: none !important; animation: robotBlink 4s infinite; }
        @keyframes robotBlink {
            0%, 90%, 100% { transform: scaleY(1); }
            95% { transform: scaleY(0.1); }
        }

        /* 2. Mata Sedih/Lemas (Sakit & Izin) - Melengkung ke Bawah */
        .expr-sad { 
            width: 11px !important; height: 10px !important; 
            background: #60a5fa !important; /* Warna biru sayu */
            box-shadow: 0 0 6px #60a5fa !important;
            border-radius: 40% 40% 0 0 !important;
            clip-path: ellipse(100% 55% at 50% 0%) !important;
            animation: lemasWobble 2s ease-in-out infinite;
        }
        @keyframes lemasWobble { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(0.5px); } }

        /* 3. Mata Senang/Gembira (Sukses Absen) - Melengkung Senyum ke Atas */
        .expr-happy {
            width: 11px !important; height: 8px !important;
            background: #34d399 !important; /* Warna hijau sukses */
            box-shadow: 0 0 8px #34d399 !important;
            border-radius: 50% 50% 0 0 !important;
            clip-path: polygon(0% 100%, 0% 40%, 15% 10%, 50% 0%, 85% 10%, 100% 40%, 100% 100%, 80% 100%, 50% 30%, 20% 100%) !important;
            animation: happyJump 0.6s ease-in-out infinite alternate;
        }
        @keyframes happyJump { from { transform: translateY(0); } to { transform: translateY(-1px); } }

        /* 4. Mata Melirik Curiga/Bingung (Belum Absen Pulang) */
        .expr-glance {
            width: 6px !important; height: 10px !important;
            border-radius: 30% !important;
            transform: translateX(-2px) !important; /* Melirik ke kiri arah teks */
            animation: glanceAlert 1s infinite alternate;
        }
        @keyframes glanceAlert { from { opacity: 0.8; } to { opacity: 1; } }

        /* Efek Geli/Ketawa pas diklik */
        .robot-giggle { animation: giggle 0.5s ease-in-out; }
        @keyframes giggle {
            0%, 100% { transform: scale(1); }
            25% { transform: scale(1.1) translateX(-4px); }
            75% { transform: scale(1.1) translateX(4px); }
        }
    </style>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const adminText = document.getElementById("robot-text-admin");
        const bubble = document.getElementById("robot-bubble-admin");
        const eyeLeft = document.getElementById("eye-left-admin");
        const eyeRight = document.getElementById("eye-right-admin");
        const antenna = document.getElementById("admin-antenna-glow");

        const pesanAdmin = [
            "Siap memantau performa hari ini, Admin? 📊",
            "Data absensi sudah diperbarui, Boss! ✅",
            "Semua sistem berjalan normal. 🚀",
            "Perlu bantuan mengelola data? 🤖",
            "Laporan bulanan siap untuk ditinjau. 📈"
        ];

        // FUNGSI EKSPRESI: Menjaga 'expr-normal' tetap ada agar kedip terus
        window.setAdminMood = function(mood) {
            const eyes = [eyeLeft, eyeRight];
            eyes.forEach(eye => {
                // Hapus kelas ekspresi, TAPI JANGAN hapus 'robot-eye'
                eye.classList.remove("expr-happy", "expr-glance", "expr-sad");
                
                // Pastikan expr-normal (kedip) selalu nempel
                eye.classList.add("expr-normal");

                if (mood === 'happy') {
                    eye.classList.add("expr-happy");
                    antenna.style.backgroundColor = "#34d399";
                    antenna.style.boxShadow = "0 0 8px #34d399";
                } else if (mood === 'glance') {
                    eye.classList.add("expr-glance");
                    antenna.style.backgroundColor = "#f59e0b";
                    antenna.style.boxShadow = "0 0 8px #f59e0b";
                } else {
                    antenna.style.backgroundColor = "#818cf8";
                    antenna.style.boxShadow = "0 0 8px #818cf8";
                }
            });
        };

        // FUNGSI MENAMPILKAN BUBBLE
        function showAdminBubble(teks) {
            adminText.innerText = teks;
            bubble.classList.remove("scale-0", "opacity-0");
            bubble.classList.add("scale-100", "opacity-100");
            bubble.style.pointerEvents = "auto";
            setAdminMood('happy');

            // Sembunyikan setelah 4 detik
            setTimeout(() => {
                bubble.classList.remove("scale-100", "opacity-100");
                bubble.classList.add("scale-0", "opacity-0");
                bubble.style.pointerEvents = "none";
                setAdminMood('normal');
            }, 4000);
        }

        // 1. Muncul pertama kali saat load
        setTimeout(() => showAdminBubble(pesanAdmin[0]), 1000);

        // 2. Muncul berkala setiap 15 detik
        setInterval(() => {
            let randomPesan = pesanAdmin[Math.floor(Math.random() * pesanAdmin.length)];
            showAdminBubble(randomPesan);
        }, 15000);
    });

    // Fungsi klik manual
    function triggerAdminGiggle() {
        // ... (kode goyang Anda)
        setAdminMood('happy');
    }
</script
</x-app-layout>

