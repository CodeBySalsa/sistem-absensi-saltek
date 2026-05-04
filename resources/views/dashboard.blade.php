<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 leading-tight tracking-tighter">
                {{ __('Dashboard Monitoring - PT Saltek') }}
            </h2>
            <div class="flex items-center gap-3">
                {{-- Countdown Area --}}
                <div id="countdown-area" class="text-xs font-bold text-white bg-slate-900 px-4 py-2 rounded-xl shadow-lg border border-slate-700">
                    <span class="opacity-80 uppercase tracking-widest mr-1 text-[10px]">Batas Absen:</span>
                    <span id="timer" class="font-mono text-rose-500">--:--:--</span>
                </div>
                
                {{-- Realtime Clock --}}
                <div id="realtime-clock" class="text-sm font-black text-indigo-600 bg-white px-4 py-2 rounded-xl border border-slate-100 shadow-sm">
                    --:--:--
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
        #map-preview { height: 120px; width: 100%; border-radius: 1rem; margin-top: 0.5rem; z-index: 1; border: 2px solid #f1f5f9; }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }
    </style>

    <div class="py-10 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- 1. HERO BANNER --}}
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
            <div class="bg-white p-8 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-white mb-6">
                <div class="flex items-center gap-6">
                    <div class="relative">
                        @if($cekAbsensi && $cekAbsensi->jam_keluar)
                            <div class="w-16 h-16 bg-blue-500 rounded-2xl shadow-lg shadow-blue-200 flex items-center justify-center text-2xl text-white">🏁</div>
                        @elseif($cekAbsensi && in_array($cekAbsensi->status, ['Hadir', 'Terlambat']))
                            <div class="w-16 h-16 bg-emerald-500 rounded-2xl shadow-lg shadow-emerald-200 flex items-center justify-center text-2xl text-white">✅</div>
                        @elseif(now()->format('H:i') > '08:30' && !$cekAbsensi)
                            <div class="w-16 h-16 bg-rose-500 rounded-2xl shadow-lg shadow-rose-200 flex items-center justify-center text-2xl text-white animate-pulse">❌</div>
                        @else
                            <div class="w-16 h-16 bg-amber-400 rounded-2xl shadow-lg shadow-amber-200 flex items-center justify-center text-2xl text-white animate-bounce">⏳</div>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-1 italic">Status Real-time Anda</p>
                        <h3 class="text-2xl font-black text-slate-800 tracking-tight uppercase">
                            @if($cekAbsensi && $cekAbsensi->jam_keluar)
                                Selesai Bertugas ({{ $cekAbsensi->status }})
                            @elseif($cekAbsensi)
                                Sudah Absen Masuk ({{ $cekAbsensi->status }})
                            @elseif(now()->format('H:i') > '08:30')
                                Batas Waktu Terlewati (Terlambat)
                            @else
                                Menunggu Absensi Masuk
                            @endif
                        </h3>
                        <p id="distance-info" class="text-[10px] font-bold text-indigo-500 mt-1 uppercase tracking-tighter">Mencari lokasi...</p>
                    </div>
                </div>
            </div>
            
            {{-- 3. ADMIN SPECIAL PANEL & STATS --}}
            @if(Auth::user()->role == 'admin')
            <div class="mb-8 bg-slate-900 rounded-[2.5rem] p-10 text-white shadow-2xl relative overflow-hidden">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-center">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-3 h-3 bg-emerald-400 rounded-full animate-pulse"></span>
                            <h3 class="text-[10px] font-black tracking-[0.2em] uppercase text-indigo-400">Administrator Access</h3>
                        </div>
                        <h1 class="text-4xl font-black tracking-tight uppercase">Halo, {{ Auth::user()->name }}!</h1>
                    </div>
                    <div class="flex space-x-3 mt-6 md:mt-0">
                        <a href="{{ route('karyawan.index') }}" class="bg-indigo-600 text-white font-bold py-3 px-6 rounded-2xl hover:bg-indigo-700 transition-all text-xs uppercase">Data Karyawan</a>
                        <a href="{{ route('karyawan.create') }}" class="bg-white text-slate-900 font-bold py-3 px-6 rounded-2xl hover:bg-slate-100 transition-all text-xs uppercase">+ Tambah</a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-[2.5rem] shadow-lg border border-slate-100 flex items-center gap-5 transition-all hover:shadow-indigo-100">
                    <div class="h-14 w-14 bg-indigo-50 rounded-2xl flex items-center justify-center text-xl">👥</div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Anggota</p>
                        <h3 class="text-3xl font-black text-slate-800">{{ $totalKaryawan ?? 0 }}</h3>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-[2.5rem] shadow-lg border border-slate-100 flex items-center gap-5 transition-all hover:shadow-emerald-100">
                    <div class="h-14 w-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-xl">✅</div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Hadir Hari Ini</p>
                        <h3 class="text-3xl font-black text-slate-800">{{ $hadirHariIni ?? 0 }}</h3>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-[2.5rem] shadow-lg border border-slate-100 flex items-center gap-5 transition-all hover:shadow-rose-100">
                    <div class="h-14 w-14 bg-rose-50 rounded-2xl flex items-center justify-center text-xl">⚠️</div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Izin / Sakit</p>
                        <h3 class="text-3xl font-black text-slate-800">{{ $izinSakit ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            @endif

            {{-- 4. USER INTERACTIVE CARDS --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                {{-- Card Total Hadir --}}
                <div class="bg-white rounded-[2.5rem] p-8 shadow-lg border-b-4 border-indigo-500 flex flex-col justify-between transition-transform hover:scale-[1.02]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Hadir Anda</p>
                            <h3 class="text-3xl font-black text-slate-800 mt-1">{{ $totalHadir ?? 0 }} Hari</h3>
                        </div>
                        <div class="bg-indigo-50 p-4 rounded-2xl text-indigo-500 text-2xl">📅</div>
                    </div>
                    <p class="text-[10px] text-indigo-400 mt-6 font-bold italic">* Terhitung masa KKN di PT Saltek</p>
                </div>

                {{-- Card Form Izin / Sakit --}}
                <div class="bg-white rounded-[2.5rem] p-8 shadow-lg border-b-4 border-amber-500 overflow-hidden relative group">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 relative z-10">Status Kehadiran</p>
                    
                    @if(!$cekAbsensi)
                        <form id="formIzinSakit" action="{{ route('absensi.izinSakit') }}" method="POST" class="space-y-2 relative z-10">
                            @csrf
                            <input type="hidden" name="status" id="status_input">
                            <input type="text" name="keterangan" id="keterangan_input" placeholder="Alasan izin/sakit..." required 
                                class="w-full text-xs border-slate-200 rounded-2xl py-3 px-4 bg-slate-50 focus:ring-amber-500 focus:border-amber-500 transition-all">
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" onclick="konfirmasiStatus('Izin')" 
                                    class="bg-amber-500 text-white font-black py-3 rounded-xl text-[10px] uppercase shadow-md hover:bg-amber-600 active:scale-95 transition-all">
                                    ✋ Izin
                                </button>
                                <button type="button" onclick="konfirmasiStatus('Sakit')" 
                                    class="bg-rose-500 text-white font-black py-3 rounded-xl text-[10px] uppercase shadow-md hover:bg-rose-600 active:scale-95 transition-all">
                                    🤒 Sakit
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="relative z-10 flex flex-col items-center justify-center h-24">
                            @if($cekAbsensi->status == 'Hadir' || $cekAbsensi->status == 'Terlambat' || $cekAbsensi->status == 'Selesai')
                                <div class="flex flex-col items-center animate-fade-in">
                                    <div class="w-12 h-12 bg-emerald-50 rounded-full flex items-center justify-center text-xl mb-2">✨</div>
                                    <span class="text-xs font-black text-slate-700 uppercase tracking-tighter text-center">Kamu Luar Biasa!</span>
                                    <p class="text-[9px] text-emerald-500 font-bold uppercase mt-1 italic">Sudah Absensi Hari Ini</p>
                                </div>
                            @else
                                <div class="flex flex-col items-center animate-fade-in text-center">
                                    <div class="w-12 h-12 {{ $cekAbsensi->status == 'Sakit' ? 'bg-rose-50' : 'bg-amber-50' }} rounded-full flex items-center justify-center text-xl mb-2">
                                        {{ $cekAbsensi->status == 'Sakit' ? '🏥' : '📝' }}
                                    </div>
                                    <span class="text-xs font-black text-slate-700 uppercase tracking-tighter">Status: {{ $cekAbsensi->status }}</span>
                                    <p class="text-[9px] text-slate-400 font-medium mt-1 truncate max-w-[150px]">"{{ $cekAbsensi->keterangan }}"</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Panel Absensi Utama --}}
                <div class="relative group cursor-pointer" id="btn-absen-main" onclick="handleAbsensi()">
                    <div class="absolute -inset-1 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-[2.5rem] blur opacity-25 group-hover:opacity-40 transition"></div>
                    <div class="relative bg-white h-full rounded-[2.5rem] p-6 flex flex-col items-center justify-center border border-slate-100 shadow-lg">
                        <div id="map-preview-container" class="w-full mb-3">
                             <div id="map-preview"></div>
                        </div>
                        
                        @if(!$cekAbsensi)
                            <h4 id="btn-text" class="font-black text-slate-800 uppercase tracking-widest text-[10px]">Klik Untuk Absen Masuk</h4>
                            <p id="btn-subtext" class="text-[9px] text-indigo-500 mt-1 uppercase font-bold italic">Kirim Lokasi Presensi 🚀</p>
                        @elseif(($cekAbsensi->status == 'Hadir' || $cekAbsensi->status == 'Terlambat') && !$cekAbsensi->jam_keluar)
                            <h4 id="btn-text" class="font-black text-rose-600 uppercase tracking-widest text-[10px]">Klik Untuk Absen Pulang</h4>
                            <p id="btn-subtext" class="text-[9px] text-slate-400 mt-1 uppercase font-bold italic">Selesaikan Tugas Hari Ini 👋</p>
                        @else
                            <h4 class="font-black text-emerald-500 uppercase tracking-widest text-[10px]">Presensi Selesai</h4>
                            <p class="text-[9px] text-slate-400 mt-1 uppercase font-bold italic">Sampai Jumpa Besok! ✨</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 5. TABEL AKTIVITAS --}}
            <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-50 overflow-hidden">
                <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
                    <h3 class="font-black text-slate-800 uppercase tracking-tight flex items-center gap-2 text-lg">
                        <span>⚡</span> Aktivitas Absensi Hari Ini
                    </h3>
                </div>
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-slate-100">
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Karyawan</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Jam Masuk</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Jam Pulang</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($recentActivities ?? [] as $activity)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-8 py-5 text-sm font-bold text-slate-700">{{ $activity->user->name ?? 'Unknown' }}</td>
                                <td class="px-8 py-5 text-sm font-mono text-slate-500">{{ $activity->jam_masuk ?? $activity->created_at->format('H:i:s') }}</td>
                                <td class="px-8 py-5 text-sm font-mono text-slate-400">{{ $activity->jam_keluar ?? '--:--:--' }}</td>
                                <td class="px-8 py-5">
                                    <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase {{ $activity->status == 'Hadir' || $activity->status == 'Selesai' ? 'bg-emerald-50 text-emerald-600' : ($activity->status == 'Terlambat' ? 'bg-rose-50 text-rose-600' : 'bg-amber-50 text-amber-600') }}">
                                        {{ $activity->status }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-8 py-10 text-center text-slate-400 italic font-medium">Belum ada data masuk untuk hari ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 6. TABEL REKAPITULASI STATISTIK (KHUSUS ADMIN) --}}
            @if(Auth::user()->role == 'admin')
            <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-50 overflow-hidden mt-8">
                <div class="p-8 border-b border-slate-50 bg-indigo-50/30">
                    <h3 class="font-black text-slate-800 uppercase tracking-tight flex items-center gap-2 text-lg">
                        <span>📊</span> Rekapitulasi Absensi - {{ $namaBulan ?? 'Bulan Ini' }}
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-slate-100">
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Karyawan</th>
                                <th class="px-8 py-5 text-center text-[10px] font-black text-emerald-500 uppercase tracking-widest">Hadir</th>
                                <th class="px-8 py-5 text-center text-[10px] font-black text-amber-500 uppercase tracking-widest">Izin</th>
                                <th class="px-8 py-5 text-center text-[10px] font-black text-rose-500 uppercase tracking-widest">Sakit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($rekapBulanan ?? [] as $rekap)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-8 py-5 text-sm font-bold text-slate-700">{{ $rekap->nama_lengkap }}</td>
                                <td class="px-8 py-5 text-center text-sm font-black text-slate-600">{{ $rekap->total_hadir ?? 0 }}</td>
                                <td class="px-8 py-5 text-center text-sm font-black text-slate-600">{{ $rekap->total_izin ?? 0 }}</td>
                                <td class="px-8 py-5 text-center text-sm font-black text-slate-600">{{ $rekap->total_sakit ?? 0 }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-8 py-10 text-center text-slate-400 italic font-medium">Data rekapitulasi belum tersedia untuk bulan ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
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
                <div class="p-8">
                    <div class="text-center mb-6">
                        <div id="modal-icon" class="w-20 h-20 bg-indigo-50 rounded-3xl flex items-center justify-center text-4xl mx-auto mb-4">📍</div>
                        <h2 id="modal-title" class="text-2xl font-black text-slate-800 uppercase tracking-tight">Konfirmasi Kehadiran</h2>
                        <p id="modal-desc" class="text-slate-400 text-sm font-medium mt-1">Sistem akan mencatat lokasi dan waktu presensi Anda secara otomatis.</p>
                    </div>

                    <form id="formUtamaAbsensi" action="{{ route('absensi.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="lat" id="lat">
                        <input type="hidden" name="lng" id="lng">
                        
                        <button type="submit" id="btn-submit-modal" class="w-full bg-indigo-600 text-white font-black py-5 rounded-[1.5rem] shadow-xl shadow-indigo-200 hover:bg-indigo-700 active:scale-95 transition-all uppercase tracking-widest flex items-center justify-center gap-3">
                            <span>🚀</span> <span id="btn-modal-text">Kirim Presensi Sekarang</span>
                        </button>
                    </form>
                    
                    <button onclick="closeAbsensiModal()" class="w-full mt-4 text-slate-400 font-bold text-xs uppercase tracking-widest hover:text-slate-600 transition-colors">
                        Nanti Saja
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // --- DATA KONFIGURASI ---
        const KANTOR_LAT = 3.5952; 
        const KANTOR_LNG = 98.6722; 
        const RADIUS_MAKS = 100; // Meter
        let userLat, userLng, userInsideRadius = false;

        // --- 1. Realtime Clock ---
        function updateClock() {
            const now = new Date();
            const clock = document.getElementById('realtime-clock');
            if(clock) clock.textContent = now.toLocaleTimeString('id-ID', { hour12: false });
        }
        setInterval(updateClock, 1000);
        updateClock();

        // --- 2. Countdown Timer ---
        function updateCountdown() {
            const now = new Date();
            const deadline = new Date();
            deadline.setHours(8, 30, 0);
            const diff = deadline - now;
            const timerElement = document.getElementById('timer');

            if (diff > 0) {
                const h = String(Math.floor((diff / (1000 * 60 * 60)) % 24)).padStart(2, '0');
                const m = String(Math.floor((diff / (1000 * 60)) % 60)).padStart(2, '0');
                const s = String(Math.floor((diff / 1000) % 60)).padStart(2, '0');
                timerElement.textContent = `${h}:${m}:${s}`;
            } else {
                timerElement.textContent = "00:00:00";
                timerElement.classList.replace('text-rose-500', 'text-slate-500');
            }
        }
        setInterval(updateCountdown, 1000);
        updateCountdown();

        // --- 3. Geofencing & Leaflet ---
        let mapPreview;
        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371e3; 
            const φ1 = lat1 * Math.PI/180;
            const φ2 = lat2 * Math.PI/180;
            const Δφ = (lat2-lat1) * Math.PI/180;
            const Δλ = (lon2-lon1) * Math.PI/180;
            const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) + Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ/2) * Math.sin(Δλ/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        }

        function initLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.watchPosition((position) => {
                    userLat = position.coords.latitude;
                    userLng = position.coords.longitude;
                    
                    document.getElementById('lat').value = userLat;
                    document.getElementById('lng').value = userLng;

                    const distance = calculateDistance(userLat, userLng, KANTOR_LAT, KANTOR_LNG);
                    const distInfo = document.getElementById('distance-info');
                    
                    if (distance <= RADIUS_MAKS) {
                        userInsideRadius = true;
                        if(distInfo) {
                            distInfo.innerHTML = `✅ Anda berada dalam jangkauan (${Math.round(distance)}m)`;
                            distInfo.className = "text-[10px] font-bold text-emerald-500 mt-1 uppercase tracking-tighter";
                        }
                    } else {
                        userInsideRadius = false;
                        if(distInfo) {
                            distInfo.innerHTML = `📍 Jarak Anda: ${Math.round(distance)}m (Maks: ${RADIUS_MAKS}m)`;
                            distInfo.className = "text-[10px] font-bold text-rose-500 mt-1 uppercase tracking-tighter";
                        }
                    }

                    if(!mapPreview) {
                        mapPreview = L.map('map-preview', { zoomControl: false, attributionControl: false }).setView([userLat, userLng], 16);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mapPreview);
                        L.marker([userLat, userLng]).addTo(mapPreview);
                        L.circle([KANTOR_LAT, KANTOR_LNG], {radius: RADIUS_MAKS, color: '#4f46e5', fillOpacity: 0.1}).addTo(mapPreview);
                    } else {
                        mapPreview.setView([userLat, userLng]);
                    }
                }, (err) => {
                    console.error(err);
                }, { enableHighAccuracy: true });
            }
        }
        initLocation();

        // --- 4. Logic Button Absensi (Hadir & Pulang) ---
        function handleAbsensi() {
            if(!userInsideRadius) {
                Swal.fire({ icon: 'error', title: 'Diluar Jangkauan!', text: 'Anda harus berada di lokasi PT Saltek untuk bisa absen.', confirmButtonColor: '#4f46e5' });
                return;
            }

            const isSudahAbsenMasuk = @json($cekAbsensi ? true : false);
            const isSudahPulang = @json(($cekAbsensi && $cekAbsensi->jam_keluar) ? true : false);

            if(isSudahPulang) {
                Swal.fire('Selesai!', 'Tugas Anda hari ini sudah tuntas. Sampai jumpa besok!', 'success');
                return;
            }

            if(isSudahAbsenMasuk) {
                document.getElementById('formUtamaAbsensi').action = "{{ route('absensi.pulang') }}";
                document.getElementById('modal-title').innerText = "Konfirmasi Pulang";
                document.getElementById('modal-desc').innerText = "Pastikan pekerjaan hari ini sudah selesai sebelum mengirim presensi pulang.";
                document.getElementById('modal-icon').innerText = "🏁";
                document.getElementById('btn-modal-text').innerText = "Kirim Absen Pulang";
                document.getElementById('btn-submit-modal').classList.replace('bg-indigo-600', 'bg-rose-600');
            } else {
                document.getElementById('formUtamaAbsensi').action = "{{ route('absensi.store') }}";
                document.getElementById('modal-title').innerText = "Konfirmasi Kehadiran";
                document.getElementById('btn-modal-text').innerText = "Kirim Presensi Sekarang";
            }

            openAbsensiModal();
        }

        function openAbsensiModal() {
            const modal = document.getElementById('absensiModal');
            const content = document.getElementById('modalContent');
            modal.classList.remove('hidden');
            setTimeout(() => content.classList.remove('translate-y-full'), 10);
        }

        function closeAbsensiModal() {
            const modal = document.getElementById('absensiModal');
            const content = document.getElementById('modalContent');
            content.classList.add('translate-y-full');
            setTimeout(() => modal.classList.add('hidden'), 500);
        }

        // --- 5. Form Izin/Sakit ---
        function konfirmasiStatus(status) {
            const keterangan = document.getElementById('keterangan_input').value;
            if(!keterangan) {
                Swal.fire({ icon: 'warning', title: 'Eitss!', text: 'Isi alasannya dulu ya.', confirmButtonColor: '#fbbf24' });
                return;
            }
            Swal.fire({
                title: `Kirim Status ${status}?`,
                text: "Pastikan data sudah benar!",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                confirmButtonText: 'Ya, Kirim!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('status_input').value = status;
                    document.getElementById('formIzinSakit').submit();
                }
            });
        }

        @if(session('success'))
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", timer: 3000, showConfirmButton: false });
        @endif
        @if(session('error'))
            Swal.fire({ icon: 'error', title: 'Waduh!', text: "{{ session('error') }}", confirmButtonColor: '#ef4444' });
        @endif
    </script>
</x-app-layout>