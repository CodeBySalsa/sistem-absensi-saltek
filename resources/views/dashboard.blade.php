<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 leading-tight tracking-tighter uppercase">
                @if(Auth::user()->role == 'admin')
                    {{ __('Dashboard Admin PT Salttek Dumpang Jaya') }}
                @else
                    {{ __('Dashboard Karyawan PT Salttek Dumpang Jaya') }}
                @endif
            </h2>
            <div class="flex items-center gap-3">
                <div id="countdown-area" class="text-xs font-bold text-white bg-slate-900 px-4 py-2 rounded-xl shadow-lg border border-slate-700">
                    <span class="opacity-80 uppercase tracking-widest mr-1 text-[10px]">Batas Absen:</span>
                    <span id="timer" class="font-mono text-rose-500">--:--:--</span>
                </div>
                
                <div id="realtime-clock" class="text-sm font-black text-indigo-600 bg-white px-4 py-2 rounded-xl border border-slate-100 shadow-sm">
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
            min-height: 160px;
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
    </style>

    <div class="py-10 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

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

            {{-- 1. HERO BANNER --}}
            @if(Auth::user()->role !== 'admin')
                <div class="mb-8 hero-premium rounded-[2rem] p-8 text-white relative overflow-hidden animate-fade-in">
                    <div class="relative z-10 flex flex-col lg:flex-row md:items-center justify-between gap-6">
                        <div class="space-y-4">
                            <div class="inline-flex items-center gap-2 bg-white/10 px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-[0.15em] border border-white/10 backdrop-blur-md">
                                <span class="flex h-1.5 w-1.5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                                </span>
                                Presence System • PT Salttek Dumpang Jaya
                            </div>
                            <h1 class="text-3xl md:text-4xl font-black tracking-tight leading-none uppercase italic">HELLO, <span class="text-indigo-300">{{ Auth::user()->name }}!</span></h1>
                            <div class="flex gap-3">
                                <div class="glass-stat px-4 py-2 rounded-2xl flex flex-col items-center min-w-[70px]">
                                    <span class="text-[10px] font-bold text-indigo-200 uppercase">Hadir</span>
                                    <span class="text-lg font-black leading-none mt-1">{{ $totalHadir ?? 0 }}</span>
                                </div>
                                <div class="glass-stat px-4 py-2 rounded-2xl flex flex-col items-center min-w-[70px]">
                                    <span class="text-[10px] font-bold text-indigo-200 uppercase">Izin</span>
                                    <span class="text-lg font-black leading-none mt-1">{{ $ringkasanStatistik->total_izin ?? 0 }}</span>
                                </div>
                                <div class="glass-stat px-4 py-2 rounded-2xl flex flex-col items-center min-w-[70px]">
                                    <span class="text-[10px] font-bold text-indigo-200 uppercase">Sakit</span>
                                    <span class="text-lg font-black leading-none mt-1">{{ $ringkasanStatistik->total_sakit ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="glass-card p-5 rounded-[1.5rem] flex items-center gap-5 min-w-[260px] shadow-xl border-white/20">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl shadow-inner
                                @if($isMinggu) bg-slate-100 @elseif($cekAbsensi && $cekAbsensi->status == 'Terlambat') bg-rose-100 @elseif($cekAbsensi) bg-emerald-50 @else bg-amber-50 @endif">
                                @if($isMinggu) ☕ @elseif($cekAbsensi && $cekAbsensi->status == 'Terlambat') ⏰ @elseif($cekAbsensi && $cekAbsensi->jam_keluar) 🏁 @elseif($cekAbsensi) ✅ @else ⏳ @endif
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5 italic">Real-time Status</p>
                                <h4 class="text-md font-black {{ ($isMinggu) ? 'text-slate-500' : (($cekAbsensi && $cekAbsensi->status == 'Terlambat') ? 'text-rose-600' : 'text-slate-800') }} uppercase leading-none italic">
                                    @if($isMinggu) HARI LIBUR @elseif($cekAbsensi) {{ $cekAbsensi->status }} @else Belum Presensi @endif
                                </h4>
                                <p class="text-[10px] text-indigo-600 font-bold mt-1.5">
                                     @if($isMinggu) Nikmati waktu istirahat @elseif($cekAbsensi) Log: {{ \Carbon\Carbon::parse($cekAbsensi->jam_masuk)->format('H:i') }} WIB @else Silakan Absensi @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- 3. ADMIN PANEL --}}
            @if(Auth::user()->role == 'admin')
                <div class="mb-10 bg-slate-900 rounded-[3rem] p-1 shadow-2xl relative overflow-hidden group">
                    <div class="relative z-10 bg-slate-900 rounded-[2.9rem] p-8 md:p-10">
                        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-8">
                            <div class="space-y-2">
                                <h3 class="text-[11px] font-black tracking-[0.3em] uppercase text-indigo-400/80 italic">Control Center PT Salttek</h3>
                                <h1 class="text-4xl font-black tracking-tighter text-white uppercase italic">HALO, <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">{{ Auth::user()->name }}</span></h1>
                                <div class="flex flex-wrap gap-3 mt-6">
                                    <a href="{{ route('karyawan.index') }}" class="px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-lg flex items-center gap-2"><span>👥</span> DATA KARYAWAN</a>
                                    <a href="{{ route('karyawan.create') }}" class="px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-lg flex items-center gap-2"><span>➕</span> TAMBAH KARYAWAN</a>
                                </div>
                            </div>
                            <div class="w-full lg:w-auto grid grid-cols-2 sm:grid-cols-4 gap-4 lg:gap-6">
                                <div class="bg-slate-800/40 border border-white/5 p-6 rounded-[2rem] flex flex-col items-center text-center"><h3 class="text-3xl font-black text-white tracking-tighter">{{ $totalKaryawan ?? 0 }}</h3><p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">ANGGOTA</p></div>
                                <div class="bg-emerald-500/10 border border-emerald-500/20 p-6 rounded-[2rem] flex flex-col items-center text-center"><h3 class="text-3xl font-black text-emerald-400 tracking-tighter">{{ $hadirHariIni ?? 0 }}</h3><p class="text-[10px] font-bold text-emerald-500/70 uppercase tracking-widest mt-1">HADIR</p></div>
                                <div class="bg-amber-500/10 border border-amber-500/20 p-6 rounded-[2rem] flex flex-col items-center text-center"><h3 class="text-3xl font-black text-amber-400 tracking-tighter">{{ $totalIzin ?? 0 }}</h3><p class="text-[10px] font-bold text-amber-500/70 uppercase tracking-widest mt-1">IZIN</p></div>
                                <div class="bg-rose-500/10 border border-rose-500/20 p-6 rounded-[2rem] flex flex-col items-center text-center"><h3 class="text-3xl font-black text-rose-400 tracking-tighter">{{ $totalSakit ?? 0 }}</h3><p class="text-[10px] font-bold text-rose-500/70 uppercase tracking-widest mt-1">SAKIT</p></div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- 4. USER CARDS --}}
            @if(Auth::user()->role !== 'admin')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    {{-- Pencapaian --}}
                    <div class="bg-indigo-50/50 rounded-[2.5rem] p-8 shadow-xl border border-indigo-100 relative group overflow-hidden">
                        <div class="flex items-center justify-between relative z-10">
                            <div>
                                <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-2">Pencapaian Anda</p>
                                <h3 class="text-5xl font-black text-slate-800 leading-none">{{ $totalHadir ?? 0 }}</h3>
                                <p class="text-sm font-bold text-indigo-500 mt-2 italic">Hari Kerja</p>
                            </div>
                            <div class="bg-white p-3 rounded-2xl shadow-sm group-hover:scale-110 transition-transform">
                                <img src="https://cdn-icons-png.flaticon.com/512/2838/2838779.png" class="w-10 h-10" alt="calendar">
                            </div>
                        </div>
                    </div>

                    {{-- Info Kehadiran / Weekend Off --}}
                    <div class="bg-amber-50/50 rounded-[2.5rem] p-8 shadow-xl border border-amber-100 relative overflow-hidden group">
                        @if($isMinggu)
                            <div class="flex flex-col items-center justify-center py-6 text-center animate-fade-in">
                                <div class="w-16 h-16 bg-white text-indigo-500 rounded-2xl flex items-center justify-center text-3xl mb-3 shadow-sm border border-indigo-100">🏡</div>
                                <h4 class="text-[11px] font-black text-slate-800 uppercase tracking-widest leading-tight">Weekend Off<br><span class="text-indigo-500 italic">Selamat Beristirahat!</span></h4>
                            </div>
                        @else
                            <p class="text-[10px] font-black text-amber-500 uppercase tracking-widest mb-4 text-center">Info Kehadiran</p>
                            @if(!$cekAbsensi)
                                <form id="formIzinSakit" action="{{ route('absensi.izinSakit') }}" method="POST" class="space-y-3">
                                    @csrf
                                    <input type="hidden" name="status" id="status_input">
                                    <div class="relative"><input type="text" name="keterangan" id="keterangan_input" placeholder="Tulis alasan singkat..." required class="w-full text-xs font-bold border-none bg-white rounded-2xl py-4 px-5 focus:ring-2 focus:ring-amber-400 transition-all shadow-inner"></div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <button type="button" onclick="konfirmasiStatus('Izin')" class="bg-amber-400 hover:bg-amber-500 text-white font-black py-4 rounded-2xl text-[10px] uppercase shadow-lg shadow-amber-200">✋ Izin</button>
                                        <button type="button" onclick="konfirmasiStatus('Sakit')" class="bg-rose-500 hover:bg-rose-600 text-white font-black py-4 rounded-2xl text-[10px] uppercase shadow-lg shadow-rose-200">🤒 Sakit</button>
                                    </div>
                                </form>
                            @else
                                <div class="flex flex-col items-center justify-center py-2 text-center animate-fade-in">
                                    @if($cekAbsensi->status == 'Izin')
                                        <div class="w-16 h-16 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center text-3xl mb-3 shadow-sm border border-amber-200">✋</div>
                                        <h4 class="text-[11px] font-black text-amber-700 uppercase leading-tight">Status: IZIN<br><span class="text-[9px] font-bold opacity-70 normal-case italic">"{{ $cekAbsensi->keterangan }}"</span></h4>
                                    @elseif($cekAbsensi->status == 'Sakit')
                                        <div class="w-16 h-16 bg-rose-100 text-rose-600 rounded-2xl flex items-center justify-center text-3xl mb-3 shadow-sm border border-rose-200">🤒</div>
                                        <h4 class="text-[11px] font-black text-rose-700 uppercase leading-tight">Status: SAKIT<br><span class="text-[9px] font-bold opacity-70 normal-case italic">"{{ $cekAbsensi->keterangan }}"</span></h4>
                                    @else
                                        <div class="w-16 h-16 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center text-3xl mb-3 shadow-sm border border-emerald-100">✨</div>
                                        <h4 class="text-[11px] font-black text-slate-800 uppercase tracking-widest leading-tight">Data Aman<br><span class="text-emerald-500 italic">Tercatat Hari Ini</span></h4>
                                    @endif
                                </div>
                            @endif
                        @endif
                    </div>

                    {{-- Card Tombol Utama (Nonaktif di Hari Minggu) --}}
                    @if($isMinggu)
                        <div class="bg-slate-100 rounded-[2.5rem] p-6 flex flex-col items-center justify-center border border-slate-200 h-full relative overflow-hidden grayscale">
                            <div class="w-20 h-20 bg-white rounded-3xl flex items-center justify-center text-4xl mb-4 shadow-sm border border-slate-100">🧊</div>
                            <h4 class="font-black text-slate-400 uppercase tracking-widest text-xs italic text-center">SISTEM DINONAKTIFKAN</h4>
                            <p class="text-[8px] text-slate-400 mt-2 font-bold uppercase tracking-tighter">Kembali lagi hari Senin</p>
                        </div>
                    @else
                        @php $jamSekarang = \Carbon\Carbon::now('Asia/Jakarta')->hour; @endphp
                        @if(!$cekAbsensi)
                            <div class="relative group cursor-pointer" onclick="handleAbsensi()">
                                <div class="absolute -inset-1 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-[2.5rem] blur opacity-25 group-hover:opacity-40 transition duration-500"></div>
                                <div class="relative bg-white h-full rounded-[2.5rem] p-6 flex flex-col items-center justify-center border border-slate-100 shadow-xl">
                                    <div class="w-20 h-20 bg-indigo-50 rounded-3xl flex items-center justify-center text-4xl mb-4 group-hover:scale-110 transition-all">🚀</div>
                                    <h4 class="font-black text-slate-800 uppercase tracking-widest text-xs italic">Klik Untuk Absen</h4>
                                </div>
                            </div>
                        @elseif(!$cekAbsensi->jam_keluar && in_array($cekAbsensi->status, ['Hadir', 'Terlambat']))
                            <div class="relative group cursor-pointer" onclick="handlePulang({{ $jamSekarang }})">
                                <div class="absolute -inset-1 bg-gradient-to-r from-rose-600 to-orange-600 rounded-[2.5rem] blur opacity-25 group-hover:opacity-40 transition duration-500"></div>
                                <div class="relative bg-white h-full rounded-[2.5rem] p-6 flex flex-col items-center justify-center border border-slate-100 shadow-lg shadow-rose-100 transition-all {{ $jamSekarang < 17 ? 'opacity-50 grayscale' : '' }}">
                                    <div class="w-20 h-20 bg-rose-50 rounded-3xl flex items-center justify-center text-4xl mb-4 group-hover:scale-110 transition-all">🏠</div>
                                    <h4 class="font-black text-slate-800 uppercase tracking-widest text-xs italic">
                                        {{ $jamSekarang < 17 ? 'BELUM WAKTUNYA PULANG' : 'KLIK UNTUK PULANG' }}
                                    </h4>
                                    @if($jamSekarang < 17)
                                        <p class="text-[9px] text-rose-500 font-bold mt-2 uppercase italic tracking-tighter">Tersedia pukul 17:00</p>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="bg-emerald-50 rounded-[2.5rem] p-6 flex flex-col items-center justify-center border border-emerald-100 h-full relative overflow-hidden animate-fade-in">
                                <div class="w-20 h-20 bg-white rounded-3xl flex items-center justify-center text-4xl mb-4 shadow-sm border border-emerald-100">🌟</div>
                                <h4 class="font-black text-emerald-600 uppercase tracking-widest text-xs italic">TUGAS SELESAI</h4>
                            </div>
                        @endif
                    @endif
                </div>
            @endif

            {{-- 5. TABEL UTAMA --}}
            <div class="mt-8 bg-white rounded-[2.5rem] shadow-xl border border-slate-50 overflow-hidden animate-fade-in">
                <div class="p-8 border-b border-slate-50 bg-indigo-50/30 flex items-center gap-3">
                    <div class="w-10 h-10 bg-indigo-600 text-white rounded-xl flex items-center justify-center shadow-lg">📋</div>
                    <h3 class="font-black text-slate-800 uppercase tracking-tight text-lg">
                        @if(Auth::user()->role == 'admin') LOG AKTIVITAS HARI INI @else LOG AKTIVITAS MINGGU INI @endif
                    </h3>
                </div>

                <div class="overflow-x-auto p-6">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                <th class="pb-4 px-4">{{ Auth::user()->role == 'admin' ? 'Nama Karyawan' : 'Hari / Tanggal' }}</th>
                                <th class="pb-4 text-center">Waktu Masuk</th>
                                <th class="pb-4 text-center">Status Kehadiran</th>
                                @if(Auth::user()->role == 'admin')
                                    <th class="pb-4 text-center">Keterangan</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="text-xs font-bold text-slate-600">
                            @if(Auth::user()->role == 'admin')
                                @forelse($absensiHariIni as $absen)
                                <tr class="border-b border-slate-50 hover:bg-indigo-50/20 transition-all">
                                    <td class="py-5 px-4 font-black text-slate-800 uppercase tracking-tighter italic">{{ $absen->user->name }}</td>
                                    <td class="py-5 text-center font-mono text-indigo-600 italic">{{ $absen->jam_masuk ?? '--:--' }}</td>
                                    <td class="py-5 text-center">
                                        <span class="px-4 py-1.5 rounded-full uppercase text-[10px] font-black tracking-tighter
                                            {{ in_array($absen->status, ['Terlambat', 'Sakit']) ? 'bg-rose-100 text-rose-600 border border-rose-200' : ($absen->status == 'Hadir' ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600') }}">
                                            {{ $absen->status }}
                                        </span>
                                    </td>
                                    <td class="py-5 text-center italic text-slate-400 text-xs">
                                        {{ $absen->keterangan ?? '-' }}
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="py-12 text-center text-slate-400 italic">Belum ada aktivitas hari ini.</td></tr>
                                @endforelse
                            @else
                                @forelse($absensis as $log)
                                    <tr class="border-b border-slate-50 hover:bg-indigo-50/20 transition-all">
                                        <td class="py-5 px-4 font-black text-slate-800 uppercase italic">{{ \Carbon\Carbon::parse($log->tanggal)->translatedFormat('l, d M Y') }}</td>
                                        <td class="py-5 text-center font-mono text-indigo-600 italic">{{ $log->jam_masuk ?? '--:--' }}</td>
                                        <td class="py-5 text-center">
                                            <span class="px-4 py-1.5 rounded-full uppercase text-[10px] font-black tracking-tighter
                                                {{ $log->status == 'Terlambat' ? 'bg-rose-100 text-rose-600' : ($log->status == 'Hadir' ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600') }}">
                                                {{ $log->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="py-12 text-center text-slate-400 italic">Belum ada aktivitas minggu ini.</td></tr>
                                @endforelse
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 6. REKAPITULASI BULANAN (ADMIN) --}}
            @if(Auth::user()->role == 'admin')
                <div class="mt-8 bg-white rounded-[2.5rem] shadow-xl border border-slate-50 overflow-hidden mb-10 animate-fade-in">
                    <div class="p-8 border-b border-slate-50 bg-slate-50/30 flex justify-between items-center">
                        <h3 class="font-black text-slate-800 uppercase tracking-tight text-lg">REKAPITULASI ABSENSI - {{ strtoupper($namaBulan) }} {{ date('Y') }}</h3>
                    </div>
                    <div class="overflow-x-auto p-6">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                    <th class="pb-4">Nama Karyawan</th>
                                    <th class="pb-4 text-center text-emerald-600">Hadir</th>
                                    <th class="pb-4 text-center text-blue-600">Izin</th>
                                    <th class="pb-4 text-center text-rose-600">Sakit</th>
                                </tr>
                            </thead>
                            <tbody class="text-xs font-bold text-slate-600">
                                @foreach($rekapBulanan as $rekap)
                                <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                                    <td class="py-5 font-black text-slate-800 uppercase">{{ $rekap->nama_lengkap }}</td>
                                    <td class="py-5 text-center">{{ $rekap->total_hadir }}</td>
                                    <td class="py-5 text-center">{{ $rekap->total_izin }}</td>
                                    <td class="py-5 text-center">{{ $rekap->total_sakit }}</td>
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
            if (typeof map !== 'undefined' && currentLat && currentLng) {
                map.setView([currentLat, currentLng], 17, { animate: true });
            }
        }
    </script>
</x-app-layout>