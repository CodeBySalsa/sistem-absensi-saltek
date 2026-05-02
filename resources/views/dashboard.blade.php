<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 leading-tight tracking-tighter">
                {{ __('Dashboard Monitoring - PT Saltek') }}
            </h2>
            <div class="flex items-center gap-3">
                {{-- Countdown Area (Warna Merah Sesuai Gambar) --}}
                <div id="countdown-area" class="text-xs font-bold text-white bg-slate-900 px-4 py-2 rounded-xl shadow-lg border border-slate-700">
                    <span class="opacity-80 uppercase tracking-widest mr-1 text-[10px]">Batas Absen:</span>
                    <span id="timer" class="font-mono text-rose-500">--:--:--</span>
                </div>
                
                {{-- Realtime Clock (Biru Lembut) --}}
                <div id="realtime-clock" class="text-sm font-black text-indigo-600 bg-white px-4 py-2 rounded-xl border border-slate-100 shadow-sm">
                    {{-- Jam otomatis muncul di sini --}}
                </div>
            </div>
        </div>
    </x-slot>

    {{-- Library Pendukung --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <style>
        .swal2-popup { border-radius: 24px !important; }
        .custom-gradient { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); }
        #map { height: 200px; width: 100%; border-radius: 1.5rem; margin-top: 1rem; z-index: 1; }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>

    <div class="py-10 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- 1. HERO BANNER (Personal Portal) --}}
            @if(Auth::user()->role !== 'admin')
            <div class="mb-8 custom-gradient rounded-[2.5rem] p-10 text-white shadow-2xl shadow-indigo-200 relative overflow-hidden transition-all hover:scale-[1.01]">
                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-[10px] font-bold uppercase tracking-widest">Personal Portal</span>
                        <span class="w-2 h-2 bg-white rounded-full animate-ping"></span>
                    </div>
                    <h1 class="text-5xl font-black uppercase tracking-tighter">Halo, {{ Auth::user()->name }}!</h1>
                    <p class="text-indigo-100 mt-4 font-medium text-lg max-w-xl leading-relaxed">Selamat datang di Sistem Absensi Digital KKN PT Saltek. Jangan lupa lakukan presensi tepat waktu ya!</p>
                </div>
                <div class="absolute -right-20 -top-20 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
            </div>
            @endif

            {{-- 2. STATUS INDICATOR --}}
            <div class="bg-white p-8 rounded-[2rem] shadow-xl shadow-slate-200/50 border border-white mb-6">
                <div class="flex items-center gap-6">
                    <div class="relative">
                        @if($cekAbsensi && ($cekAbsensi->status == 'Hadir' || $cekAbsensi->status == 'Selesai'))
                            <div class="w-16 h-16 bg-emerald-500 rounded-2xl shadow-lg shadow-emerald-200 flex items-center justify-center text-2xl text-white">✅</div>
                        @elseif(now()->format('H:i') > '08:30')
                            <div class="w-16 h-16 bg-rose-500 rounded-2xl shadow-lg shadow-rose-200 flex items-center justify-center text-2xl text-white animate-pulse">❌</div>
                        @else
                            <div class="w-16 h-16 bg-amber-400 rounded-2xl shadow-lg shadow-amber-200 flex items-center justify-center text-2xl text-white animate-bounce">⏳</div>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-1 italic">Status Real-time Anda</p>
                        <h3 class="text-2xl font-black text-slate-800 tracking-tight uppercase">
                            @if($cekAbsensi && ($cekAbsensi->status == 'Hadir' || $cekAbsensi->status == 'Selesai'))
                                Presensi Berhasil Dikirim
                            @elseif(now()->format('H:i') > '08:30')
                                Batas Waktu Terlewati (Terlambat)
                            @else
                                Menunggu Absensi Masuk
                            @endif
                        </h3>
                    </div>
                </div>
            </div>
            
            {{-- 3. ADMIN SPECIAL PANEL --}}
            @if(Auth::user()->role == 'admin')
            <div class="mb-8 bg-slate-900 rounded-[2.5rem] p-10 text-white shadow-2xl relative overflow-hidden">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-center">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-3 h-3 bg-emerald-400 rounded-full animate-pulse"></span>
                            <h3 class="text-[10px] font-black tracking-[0.2em] uppercase text-indigo-400">Administrator Access</h3>
                        </div>
                        <h1 class="text-4xl font-black tracking-tight uppercase">Halo, {{ Auth::user()->name }}!</h1>
                        <p class="text-slate-400 mt-2 font-medium">Monitoring kehadiran karyawan KKN PT Saltek.</p>
                    </div>
                    <div class="flex space-x-3 mt-6 md:mt-0">
                        <a href="{{ route('karyawan.index') }}" class="bg-indigo-600 text-white font-bold py-3 px-6 rounded-2xl hover:bg-indigo-700 transition-all text-xs uppercase">Data Karyawan</a>
                        <a href="{{ route('karyawan.create') }}" class="bg-white text-slate-900 font-bold py-3 px-6 rounded-2xl hover:bg-slate-100 transition-all text-xs uppercase">+ Tambah</a>
                    </div>
                </div>
            </div>

            {{-- 4. ADMIN STATS CARDS --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex items-center gap-5">
                    <div class="h-14 w-14 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 text-xl font-bold">👥</div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Anggota</p>
                        <h3 class="text-3xl font-black text-slate-800">{{ $totalKaryawan ?? 0 }}</h3>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex items-center gap-5">
                    <div class="h-14 w-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 text-xl">✅</div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Hadir Hari Ini</p>
                        <h3 class="text-3xl font-black text-slate-800">{{ $hadirHariIni ?? 0 }}</h3>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex items-center gap-5">
                    <div class="h-14 w-14 bg-rose-50 rounded-2xl flex items-center justify-center text-rose-600 text-xl">⚠️</div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Izin / Sakit</p>
                        <h3 class="text-3xl font-black text-slate-800">{{ $izinSakit ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            @endif

            {{-- 5. USER INTERACTIVE CARDS --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-[2rem] p-8 shadow-lg border-b-4 border-indigo-500 flex flex-col justify-between transition-transform hover:scale-[1.02]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Hadir Anda</p>
                            <h3 class="text-3xl font-black text-slate-800 mt-1">{{ $totalHadir ?? 0 }} Hari</h3>
                        </div>
                        <div class="bg-indigo-50 p-4 rounded-2xl text-indigo-500 text-2xl">📅</div>
                    </div>
                    <p class="text-[10px] text-indigo-400 mt-6 font-bold italic">* Terhitung masa KKN di PT Saltek</p>
                </div>

                <div class="bg-white rounded-[2rem] p-8 shadow-lg border-b-4 border-amber-500">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Form Izin / Sakit</p>
                    @if($cekAbsensi)
                        <div class="bg-slate-50 rounded-2xl p-4 text-center border border-slate-100">
                            <span class="text-2xl italic font-black text-amber-600 uppercase">{{ $cekAbsensi->status }}</span>
                        </div>
                    @else
                        <form id="formIzinSakit" action="{{ route('absensi.izinSakit') }}" method="POST" class="space-y-2">
                            @csrf
                            <input type="hidden" name="status" id="status_input">
                            <input type="text" name="keterangan" id="keterangan_input" placeholder="Alasan singkat..." required class="w-full text-xs border-slate-200 rounded-xl py-3 px-4 bg-slate-50 focus:ring-amber-500">
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" onclick="konfirmasiStatus('Izin')" class="bg-amber-500 text-white font-black py-3 rounded-xl text-[10px] uppercase shadow-md active:scale-95">✋ Izin</button>
                                <button type="button" onclick="konfirmasiStatus('Sakit')" class="bg-rose-500 text-white font-black py-3 rounded-xl text-[10px] uppercase shadow-md active:scale-95">🤒 Sakit</button>
                            </div>
                        </form>
                    @endif
                </div>

                <div class="relative group cursor-pointer" onclick="openAbsensiModal()">
                    <div class="absolute -inset-1 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-[2.2rem] blur opacity-25 group-hover:opacity-40 transition"></div>
                    <div class="relative bg-white h-full rounded-[2.2rem] p-8 flex flex-col items-center justify-center border border-slate-100">
                        <div class="w-16 h-16 bg-indigo-50 rounded-full flex items-center justify-center text-3xl mb-3">🚀</div>
                        <h4 class="font-black text-slate-800 uppercase tracking-widest text-xs">Panel Absensi Utama</h4>
                        <p class="text-[9px] text-slate-400 mt-2 uppercase font-bold italic">Buka untuk kirim lokasi</p>
                    </div>
                </div>
            </div>

            {{-- 6. ADMIN TABLES (VERSI LENGKAP) --}}
            @if(Auth::user()->role == 'admin')
            <div class="grid grid-cols-1 gap-8">
                {{-- Aktivitas Terbaru --}}
                <div class="bg-white rounded-[2rem] shadow-xl border border-slate-50 overflow-hidden">
                    <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
                        <h3 class="font-black text-slate-800 uppercase tracking-tight flex items-center gap-2 text-lg">
                            <span>⚡</span> Aktivitas Absensi Hari Ini
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b border-slate-100">
                                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Karyawan</th>
                                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Jam</th>
                                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($recentActivities as $activity)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-8 py-5 text-sm font-bold text-slate-700">{{ $activity->karyawan->nama_lengkap ?? 'Unknown' }}</td>
                                    <td class="px-8 py-5 text-sm font-mono text-slate-500">{{ $activity->jam_masuk }}</td>
                                    <td class="px-8 py-5">
                                        <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase {{ $activity->status == 'Hadir' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                            {{ $activity->status }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="px-8 py-10 text-center text-slate-400 italic">Belum ada data masuk.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Rekap Bulanan --}}
                <div class="bg-white rounded-[2rem] shadow-xl border border-slate-50 overflow-hidden mb-10">
                    <div class="p-8 border-b border-slate-50 bg-slate-50/30">
                        <h3 class="font-black text-slate-800 uppercase tracking-tight text-lg">📊 Rekapitulasi ({{ $namaBulan }})</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b border-slate-100">
                                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama</th>
                                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">H</th>
                                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">I</th>
                                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">S</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($rekapBulanan as $rekap)
                                <tr>
                                    <td class="px-8 py-5 text-sm font-bold text-slate-700">{{ $rekap->nama_lengkap }}</td>
                                    <td class="px-8 py-5 text-center font-mono font-bold text-emerald-600">{{ $rekap->total_hadir }}</td>
                                    <td class="px-8 py-5 text-center font-mono font-bold text-amber-600">{{ $rekap->total_izin }}</td>
                                    <td class="px-8 py-5 text-center font-mono font-bold text-rose-600">{{ $rekap->total_sakit }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="px-8 py-10 text-center text-slate-400 italic">Data kosong.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>

    {{-- MODAL AREA --}}
    <div id="absensiModal" class="fixed inset-0 z-[999] hidden">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeAbsensiModal()"></div>
        <div class="relative flex items-center justify-center min-h-screen p-4 pointer-events-none">
            <div id="modalContent" class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl overflow-hidden transform translate-y-full transition-transform duration-500 ease-out pointer-events-auto">
                <div class="p-6">
                    @include('absensi.panel-content')
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Copy semua script JavaScript dari kode awalmu (Timer, Clock, Modal Logic, KonfirmasiStatus) ke sini.
        // Pastikan tidak ada yang tertinggal.
    </script>
</x-app-layout>