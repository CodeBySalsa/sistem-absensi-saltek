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
            .force-no-p { padding: 6px !important; border-radius: 1rem !important; }
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
            @if(Auth::user()->role !== 'admin')
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
                        
                        {{-- DISINI PERBAIKAN CARDNYA: Ditambahkan efek hover zoom transform & shadow glow lembut --}}
                        <div class="flex gap-1.5 md:gap-3 mt-2 md:mt-4">
                            <div class="glass-stat cursor-pointer transform hover:scale-110 transition-all duration-300 ease-in-out hover:bg-emerald-500/20 hover:border-emerald-400/40 shadow-sm hover:shadow-emerald-500/20 px-2 py-1 md:px-4 md:py-2 rounded-xl md:rounded-2xl flex flex-col items-center min-w-[45px] md:min-w-[70px]">
                                <span class="text-[6px] md:text-[10px] font-bold text-indigo-200 uppercase tracking-wider">Hadir</span>
                                <span class="text-xs md:text-lg font-black leading-none mt-0.5 md:mt-1 text-emerald-300">{{ $totalHadir ?? 0 }}</span>
                            </div>
                            <div class="glass-stat cursor-pointer transform hover:scale-110 transition-all duration-300 ease-in-out hover:bg-blue-500/20 hover:border-blue-400/40 shadow-sm hover:shadow-blue-500/20 px-2 py-1 md:px-4 md:py-2 rounded-xl md:rounded-2xl flex flex-col items-center min-w-[45px] md:min-w-[70px]">
                                <span class="text-[6px] md:text-[10px] font-bold text-indigo-200 uppercase tracking-wider">Izin</span>
                                <span class="text-xs md:text-lg font-black leading-none mt-0.5 md:mt-1 text-blue-300">{{ $ringkasanStatistik->total_izin ?? 0 }}</span>
                            </div>
                            <div class="glass-stat cursor-pointer transform hover:scale-110 transition-all duration-300 ease-in-out hover:bg-rose-500/20 hover:border-rose-400/40 shadow-sm hover:shadow-rose-500/20 px-2 py-1 md:px-4 md:py-2 rounded-xl md:rounded-2xl flex flex-col items-center min-w-[45px] md:min-w-[70px]">
                                <span class="text-[6px] md:text-[10px] font-bold text-indigo-200 uppercase tracking-wider">Sakit</span>
                                <span class="text-xs md:text-lg font-black leading-none mt-0.5 md:mt-1 text-rose-300">{{ $ringkasanStatistik->total_sakit ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Status Kanan juga ditambahkan efek interaktif hover zoom --}}
                    <div class="glass-card cursor-pointer transform hover:scale-105 transition-all duration-300 ease-in-out live-badge p-2 md:p-5 rounded-xl md:rounded-[1.5rem] flex items-center gap-2 md:gap-5 w-5/12 md:w-auto shadow-xl border-white/20 shrink-0">
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
                                 @if($isMinggu) Istirahat @elseif($cekAbsensi) Log: {{ \Carbon\Carbon::parse($cekAbsening->jam_masuk)->format('H:i') }} WIB @else Silakan Absensi @endif
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
                    <h3 class="text-[7px] md:text-[11px] font-black tracking-[0.2em] md:tracking-[0.3em] uppercase text-indigo-400/80 italic leading-none">Control Center PT Saltek</h3>
                    <h1 class="text-sm md:text-4xl font-black tracking-tighter text-white uppercase italic leading-tight">HALO, <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">{{ Auth::user()->name }}</span></h1>
                    <div class="flex flex-row gap-2 mt-2 md:mt-6">
                        <a href="{{ route('karyawan.index') }}" class="px-2.5 py-1.5 md:px-5 md:py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-[6px] md:text-xs font-black uppercase tracking-wider rounded-lg md:rounded-xl transition-all shadow-lg flex items-center gap-1 whitespace-nowrap">
                            <span>👥</span> DATA KARYAWAN
                        </a>
                    </div>
                </div> {{-- Penutup tag kiri yang tadi sempat hilang --}}

                <div class="grid grid-cols-4 gap-1.5 md:gap-6 mt-2 lg:mt-0 flex-1 md:flex-none">
                    
                    <div class="bg-slate-800/40 border border-white/5 p-2 md:p-6 rounded-xl md:rounded-[2rem] flex items-center justify-between transition-all duration-300 hover:scale-105 hover:shadow-lg gap-4 group cursor-pointer">
                        <div>
                            <h3 class="text-xs md:text-3xl font-black text-white tracking-tighter">{{ $totalKaryawan ?? 0 }}</h3>
                            <p class="text-[5px] md:text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Anggota</p>
                        </div>
                        <div class="text-blue-400 bg-blue-500/10 p-1 md:p-2 rounded-lg hidden md:block transition-transform duration-300 group-hover:rotate-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>

                    <div class="bg-emerald-500/10 border border-emerald-500/20 p-2 md:p-6 rounded-xl md:rounded-[2rem] flex items-center justify-between transition-all duration-300 hover:scale-105 hover:shadow-lg gap-4 group cursor-pointer">
                        <div>
                            <h3 class="text-xs md:text-3xl font-black text-emerald-400 tracking-tighter">{{ $hadirHariIni ?? 0 }}</h3>
                            <p class="text-[5px] md:text-[10px] font-bold text-emerald-500/70 uppercase tracking-wider mt-0.5">Hadir</p>
                        </div>
                        <div class="text-emerald-400 bg-emerald-500/10 p-1 md:p-2 rounded-lg hidden md:block transition-transform duration-300 group-hover:rotate-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>

                    <div class="bg-amber-500/10 border border-amber-500/20 p-2 md:p-6 rounded-xl md:rounded-[2rem] flex items-center justify-between transition-all duration-300 hover:scale-105 hover:shadow-lg gap-4 group cursor-pointer">
                        <div>
                            <h3 class="text-xs md:text-3xl font-black text-amber-400 tracking-tighter">{{ $totalIzin ?? 0 }}</h3>
                            <p class="text-[5px] md:text-[10px] font-bold text-amber-500/70 uppercase tracking-wider mt-0.5">Izin</p>
                        </div>
                        <div class="text-amber-400 bg-amber-500/10 p-1 md:p-2 rounded-lg hidden md:block transition-transform duration-300 group-hover:rotate-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>

                    <div class="bg-rose-500/10 border border-rose-500/20 p-2 md:p-6 rounded-xl md:rounded-[2rem] flex items-center justify-between transition-all duration-300 hover:scale-105 hover:shadow-lg gap-4 group cursor-pointer">
                        <div>
                            <h3 class="text-xs md:text-3xl font-black text-rose-400 tracking-tighter">{{ $totalSakit ?? 0 }}</h3>
                            <p class="text-[5px] md:text-[10px] font-bold text-rose-500/70 uppercase tracking-wider mt-0.5">Sakit</p>
                        </div>
                        <div class="text-rose-400 bg-rose-500/10 p-1 md:p-2 rounded-lg hidden md:block transition-transform duration-300 group-hover:rotate-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
@endif
            {{-- 4. USER CARDS --}}
            @if(Auth::user()->role !== 'admin')
                <div class="grid grid-cols-3 gap-2 md:gap-6 mb-8">
                    
                    {{-- Card 1: Pencapaian Anda --}}
                    <div class="bg-indigo-50/50 rounded-[1.2rem] md:rounded-[2.5rem] p-3 md:p-8 shadow-xl border border-indigo-100 flex items-center justify-between gap-1 force-no-p">
                        <div class="min-w-0">
                            <p class="text-[6px] md:text-[10px] font-black text-indigo-400 uppercase tracking-wider md:tracking-widest leading-none">PENCAPAIAN ANDA</p>
                            <h3 class="text-sm md:text-5xl font-black text-slate-800 leading-none mt-1 md:mt-2">{{ $totalHadir ?? 0 }}</h3>
                            <p class="text-[5px] md:text-sm font-bold text-indigo-500 mt-1 italic mobile-sub leading-none">Hari Kerja</p>
                        </div>
                        <div class="bg-white p-1 md:p-3 rounded-lg md:rounded-2xl shadow-sm shrink-0">
                            <img src="https://cdn-icons-png.flaticon.com/512/2838/2838779.png" class="w-5 h-5 md:w-12 md:h-12 force-icon-size" alt="calendar">
                        </div>
                    </div>

                    {{-- Card 2: Info Kehadiran --}}
                    <div class="bg-amber-50/50 rounded-[1.2rem] md:rounded-[2.5rem] p-3 md:p-8 shadow-xl border border-amber-100 flex flex-col justify-center items-center text-center force-no-p">
                        @if($isMinggu)
                            <div class="w-5 h-5 md:w-16 md:h-16 bg-white text-indigo-500 rounded-xl flex items-center justify-center text-xs md:text-3xl mb-1 md:shadow-sm">🏡</div>
                            <h4 class="text-[5px] md:text-[11px] font-black text-slate-800 uppercase tracking-tight leading-tight">Weekend Off</h4>
                        @else
                            <p class="text-[5px] md:text-[10px] font-black text-amber-500 uppercase tracking-wider md:tracking-widest leading-none mb-1 md:mb-3">INFO KEHADIRAN</p>
                            @if(!$cekAbsensi)
                                <form id="formIzinSakit" action="{{ route('absensi.izinSakit') }}" method="POST" class="w-full space-y-1">
                                    @csrf
                                    <input type="hidden" name="status" id="status_input">
                                    <input type="text" name="keterangan" id="keterangan_input" placeholder="Alasan..." required class="w-full text-[5px] md:text-xs font-bold border-none bg-white rounded-md md:rounded-2xl py-0.5 px-1 shadow-inner text-center">
                                    <div class="grid grid-cols-2 gap-1">
                                        <button type="button" onclick="konfirmasiStatus('Izin')" class="bg-amber-400 text-white font-black py-0.5 rounded text-[5px] md:text-[10px] uppercase">Izin</button>
                                        <button type="button" onclick="konfirmasiStatus('Sakit')" class="bg-rose-500 text-white font-black py-0.5 rounded text-[5px] md:text-[10px] uppercase">Sakit</button>
                                    </div>
                                </form>
                            @else
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-5 h-5 md:w-12 md:h-12 bg-white rounded-md md:rounded-xl flex items-center justify-center text-xs md:text-2xl shadow-sm mb-1">
                                        @if($cekAbsensi->status == 'Izin') ✋ @elseif($cekAbsensi->status == 'Sakit') 🤒 @else ✨ @endif
                                    </div>
                                    <h4 class="text-[6px] md:text-[11px] font-black {{ in_array($cekAbsensi->status, ['Izin', 'Sakit']) ? 'text-amber-600' : 'text-emerald-600' }} uppercase leading-none truncate">
                                        {{ $cekAbsensi->status == 'Hadir' || $cekAbsensi->status == 'Terlambat' ? 'DATA AMAN' : $cekAbsensi->status }}
                                    </h4>
                                    <p class="text-[4px] md:text-[9px] font-bold text-slate-400 uppercase mt-0.5 tracking-tighter truncate">
                                        {{ $cekAbsensi->status == 'Hadir' || $cekAbsensi->status == 'Terlambat' ? 'TERCATAT HARI INI' : 'DISETUJUI' }}
                                    </p>
                                </div>
                            @endif
                        @endif
                    </div>

                    {{-- Card 3: Card Utama Absensi --}}
                    <div class="rounded-[1.2rem] md:rounded-[2.5rem] shadow-xl border border-slate-100 overflow-hidden h-full">
                        @if($isMinggu)
                            <div class="bg-slate-100 p-2 md:p-6 flex flex-col items-center justify-center h-full text-center grayscale force-no-p">
                                <h4 class="font-black text-slate-400 uppercase text-[5px] md:text-xs">SYSTEM OFF</h4>
                            </div>
                        @else
                            @php $jamSekarang = \Carbon\Carbon::now('Asia/Jakarta')->hour; @endphp
                            @if(!$cekAbsensi)
                                <div class="bg-white h-full p-2 md:p-6 flex flex-col items-center justify-center cursor-pointer text-center force-no-p" onclick="handleAbsensi()">
                                 <div class="text-lg md:text-4xl mb-1 rocket-float">🚀</div>
                                    <h4 class="font-black text-indigo-600 uppercase text-[6px] md:text-xs tracking-wider">Presensi Now</h4>
                                </div>
                            @elseif(!$cekAbsensi->jam_keluar && in_array($cekAbsensi->status, ['Hadir', 'Terlambat']))
                                <div class="h-full flex flex-col items-center justify-center cursor-pointer text-center p-2 md:p-6 transition-all {{ $jamSekarang < 17 ? 'bg-rose-50/60' : 'bg-white' }}" onclick="handlePulang({{ $jamSekarang }})">
                                    <div class="text-xl md:text-5xl shadow-sm rounded-full p-1 bg-white/50 shrink-0 mb-1">🏠</div>
                                    <h4 class="font-black text-rose-600 uppercase text-[5px] md:text-[11px] leading-tight tracking-tight max-w-full truncate">
                                        {{ $jamSekarang < 17 ? 'BELUM WAKTUNYA PULANG' : 'KLIK UNTUK PULANG' }}
                                    </h4>
                                    @if($jamSekarang < 17)
                                        <p class="text-[4px] md:text-[9px] text-rose-400 font-bold uppercase tracking-tighter mt-0.5 leading-none">TERSEDIA PUKUL 17:00</p>
                                    @endif
                                </div>
                            @else
                                <div class="bg-emerald-50 h-full p-2 md:p-6 flex flex-col items-center justify-center text-center force-no-p">
                                    <div class="text-lg md:text-4xl mb-1">🌟</div>
                                    <h4 class="font-black text-emerald-600 uppercase text-[6px] md:text-xs">TUGAS SELESAI</h4>
                                </div>
                            @endif
                        @endif
                    </div>

                </div>
            @endif

            {{-- 5. DISINI PERBAIKAN POIN 2 & 3: TABEL UTAMA LOG AKTIVITAS (Didesain lebih interaktif & dinamis) --}}
            <div class="mt-8 bg-white rounded-xl md:rounded-[2.5rem] shadow-xl border border-slate-100 overflow-hidden animate-fade-in w-full">
                <div class="p-4 md:p-8 border-b border-slate-100 bg-indigo-50/30 flex items-center justify-between">
                    <div class="flex items-center gap-2 md:gap-3">
                        <div class="w-6 h-6 md:w-10 md:h-10 bg-indigo-600 text-white rounded-lg flex items-center justify-center shadow-lg text-xs md:text-base">📋</div>
                        <h3 class="font-black text-slate-800 uppercase tracking-tight text-xs md:text-lg">
                            @if(Auth::user()->role == 'admin') LOG AKTIVITAS HARI INI @else LOG AKTIVITAS MINGGU INI @endif
                        </h3>
                    </div>
                    <span class="text-[8px] md:text-xs bg-indigo-100 text-indigo-700 px-2.5 py-1 rounded-full font-bold uppercase tracking-wider">Live Log</span>
                </div>

                <div class="w-full overflow-hidden rounded-t-xl md:rounded-t-[2rem]">
                    <table class="w-full text-left mobile-table-text md:text-sm">
                        <thead class="bg-slate-900 text-white">
                            <tr>
                                <th class="mobile-padding md:p-6 text-[7px] md:text-[10px] font-black uppercase tracking-widest text-left w-3/12 md:w-3/12">
                                    {{ Auth::user()->role == 'admin' ? 'Nama Karyawan' : 'Hari / Tanggal' }}
                                </th>
                                <th class="mobile-padding md:p-6 text-[7px] md:text-[10px] font-black uppercase tracking-widest text-center w-1.5/12 md:w-1.5/12">Jam Masuk</th>
                                <th class="mobile-padding md:p-6 text-[7px] md:text-[10px] font-black uppercase tracking-widest text-center w-1.5/12 md:w-1.5/12">Jam Pulang</th>
                                <th class="mobile-padding md:p-6 text-[7px] md:text-[10px] font-black uppercase tracking-widest text-center w-2/12 md:w-2/12">Status Kehadiran</th>
                                <th class="mobile-padding md:p-6 text-[7px] md:text-[10px] font-black uppercase tracking-widest text-left w-4/12 md:w-4/12">Keterangan / Alasan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @if(Auth::user()->role == 'admin')
                                @forelse($absensiHariIni as $absen)
                                <tr class="hover:bg-slate-50/80 transition-all duration-200">
                                    <td class="mobile-padding md:p-6 font-bold text-slate-800 mobile-truncate text-left">{{ $absen->user->name }}</td>
                                    <td class="mobile-padding md:p-6 text-center font-mono text-[7px] md:text-sm font-bold text-blue-600">{{ $absen->jam_masuk ?? '--:--' }}</td>
                                    <td class="mobile-padding md:p-6 text-center font-mono text-[7px] md:text-sm font-bold text-emerald-600">{{ $absen->jam_keluar ?? '--:--' }}</td>
                                    <td class="mobile-padding md:p-6 text-center">
                                        <span class="px-1.5 py-0.5 md:px-4 md:py-1.5 rounded-full text-[6px] md:text-[9px] font-black uppercase block md:inline text-center mobile-badge shadow-sm
                                            {{ in_array($absen->status, ['Terlambat', 'Sakit']) ? 'bg-rose-100 text-rose-600 border border-rose-200' : ($absen->status == 'Hadir' ? 'bg-emerald-100 text-emerald-600 border border-emerald-200' : 'bg-amber-100 text-amber-600 border border-amber-200') }}">
                                            {{ $absen->status }}
                                        </span>
                                    </td>
                                    <td class="mobile-padding md:p-6 text-left">
                                        @if(in_array($absen->status, ['Izin', 'Sakit']))
                                            <div class="flex items-center gap-1.5 text-slate-700 font-semibold text-[7px] md:text-xs bg-slate-50 px-2 py-1 rounded-lg border border-slate-100">
                                                <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $absen->status == 'Izin' ? 'bg-amber-500' : 'bg-rose-500' }}"></span>
                                                <span class="truncate max-w-[100px] md:max-w-[250px] italic text-slate-600">{{ $absen->keterangan ?? 'Tanpa alasan tertulis' }}</span>
                                            </div>
                                        @else
                                            <span class="text-slate-300 font-bold font-mono text-[7px] md:text-xs ml-2">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="p-8 text-center text-slate-400 italic mobile-table-text md:text-sm">Belum ada aktivitas hari ini.</td></tr>
                                @endforelse
                            @else
                                @forelse($absensis as $log)
                                <tr class="hover:bg-slate-50/80 transition-all duration-200">
                                    <td class="mobile-padding md:p-6 font-bold text-slate-800 mobile-truncate text-left">
                                        {{ \Carbon\Carbon::parse($log->tanggal)->translatedFormat('l, d M Y') }}
                                    </td>
                                    <td class="mobile-padding md:p-6 text-center font-mono text-[7px] md:text-sm font-bold text-blue-600">{{ $log->jam_masuk ?? '--:--' }}</td>
                                    <td class="mobile-padding md:p-6 text-center font-mono text-[7px] md:text-sm font-bold text-emerald-600">{{ $log->jam_keluar ?? '--:--' }}</td>
                                    <td class="mobile-padding md:p-6 text-center">
                                        <span class="px-1.5 py-0.5 md:px-4 md:py-1.5 rounded-full text-[6px] md:text-[9px] font-black uppercase block md:inline text-center mobile-badge shadow-sm
                                            {{ $log->status == 'Terlambat' ? 'bg-rose-100 text-rose-600 border border-rose-200' : ($log->status == 'Hadir' ? 'bg-emerald-100 text-emerald-600 border border-emerald-200' : 'bg-amber-100 text-amber-600 border border-amber-200') }}">
                                            {{ $log->status }}
                                        </span>
                                    </td>
                                    <td class="mobile-padding md:p-6 text-left">
                                        @if(in_array($log->status, ['Izin', 'Sakit']))
                                            <div class="flex items-center gap-1.5 text-slate-700 font-semibold text-[7px] md:text-xs bg-slate-50 px-2 py-1 rounded-lg border border-slate-100">
                                                <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $log->status == 'Izin' ? 'bg-amber-500' : 'bg-rose-500' }}"></span>
                                                <span class="truncate max-w-[100px] md:max-w-[250px] italic text-slate-600">{{ $log->keterangan ?? 'Tanpa alasan tertulis' }}</span>
                                            </div>
                                        @else
                                            <span class="text-slate-300 font-bold font-mono text-[7px] md:text-xs ml-2">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                    <tr><td colspan="5" class="p-8 text-center text-slate-400 italic mobile-table-text md:text-sm">Belum ada aktivitas minggu ini.</td></tr>
                                @endforelse
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 6. DISINI PERBAIKAN POIN 3: REKAPITULASI BULANAN DENGAN BARIS INTERAKTIF --}}
            @if(Auth::user()->role == 'admin')
                <div class="mt-8 bg-white rounded-xl md:rounded-[2.5rem] shadow-xl border border-slate-100 overflow-hidden mb-10 animate-fade-in w-full">
                    <div class="p-4 md:p-8 border-b border-slate-100 bg-indigo-50/30 flex items-center justify-between">
                        <div class="flex items-center gap-2 md:gap-3">
                            <div class="w-6 h-6 md:w-10 md:h-10 bg-indigo-950 text-white rounded-lg flex items-center justify-center shadow-lg text-xs md:text-base">📊</div>
                            <h3 class="font-black text-slate-800 uppercase tracking-tight text-xs md:text-lg">
                                REKAPITULASI ABSENSI KARYAWAN - {{ strtoupper($namaBulan) }} {{ date('Y') }}
                            </h3>
                        </div>
                        <span class="text-[8px] md:text-xs bg-slate-900 text-white px-2.5 py-1 rounded-full font-mono font-bold shadow-sm">Monthly Report</span>
                    </div>
                    <div class="w-full overflow-hidden rounded-t-xl md:rounded-t-[2rem]">
                        <table class="w-full text-left mobile-table-text md:text-sm">
                            <thead class="bg-indigo-950 text-white">
                                <tr>
                                    <th class="mobile-padding md:p-6 text-[7px] md:text-[10px] font-black uppercase tracking-widest text-left w-4/12 md:w-3/12">Nama Karyawan</th>
                                    <th class="mobile-padding md:p-6 text-[7px] md:text-[10px] font-black uppercase tracking-widest text-center bg-emerald-950/40 w-2/12 md:w-3/12">Hadir</th>
                                    <th class="mobile-padding md:p-6 text-[7px] md:text-[10px] font-black uppercase tracking-widest text-center bg-amber-950/40 w-2/12 md:w-3/12">Izin</th>
                                    <th class="mobile-padding md:p-6 text-[7px] md:text-[10px] font-black uppercase tracking-widest text-center bg-rose-950/40 w-2/12 md:w-3/12">Sakit</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($rekapBulanan as $rekap)
                                <tr class="hover:bg-slate-50/80 transition-all duration-200">
                                    <td class="mobile-padding md:p-6 font-black text-slate-800 uppercase text-left mobile-truncate">{{ $rekap->nama_lengkap }}</td>
                                    <td class="mobile-padding md:p-6 text-center font-bold text-emerald-600 font-mono bg-emerald-50/20">{{ $rekap->total_hadir }}</td>
                                    <td class="mobile-padding md:p-6 text-center font-bold text-blue-600 font-mono bg-blue-50/20">{{ $rekap->total_izin }}</td>
                                    <td class="mobile-padding md:p-6 text-center font-bold text-rose-600 font-mono bg-rose-50/20">{{ $rekap->total_sakit }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL ABSENSI --}}
    <div id="absensiModal" class="fixed inset-0 z-[999] hidden">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeAbsensiModal()"></div>
        <div class="relative flex items-center justify-center min-h-screen p-4">
            <div id="modalContent" class="relative bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl p-8 transform translate-y-full transition-transform duration-500 border border-white/20">
                <button type="button" onclick="closeAbsensiModal()" class="absolute top-5 right-6 text-slate-400 hover:text-rose-500 transition-all text-3xl font-bold">✕</button>
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-black text-slate-800 uppercase tracking-tight mb-4 italic">Konfirmasi Lokasi</h2>
                    <div style="position: relative;" class="rounded-3xl overflow-hidden border-4 border-slate-50">
                        <div id="map-preview" style="height: 200px; width: 100%;"></div>
                        <button type="button" onclick="manualRecenter()" class="absolute bottom-4 right-4 z-[1000] bg-white px-3 py-2 rounded-xl text-[10px] font-black shadow-lg border border-slate-100 color-indigo-600 uppercase">📍 FOKUS</button>
                    </div>
                </div>
                <form id="formUtamaAbsensi" action="{{ route('absensi.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="lat" id="lat"><input type="hidden" name="lng" id="lng">
                    <button type="submit" class="w-full bg-indigo-600 text-white font-black py-5 rounded-[1.5rem] shadow-xl uppercase active:scale-95 transition-all tracking-widest">Kirim Presensi 🚀</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        let map; let currentLat, currentLng;
        const KANTOR_LAT = 3.50690; const KANTOR_LNG = 98.66092; const MAX_RADIUS = 20;

        function updateClock() {
            const clock = document.getElementById('realtime-clock');
            if(clock) clock.textContent = new Date().toLocaleTimeString('id-ID', { hour12: false });
        }
        setInterval(updateClock, 1000);
    </script>
</x-app-layout>