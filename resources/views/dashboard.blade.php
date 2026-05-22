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
    
        <div class="floating-emoji" style="left:10%; animation-delay:0s;">✨</div>
        <div class="floating-emoji" style="left:30%; animation-delay:2s;">🚀</div>
        <div class="floating-emoji" style="left:60%; animation-delay:4s;">⚡</div>
        <div class="floating-emoji" style="left:80%; animation-delay:1s;">💎</div>
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
                        <div class="flex gap-1.5 md:gap-3 mt-2 md:mt-4">
                            <div class="glass-stat hover-card px-2 py-1 md:px-4 md:py-2 rounded-xl md:rounded-2xl flex flex-col items-center min-w-[45px] md:min-w-[70px]">
                                <span class="text-[6px] md:text-[10px] font-bold text-indigo-200 uppercase">Hadir</span>
                                <span class="text-xs md:text-lg font-black leading-none mt-0.5 md:mt-1">{{ $totalHadir ?? 0 }}</span>
                            </div>
                            <div class="glass-stat hover-card px-2 py-1 md:px-4 md:py-2 rounded-xl md:rounded-2xl flex flex-col items-center min-w-[45px] md:min-w-[70px]">
                                <span class="text-[6px] md:text-[10px] font-bold text-indigo-200 uppercase">Izin</span>
                                <span class="text-xs md:text-lg font-black leading-none mt-0.5 md:mt-1">{{ $ringkasanStatistik->total_izin ?? 0 }}</span>
                            </div>
                            <div class="glass-stat hover-card px-2 py-1 md:px-4 md:py-2 rounded-xl md:rounded-2xl flex flex-col items-center min-w-[45px] md:min-w-[70px]">
                                <span class="text-[6px] md:text-[10px] font-bold text-indigo-200 uppercase">Sakit</span>
                                <span class="text-xs md:text-lg font-black leading-none mt-0.5 md:mt-1">{{ $ringkasanStatistik->total_sakit ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="glass-card hover-card live-badge p-2 md:p-5 rounded-xl md:rounded-[1.5rem] flex items-center gap-2 md:gap-5 w-5/12 md:w-auto shadow-xl border-white/20 shrink-0">
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
                                <div class="bg-slate-800/40 border border-white/5 p-2 md:p-6 rounded-xl md:rounded-[2rem] flex flex-col items-center text-center"><h3 class="text-xs md:text-3xl font-black text-white tracking-tighter">{{ $totalKaryawan ?? 0 }}</h3><p class="text-[5px] md:text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">ANGGOTA</p></div>
                                <div class="bg-emerald-500/10 border border-emerald-500/20 p-2 md:p-6 rounded-xl md:rounded-[2rem] flex flex-col items-center text-center"><h3 class="text-xs md:text-3xl font-black text-emerald-400 tracking-tighter">{{ $hadirHariIni ?? 0 }}</h3><p class="text-[5px] md:text-[10px] font-bold text-emerald-500/70 uppercase tracking-wider mt-0.5">HADIR</p></div>
                                <div class="bg-amber-500/10 border border-amber-500/20 p-2 md:p-6 rounded-xl md:rounded-[2rem] flex flex-col items-center text-center"><h3 class="text-xs md:text-3xl font-black text-amber-400 tracking-tighter">{{ $totalIzin ?? 0 }}</h3><p class="text-[5px] md:text-[10px] font-bold text-amber-500/70 uppercase tracking-wider mt-0.5">IZIN</p></div>
                                <div class="bg-rose-500/10 border border-rose-500/20 p-2 md:p-6 rounded-xl md:rounded-[2rem] flex flex-col items-center text-center"><h3 class="text-xs md:text-3xl font-black text-rose-400 tracking-tighter">{{ $totalSakit ?? 0 }}</h3><p class="text-[5px] md:text-[10px] font-bold text-rose-500/70 uppercase tracking-wider mt-0.5">SAKIT</p></div>
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
                            {{-- Perbaikan Poin Utama: Mengubah Typo penutup php menjadi valid @endphp --}}
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

            {{-- 5. TABEL UTAMA: LOG MINGGUAN --}}
            <div class="mt-8 bg-white rounded-xl md:rounded-[2.5rem] shadow-xl border border-slate-100 overflow-hidden animate-fade-in w-full">
                <div class="p-4 md:p-8 border-b border-slate-100 bg-indigo-50/30 flex items-center gap-2 md:gap-3">
                    <div class="w-6 h-6 md:w-10 md:h-10 bg-indigo-600 text-white rounded-lg flex items-center justify-center shadow-lg text-xs md:text-base">📋</div>
                    <h3 class="font-black text-slate-800 uppercase tracking-tight text-xs md:text-lg">
                        @if(Auth::user()->role == 'admin') LOG AKTIVITAS HARI INI @else LOG AKTIVITAS MINGGU INI @endif
                    </h3>
                </div>

                <div class="w-full overflow-hidden rounded-t-xl md:rounded-t-[2rem]">
                    <table class="w-full text-left mobile-table-text md:text-sm">
                        <thead class="bg-slate-900 text-white">
                            <tr>
                                <th class="mobile-padding md:p-6 text-[7px] md:text-[10px] font-black uppercase tracking-widest text-left w-4/12 md:w-3/12">
                                    {{ Auth::user()->role == 'admin' ? 'Nama Karyawan' : 'Hari / Tanggal' }}
                                </th>
                                <th class="mobile-padding md:p-6 text-[7px] md:text-[10px] font-black uppercase tracking-widest text-center w-2/12 md:w-2/12">Jam Masuk</th>
                                <th class="mobile-padding md:p-6 text-[7px] md:text-[10px] font-black uppercase tracking-widest text-center w-2/12 md:w-2/12">Jam Pulang</th>
                                <th class="mobile-padding md:p-6 text-[7px] md:text-[10px] font-black uppercase tracking-widest text-center w-4/12 md:w-5/12">Status Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @if(Auth::user()->role == 'admin')
                                @forelse($absensiHariIni as $absen)
                                <tr class="hover:bg-indigo-50/20 transition-all">
                                    <td class="mobile-padding md:p-6 font-bold text-slate-800 mobile-truncate text-left">{{ $absen->user->name }}</td>
                                    <td class="mobile-padding md:p-6 text-center font-mono text-[7px] md:text-sm font-bold text-blue-600">{{ $absen->jam_masuk ?? '--:--' }}</td>
                                    <td class="mobile-padding md:p-6 text-center font-mono text-[7px] md:text-sm font-bold text-emerald-600">{{ $absen->jam_keluar ?? '--:--' }}</td>
                                    <td class="mobile-padding md:p-6 text-center">
                                        <span class="px-1.5 py-0.5 md:px-4 md:py-1.5 rounded-full text-[6px] md:text-[9px] font-black uppercase block md:inline text-center mobile-badge
                                            {{ in_array($absen->status, ['Terlambat', 'Sakit']) ? 'bg-rose-100 text-rose-600' : ($absen->status == 'Hadir' ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600') }}">
                                            {{ $absen->status }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="p-8 text-center text-slate-400 italic mobile-table-text md:text-sm">Belum ada aktivitas hari ini.</td></tr>
                                @endforelse
                            @else
                                @forelse($absensis as $log)
                                <tr class="hover:bg-indigo-50/20 transition-all">
                                    <td class="mobile-padding md:p-6 font-bold text-slate-800 mobile-truncate text-left">
                                        {{ \Carbon\Carbon::parse($log->tanggal)->translatedFormat('l, d M Y') }}
                                    </td>
                                    <td class="mobile-padding md:p-6 text-center font-mono text-[7px] md:text-sm font-bold text-blue-600">{{ $log->jam_masuk ?? '--:--' }}</td>
                                    <td class="mobile-padding md:p-6 text-center font-mono text-[7px] md:text-sm font-bold text-emerald-600">{{ $log->jam_keluar ?? '--:--' }}</td>
                                    <td class="mobile-padding md:p-6 text-center">
                                        <span class="px-1.5 py-0.5 md:px-4 md:py-1.5 rounded-full text-[6px] md:text-[9px] font-black uppercase block md:inline text-center mobile-badge
                                            {{ $log->status == 'Terlambat' ? 'bg-rose-100 text-rose-600' : ($log->status == 'Hadir' ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600') }}">
                                            {{ $log->status }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                    <tr><td colspan="4" class="p-8 text-center text-slate-400 italic mobile-table-text md:text-sm">Belum ada aktivitas minggu ini.</td></tr>
                                @endforelse
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 6. REKAPITULASI BULANAN (PERBAIKAN: Judul Diperbesar, Diberi Jarak, dan Ditambahkan Ikon Grafik Ungu Premium) --}}
            @if(Auth::user()->role == 'admin')
                <div class="mt-8 bg-white rounded-xl md:rounded-[2.5rem] shadow-xl border border-slate-100 overflow-hidden mb-10 animate-fade-in w-full">
                    {{-- Desain Header Rekapitulasi Baru: Besar, Rapi, Sejajar dengan Ikon Grafik 📊 --}}
                    <div class="p-4 md:p-8 border-b border-slate-100 bg-indigo-50/30 flex items-center gap-2 md:gap-3">
                        <div class="w-6 h-6 md:w-10 md:h-10 bg-indigo-950 text-white rounded-lg flex items-center justify-center shadow-lg text-xs md:text-base">📊</div>
                        <h3 class="font-black text-slate-800 uppercase tracking-tight text-xs md:text-lg">
                            REKAPITULASI ABSENSI KARYAWAN - {{ strtoupper($namaBulan) }} {{ date('Y') }}
                        </h3>
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
                            <tbody class="divide-y divide-slate-50">
                                @foreach($rekapBulanan as $rekap)
                                <tr class="hover:bg-slate-50/50">
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
                    <button type="submit" class="pulse-btn class="w-full bg-indigo-600 text-white font-black py-5 rounded-[1.5rem] shadow-xl uppercase active:scale-95 transition-all tracking-widest">Kirim Presensi 🚀</button>
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

        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371e3;
            const dLat = (lat2-lat1) * Math.PI/180; const dLon = (lon2-lon1) * Math.PI/180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(lat1 * Math.PI/180) * Math.cos(lat2 * Math.PI/180) * Math.sin(dLon/2) * Math.sin(dLon/2);
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        }

        function konfirmasiStatus(status) {
            const ket = document.getElementById('keterangan_input').value;
            if(!ket) { Swal.fire({ title: 'Isi Alasan', text: 'Silakan isi alasan singkat.', icon: 'warning' }); return; }
            document.getElementById('status_input').value = status;
            document.getElementById('formIzinSakit').submit();
        }

        function handleAbsensi() {
            if (!navigator.geolocation) { Swal.fire('Gagal!', 'GPS tidak didukung.', 'error'); return; }
            Swal.fire({ title: 'Mendeteksi Lokasi...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude; const lng = position.coords.longitude;
                    const distance = calculateDistance(lat, lng, KANTOR_LAT, KANTOR_LNG);
                    Swal.close();
                    if (distance > MAX_RADIUS) {
                        Swal.fire({ icon: 'error', title: 'Diluar Jangkauan!', text: `Jarak: ${Math.round(distance)}m.` });
                    } else { showModalAbsen(lat, lng); }
                },
                () => { Swal.close(); Swal.fire('Error', 'GPS Gagal.', 'error'); },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        }

        function handlePulang(jam) {
            if (jam < 17) {
                Swal.fire({ icon: 'warning', title: 'Belum Waktunya!', text: 'Absen pulang baru bisa dilakukan mulai pukul 17:00 WIB.', confirmButtonColor: '#e11d48' });
                return;
            }
            handleAbsensi();
        }

        function showModalAbsen(lat, lng) {
            currentLat = lat; currentLng = lng;
            document.getElementById('lat').value = lat; document.getElementById('lng').value = lng;
            document.getElementById('absensiModal').classList.remove('hidden');
            setTimeout(() => { document.getElementById('modalContent').classList.remove('translate-y-full'); initMap(lat, lng); }, 50);
        }

        function initMap(lat, lng) {
            if (map) { map.remove(); }
            map = L.map('map-preview', { center: [lat, lng], zoom: 17 });
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
            L.marker([lat, lng]).addTo(map).bindPopup('Lokasi Anda').openPopup();
            L.circle([KANTOR_LAT, KANTOR_LNG], { color: '#4f46e5', radius: MAX_RADIUS }).addTo(map);
        }

        function closeAbsensiModal() {
            document.getElementById('modalContent').classList.add('translate-y-full');
            setTimeout(() => { document.getElementById('absensiModal').classList.add('hidden'); }, 500);
        }

        function manualRecenter() {
            if(map && currentLat && currentLng) { map.setView([currentLat, currentLng], 17); }
        }
    </script>
</x-app-layout>

<!-- ===================================================== -->
<!-- PREMIUM INTERACTIVE UI -->
<!-- ===================================================== -->

<style>

/* ===================================================== */
/* GLOBAL */
/* ===================================================== */

html{
    scroll-behavior:smooth;
}

body{
    background:#f5f7ff;
}

/* ===================================================== */
/* CARD HOVER INTERACTIVE */
/* ===================================================== */

.hover-card{
    transition:.35s ease;
    cursor:pointer;
}

.hover-card:hover{
    transform:
    translateY(-10px)
    scale(1.02);

    box-shadow:
    0 25px 45px rgba(79,70,229,.15);
}

/* ===================================================== */
/* HERO */
/* ===================================================== */

.hero-premium{
    position:relative;
    overflow:hidden;

    background:
    linear-gradient(
        135deg,
        #1e1b4b 0%,
        #312e81 40%,
        #4338ca 100%
    );

    border:1px solid rgba(255,255,255,.06);

    box-shadow:
    0 20px 60px rgba(79,70,229,.18);

    animation:fadeHero 1s ease;
}

@keyframes fadeHero{
    from{
        opacity:0;
        transform:translateY(30px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

.hero-premium::before{
    content:'';
    position:absolute;
    inset:0;

    background:
    linear-gradient(
        120deg,
        transparent,
        rgba(255,255,255,.06),
        transparent
    );

    transform:translateX(-100%);
    animation:heroShine 8s linear infinite;
}

@keyframes heroShine{
    100%{
        transform:translateX(100%);
    }
}

/* ===================================================== */
/* GLASS */
/* ===================================================== */

.glass-card{
    background:rgba(255,255,255,.92);
    backdrop-filter:blur(12px);
    border:1px solid rgba(255,255,255,.15);
}

.glass-stat{
    background:rgba(255,255,255,.08);
    backdrop-filter:blur(10px);
    border:1px solid rgba(255,255,255,.1);
    transition:.3s;
}

.glass-stat:hover{
    transform:translateY(-6px);
    background:rgba(255,255,255,.14);
}

/* ===================================================== */
/* TABLE */
/* ===================================================== */

tbody tr{
    transition:.3s;
}

tbody tr:hover{
    background:#eef2ff;
    transform:scale(1.005);
}

/* ===================================================== */
/* BUTTON */
/* ===================================================== */

.pulse-btn{
    background:linear-gradient(
        135deg,
        #4f46e5,
        #7c3aed
    );

    transition:.3s;
}

.pulse-btn:hover{
    transform:translateY(-4px);
    box-shadow:
    0 18px 35px rgba(79,70,229,.25);
}

/* ===================================================== */
/* CLOCK */
/* ===================================================== */

#realtime-clock{
    animation:clockGlow 2s infinite alternate;
}

@keyframes clockGlow{
    from{
        box-shadow:0 0 0 rgba(99,102,241,.1);
    }
    to{
        box-shadow:0 0 20px rgba(99,102,241,.25);
    }
}

/* ===================================================== */
/* TOAST */
/* ===================================================== */

#liveToast{
    position:fixed;
    top:25px;
    right:25px;

    background:white;
    width:320px;

    border-radius:24px;

    box-shadow:
    0 25px 50px rgba(0,0,0,.12);

    z-index:999999;

    overflow:hidden;

    animation:toastSlide .8s ease;
}

@keyframes toastSlide{
    from{
        opacity:0;
        transform:translateX(80px);
    }
    to{
        opacity:1;
        transform:translateX(0);
    }
}

.toast-top{
    background:
    linear-gradient(
        135deg,
        #4f46e5,
        #7c3aed
    );

    padding:16px 20px;

    color:white;

    font-weight:800;

    display:flex;
    align-items:center;
    gap:10px;
}

.toast-body{
    padding:20px;
}

.toast-body h3{
    font-size:15px;
    font-weight:800;
    color:#0f172a;
    margin-bottom:5px;
}

.toast-body p{
    font-size:13px;
    color:#64748b;
}

.toast-btn{
    margin-top:15px;

    width:100%;

    border:none;

    background:#4f46e5;

    color:white;

    padding:12px;

    border-radius:14px;

    font-weight:700;

    transition:.3s;
}

.toast-btn:hover{
    transform:translateY(-3px);
}

/* ===================================================== */
/* MINI ASSISTANT */
/* ===================================================== */

#miniAssistant{
    position:fixed;
    right:20px;
    bottom:20px;
    z-index:99999;
}

#miniAssistant img{
    width:82px;
    cursor:pointer;

    transition:.3s;

    filter:
    drop-shadow(
        0 15px 25px rgba(79,70,229,.25)
    );
}

#miniAssistant img:hover{
    transform:
    scale(1.08)
    rotate(-5deg);
}

.assistant-chat{
    position:absolute;

    right:90px;
    bottom:15px;

    background:white;

    padding:12px 16px;

    border-radius:16px;

    font-size:12px;

    font-weight:700;

    white-space:nowrap;

    color:#1e293b;

    box-shadow:
    0 15px 35px rgba(0,0,0,.08);
}

/* ===================================================== */
/* POPUP */
/* ===================================================== */

#popupInfo{
    position:fixed;

    bottom:120px;
    right:25px;

    width:300px;

    background:white;

    border-radius:24px;

    padding:22px;

    z-index:99999;

    box-shadow:
    0 20px 50px rgba(0,0,0,.12);

    display:none;

    animation:popupShow .5s ease;
}

@keyframes popupShow{
    from{
        opacity:0;
        transform:translateY(30px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

#popupInfo h3{
    font-size:16px;
    font-weight:800;
    margin-bottom:8px;
    color:#0f172a;
}

#popupInfo p{
    font-size:13px;
    color:#64748b;
    line-height:1.6;
}

#popupInfo button{
    margin-top:18px;

    width:100%;

    border:none;

    padding:12px;

    border-radius:14px;

    background:
    linear-gradient(
        135deg,
        #4f46e5,
        #7c3aed
    );

    color:white;

    font-weight:700;
}

/* ===================================================== */
/* ROCKET */
/* ===================================================== */

.rocket-float{
    transition:.3s;
}

.rocket-float:hover{
    transform:
    translateY(-10px)
    rotate(-8deg)
    scale(1.1);
}

/* ===================================================== */
/* CARD SHOW */
/* ===================================================== */

.grid > div{
    animation:cardShow .7s ease both;
}

.grid > div:nth-child(2){
    animation-delay:.2s;
}

.grid > div:nth-child(3){
    animation-delay:.4s;
}

@keyframes cardShow{
    from{
        opacity:0;
        transform:translateY(20px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

/* ===================================================== */
/* MOBILE */
/* ===================================================== */

@media(max-width:768px){

    #liveToast{
        width:90%;
        right:5%;
    }

    #popupInfo{
        width:90%;
        right:5%;
    }

    #miniAssistant img{
        width:70px;
    }

}

</style>

<!-- ===================================================== -->
<!-- PREMIUM TOAST -->
<!-- ===================================================== -->

<!-- ===================================================== -->
<!-- REMINDER POPUP -->
<!-- ===================================================== -->



</div>
<!-- ===================================================== -->
<!-- MINI ASSISTANT -->
<!-- ===================================================== -->


</div>

<!-- ===================================================== -->
<!-- POPUP INFO -->
<!-- ===================================================== -->

<div id="popupInfo">

    <h3>
        🔔 Reminder Kehadiran
    </h3>

    <p>
        Pastikan GPS aktif sebelum melakukan absensi agar lokasi terdeteksi dengan akurat 📍
    </p>

    <button onclick="handleAbsensi()">
        Mulai Absensi
    </button>

</div>

<!-- ===================================================== -->
<!-- SCRIPT -->
<!-- ===================================================== -->

<script>

/* ===================================================== */
/* AUTO HIDE TOAST */
/* ===================================================== */

setTimeout(() => {

    const toast =
    document.getElementById('liveToast');

    if(toast){

        toast.style.transition='.5s';

        toast.style.opacity='0';

        toast.style.transform='translateX(80px)';

        setTimeout(() => {

            toast.style.display='none';

        },500);

    }

},7000);

/* ===================================================== */
/* ASSISTANT CLICK */
/* ===================================================== */

const assistant =
document.getElementById('assistantImg');

assistant.addEventListener('click',function(){

    const popup =
    document.getElementById('popupInfo');

    if(
        popup.style.display === 'block'
    ){

        popup.style.display='none';

    }else{

        popup.style.display='block';

    }

});

/* ===================================================== */
/* CLOSE POPUP OUTSIDE */
/* ===================================================== */

document.addEventListener('click',function(e){

    const popup =
    document.getElementById('popupInfo');

    const assistant =
    document.getElementById('miniAssistant');

    if(
        !assistant.contains(e.target)
        &&
        !popup.contains(e.target)
    ){

        popup.style.display='none';

    }

});

/* ===================================================== */
/* HOVER CARD EFFECT */
/* ===================================================== */

document.querySelectorAll('.hover-card')
.forEach(card => {

    card.addEventListener('mouseenter',() => {

        card.style.transform =
        'translateY(-10px) scale(1.02)';

    });

    card.addEventListener('mouseleave',() => {

        card.style.transform =
        'translateY(0px) scale(1)';

    });

});

</script>


<!-- ===================================================== -->
<!-- PREMIUM INTERACTIVE UI -->
<!-- ===================================================== -->

<style>

/* ===================================================== */
/* GLOBAL */
/* ===================================================== */

html{
    scroll-behavior:smooth;
}

body{
    background:#f5f7ff;
}

/* ===================================================== */
/* CARD HOVER INTERACTIVE */
/* ===================================================== */

.hover-card{
    transition:.35s ease;
    cursor:pointer;
}

.hover-card:hover{
    transform:
    translateY(-10px)
    scale(1.02);

    box-shadow:
    0 25px 45px rgba(79,70,229,.15);
}

/* ===================================================== */
/* HERO */
/* ===================================================== */

.hero-premium{
    position:relative;
    overflow:hidden;

    background:
    linear-gradient(
        135deg,
        #1e1b4b 0%,
        #312e81 40%,
        #4338ca 100%
    );

    border:1px solid rgba(255,255,255,.06);

    box-shadow:
    0 20px 60px rgba(79,70,229,.18);

    animation:fadeHero 1s ease;
}

@keyframes fadeHero{
    from{
        opacity:0;
        transform:translateY(30px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

.hero-premium::before{
    content:'';
    position:absolute;
    inset:0;

    background:
    linear-gradient(
        120deg,
        transparent,
        rgba(255,255,255,.06),
        transparent
    );

    transform:translateX(-100%);
    animation:heroShine 8s linear infinite;
}

@keyframes heroShine{
    100%{
        transform:translateX(100%);
    }
}

/* ===================================================== */
/* GLASS */
/* ===================================================== */

.glass-card{
    background:rgba(255,255,255,.92);
    backdrop-filter:blur(12px);
    border:1px solid rgba(255,255,255,.15);
}

.glass-stat{
    background:rgba(255,255,255,.08);
    backdrop-filter:blur(10px);
    border:1px solid rgba(255,255,255,.1);
    transition:.3s;
}

.glass-stat:hover{
    transform:translateY(-6px);
    background:rgba(255,255,255,.14);
}

/* ===================================================== */
/* TABLE */
/* ===================================================== */

tbody tr{
    transition:.3s;
}

tbody tr:hover{
    background:#eef2ff;
    transform:scale(1.005);
}

/* ===================================================== */
/* BUTTON */
/* ===================================================== */

.pulse-btn{
    background:linear-gradient(
        135deg,
        #4f46e5,
        #7c3aed
    );

    transition:.3s;
}

.pulse-btn:hover{
    transform:translateY(-4px);
    box-shadow:
    0 18px 35px rgba(79,70,229,.25);
}

/* ===================================================== */
/* CLOCK */
/* ===================================================== */

#realtime-clock{
    animation:clockGlow 2s infinite alternate;
}

@keyframes clockGlow{
    from{
        box-shadow:0 0 0 rgba(99,102,241,.1);
    }
    to{
        box-shadow:0 0 20px rgba(99,102,241,.25);
    }
}

/* ===================================================== */
/* TOAST */
/* ===================================================== */

#liveToast{
    position:fixed;
    top:25px;
    right:25px;

    background:white;
    width:320px;

    border-radius:24px;

    box-shadow:
    0 25px 50px rgba(0,0,0,.12);

    z-index:999999;

    overflow:hidden;

    animation:toastSlide .8s ease;
}

@keyframes toastSlide{
    from{
        opacity:0;
        transform:translateX(80px);
    }
    to{
        opacity:1;
        transform:translateX(0);
    }
}

.toast-top{
    background:
    linear-gradient(
        135deg,
        #4f46e5,
        #7c3aed
    );

    padding:16px 20px;

    color:white;

    font-weight:800;

    display:flex;
    align-items:center;
    gap:10px;
}

.toast-body{
    padding:20px;
}

.toast-body h3{
    font-size:15px;
    font-weight:800;
    color:#0f172a;
    margin-bottom:5px;
}

.toast-body p{
    font-size:13px;
    color:#64748b;
}

.toast-btn{
    margin-top:15px;

    width:100%;

    border:none;

    background:#4f46e5;

    color:white;

    padding:12px;

    border-radius:14px;

    font-weight:700;

    transition:.3s;
}

.toast-btn:hover{
    transform:translateY(-3px);
}

/* ===================================================== */
/* MINI ASSISTANT */
/* ===================================================== */

#miniAssistant{
    position:fixed;
    right:20px;
    bottom:20px;
    z-index:99999;
}

#miniAssistant img{
    width:82px;
    cursor:pointer;

    transition:.3s;

    filter:
    drop-shadow(
        0 15px 25px rgba(79,70,229,.25)
    );
}

#miniAssistant img:hover{
    transform:
    scale(1.08)
    rotate(-5deg);
}

.assistant-chat{
    position:absolute;

    right:90px;
    bottom:15px;

    background:white;

    padding:12px 16px;

    border-radius:16px;

    font-size:12px;

    font-weight:700;

    white-space:nowrap;

    color:#1e293b;

    box-shadow:
    0 15px 35px rgba(0,0,0,.08);
}

/* ===================================================== */
/* POPUP */
/* ===================================================== */

#popupInfo{
    position:fixed;

    bottom:120px;
    right:25px;

    width:300px;

    background:white;

    border-radius:24px;

    padding:22px;

    z-index:99999;

    box-shadow:
    0 20px 50px rgba(0,0,0,.12);

    display:none;

    animation:popupShow .5s ease;
}

@keyframes popupShow{
    from{
        opacity:0;
        transform:translateY(30px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

#popupInfo h3{
    font-size:16px;
    font-weight:800;
    margin-bottom:8px;
    color:#0f172a;
}

#popupInfo p{
    font-size:13px;
    color:#64748b;
    line-height:1.6;
}

#popupInfo button{
    margin-top:18px;

    width:100%;

    border:none;

    padding:12px;

    border-radius:14px;

    background:
    linear-gradient(
        135deg,
        #4f46e5,
        #7c3aed
    );

    color:white;

    font-weight:700;
}

/* ===================================================== */
/* ROCKET */
/* ===================================================== */

.rocket-float{
    transition:.3s;
}

.rocket-float:hover{
    transform:
    translateY(-10px)
    rotate(-8deg)
    scale(1.1);
}

/* ===================================================== */
/* CARD SHOW */
/* ===================================================== */

.grid > div{
    animation:cardShow .7s ease both;
}

.grid > div:nth-child(2){
    animation-delay:.2s;
}

.grid > div:nth-child(3){
    animation-delay:.4s;
}

@keyframes cardShow{
    from{
        opacity:0;
        transform:translateY(20px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

/* ===================================================== */
/* MOBILE */
/* ===================================================== */

@media(max-width:768px){

    #liveToast{
        width:90%;
        right:5%;
    }

    #popupInfo{
        width:90%;
        right:5%;
    }

    #miniAssistant img{
        width:70px;
    }

}

</style>

<!-- ===================================================== -->
<!-- ROBOT ASSISTANT -->
<!-- ===================================================== -->

<div id="robotAssistant">

    <div class="robotBubble">
        Semangat bekerja🥰
    </div>

    <img 
    src="https://cdn-icons-png.flaticon.com/512/4712/4712027.png"
    alt="robot">

</div>

<!-- ===================================================== -->
<!-- STYLE -->
<!-- ===================================================== -->

<style>

/* ===================================================== */
/* MINI POPUP REMINDER */
/* ===================================================== */

#miniPopupReminder{

    position:fixed;

    top:22px;
    right:22px;

    background:white;

    border-radius:18px;

    padding:10px 14px;

    display:flex;
    align-items:flex-start;
    gap:10px;

    z-index:99999;

    min-width:240px;
    max-width:260px;

    border:1px solid #eef2ff;

    box-shadow:
    0 10px 30px rgba(0,0,0,.08);

    animation:
    popupFade .5s ease,
    popupOut .5s ease 3s forwards;
}

/* ICON */

.miniBell{

    width:34px;
    height:34px;

    min-width:34px;

    border-radius:12px;

    background:#eef2ff;

    display:flex;
    align-items:center;
    justify-content:center;

    font-size:15px;

    animation:bellShake 2s infinite;
}

/* CONTENT */

.miniPopupContent h4{

    margin:0;

    font-size:12px;

    font-weight:800;

    color:#0f172a;
}

.miniPopupContent p{

    margin-top:3px;

    font-size:10px;

    line-height:1.5;

    color:#64748b;

    font-weight:600;
}

/* ANIMATION */

@keyframes popupFade{

    from{
        opacity:0;
        transform:
        translateY(-15px)
        scale(.95);
    }

    to{
        opacity:1;
        transform:
        translateY(0)
        scale(1);
    }
}

@keyframes popupOut{

    to{
        opacity:0;
        transform:translateY(-10px);
        visibility:hidden;
    }
}

/* LONCENG */

@keyframes bellShake{

    0%{
        transform:rotate(0deg);
    }

    5%{
        transform:rotate(10deg);
    }

    10%{
        transform:rotate(-10deg);
    }

    15%{
        transform:rotate(8deg);
    }

    20%{
        transform:rotate(0deg);
    }

    100%{
        transform:rotate(0deg);
    }
}

/* MOBILE */

@media(max-width:768px){

    #miniPopupReminder{

        right:10px;
        left:10px;

        max-width:none;
        min-width:auto;

    }

}

/* MOBILE */

@media(max-width:768px){

    #miniPopupReminder{

        right:10px;
        left:10px;

        max-width:none;
        min-width:auto;

    }

}

/* ===================================================== */
/* ROBOT ASSISTANT */
/* ===================================================== */

#robotAssistant{
    position:fixed;
    bottom:18px;
    right:18px;
    z-index:9999;

    animation:robotFloat 3s ease-in-out infinite;
}

#robotAssistant img{
    width:85px;
    cursor:pointer;

    filter:
    drop-shadow(0 15px 30px rgba(79,70,229,.35));

    transition:.3s;
}

#robotAssistant img:hover{
    transform:scale(1.08) rotate(-5deg);
}

.robotBubble{
    position:absolute;
    right:90px;
    bottom:25px;

    background:white;

    padding:10px 16px;

    border-radius:18px;

    font-size:12px;
    font-weight:700;

    color:#1e293b;

    white-space:nowrap;

    box-shadow:
    0 10px 25px rgba(0,0,0,.08);

    animation:bubbleGlow 2s infinite alternate;
}

@keyframes bubbleGlow{
    from{
        box-shadow:
        0 10px 25px rgba(79,70,229,.08);
    }
    to{
        box-shadow:
        0 15px 35px rgba(124,58,237,.20);
    }
}

@keyframes robotFloat{
    0%{
        transform:translateY(0px);
    }
    50%{
        transform:translateY(-12px);
    }
    100%{
        transform:translateY(0px);
    }
}

/* ===================================================== */
/* GLOBAL CARD STYLE */
/* ===================================================== */

.grid > div{

    transition:.35s ease;
    position:relative;
    overflow:hidden;
    border-radius:28px;

}

/* hover semua card */

.grid > div:hover{

    transform:
    translateY(-8px)
    scale(1.02);

    box-shadow:
    0 20px 40px rgba(79,70,229,.12);

}

/* glow effect */

.grid > div::before{

    content:'';

    position:absolute;
    inset:0;

    background:
    linear-gradient(
        120deg,
        transparent,
        rgba(255,255,255,.25),
        transparent
    );

    transform:translateX(-100%);

}

.grid > div:hover::before{

    animation:cardShine 1.2s linear;

}

@keyframes cardShine{

    100%{
        transform:translateX(100%);
    }

}

/* ===================================================== */
/* CARD PENCAPAIAN */
/* ===================================================== */

.pencapaian-card{

    background:
    linear-gradient(
        135deg,
        #eef2ff,
        #dbeafe
    ) !important;

    border:1px solid rgba(99,102,241,.08);

}

/* icon kecil */

.pencapaian-card img{

    transition:.4s ease;
    cursor:pointer;

}

/* bergerak saat disentuh */

.pencapaian-card img:hover{

    transform:
    rotate(15deg)
    scale(1.18);

    filter:drop-shadow(0 10px 20px rgba(79,70,229,.25));

}

/* efek klik */

.pencapaian-card img:active{

    transform:
    scale(.92);

}

/* sparkle */

.pencapaian-card::after{

    content:'✨';

    position:absolute;

    top:14px;
    right:18px;

    font-size:14px;

    animation:sparkle 2s infinite;

}

@keyframes sparkle{

    0%{
        opacity:0;
        transform:scale(.5);
    }

    50%{
        opacity:1;
        transform:scale(1.2);
    }

    100%{
        opacity:0;
        transform:scale(.5);
    }

}

/* ===================================================== */
/* CARD INFO KEHADIRAN */
/* ===================================================== */

.info-kehadiran-card{

    background:
    linear-gradient(
        135deg,
        #fff7ed,
        #fef3c7
    ) !important;

    border:1px solid rgba(251,191,36,.12);

}

/* input alasan */

#formIzinSakit input{

    height:58px !important;

    font-size:13px !important;

    border-radius:18px !important;

    padding:14px 18px !important;

    background:#ffffff !important;

    box-shadow:
    inset 0 2px 6px rgba(0,0,0,.04);

    border:none !important;

}

/* tombol izin sakit */

#formIzinSakit button{

    height:48px !important;

    font-size:13px !important;

    border-radius:16px !important;

    transition:.3s ease;

    font-weight:600;

}

/* hover tombol */

#formIzinSakit button:hover{

    transform:
    translateY(-3px)
    scale(1.03);

    box-shadow:
    0 10px 20px rgba(0,0,0,.08);

}

/* tombol izin */

#formIzinSakit button:first-child{

    background:
    linear-gradient(
        135deg,
        #fbbf24,
        #f59e0b
    ) !important;

    color:white !important;

}

/* tombol sakit */

#formIzinSakit button:last-child{

    background:
    linear-gradient(
        135deg,
        #fb7185,
        #f43f5e
    ) !important;

    color:white !important;

}

/* ===================================================== */
/* CARD ABSENSI */
/* ===================================================== */

.absensi-card{

    background:
    linear-gradient(
        135deg,
        #eff6ff,
        #dbeafe
    ) !important;

    border:1px solid rgba(59,130,246,.10);

}

/* rocket default diam */

.rocket-float{

    transition:.35s ease;
    cursor:pointer;

}

/* bergerak hanya saat disentuh */

.rocket-float:hover{

    animation:rocketMove .8s ease infinite;

}

@keyframes rocketMove{

    0%{
        transform:translateY(0px) rotate(0deg);
    }

    25%{
        transform:translateY(-8px) rotate(-6deg);
    }

    50%{
        transform:translateY(0px) rotate(6deg);
    }

    75%{
        transform:translateY(-5px) rotate(-4deg);
    }

    100%{
        transform:translateY(0px) rotate(0deg);
    }

}

/* ===================================================== */
/* REALTIME STATUS */
/* ===================================================== */

.glass-card{

    background:
    linear-gradient(
        135deg,
        rgba(255,255,255,.95),
        rgba(255,255,255,.82)
    ) !important;

    backdrop-filter:blur(12px);

    transition:.4s ease;

    border-radius:24px;

}

.glass-card:hover{

    transform:
    translateY(-6px);

    box-shadow:
    0 20px 40px rgba(79,70,229,.12);

}

/* ===================================================== */
/* POPUP PENGINGAT */
/* ===================================================== */

#liveToast{

    position:fixed;
    top:25px;
    right:25px;

    width:260px;

    background:#ffffff;

    border-radius:20px;

    box-shadow:
    0 15px 40px rgba(0,0,0,.12);

    z-index:9999;

    overflow:hidden;

    animation:
    toastSlide .5s ease,
    toastHide .5s ease 3s forwards;

}

/* isi popup */

.toast-body{

    padding:16px;

}

/* top popup */

.toast-top{

    display:flex;
    align-items:center;
    gap:10px;

    padding:12px 16px;

    background:#ffffff;

    border-bottom:1px solid #f1f5f9;

    font-size:14px;
    font-weight:700;
    color:#111827;

}

/* icon lonceng */

.toast-top::before{

    content:'🔔';

    font-size:16px;

    animation:ring 1s infinite;

}

/* animasi lonceng */

@keyframes ring{

    0%{
        transform:rotate(0deg);
    }

    25%{
        transform:rotate(15deg);
    }

    50%{
        transform:rotate(-15deg);
    }

    75%{
        transform:rotate(10deg);
    }

    100%{
        transform:rotate(0deg);
    }

}

.toast-body h3{

    font-size:15px;
    font-weight:700;
    margin-bottom:6px;
    color:#111827;

}

.toast-body p{

    font-size:12px;
    color:#64748b;
    line-height:1.5;

}

/* animasi muncul */

@keyframes toastSlide{

    from{

        opacity:0;

        transform:
        translateY(-20px)
        scale(.9);

    }

    to{

        opacity:1;

        transform:
        translateY(0)
        scale(1);

    }

}

/* animasi hilang */

@keyframes toastHide{

    to{

        opacity:0;
        visibility:hidden;

        transform:
        translateY(-20px)
        scale(.9);

    }

}

/* ===================================================== */
/* ROBOT */
/* ===================================================== */

.robot-chat{

    position:fixed;
    right:20px;
    bottom:18px;

    display:flex;
    align-items:center;
    gap:10px;

    z-index:999;

}

.robot-text{

    background:#ffffff;

    padding:10px 14px;

    border-radius:16px;

    font-size:12px;
    font-weight:600;

    color:#4f46e5;

    box-shadow:
    0 8px 20px rgba(0,0,0,.08);

    animation:robotFloat 2s infinite ease-in-out;

}

/* robot bergerak terus */

.robot-img{

    animation:robotMove 2s infinite ease-in-out;

}

@keyframes robotMove{

    0%{
        transform:translateY(0px) rotate(0deg);
    }

    50%{
        transform:translateY(-8px) rotate(4deg);
    }

    100%{
        transform:translateY(0px) rotate(0deg);
    }

}

@keyframes robotFloat{

    0%{
        transform:translateY(0px);
    }

    50%{
        transform:translateY(-4px);
    }

    100%{
        transform:translateY(0px);
    }

}

/* ===================================================== */
/* MOBILE */
/* ===================================================== */

@media(max-width:768px){

    #liveToast{

        width:220px;
        right:15px;
        top:15px;

    }

    #formIzinSakit input{

        height:50px !important;
        font-size:11px !important;

    }

    #formIzinSakit button{

        height:42px !important;
        font-size:11px !important;

    }

    .robot-text{

        display:none;

    }

}

/* ===================================================== */
/* CARD UMUM */
/* ===================================================== */

.grid > div{

    transition:.35s;
}

.grid > div:hover{

    transform:translateY(-5px);

}

/* ===================================================== */
/* MOBILE */
/* ===================================================== */

@media(max-width:768px){

    #miniPopupReminder{

        width:88%;
        right:6%;
        top:15px;

    }

    #robotAssistant img{

        width:70px;

    }

    .robotBubble{

        font-size:10px;
        right:75px;

    }

}

</style>

<!-- ===================================================== -->
<!-- SCRIPT -->
<!-- ===================================================== -->

<script>

/* ===================================================== */
/* AUTO HIDE POPUP */
/* ===================================================== */

setTimeout(() => {

    const popup = document.getElementById('miniPopupReminder');

    if(popup){

        popup.style.display = 'none';

    }

},3000);

</script>
</div>

<!-- ===================================================== -->
<!-- POPUP INFO -->
<!-- ===================================================== -->

<div id="popupInfo">

    <h3>
        🔔 Reminder Kehadiran
    </h3>

    <p>
        Pastikan GPS aktif sebelum melakukan absensi agar lokasi terdeteksi dengan akurat 📍
    </p>

    <button onclick="handleAbsensi()">
        Mulai Absensi
    </button>

</div>

<!-- ===================================================== -->
<!-- SCRIPT -->
<!-- ===================================================== -->

<script>

/* ===================================================== */
/* AUTO HIDE TOAST */
/* ===================================================== */

setTimeout(() => {

    const toast =
    document.getElementById('liveToast');

    if(toast){

        toast.style.transition='.5s';

        toast.style.opacity='0';

        toast.style.transform='translateX(80px)';

        setTimeout(() => {

            toast.style.display='none';

        },500);

    }

},7000);

/* ===================================================== */
/* ASSISTANT CLICK */
/* ===================================================== */

const assistant =
document.getElementById('assistantImg');

assistant.addEventListener('click',function(){

    const popup =
    document.getElementById('popupInfo');

    if(
        popup.style.display === 'block'
    ){

        popup.style.display='none';

    }else{

        popup.style.display='block';

    }

});

/* ===================================================== */
/* CLOSE POPUP OUTSIDE */
/* ===================================================== */

document.addEventListener('click',function(e){

    const popup =
    document.getElementById('popupInfo');

    const assistant =
    document.getElementById('miniAssistant');

    if(
        !assistant.contains(e.target)
        &&
        !popup.contains(e.target)
    ){

        popup.style.display='none';

    }

});

/* ===================================================== */
/* HOVER CARD EFFECT */
/* ===================================================== */

document.querySelectorAll('.hover-card')
.forEach(card => {

    card.addEventListener('mouseenter',() => {

        card.style.transform =
        'translateY(-10px) scale(1.02)';

    });

    card.addEventListener('mouseleave',() => {

        card.style.transform =
        'translateY(0px) scale(1)';

    });

});


