<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 leading-tight tracking-tighter">
                {{ __('Dashboard Monitoring - PT Saltek') }}
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
        .custom-gradient { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); }
        .animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        #map-preview { height: 200px; width: 100%; border-radius: 1.5rem; z-index: 1; }
    </style>

    <div class="py-10 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Message --}}
            @if(session('success'))
                <script>
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", showConfirmButton: false, timer: 2000 });
                </script>
            @endif
            @if(session('error'))
                <script>
                    Swal.fire({ icon: 'error', title: 'Gagal!', text: "{{ session('error') }}", showConfirmButton: true });
                </script>
            @endif

            {{-- 1. HERO BANNER --}}
            @if(Auth::user()->role !== 'admin')
                <div class="mb-6 custom-gradient rounded-[2rem] p-7 text-white shadow-lg relative overflow-hidden transition-all duration-500">
                    <div class="relative z-10">
                        <h1 class="text-3xl font-black uppercase tracking-tighter italic">HALO, {{ Auth::user()->name }}!</h1>
                        <p class="text-white/90 mt-1 font-medium text-sm max-w-xl opacity-90">Selamat datang di Sistem Absensi Digital KKN PT Saltek.</p>
                    </div>
                </div>
            @endif

            {{-- 2. STATUS INDICATOR --}}
            <div class="bg-white p-8 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-white mb-6">
                <div class="flex items-center gap-6">
                    <div class="relative">
                        @if($cekAbsensi && $cekAbsensi->jam_keluar)
                            <div class="w-16 h-16 bg-blue-500 rounded-2xl shadow-lg flex items-center justify-center text-2xl text-white">🏁</div>
                        @elseif($cekAbsensi)
                            <div class="w-16 h-16 bg-emerald-500 rounded-2xl shadow-lg flex items-center justify-center text-2xl text-white">✅</div>
                        @else
                            <div class="w-16 h-16 bg-amber-400 rounded-2xl shadow-lg flex items-center justify-center text-2xl text-white animate-bounce">⏳</div>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-1 italic">Status Real-time Anda</p>
                        <h3 class="text-2xl font-black text-slate-800 tracking-tight uppercase">
                            @if($cekAbsensi) Sudah Absen ({{ $cekAbsensi->status }}) @else MENUNGGU ABSENSI MASUK @endif
                        </h3>
                    </div>
                </div>
            </div>
            
            {{-- 3. ADMIN PANEL --}}
            @if(Auth::user()->role == 'admin')
            <div class="mb-10 bg-slate-900 rounded-[3rem] p-1 shadow-2xl relative overflow-hidden group">
                <div class="relative z-10 bg-slate-900 rounded-[2.9rem] p-8 md:p-10">
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-8">
                        <div class="space-y-2">
                            <h3 class="text-[11px] font-black tracking-[0.3em] uppercase text-indigo-400/80">Control Center PT Saltek</h3>
                            <h1 class="text-4xl font-black tracking-tighter text-white uppercase italic">
                                HELLO, <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">{{ Auth::user()->name }}</span>!
                            </h1>
                            
                            <div class="flex flex-wrap gap-3 mt-6">
                                <a href="{{ route('karyawan.index') }}" class="px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-lg flex items-center gap-2">
                                    <span>👥</span> DATA KARYAWAN
                                </a>
                                <a href="{{ route('karyawan.create') }}" class="px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-lg flex items-center gap-2">
                                    <span>➕</span> TAMBAH KARYAWAN
                                </a>
                            </div>
                        </div>

                        <div class="w-full lg:w-auto grid grid-cols-1 sm:grid-cols-3 gap-4 lg:gap-6">
                            <div class="bg-slate-800/40 border border-white/5 p-6 rounded-[2rem] flex flex-col items-center text-center">
                                <h3 class="text-3xl font-black text-white tracking-tighter">{{ $totalKaryawan ?? 0 }}</h3>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">ANGGOTA</p>
                            </div>
                            <div class="bg-emerald-500/10 border border-emerald-500/20 p-6 rounded-[2rem] flex flex-col items-center text-center">
                                <h3 class="text-3xl font-black text-emerald-400 tracking-tighter">{{ $hadirHariIni ?? 0 }}</h3>
                                <p class="text-[10px] font-bold text-emerald-500/70 uppercase tracking-widest mt-1">HADIR HARI INI</p>
                            </div>
                            <div class="bg-rose-500/10 border border-rose-500/20 p-6 rounded-[2rem] flex flex-col items-center text-center">
                                <h3 class="text-3xl font-black text-rose-400 tracking-tighter">{{ $izinSakit ?? 0 }}</h3>
                                <p class="text-[10px] font-bold text-rose-500/70 uppercase tracking-widest mt-1">IZIN & SAKIT</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- 4. USER CARDS (VERSI RAPI & ESTETIK) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                
                {{-- CARD TOTAL HADIR --}}
                <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/60 border border-slate-50 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform duration-500">
                        <span class="text-7xl">📅</span>
                    </div>
                    <div class="relative z-10">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Pencapaian Anda</p>
                        <h3 class="text-4xl font-black text-slate-800 leading-none">{{ $totalHadir ?? 0 }}</h3>
                        <p class="text-sm font-bold text-indigo-500 mt-1">Hari Kerja Terlewati</p>
                        <div class="mt-4 w-12 h-1.5 bg-indigo-500 rounded-full"></div>
                    </div>
                </div>

                {{-- CARD STATUS KEHADIRAN --}}
                <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/60 border border-slate-50 relative overflow-hidden group">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 text-center">Update Kehadiran</p>
                    
                    @if(!$cekAbsensi)
                        <form id="formIzinSakit" action="{{ route('absensi.izinSakit') }}" method="POST" class="space-y-3">
                            @csrf
                            <input type="hidden" name="status" id="status_input">
                            <div class="relative">
                                <input type="text" name="keterangan" id="keterangan_input" placeholder="Tulis alasan singkat..." required 
                                    class="w-full text-xs font-bold border-none bg-slate-50 rounded-2xl py-4 px-5 focus:ring-2 focus:ring-indigo-500 transition-all placeholder:text-slate-300">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <button type="button" onclick="konfirmasiStatus('Izin')" 
                                    class="bg-amber-400 hover:bg-amber-500 text-white font-black py-4 rounded-2xl text-[10px] uppercase tracking-widest shadow-lg shadow-amber-200 active:scale-95 transition-all">
                                    ✋ Izin
                                </button>
                                <button type="button" onclick="konfirmasiStatus('Sakit')" 
                                    class="bg-rose-500 hover:bg-rose-600 text-white font-black py-4 rounded-2xl text-[10px] uppercase tracking-widest shadow-lg shadow-rose-200 active:scale-95 transition-all">
                                    🤒 Sakit
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="flex flex-col items-center justify-center py-2">
                            @php
                                $statusColor = 'bg-emerald-500';
                                $statusIcon = '✨';
                                if($cekAbsensi->status == 'Izin') { $statusColor = 'bg-amber-500'; $statusIcon = '✋'; }
                                if($cekAbsensi->status == 'Sakit') { $statusColor = 'bg-rose-500'; $statusIcon = '🤒'; }
                            @endphp
                            <div class="w-16 h-16 {{ $statusColor }} text-white rounded-2xl flex items-center justify-center text-2xl shadow-lg mb-3 animate-bounce">
                                {{ $statusIcon }}
                            </div>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Status Aktif</span>
                            <h4 class="text-lg font-black text-slate-800 uppercase tracking-tighter italic">{{ $cekAbsensi->status }}</h4>
                        </div>
                    @endif
                </div>

                @if(!$cekAbsensi)
                    {{-- TOMBOL ABSEN MASUK --}}
                    <div class="relative group cursor-pointer" onclick="handleAbsensi()">
                        <div class="absolute -inset-1 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-[2.5rem] blur opacity-25 group-hover:opacity-40 transition duration-500"></div>
                        <div class="relative bg-white h-full rounded-[2.5rem] p-6 flex flex-col items-center justify-center border border-slate-100 shadow-xl shadow-indigo-100">
                            <div class="w-20 h-20 bg-indigo-50 rounded-3xl flex items-center justify-center text-4xl mb-4 group-hover:scale-110 transition-transform duration-500">🚀</div>
                            <h4 class="font-black text-slate-800 uppercase tracking-[0.2em] text-xs">Klik Untuk Absen</h4>
                        </div>
                    </div>
                @elseif(!$cekAbsensi->jam_keluar && in_array($cekAbsensi->status, ['Hadir', 'Terlambat']))
                    {{-- TOMBOL ABSEN PULANG --}}
                    @php
                        $jamSekarang = \Carbon\Carbon::now('Asia/Jakarta')->hour;
                    @endphp

                    @if($jamSekarang < 17)
                        <div class="relative group cursor-not-allowed opacity-60" onclick="Swal.fire('Sabar!', 'Absen pulang baru bisa diklik jam 17:00 sore ya!', 'info')">
                            <div class="relative bg-slate-100 h-full rounded-[2.5rem] p-6 flex flex-col items-center justify-center border border-slate-200">
                                <div class="w-20 h-20 bg-slate-200 rounded-3xl flex items-center justify-center text-4xl mb-4 grayscale">🔒</div>
                                <h4 class="font-black text-slate-500 uppercase tracking-widest text-xs text-center">Pulang Terkunci</h4>
                            </div>
                        </div>
                    @else
                        <div class="relative group cursor-pointer" onclick="handleAbsensi()">
                            <div class="absolute -inset-1 bg-gradient-to-r from-rose-600 to-orange-600 rounded-[2.5rem] blur opacity-25 group-hover:opacity-40 transition duration-500"></div>
                            <div class="relative bg-white h-full rounded-[2.5rem] p-6 flex flex-col items-center justify-center border border-slate-100 shadow-lg">
                                <div class="w-20 h-20 bg-rose-50 rounded-3xl flex items-center justify-center text-4xl mb-4 group-hover:scale-110 transition-transform duration-500">🏠</div>
                                <h4 class="font-black text-slate-800 uppercase tracking-widest text-xs">KLIK UNTUK PULANG</h4>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="bg-slate-50 rounded-[2.5rem] p-6 flex flex-col items-center justify-center border border-slate-100 shadow-inner text-center h-full opacity-80">
                        <div class="w-20 h-20 bg-white rounded-3xl flex items-center justify-center text-4xl mb-4 shadow-sm">✨</div>
                        <h4 class="font-black text-slate-500 uppercase tracking-widest text-xs">SELESAI UNTUK HARI INI</h4>
                    </div>
                @endif
            </div>

            {{-- 5. LOG AKTIVITAS HARI INI --}}
            <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-50 overflow-hidden mb-8 animate-fade-in">
                <div class="p-8 border-b border-slate-50 bg-slate-50/30">
                    <h3 class="font-black text-slate-800 uppercase tracking-tight flex items-center gap-2 text-lg">
                        <span>⚡</span> LOG AKTIVITAS HARI INI
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-slate-100">
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">WAKTU</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">KARYAWAN</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">STATUS</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($recentActivities as $activity)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-8 py-5 text-sm font-bold text-indigo-600">{{ $activity->created_at->format('H:i') }}</td>
                                <td class="px-8 py-5 text-sm font-bold text-slate-700">{{ $activity->karyawan->nama_lengkap ?? $activity->user->name }}</td>
                                <td class="px-8 py-5">
                                    <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase {{ $activity->status == 'Hadir' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                                        {{ $activity->status }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="px-8 py-10 text-center text-slate-400 italic">BELUM ADA AKTIVITAS.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 6. REKAPITULASI BULANAN --}}
            @if(Auth::user()->role == 'admin')
            <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-50 overflow-hidden animate-fade-in">
                <div class="p-8 border-b border-slate-100 bg-indigo-50/30">
                    <h3 class="font-black text-slate-800 uppercase tracking-tight flex items-center gap-2 text-lg">
                        <span>📊</span> REKAPITULASI ABSENSI BULANAN ({{ $namaBulan }})
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50">
                                <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase">NAMA KARYAWAN</th>
                                <th class="px-8 py-4 text-[10px] font-black text-emerald-600 uppercase text-center">HADIR</th>
                                <th class="px-8 py-4 text-[10px] font-black text-amber-600 uppercase text-center">IZIN</th>
                                <th class="px-8 py-4 text-[10px] font-black text-rose-600 uppercase text-center">SAKIT</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($rekapBulanan as $rekap)
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-8 py-4 text-sm font-bold text-slate-700">{{ $rekap->nama_lengkap }}</td>
                                <td class="px-8 py-4 text-sm font-black text-emerald-500 text-center">{{ $rekap->total_hadir }}</td>
                                <td class="px-8 py-4 text-sm font-black text-amber-500 text-center">{{ $rekap->total_izin }}</td>
                                <td class="px-8 py-4 text-sm font-black text-rose-500 text-center">{{ $rekap->total_sakit }}</td>
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
            <div id="modalContent" class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl p-8 transform translate-y-full transition-transform duration-500">
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-black text-slate-800 uppercase tracking-tight mb-4">Konfirmasi Lokasi</h2>
                    <div id="map-preview" class="border-4 border-slate-50 shadow-inner"></div>
                </div>
                <form id="formUtamaAbsensi" action="{{ route('absensi.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="lat" id="lat">
                    <input type="hidden" name="lng" id="lng">
                    <button type="submit" class="w-full bg-indigo-600 text-white font-black py-5 rounded-[1.5rem] shadow-xl uppercase active:scale-95 transition-all">Kirim Sekarang</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        let map;

        function updateClock() {
            const clock = document.getElementById('realtime-clock');
            if(clock) clock.textContent = new Date().toLocaleTimeString('id-ID', { hour12: false });
        }
        setInterval(updateClock, 1000);

        function konfirmasiStatus(status) {
            const ket = document.getElementById('keterangan_input').value;
            if(!ket) { Swal.fire('Isi Alasan', '', 'warning'); return; }
            document.getElementById('status_input').value = status;
            document.getElementById('formIzinSakit').submit();
        }

        function handleAbsensi() {
            Swal.fire({
                title: 'Mendeteksi Lokasi...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            setTimeout(() => {
                Swal.close();
                showModalAbsen(3.595188, 98.672223); 
            }, 1000);
        }

        function showModalAbsen(lat, lng) {
            document.getElementById('lat').value = lat;
            document.getElementById('lng').value = lng;
            document.getElementById('absensiModal').classList.remove('hidden');
            setTimeout(() => {
                document.getElementById('modalContent').classList.remove('translate-y-full');
                initMap(lat, lng);
            }, 10);
        }

        function initMap(lat, lng) {
            if (map) { map.remove(); }
            map = L.map('map-preview').setView([lat, lng], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
            L.marker([lat, lng]).addTo(map).bindPopup('Lokasi Kantor').openPopup();
        }

        function closeAbsensiModal() {
            document.getElementById('modalContent').classList.add('translate-y-full');
            setTimeout(() => document.getElementById('absensiModal').classList.add('hidden'), 500);
        }
    </script>
</x-app-layout>