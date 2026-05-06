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
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }
        /* Pastikan map memiliki tinggi */
        #map-preview { height: 200px; width: 100%; border-radius: 1.5rem; z-index: 1; }
        @keyframes spin-slow { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        .animate-spin-slow { animation: spin-slow 8s linear infinite; }
    </style>

    <div class="py-10 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Message --}}
            @if(session('success'))
                <script>
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", showConfirmButton: false, timer: 2000 });
                </script>
            @endif

            {{-- 1. HERO BANNER --}}
            @if(Auth::user()->role !== 'admin')
                @php
                    $bannerGradient = 'custom-gradient';
                    if ($cekAbsensi && $cekAbsensi->jam_keluar) {
                        $bannerGradient = 'bg-gradient-to-br from-blue-600 to-indigo-800';
                    } elseif ($cekAbsensi && in_array($cekAbsensi->status, ['Hadir', 'Terlambat'])) {
                        $bannerGradient = 'bg-gradient-to-br from-emerald-500 to-teal-700';
                    } elseif ($cekAbsensi && in_array($cekAbsensi->status, ['Izin', 'Sakit'])) {
                        $bannerGradient = 'bg-gradient-to-br from-amber-400 to-orange-600';
                    }
                @endphp
                <div class="mb-6 {{ $bannerGradient }} rounded-[2rem] p-7 text-white shadow-lg relative overflow-hidden transition-all duration-500">
                    <div class="relative z-10">
                        <h1 class="text-3xl font-black uppercase tracking-tighter italic">Halo, {{ Auth::user()->name }}!</h1>
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
                            @if($cekAbsensi) Sudah Absen ({{ $cekAbsensi->status }}) @else Menunggu Absensi Masuk @endif
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
                                Hello, <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">{{ Auth::user()->name }}</span>!
                            </h1>
                        </div>

                        <div class="w-full lg:w-auto grid grid-cols-1 sm:grid-cols-3 gap-4 lg:gap-6">
                            <div class="bg-slate-800/40 border border-white/5 p-6 rounded-[2rem] flex flex-col items-center text-center">
                                <h3 class="text-3xl font-black text-white tracking-tighter">{{ $totalKaryawan ?? 0 }}</h3>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Total Anggota</p>
                            </div>
                            <div class="bg-emerald-500/10 border border-emerald-500/20 p-6 rounded-[2rem] flex flex-col items-center text-center">
                                <h3 class="text-3xl font-black text-emerald-400 tracking-tighter">{{ $hadirHariIni ?? 0 }}</h3>
                                <p class="text-[10px] font-bold text-emerald-500/70 uppercase tracking-widest mt-1">Hadir Hari Ini</p>
                            </div>
                            <div class="bg-rose-500/10 border border-rose-500/20 p-6 rounded-[2rem] flex flex-col items-center text-center">
                                <h3 class="text-3xl font-black text-rose-400 tracking-tighter">{{ $izinSakit ?? 0 }}</h3>
                                <p class="text-[10px] font-bold text-rose-500/70 uppercase tracking-widest mt-1">Izin & Sakit</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-8 border-t border-white/5 grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="p-4 bg-white/5 rounded-2xl border border-white/5">
                            <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-1">Rekap Seluruh Karyawan</p>
                            <h4 class="text-lg font-bold text-white uppercase">{{ $namaBulan }} {{ now()->year }}</h4>
                        </div>
                        <div class="p-4 bg-white/5 rounded-2xl border border-white/5 text-center">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Hadir</p>
                            <h4 class="text-xl font-black text-emerald-400">{{ $ringkasanStatistik->total_hadir ?? 0 }}</h4>
                        </div>
                        <div class="p-4 bg-white/5 rounded-2xl border border-white/5 text-center">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Izin</p>
                            <h4 class="text-xl font-black text-amber-400">{{ $ringkasanStatistik->total_izin ?? 0 }}</h4>
                        </div>
                        <div class="p-4 bg-white/5 rounded-2xl border border-white/5 text-center">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Sakit</p>
                            <h4 class="text-xl font-black text-rose-400">{{ $ringkasanStatistik->total_sakit ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- 4. USER CARDS --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-[2.5rem] p-8 shadow-lg border-b-4 border-indigo-500">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Hadir Anda</p>
                    <h3 class="text-3xl font-black text-slate-800 mt-1">{{ $totalHadir ?? 0 }} Hari</h3>
                </div>

                <div class="bg-white rounded-[2.5rem] p-8 shadow-lg border-b-4 border-amber-500">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Status Kehadiran</p>
                    @if(!$cekAbsensi)
                        <form id="formIzinSakit" action="{{ route('absensi.izinSakit') }}" method="POST" class="space-y-2">
                            @csrf
                            <input type="hidden" name="status" id="status_input">
                            <input type="text" name="keterangan" id="keterangan_input" placeholder="Alasan..." required class="w-full text-xs border-slate-200 rounded-2xl py-3 px-4 bg-slate-50">
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" onclick="konfirmasiStatus('Izin')" class="bg-amber-500 text-white font-black py-3 rounded-xl text-[10px] uppercase shadow-md transition-all active:scale-95">✋ Izin</button>
                                <button type="button" onclick="konfirmasiStatus('Sakit')" class="bg-rose-500 text-white font-black py-3 rounded-xl text-[10px] uppercase shadow-md transition-all active:scale-95">🤒 Sakit</button>
                            </div>
                        </form>
                    @else
                        <div class="flex flex-col items-center justify-center h-24">
                            <span class="text-xs font-black text-slate-700 uppercase">Status Aktif: {{ $cekAbsensi->status }}</span>
                        </div>
                    @endif
                </div>

                {{-- KOLOM KETIGA: LOGIKA DINAMIS --}}
                @if(!$cekAbsensi)
                    {{-- Belum absen sama sekali --}}
                    <div class="relative group cursor-pointer" onclick="handleAbsensi()">
                        <div class="absolute -inset-1 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-[2.5rem] blur opacity-25 group-hover:opacity-40 transition"></div>
                        <div class="relative bg-white h-full rounded-[2.5rem] p-6 flex flex-col items-center justify-center border border-slate-100 shadow-lg">
                            <div class="w-20 h-20 bg-indigo-50 rounded-3xl flex items-center justify-center text-4xl mb-4">🚀</div>
                            <h4 class="font-black text-slate-800 uppercase tracking-widest text-xs">Klik Untuk Absen</h4>
                        </div>
                    </div>
                @elseif($cekAbsensi->status == 'Sakit')
                    <div class="bg-white rounded-[2.5rem] p-6 flex flex-col items-center justify-center border border-rose-100 shadow-lg text-center">
                        <div class="text-5xl mb-3 animate-bounce">🤒</div>
                        <h4 class="font-black text-slate-800 uppercase tracking-tighter text-sm">Lekas Sembuh!</h4>
                        <p class="text-[9px] text-slate-400 mt-1 leading-tight">Istirahat yang cukup ya, {{ Auth::user()->name }}!</p>
                    </div>
                @elseif($cekAbsensi->status == 'Izin')
                    <div class="bg-white rounded-[2.5rem] p-6 flex flex-col items-center justify-center border border-amber-100 shadow-lg text-center">
                        <div class="text-5xl mb-3 animate-pulse">🗓️</div>
                        <h4 class="font-black text-slate-800 uppercase tracking-tighter text-sm">Sedang Izin</h4>
                        <p class="text-[9px] text-slate-400 mt-1 leading-tight">Urusan Anda terpantau sistem. Tetap semangat!</p>
                    </div>
                @elseif(!$cekAbsensi->jam_keluar && in_array($cekAbsensi->status, ['Hadir', 'Terlambat']))
                    {{-- Sudah absen masuk, belum absen pulang --}}
                    <div class="relative group cursor-pointer" onclick="handleAbsensi()">
                        <div class="absolute -inset-1 bg-gradient-to-r from-rose-600 to-orange-600 rounded-[2.5rem] blur opacity-25 group-hover:opacity-40 transition"></div>
                        <div class="relative bg-white h-full rounded-[2.5rem] p-6 flex flex-col items-center justify-center border border-slate-100 shadow-lg">
                            <div class="w-20 h-20 bg-rose-50 rounded-3xl flex items-center justify-center text-4xl mb-4">🏠</div>
                            <h4 class="font-black text-slate-800 uppercase tracking-widest text-xs">Klik Untuk Pulang</h4>
                        </div>
                    </div>
                @else
                    {{-- Sudah Selesai Kerja (Sudah Absen Pulang) --}}
                    <div class="bg-slate-100 rounded-[2.5rem] p-6 flex flex-col items-center justify-center border border-slate-200 shadow-inner opacity-80 text-center">
                        <div class="w-16 h-16 bg-slate-200 rounded-3xl flex items-center justify-center text-3xl mb-3">✨</div>
                        <h4 class="font-black text-slate-500 uppercase tracking-widest text-xs">Aktivitas Selesai</h4>
                        <p class="text-[9px] text-slate-400 mt-1 italic">Terima kasih untuk hari ini!</p>
                    </div>
                @endif
            </div>

            {{-- 5. TABEL MONITORING --}}
            <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-50 overflow-hidden mb-8 animate-fade-in">
                <div class="p-8 border-b border-slate-50 bg-slate-50/30">
                    <h3 class="font-black text-slate-800 uppercase tracking-tight flex items-center gap-2 text-lg">
                        <span>⚡</span> Monitor Absensi Hari Ini
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-slate-100">
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Waktu</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Karyawan</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Masuk</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($recentActivities as $activity)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-8 py-5 text-sm font-bold text-indigo-600">{{ $activity->created_at->format('H:i') }}</td>
                                <td class="px-8 py-5 text-sm font-bold text-slate-700">{{ $activity->karyawan->nama_lengkap ?? $activity->user->name }}</td>
                                <td class="px-8 py-5 text-sm font-mono text-slate-500">{{ $activity->jam_masuk ?? '--:--' }}</td>
                                <td class="px-8 py-5">
                                    <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase {{ $activity->status == 'Hadir' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                                        {{ $activity->status }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-8 py-10 text-center text-slate-400 italic">Belum ada aktivitas hari ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 6. REKAP BULANAN ADMIN --}}
            @if(Auth::user()->role == 'admin')
            <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-50 overflow-hidden animate-fade-in">
                <div class="p-8 border-b border-slate-100 bg-indigo-50/30">
                    <h3 class="font-black text-slate-800 uppercase tracking-tight flex items-center gap-2 text-lg">
                        <span>📊</span> Rekap Kehadiran Karyawan ({{ $namaBulan }})
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50">
                                <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase">Nama Karyawan</th>
                                <th class="px-8 py-4 text-[10px] font-black text-emerald-600 uppercase text-center">Hadir</th>
                                <th class="px-8 py-4 text-[10px] font-black text-amber-600 uppercase text-center">Izin</th>
                                <th class="px-8 py-4 text-[10px] font-black text-rose-600 uppercase text-center">Sakit</th>
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

    {{-- MODAL --}}
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
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition((pos) => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    
                    document.getElementById('lat').value = lat;
                    document.getElementById('lng').value = lng;
                    
                    document.getElementById('absensiModal').classList.remove('hidden');
                    setTimeout(() => {
                        document.getElementById('modalContent').classList.remove('translate-y-full');
                        initMap(lat, lng);
                    }, 10);
                }, (err) => {
                    Swal.fire('Error', 'Gagal mengambil lokasi. Pastikan izin GPS aktif.', 'error');
                });
            }
        }

        function initMap(lat, lng) {
            if (map) { map.remove(); }
            map = L.map('map-preview').setView([lat, lng], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);
            L.marker([lat, lng]).addTo(map).bindPopup('Lokasi Anda Sekarang').openPopup();
        }

        function closeAbsensiModal() {
            document.getElementById('modalContent').classList.add('translate-y-full');
            setTimeout(() => document.getElementById('absensiModal').classList.add('hidden'), 500);
        }
    </script>
</x-app-layout>