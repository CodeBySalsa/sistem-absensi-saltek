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
        #map-preview { display: none; }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }
    </style>

    <div class="py-10 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="bg-rose-100 border-l-4 border-rose-500 text-rose-700 p-4 rounded-2xl mb-6 shadow-sm" role="alert">
                    <p class="font-bold text-xs uppercase mb-1">Terjadi Kesalahan:</p>
                    <ul class="text-xs">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Flash Message Sukses --}}
            @if(session('success'))
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: "{{ session('success') }}",
                        showConfirmButton: false,
                        timer: 2000
                    });
                </script>
            @endif

            {{-- 1. HERO BANNER (DENGAN LOGIKA PERUBAHAN WARNA) --}}
            @if(Auth::user()->role !== 'admin')
                @php
                    // Logika penentuan warna banner berdasarkan status absensi
                    $bannerGradient = 'custom-gradient'; // Default Ungu

                    if ($cekAbsensi && $cekAbsensi->jam_keluar) {
                        $bannerGradient = 'bg-gradient-to-br from-blue-600 to-indigo-800'; // Selesai (Biru)
                    } elseif ($cekAbsensi && in_array($cekAbsensi->status, ['Hadir', 'Terlambat'])) {
                        $bannerGradient = 'bg-gradient-to-br from-emerald-500 to-teal-700'; // Sudah Masuk (Hijau)
                    } elseif ($cekAbsensi && in_array($cekAbsensi->status, ['Izin', 'Sakit'])) {
                        $bannerGradient = 'bg-gradient-to-br from-amber-400 to-orange-600'; // Izin/Sakit (Oranye)
                    } elseif (now()->format('H:i') > '08:30' && !$cekAbsensi) {
                        $bannerGradient = 'bg-gradient-to-br from-rose-500 to-red-700'; // Telat Belum Absen (Merah)
                    }
                @endphp

                <div class="mb-6 {{ $bannerGradient }} rounded-[2rem] p-7 text-white shadow-lg shadow-indigo-100 relative overflow-hidden transition-all duration-500 hover:shadow-indigo-200">
                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-0.5 bg-white/20 backdrop-blur-md rounded-lg text-[9px] font-black uppercase tracking-widest">
                                @if($cekAbsensi && $cekAbsensi->jam_keluar) Tugas Selesai
                                @elseif($cekAbsensi) Sesi Aktif
                                @else Portal KKN @endif
                            </span>
                            <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
                        </div>
                        <h1 class="text-3xl font-black uppercase tracking-tighter italic">Halo, {{ Auth::user()->name }}!</h1>
                        <p class="text-white/90 mt-1 font-medium text-sm max-w-xl opacity-90">
                            @if($cekAbsensi && $cekAbsensi->jam_keluar)
                                Kerja bagus! Sampai jumpa di hari esok untuk KKN di PT Saltek.
                            @elseif($cekAbsensi)
                                Selamat bertugas! Jangan lupa kirim presensi pulang nanti ya.
                            @else
                                Selamat datang di Sistem Absensi Digital KKN PT Saltek. Jangan lupa presensi tepat waktu ya!
                            @endif
                        </p>
                    </div>
                    <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-white/10 rounded-full blur-3xl"></div>
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
                        @elseif($cekAbsensi && in_array($cekAbsensi->status, ['Izin', 'Sakit']))
                            <div class="w-16 h-16 bg-amber-400 rounded-2xl shadow-lg shadow-amber-200 flex items-center justify-center text-2xl text-white">✨</div>
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
                        @if($cekAbsensi && in_array($cekAbsensi->status, ['Izin', 'Sakit']))
                            <p class="text-[10px] font-bold text-amber-500 mt-1 uppercase tracking-tighter italic">Bebas Tugas Hari Ini</p>
                        @else
                            <p id="distance-info" class="text-[10px] font-bold text-indigo-500 mt-1 uppercase tracking-tighter italic">Mencari lokasi...</p>
                        @endif
                    </div>
                </div>
            </div>
            
            {{-- 3. ADMIN PANEL --}}
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

                <div class="bg-white rounded-[2.5rem] p-8 shadow-lg border-b-4 border-amber-500 overflow-hidden relative group">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 relative z-10">Status Kehadiran</p>
                    @if(!$cekAbsensi)
                        <form id="formIzinSakit" action="{{ route('absensi.izinSakit') }}" method="POST" class="space-y-2 relative z-10">
                            @csrf
                            <input type="hidden" name="status" id="status_input">
                            <input type="text" name="keterangan" id="keterangan_input" placeholder="Alasan izin/sakit..." required 
                                class="w-full text-xs border-slate-200 rounded-2xl py-3 px-4 bg-slate-50 focus:ring-amber-500">
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" onclick="konfirmasiStatus('Izin')" class="bg-amber-500 text-white font-black py-3 rounded-xl text-[10px] uppercase shadow-md transition-all active:scale-95">✋ Izin</button>
                                <button type="button" onclick="konfirmasiStatus('Sakit')" class="bg-rose-500 text-white font-black py-3 rounded-xl text-[10px] uppercase shadow-md transition-all active:scale-95">🤒 Sakit</button>
                            </div>
                        </form>
                    @else
                        <div class="relative z-10 flex flex-col items-center justify-center h-24">
                            <div class="w-12 h-12 bg-indigo-50 rounded-full flex items-center justify-center text-xl mb-2">✨</div>
                            <span class="text-xs font-black text-slate-700 uppercase tracking-tighter">Status: {{ $cekAbsensi->status }}</span>
                        </div>
                    @endif
                </div>

                {{-- Card Absensi Utama --}}
                <div class="relative group cursor-pointer" id="btn-absen-main" onclick="handleAbsensi()">
                    <div class="absolute -inset-1 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-[2.5rem] blur opacity-25 group-hover:opacity-40 transition"></div>
                    <div class="relative bg-white h-full rounded-[2.5rem] p-6 flex flex-col items-center justify-center border border-slate-100 shadow-lg text-center">
                        <div class="w-20 h-20 bg-indigo-50 rounded-3xl flex items-center justify-center text-4xl mb-4 animate-fade-in">
                            @if(!$cekAbsensi) 🚀 
                            @elseif(in_array($cekAbsensi->status, ['Izin', 'Sakit'])) 🏡
                            @elseif(!$cekAbsensi->jam_keluar) 🏁 
                            @else ✨ @endif
                        </div>
                        
                        <div id="map-preview"></div>

                        @if(!$cekAbsensi)
                            <h4 class="font-black text-slate-800 uppercase tracking-widest text-xs">Klik Untuk Absen Masuk</h4>
                            <p class="text-[10px] text-indigo-500 mt-2 uppercase font-bold italic">Kirim Lokasi Presensi Sekarang 📍</p>
                        @elseif(in_array($cekAbsensi->status, ['Izin', 'Sakit']))
                            <h4 class="font-black text-amber-500 uppercase tracking-widest text-xs">Sedang {{ $cekAbsensi->status }}</h4>
                            <p class="text-[10px] text-slate-400 mt-2 uppercase font-bold italic">Tidak Perlu Absen Pulang ✨</p>
                        @elseif(!$cekAbsensi->jam_keluar)
                            <h4 class="font-black text-rose-600 uppercase tracking-widest text-xs">Klik Untuk Absen Pulang</h4>
                            <p class="text-[10px] text-slate-400 mt-2 uppercase font-bold italic">Selesaikan tugas hari ini 👋</p>
                        @else
                            <h4 class="font-black text-emerald-500 uppercase tracking-widest text-xs">Presensi Selesai</h4>
                            <p class="text-[10px] text-slate-400 mt-2 uppercase font-bold italic">Sampai jumpa besok! ✨</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 5. TABEL AKTIVITAS MINGGUAN --}}
            <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-50 overflow-hidden animate-fade-in">
                <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
                    <h3 class="font-black text-slate-800 uppercase tracking-tight flex items-center gap-2 text-lg">
                        <span>⚡</span> Aktivitas Absensi Mingguan
                    </h3>
                </div>
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-slate-100">
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Tanggal</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Karyawan</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Jam Masuk</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Jam Pulang</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($recentActivities ?? [] as $activity)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-8 py-5 text-sm font-bold text-indigo-600">{{ \Carbon\Carbon::parse($activity->tanggal ?? $activity->created_at)->format('d/m/Y') }}</td>
                                <td class="px-8 py-5 text-sm font-bold text-slate-700">{{ $activity->user->name ?? 'Unknown' }}</td>
                                <td class="px-8 py-5 text-sm font-mono text-slate-500">{{ $activity->jam_masuk ?? '--:--:--' }}</td>
                                <td class="px-8 py-5 text-sm font-mono text-slate-400">{{ $activity->jam_keluar ?? '--:--:--' }}</td>
                                <td class="px-8 py-5">
                                    <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase {{ in_array($activity->status, ['Hadir', 'Selesai']) ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                                        {{ $activity->status }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-8 py-10 text-center text-slate-400 italic">Belum ada aktivitas dalam 7 hari terakhir.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
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
                        <p class="text-slate-400 text-sm font-medium mt-1">Mencatat lokasi dan waktu Anda secara otomatis.</p>
                    </div>

                    <form id="formUtamaAbsensi" action="{{ route('absensi.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="lat" id="lat">
                        <input type="hidden" name="lng" id="lng">
                        <button type="submit" id="btn-submit-modal" class="w-full bg-indigo-600 text-white font-black py-5 rounded-[1.5rem] shadow-xl uppercase tracking-widest active:scale-95 transition-all">
                            Kirim Presensi Sekarang
                        </button>
                    </form>
                    <button onclick="closeAbsensiModal()" class="w-full mt-4 text-slate-400 font-bold text-xs uppercase tracking-widest">Nanti Saja</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Koordinat PT Saltek
        const KANTOR_LAT = 3.5952; 
        const KANTOR_LNG = 98.6722; 
        const RADIUS_MAKS = 100; // Radius dalam meter
        let userLat, userLng, userInsideRadius = false;

        function updateClock() {
            const clock = document.getElementById('realtime-clock');
            if(clock) clock.textContent = new Date().toLocaleTimeString('id-ID', { hour12: false });
        }
        setInterval(updateClock, 1000);

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
                if(timerElement) timerElement.textContent = "00:00:00";
            }
        }
        setInterval(updateCountdown, 1000);

        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371e3;
            const dLat = (lat2-lat1) * Math.PI/180;
            const dLon = (lon2-lon1) * Math.PI/180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(lat1 * Math.PI/180) * Math.cos(lat2 * Math.PI/180) * Math.sin(dLon/2) * Math.sin(dLon/2);
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
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
                        if(distInfo) distInfo.innerHTML = `✅ Dalam Jangkauan (${Math.round(distance)}m)`;
                    } else {
                        userInsideRadius = false;
                        if(distInfo) distInfo.innerHTML = `📍 Jarak: ${Math.round(distance)}m (Maks: ${RADIUS_MAKS}m)`;
                    }
                }, (error) => {
                    console.error("Geolocation error:", error);
                }, { enableHighAccuracy: true });
            }
        }

        function handleAbsensi() {
            const statusSekarang = @json($cekAbsensi->status ?? '');
            if(['Izin', 'Sakit'].includes(statusSekarang)) {
                Swal.fire({ icon: 'info', title: 'Bebas Tugas', text: 'Anda sedang izin/sakit.', confirmButtonColor: '#f59e0b' });
                return;
            }

            const isSudahAbsenMasuk = @json($cekAbsensi ? true : false);
            const isSudahAbsenPulang = @json($cekAbsensi && $cekAbsensi->jam_keluar ? true : false);
            if(isSudahAbsenMasuk && isSudahAbsenPulang) return;

            if(!userInsideRadius) {
                Swal.fire({ icon: 'error', title: 'Diluar Jangkauan!', text: 'Anda harus di area PT Saltek.', confirmButtonColor: '#4f46e5' });
                return;
            }

            if(isSudahAbsenMasuk) {
                document.getElementById('formUtamaAbsensi').action = "{{ route('absensi.pulang') }}";
                document.getElementById('modal-title').innerText = "Konfirmasi Pulang";
            }
            openAbsensiModal();
        }

        function openAbsensiModal() {
            document.getElementById('absensiModal').classList.remove('hidden');
            setTimeout(() => document.getElementById('modalContent').classList.remove('translate-y-full'), 10);
        }

        function closeAbsensiModal() {
            document.getElementById('modalContent').classList.add('translate-y-full');
            setTimeout(() => document.getElementById('absensiModal').classList.add('hidden'), 500);
        }

        function konfirmasiStatus(status) {
            const keterangan = document.getElementById('keterangan_input').value;
            if(!keterangan) {
                Swal.fire('Isi Alasan', 'Tuliskan alasan izin/sakit.', 'warning');
                return;
            }
            document.getElementById('status_input').value = status;
            document.getElementById('formIzinSakit').submit();
        }

        initLocation();
    </script>
</x-app-layout>