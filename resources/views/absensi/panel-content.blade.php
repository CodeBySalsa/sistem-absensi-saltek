<div class="p-2">
    <!-- PEMBUNGKUS SCROLL: Agar modal bisa di-scroll ke atas/bawah -->
    <div class="max-h-[80vh] overflow-y-auto pr-1 custom-scrollbar">
        
        <!-- Card Utama dengan Gradasi Biru yang Dipercantik -->
        <div class="bg-gradient-to-br from-indigo-600 via-blue-700 to-slate-900 rounded-[2.5rem] shadow-2xl p-8 text-white relative overflow-hidden mb-6 border border-white/10">
            <!-- Dekorasi Cahaya -->
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-indigo-500/20 rounded-full blur-2xl"></div>
            
            <div class="text-center relative z-10">
                <div class="flex items-center justify-center gap-2 mb-2">
                    <span class="w-2 h-2 bg-blue-300 rounded-full animate-pulse"></span>
                    <span class="text-[10px] font-black text-blue-100 uppercase tracking-[0.4em]">PT Saltek Dumpang Jaya</span>
                </div>
                <h2 class="text-3xl font-black tracking-tighter mb-6 uppercase italic">Presensi Digital</h2>

                <!-- Digital Clock Box -->
                <div class="bg-white/10 backdrop-blur-xl rounded-[2rem] py-8 mb-6 border border-white/20 shadow-[inset_0_2px_10px_rgba(255,255,255,0.1)]">
                    <div id="modal-clock" class="text-6xl font-mono font-bold tracking-tighter text-white drop-shadow-lg">00:00:00</div>
                    <div class="flex items-center justify-center gap-2 mt-2">
                        <span class="text-[11px] text-blue-100 uppercase font-black tracking-widest" id="modal-date">Memuat Tanggal...</span>
                    </div>
                </div>

                <!-- Logika Tombol Absen -->
                <div class="relative z-20">
                    @if(!$cekAbsensi)
                        <form id="formAbsensi" action="{{ route('absensi.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="latitude" class="lat-input">
                            <input type="hidden" name="longitude" class="lng-input">
                            
                            <button type="button" onclick="submitAbsensi('masuk')" id="btn-absen" class="w-full bg-white text-indigo-900 font-black py-5 rounded-2xl shadow-[0_10px_20px_rgba(0,0,0,0.2)] hover:bg-indigo-50 transition-all active:scale-95 flex items-center justify-center gap-3 uppercase tracking-[0.1em] text-sm group">
                                <span class="group-hover:translate-x-1 transition-transform">Kirim Kehadiran</span> 
                                <span class="text-xl group-hover:animate-bounce">🚀</span>
                            </button>
                        </form>
                    @elseif($cekAbsensi->status == 'Hadir' || $cekAbsensi->status == 'Terlambat')
                        @php
                            $jamSekarang = \Carbon\Carbon::now()->format('H:i');
                            $jamPulang = '16:00'; 
                        @endphp

                        @if($jamSekarang < $jamPulang)
                            <div class="bg-emerald-500/20 backdrop-blur-md border border-emerald-400/40 rounded-2xl py-5 mb-3">
                                <p class="text-sm font-black text-emerald-300 uppercase tracking-widest flex items-center justify-center gap-2">
                                    <span class="text-lg">✅</span> Absen {{ $cekAbsensi->status }} Berhasil
                                </p>
                            </div>
                            <div class="flex items-center justify-center gap-1.5 opacity-70">
                                <span class="w-1.5 h-1.5 bg-amber-400 rounded-full animate-ping"></span>
                                <p class="text-[10px] text-blue-100 uppercase font-bold tracking-tighter italic">Tombol pulang aktif pukul {{ $jamPulang }}</p>
                            </div>
                        @else
                            <form id="formPulang" action="{{ route('absensi.pulang', $cekAbsensi->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="latitude" class="lat-input">
                                <input type="hidden" name="longitude" class="lng-input">

                                <button type="button" onclick="submitAbsensi('pulang')" class="w-full bg-gradient-to-r from-orange-500 to-rose-500 text-white font-black py-5 rounded-2xl shadow-lg hover:shadow-orange-500/40 transition-all active:scale-95 flex items-center justify-center gap-3 uppercase tracking-[0.1em] text-sm group">
                                    <span class="group-hover:-translate-y-1 transition-transform">Absen Pulang</span>
                                    <span class="text-xl group-hover:rotate-12 transition-transform">🏠</span>
                                </button>
                            </form>
                        @endif
                    @elseif($cekAbsensi->status == 'Selesai')
                        <div class="bg-slate-500/30 backdrop-blur-md border border-slate-400/30 rounded-2xl py-6">
                            <p class="text-sm font-black text-slate-200 uppercase tracking-widest flex items-center justify-center gap-2">
                                <span class="text-xl">✨</span> Tugas Selesai. Selamat Istirahat!
                            </p>
                        </div>
                    @else
                        <div class="bg-amber-500/30 backdrop-blur-md border border-amber-400/30 rounded-2xl py-5">
                            <p class="text-sm font-black text-amber-200 uppercase tracking-widest flex items-center justify-center gap-2">
                                <span class="text-lg">ℹ️</span> Status: {{ $cekAbsensi->status }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Map Section yang Lebih Luas -->
        <div class="bg-white rounded-[2.5rem] p-3 shadow-xl border border-slate-100 mb-6">
            <div class="flex items-center justify-between px-4 mb-3">
                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Live Tracking Lokasi</h4>
                <div id="status-badge" class="px-2 py-1 bg-slate-100 rounded-full text-[9px] font-bold text-slate-400 uppercase tracking-tighter">GPS Connecting...</div>
            </div>
            <div id="map" style="height: 260px; width: 100%; border-radius: 1.8rem; z-index: 1; border: 1px solid #f1f5f9;"></div>
            <div id="distance-info" class="mt-4 p-4 rounded-2xl bg-slate-50 border border-slate-100 text-center shadow-inner">
                <p id="status-text" class="text-xs font-bold text-slate-700 italic tracking-tight">🛰️ Menghubungkan satelit GPS...</p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling scrollbar agar lebih tipis dan modern */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #cbd5e1;
    }
</style>

<script>
    // 1. Fungsi Jam & Tanggal Digital
    function updateModalClock() {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        
        const clockElement = document.getElementById('modal-clock');
        const dateElement = document.getElementById('modal-date');

        if(clockElement) clockElement.innerText = now.toLocaleTimeString('en-GB');
        if(dateElement) dateElement.innerText = now.toLocaleDateString('id-ID', options);
    }
    setInterval(updateModalClock, 1000);
    updateModalClock();

    // 2. Logika Map & Geolocation
    let map, marker;
    let currentLat = null;
    let currentLng = null;

    function initMap() {
        const statusText = document.getElementById('status-text');
        const statusBadge = document.getElementById('status-badge');

        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(
                function(position) {
                    currentLat = position.coords.latitude;
                    currentLng = position.coords.longitude;

                    document.querySelectorAll('.lat-input').forEach(el => el.value = currentLat);
                    document.querySelectorAll('.lng-input').forEach(el => el.value = currentLng);

                    if (!map) {
                        map = L.map('map').setView([currentLat, currentLng], 17);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© OpenStreetMap'
                        }).addTo(map);
                        marker = L.marker([currentLat, currentLng]).addTo(map).bindPopup("Lokasi Anda").openPopup();
                    } else {
                        map.setView([currentLat, currentLng], 17);
                        marker.setLatLng([currentLat, currentLng]);
                    }
                    
                    statusText.innerText = "🛰️ GPS Terkunci (Lokasi Akurat)";
                    statusText.className = "text-xs font-bold text-emerald-600 italic";
                    statusBadge.innerText = "Active";
                    statusBadge.className = "px-2 py-1 bg-emerald-100 rounded-full text-[9px] font-bold text-emerald-600 uppercase tracking-tighter";
                },
                function(error) {
                    statusText.innerText = "❌ Akses GPS Ditolak/Gagal.";
                    statusText.className = "text-xs font-bold text-rose-600 italic";
                    statusBadge.innerText = "Error";
                    statusBadge.className = "px-2 py-1 bg-rose-100 rounded-full text-[9px] font-bold text-rose-600 uppercase tracking-tighter";
                },
                { enableHighAccuracy: true }
            );
        }
    }
    setTimeout(initMap, 500);

    function submitAbsensi(tipe) {
        if (!currentLat || !currentLng) {
            Swal.fire({ icon: 'warning', title: 'GPS Belum Siap', text: 'Tunggu lokasi terdeteksi di peta.', confirmButtonColor: '#4f46e5' });
            return;
        }

        const config = {
            title: tipe === 'masuk' ? 'Kirim Presensi Masuk?' : 'Kirim Presensi Pulang?',
            text: "Pastikan Anda sudah berada di area kantor.",
            formId: tipe === 'masuk' ? 'formAbsensi' : 'formPulang'
        };

        Swal.fire({
            title: config.title,
            text: config.text,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: tipe === 'masuk' ? '#4f46e5' : '#f43f5e',
            confirmButtonText: 'Ya, Kirim',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-[2rem]',
                confirmButton: 'rounded-xl px-6 py-3',
                cancelButton: 'rounded-xl px-6 py-3'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Memverifikasi...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                document.getElementById(config.formId).submit();
            }
        });
    }
</script>