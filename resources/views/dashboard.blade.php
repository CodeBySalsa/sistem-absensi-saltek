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
                        
                        {{-- REQUEST 1A: CARD HADIR, IZIN, SAKIT BISA NGEZOOM JIKA DISENTUH/DIKLIK --}}
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
                    
                    {{-- REQUEST 1B: CARD STATUS REAL TIME BISA NGEZOOM JIKA DISENTUH/DIKLIK --}}
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
                    
                    {{-- REQUEST 2: CARD PENCAPAIAN HARI INI (IKON KALENDER) BISA NGEZOOM JIKA DIKLIK/HOVER --}}
                    <div class="bg-indigo-50/50 rounded-[1.2rem] md:rounded-[2.5rem] p-3 md:p-8 shadow-xl border border-indigo-100 flex items-center justify-between gap-1 force-no-p cursor-pointer transition-all duration-300 hover:scale-105 active:scale-95 group">
                        <div class="min-w-0">
                            <p class="text-[6px] md:text-[10px] font-black text-indigo-400 uppercase tracking-wider md:tracking-widest leading-none">PENCAPAIAN ANDA</p>
                            <h3 class="text-sm md:text-5xl font-black text-slate-800 leading-none mt-1 md:mt-2">{{ $totalHadir ?? 0 }}</h3>
                            <p class="text-[5px] md:text-sm font-bold text-indigo-500 mt-1 italic mobile-sub leading-none">Hari Kerja</p>
                        </div>
                        <div class="bg-white p-1 md:p-3 rounded-lg md:rounded-2xl shadow-sm shrink-0 transition-transform duration-300 group-hover:rotate-3">
                            <img src="https://cdn-icons-png.flaticon.com/512/2838/2838779.png" class="w-5 h-5 md:w-12 md:h-12 force-icon-size" alt="calendar">
                        </div>
                    </div>

                    {{-- REQUEST 3: CARD SEBELAH PENCAPAIAN (INFO KEHADIRAN / GAMBAR RUMAH WEEKEND OFF) BISA NGEZOOM JIKA DIKLIK/HOVER --}}
                    <div class="bg-amber-50/50 rounded-[1.2rem] md:rounded-[2.5rem] p-3 md:p-8 shadow-xl border border-amber-100 flex flex-col justify-center items-center text-center force-no-p cursor-pointer transition-all duration-300 hover:scale-105 active:scale-95 group">
                        @if($isMinggu)
                            <div class="w-5 h-5 md:w-16 md:h-16 bg-white text-indigo-500 rounded-xl flex items-center justify-center text-xs md:text-3xl mb-1 shadow-sm transition-transform duration-300 group-hover:scale-110">🏡</div>
                            <h4 class="text-[5px] md:text-[11px] font-black text-slate-800 uppercase tracking-tight leading-tight">Weekend Off</h4>
                        @else
                            <p class="text-[5px] md:text-[10px] font-black text-amber-500 uppercase tracking-wider md:tracking-widest leading-none mb-1 md:mb-3">INFO KEHADIRAN</p>
                            @if(!$cekAbsensi)
                                <form id="formIzinSakit" action="{{ route('absensi.izinSakit') }}" method="POST" class="w-full space-y-1" onclick="event.stopPropagation();">
                                    @csrf
                                    <input type="hidden" name="status" id="status_input">
                                    <input type="text" name="keterangan" id="keterangan_input" placeholder="Alasan..." required class="w-full text-[5px] md:text-xs font-bold border-none bg-white rounded-md md:rounded-2xl py-0.5 px-1 shadow-inner text-center">
                                    <div class="grid grid-cols-2 gap-1">
                                        <button type="button" onclick="konfirmasiStatus('Izin')" class="bg-amber-400 text-white font-black py-0.5 rounded text-[5px] md:text-[10px] uppercase hover:bg-amber-500 transition-colors">Izin</button>
                                        <button type="button" onclick="konfirmasiStatus('Sakit')" class="bg-rose-500 text-white font-black py-0.5 rounded text-[5px] md:text-[10px] uppercase hover:bg-rose-600 transition-colors">Sakit</button>
                                    </div>
                                </form>
                            @else
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-5 h-5 md:w-12 md:h-12 bg-white rounded-md md:rounded-xl flex items-center justify-center text-xs md:text-2xl shadow-sm mb-1 transition-transform duration-300 group-hover:scale-110">
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
                    <div class="rounded-[1.2rem] md:rounded-[2.5rem] shadow-xl border border-slate-100 overflow-hidden h-full transition-all duration-300 hover:scale-105 active:scale-95">
                        @if($isMinggu)
                            <div class="bg-slate-100 p-2 md:p-6 flex flex-col items-center justify-center h-full text-center grayscale force-no-p">
                                <h4 class="font-black text-slate-400 uppercase text-[5px] md:text-xs">SYSTEM OFF</h4>
                            </div>
                        @else
                            @php $jamSekarang = \Carbon\Carbon::now('Asia/Jakarta')->hour; @endphp
                            @if(!$cekAbsensi)
                                <div class="bg-white h-full p-2 md:p-6 flex flex-col items-center justify-center cursor-pointer text-center force-no-p group" onclick="handleAbsensi()">
                                    <div class="text-lg md:text-4xl mb-1 transition-transform duration-300 group-hover:translate-y-[-4px]">🚀</div>
                                    <h4 class="font-black text-indigo-600 uppercase text-[6px] md:text-xs tracking-wider">Presensi</h4>
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
                                {{-- PERUBAHAN DI SINI: Ditambahkan class hover zoom untuk Admin --}}
                                <tr class="hover:bg-indigo-50/80 hover:scale-[1.015] md:hover:scale-[1.02] transition-all duration-300 ease-in-out cursor-pointer origin-center shadow-sm hover:shadow-md">
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
                                {{-- PERUBAHAN DI SINI: Ditambahkan class hover zoom untuk Karyawan --}}
                                <tr class="hover:bg-indigo-50/80 hover:scale-[1.015] md:hover:scale-[1.02] transition-all duration-300 ease-in-out cursor-pointer origin-center shadow-sm hover:shadow-md">
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

            {{-- 6. REKAPITULASI BULANAN --}}
            @if(Auth::user()->role == 'admin')
                <div class="mt-8 bg-white rounded-xl md:rounded-[2.5rem] shadow-xl border border-slate-100 overflow-hidden mb-10 animate-fade-in w-full">
                    <div class="p-4 md:p-8 border-b border-slate-100 bg-indigo-50/30 flex items-center gap-2 md:gap-3">
                        <div class="w-6 h-6 md:w-10 md:h-10 bg-indigo-950 text-white rounded-lg flex items-center justify-center shadow-lg text-xs md:text-base">📊</div>
                        <h3 class="font-black text-slate-800 uppercase tracking-tight text-xs md:text-lg">
                            REKAPITULASI ABSENSI KARYAWAN - {{ strtoupper($namaBulan ?? '') }} {{ date('Y') }}
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
                                @foreach($rekapBulanan ?? [] as $rekap)
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

        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371e3;
            const dLat = (lat2-lat1) * Math.PI/180; const dLon = (lon2-lon1) * Math.PI/180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(lat1*Math.PI/180) * Math.cos(lat2*Math.PI/180) * Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        }

        function handleAbsensi() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    currentLat = position.coords.latitude;
                    currentLng = position.coords.longitude;
                    
                    const jarak = calculateDistance(currentLat, currentLng, KANTOR_LAT, KANTOR_LNG);
                    
                    if (jarak > MAX_RADIUS) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Di Luar Jangkauan!',
                            text: `Jarak Anda ${Math.round(jarak)} meter dari kantor. Maksimal radius absensi adalah ${MAX_RADIUS} meter!`,
                            confirmButtonColor: '#4338ca'
                        });
                        return;
                    }
                    
                    document.getElementById('lat').value = currentLat;
                    document.getElementById('lng').value = currentLng;
                    
                    // Buka Modal & Tampilkan Peta
                    const modal = document.getElementById('absensiModal');
                    const content = document.getElementById('modalContent');
                    modal.classList.remove('hidden');
                    setTimeout(() => { content.classList.remove('translate-y-full'); }, 50);
                    
                    // Inisialisasi Peta Leaflet
                    setTimeout(() => {
                        if (!map) {
                            map = L.map('map-preview').setView([currentLat, currentLng], 18);
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                            L.circle([KANTOR_LAT, KANTOR_LNG], { radius: MAX_RADIUS, color: '#4338ca', fillColor: '#4338ca', fillOpacity: 0.15 }).addTo(map);
                            L.marker([KANTOR_LAT, KANTOR_LNG]).addTo(map).bindPopup('Kantor PT Salttek').openPopup();
                            L.marker([currentLat, currentLng]).addTo(map).bindPopup('Lokasi Anda');
                        } else {
                            map.setView([currentLat, currentLng], 18);
                        }
                    }, 200);
                    
                }, function() {
                    Swal.fire({ icon: 'error', title: 'GPS Gagal', text: 'Gagal mengambil lokasi. Pastikan izin lokasi browser Anda aktif.' });
                });
            } else {
                Swal.fire({ icon: 'error', title: 'Tidak Didukung', text: 'Browser tidak mendukung pelacakan lokasi.' });
            }
        }

        function closeAbsensiModal() {
            const content = document.getElementById('modalContent');
            content.classList.add('translate-y-full');
            setTimeout(() => { document.getElementById('absensiModal').class List.add('hidden'); }, 400);
        }

        function manualRecenter() {
            if(map && currentLat && currentLng) map.setView([currentLat, currentLng], 18);
        }

        function konfirmasiStatus(status) {
            const ketInput = document.getElementById('keterangan_input').value;
            if(!ketInput.trim()) {
                Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Silakan isi kolom keterangan/alasan terlebih dahulu.' });
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
        document.addEventListener("DOMContentLoaded", function() {
            const robotText = document.getElementById("robot-text");
            const robotBubble = document.getElementById("robot-bubble");
            const eyeLeft = document.getElementById("eye-left");
            const eyeRight = document.getElementById("eye-right");
            const antenna = document.getElementById("robot-antenna-glow");

            // Ambil data status dari Laravel Blade
            const isMinggu = @json($isMinggu);
            const cekAbsensi = @json($cekAbsensi);
            const jamSekarang = new Date().getHours();

            let teksAwal = "";
            let teksLanjutan = "";
            let ekspresiHalamanUtama = "normal"; // Default ekspresi

            // 🤖 LOGIKA DETEKSI STATUS & PENENTUAN EKSPRESI WIWAJAHAN
            if (isMinggu) {
                teksAwal = "Yeeayy, hari Minggu waktunya libur! 🏡☕";
                teksLanjutan = "Selamat beristirahat yaa! Jangan mikirin kerjaan dulu~ ✨";
                ekspresiHalamanUtama = "happy";
            } else if (!cekAbsensi) {
                // BELUM ABSEN MASUK
                teksAwal = "Haloo! Jangan lupa absen masuk hari ini yaa! 🚀";
                teksLanjutan = "Ketik kehadiranmu sekarang agar tidak terlambat! 😉";
                ekspresiHalamanUtama = "normal";
            } else if (cekAbsensi.status === 'Izin') {
                // JIKA IZIN (EKSPRESI SEDIH)
                teksAwal = "Surat izinmu sudah tersimpan di sistem! ✋";
                teksLanjutan = "Semoga urusanmu hari ini berjalan lancar yaa! ✨";
                ekspresiHalamanUtama = "sad";
            } else if (cekAbsensi.status === 'Sakit') {
                // JIKA SAKIT (EKSPRESI LEMAS/SEDIH)
                teksAwal = "Gws yaa, kamu lagi kurang sehat... 🤒";
                teksLanjutan = "Istirahat yang cukup dan lekas sembuh! Jangan lupa minum obat! 💊";
                ekspresiHalamanUtama = "sad";
            } else if (cekAbsensi.status === 'Hadir' || cekAbsensi.status === 'Terlambat') {
                
                if (!cekAbsensi.jam_keluar) {
                    // SUDAH ABSEN MASUK, BELUM ABSEN PULANG
                    if (jamSekarang >= 17) {
                        teksAwal = "Hei! Sudah jam pulang nih, jangan lupa absen pulang yaa! 🏡";
                        teksLanjutan = "Yuk klik tombol pulang, rapi-rapi barangmu! 🏁";
                        ekspresiHalamanUtama = "glance"; // Melirik ngingetin pulang
                    } else {
                        teksAwal = "Hore! Absen masukmu berhasil dicatat tadi! ✅";
                        teksLanjutan = "Semangat bekerja hari ini yaa! Kerja kerasmu luar biasa! 💪";
                        ekspresiHalamanUtama = "happy"; // Bahagia karena udah absen aman
                    }
                } else {
                    // SUDAH ABSEN PULANG
                    teksAwal = "Presensi pulang berhasil! Tugas selesai hari ini! 🏁";
                    teksLanjutan = "Selamat beristirahat di rumah, hati-hati di jalan pulang! 🛵🌟";
                    ekspresiHalamanUtama = "happy";
                }
            }

            // 🔥 BERIKAN EKSPRESI PADA MATA & ANTENA SEJAK AWAL HALAMAN DIMUAT
            terapkanEkspresi(ekspresiHalamanUtama);
            robotText.innerHTML = teksAwal;

            // Fungsi utility pengubah class ekspresi CSS
            function terapkanEkspresi(ekspresi) {
                // Reset class mata terdahulu
                eyeLeft.className = "robot-eye transition-all duration-500";
                eyeRight.className = "robot-eye transition-all duration-500";
                
                if (ekspresi === "sad") {
                    eyeLeft.classList.add("expr-sad");
                    eyeRight.classList.add("expr-sad");
                    // Ubah warna lampu antena jadi merah redup sendu
                    antenna.style.backgroundColor = "#ef4444";
                    antenna.style.boxShadow = "0 0 8px #ef4444";
                } else if (ekspresi === "happy") {
                    eyeLeft.classList.add("expr-happy");
                    eyeRight.classList.add("expr-happy");
                    antenna.style.backgroundColor = "#10b981";
                    antenna.style.boxShadow = "0 0 8px #10b981";
                } else if (ekspresi === "glance") {
                    eyeLeft.classList.add("expr-glance");
                    eyeRight.classList.add("expr-glance");
                    antenna.style.backgroundColor = "#f59e0b";
                    antenna.style.boxShadow = "0 0 8px #f59e0b";
                } else {
                    eyeLeft.classList.add("expr-normal");
                    eyeRight.classList.add("expr-normal");
                    antenna.style.backgroundColor = "#22d3ee";
                    antenna.style.boxShadow = "0 0 8px #22d3ee";
                }
            }

            // 🔄 ANIMASI PERUBAHAN BALON TEKS (Setelah 5 Detik)
            setTimeout(() => {
                if(teksLanjutan !== "") {
                    robotBubble.classList.remove("scale-100", "opacity-100");
                    robotBubble.classList.add("scale-75", "opacity-0");

                    setTimeout(() => {
                        robotText.innerHTML = teksLanjutan;
                        robotBubble.classList.remove("scale-75", "opacity-0");
                        robotBubble.classList.add("scale-100", "opacity-100");
                    }, 400);
                }
            }, 5000);
        });

        // Efek ketawa saat robot disentuh
        function triggerRobotGiggle() {
            const body = document.querySelector('.robot-body');
            body.classList.add('robot-giggle');
            setTimeout(() => { body.classList.remove('robot-giggle'); }, 500);
        }
    </script>
    @endif
</x-app-layout>