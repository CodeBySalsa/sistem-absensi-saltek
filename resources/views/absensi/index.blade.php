<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Digital - PT Saltek Dumpang Jaya</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    {{-- Leaflet CSS untuk Peta --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        #map { 
            height: 180px; 
            width: 100%;
            border-radius: 1.5rem; 
            margin-bottom: 1.5rem;
            z-index: 1;
            border: 1px solid #e2e8f0;
        }
    </style>
</head>
<body class="bg-slate-100 font-sans">

    <div class="max-w-md mx-auto min-h-screen flex flex-col p-4">
        
        <div class="mt-6 mb-8 flex justify-between items-start px-2">
            <div>
                <a href="{{ route('dashboard') }}" class="text-blue-600 text-xs font-bold hover:underline flex items-center">
                    ← KEMBALI KE DASHBOARD
                </a>
                <h1 class="text-3xl font-bold text-slate-800 tracking-tight mt-2">PT SALTEK</h1>
                <p class="text-slate-500 text-sm">Sistem Absensi Digital KKN</p>
            </div>
            
            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                @csrf
                <button type="submit" class="bg-red-50 text-[10px] text-red-500 px-3 py-1 rounded-full border border-red-100 hover:bg-red-500 hover:text-white transition-all font-bold">
                    LOGOUT
                </button>
            </form>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-8 text-center border border-slate-200 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-indigo-600"></div>
            
            <div class="mb-6">
                <span class="text-sm font-semibold text-blue-600 uppercase tracking-widest italic">Status Anda:</span>
                <h2 class="text-xl font-bold text-slate-800 mt-1 uppercase tracking-tight">{{ Auth::user()->name }}</h2>
            </div>

            <div class="bg-slate-50 rounded-2xl py-6 mb-8 border border-dashed border-slate-300">
                <div id="clock" class="text-4xl font-mono font-bold text-slate-700 tracking-widest">00:00:00</div>
                <p class="text-xs text-slate-400 mt-1 font-medium" id="date">-</p>
            </div>

            {{-- TAMPILAN PETA --}}
            <div id="map"></div>

            {{-- INDIKATOR JARAK --}}
            <div id="distance-info" class="mb-4 p-2 rounded-xl bg-slate-100 text-slate-500 text-[10px] font-bold uppercase tracking-widest">
                🛰️ Mencari Lokasi GPS...
            </div>

            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded-xl mb-4 text-sm font-medium border border-green-200">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 text-red-700 p-3 rounded-xl mb-4 text-sm font-medium border border-red-200">
                    {{ session('error') }}
                </div>
            @endif

            @php
                $karyawan = Auth::user()->karyawan;
                $absenHariIni = $karyawan ? $absensis->where('karyawan_id', $karyawan->id)->where('tanggal', date('Y-m-d'))->first() : null;
                $jamSekarang = (int)date('H');
            @endphp

            <div class="space-y-4">
                @if(!$karyawan)
                    <div class="bg-yellow-50 text-yellow-700 p-4 rounded-2xl text-sm border border-yellow-200 font-medium">
                        ⚠️ Akun belum terhubung ke data Karyawan.
                    </div>
                @elseif(!$absenHariIni)
                    <form action="{{ route('absensi.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="latitude" class="lat-input">
                        <input type="hidden" name="longitude" class="lng-input">
                        
                        <button type="submit" id="btn-absen" disabled class="w-full bg-slate-300 opacity-50 cursor-not-allowed text-white font-bold py-5 rounded-2xl shadow-lg transition-all text-lg">
                            ABSEN MASUK SEKARANG
                        </button>
                    </form>
                @elseif(in_array($absenHariIni->status, ['Izin', 'Sakit']))
                    <div class="bg-amber-50 border border-amber-100 p-5 rounded-2xl text-center">
                        <p class="text-amber-700 font-bold text-sm italic">✓ Status Hari Ini: {{ $absenHariIni->status }}</p>
                        <p class="text-amber-500 text-[10px] mt-1 uppercase tracking-widest font-black">
                            Ket: {{ $absenHariIni->keterangan ?? 'Izin diproses' }}
                        </p>
                    </div>
                @elseif(!$absenHariIni->jam_pulang)
                    <form action="{{ route('absensi.update', $absenHariIni->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="latitude" class="lat-input">
                        <input type="hidden" name="longitude" class="lng-input">

                        <button type="submit" id="btn-absen" disabled class="w-full bg-slate-300 opacity-50 cursor-not-allowed text-white font-bold py-5 rounded-2xl shadow-lg transition-all text-lg">
                            ABSEN PULANG SEKARANG
                        </button>
                    </form>
                    @if($jamSekarang < 17)
                        <p class="text-[9px] text-blue-500 font-bold mt-2 italic">* Tombol pulang idealnya aktif jam 17:00</p>
                    @endif
                @else
                    <div class="bg-slate-100 text-slate-500 py-5 rounded-2xl font-bold border border-slate-200 flex flex-col items-center">
                        <span class="text-2xl mb-1">✅</span>
                        TUGAS HARI INI SELESAI
                    </div>
                @endif
            </div>
            <p class="text-[8px] text-slate-400 mt-4 italic">* Radius aman: 20m dari kantor PT Saltek.</p>
        </div>

        <div class="mt-10 mb-10">
            <h3 class="font-bold text-slate-700 mb-4 px-1 flex items-center">
                <span class="w-2 h-6 bg-blue-600 rounded-full mr-3"></span>
                Riwayat Hari Ini
            </h3>
            
            <div class="space-y-3">
                @forelse($absensis->where('tanggal', date('Y-m-d')) as $absen)
                <div class="bg-white p-4 rounded-3xl flex justify-between items-center shadow-sm border border-slate-100">
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-black text-sm">
                            {{ strtoupper(substr($absen->karyawan->nama_lengkap ?? 'K', 0, 1)) }}
                        </div>
                        <div class="ml-4">
                            <p class="font-black text-slate-800 text-sm mb-1">{{ $absen->karyawan->nama_lengkap }}</p>
                            <div class="flex gap-2">
                                <span class="text-[9px] bg-blue-50 text-blue-600 px-2 py-0.5 rounded-md font-bold">IN: {{ $absen->jam_masuk ?? '--:--' }}</span>
                                @if($absen->jam_pulang)
                                <span class="text-[9px] bg-orange-50 text-orange-600 px-2 py-0.5 rounded-md font-bold">OUT: {{ $absen->jam_pulang }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <span class="text-[8px] px-3 py-1.5 rounded-xl font-black uppercase {{ $absen->status == 'Hadir' ? 'bg-green-500 text-white' : 'bg-amber-100 text-amber-600' }}">
                        {{ $absen->status }}
                    </span>
                </div>
                @empty
                <div class="text-center bg-slate-50 border border-dashed border-slate-200 rounded-3xl py-10">
                    <p class="text-slate-400 text-sm italic font-medium">Belum ada absen masuk.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        function updateClock() {
            const now = new Date();
            document.getElementById('clock').innerText = now.toLocaleTimeString('id-ID', { hour12: false });
            document.getElementById('date').innerText = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        }
        setInterval(updateClock, 1000);
        updateClock();

        // KOORDINAT KANTOR PT SALTEK
        const KANTOR_LAT = 3.5952; 
        const KANTOR_LNG = 98.6722;
        const RADIUS_MAKS = 20; 

        const map = L.map('map', { zoomControl: false }).setView([KANTOR_LAT, KANTOR_LNG], 18);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        L.circle([KANTOR_LAT, KANTOR_LNG], { color: '#2563eb', fillColor: '#3b82f6', fillOpacity: 0.2, radius: RADIUS_MAKS }).addTo(map);
        L.marker([KANTOR_LAT, KANTOR_LNG]).addTo(map).bindPopup("Kantor PT Saltek");

        let userMarker;

        function hitungJarak(lat1, lon1, lat2, lon2) {
            const R = 6371e3; // Radius bumi dalam meter
            const p1 = lat1 * Math.PI/180;
            const p2 = lat2 * Math.PI/180;
            const dp = (lat2-lat1) * Math.PI/180;
            const dl = (lon2-lon1) * Math.PI/180;
            const a = Math.sin(dp/2) * Math.sin(dp/2) + Math.cos(p1) * Math.cos(p2) * Math.sin(dl/2) * Math.sin(dl/2);
            return R * (2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)));
        }

        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(function(pos) {
                const uLat = pos.coords.latitude;
                const uLng = pos.coords.longitude;
                const jarak = hitungJarak(uLat, uLng, KANTOR_LAT, KANTOR_LNG);
                
                const btn = document.getElementById('btn-absen');
                const info = document.getElementById('distance-info');

                // ISI VALUE INPUT LATITUDE & LONGITUDE FORM AGAR MASUK KE CONTROLLER
                document.querySelectorAll('.lat-input').forEach(input => input.value = uLat);
                document.querySelectorAll('.lng-input').forEach(input => input.value = uLng);

                if (userMarker) map.removeLayer(userMarker);
                userMarker = L.circleMarker([uLat, uLng], { radius: 7, color: 'white', fillColor: '#ef4444', fillOpacity: 1, weight: 2 }).addTo(map);
                
                if (btn) {
                    if (jarak <= RADIUS_MAKS) {
                        btn.disabled = false;
                        if (btn.innerText.includes('MASUK')) {
                            btn.className = "w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-5 rounded-2xl shadow-lg transition-all active:scale-95 text-lg";
                        } else {
                            btn.className = "w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-5 rounded-2xl shadow-lg transition-all active:scale-95 text-lg";
                        }
                        info.innerText = `📍 Lokasi Sesuai ✅ (${Math.round(jarak)}m)`;
                        info.className = "mb-4 p-2 rounded-xl bg-green-100 text-green-600 text-[10px] font-bold uppercase tracking-widest";
                    } else {
                        btn.disabled = true;
                        btn.className = "w-full bg-slate-300 opacity-50 cursor-not-allowed text-white font-bold py-5 rounded-2xl shadow-lg transition-all text-lg";
                        info.innerText = `📍 Jarak: ${Math.round(jarak)}m (Luar Area)`;
                        info.className = "mb-4 p-2 rounded-xl bg-red-50 text-red-500 text-[10px] font-bold uppercase tracking-widest";
                    }
                }
            }, function(err) {
                document.getElementById('distance-info').innerText = "❌ GPS Error: Berikan izin lokasi";
            }, { enableHighAccuracy: true });
        }
    </script>
</body>
</html>