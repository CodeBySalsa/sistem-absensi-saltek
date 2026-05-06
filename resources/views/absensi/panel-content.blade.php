<div class="p-2">
    <!-- PEMBUNGKUS SCROLL (Agar tidak merusak layout dashboard utama) -->
    <div class="max-h-[80vh] overflow-y-auto pr-1 custom-scrollbar">
        
        <!-- Card Utama Presensi -->
        <div class="bg-gradient-to-br from-indigo-600 via-blue-700 to-slate-900 rounded-[2.5rem] shadow-2xl p-8 text-white relative overflow-hidden mb-6 border border-white/10">
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
                        {{-- KONDISI 1: BELUM ABSEN SAMA SEKALI --}}
                        <form id="formAbsensi" action="{{ route('absensi.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="latitude" id="lat-input-masuk">
                            <input type="hidden" name="longitude" id="lng-input-masuk">
                            
                            <button type="button" onclick="submitAbsensi('masuk')" class="w-full bg-white text-indigo-900 font-black py-5 rounded-2xl shadow-xl hover:bg-indigo-50 transition-all active:scale-95 flex items-center justify-center gap-3 uppercase tracking-[0.1em] text-sm group">
                                <span class="group-hover:translate-x-1 transition-transform">Kirim Kehadiran</span> 
                                <span class="text-xl group-hover:animate-bounce">🚀</span>
                            </button>
                        </form>

                    @elseif(($cekAbsensi->status == 'Hadir' || $cekAbsensi->status == 'Terlambat') && $cekAbsensi->jam_keluar == null)
                        {{-- KONDISI 2: SUDAH MASUK, TUNGGU JAM PULANG --}}
                        @php
                            $jamSekarang = \Carbon\Carbon::now('Asia/Jakarta')->format('H:i');
                            $jamPulang = '16:00'; 
                        @endphp

                        @if($jamSekarang < $jamPulang)
                            <div class="bg-emerald-500/20 backdrop-blur-md border border-emerald-400/40 rounded-2xl py-5 mb-3">
                                <p class="text-sm font-black text-emerald-300 uppercase tracking-widest flex items-center justify-center gap-2">
                                    <span class="text-lg">✅</span> {{ $cekAbsensi->status }} Telah Dicatat
                                </p>
                            </div>
                            <div class="flex items-center justify-center gap-1.5 opacity-70">
                                <span class="w-1.5 h-1.5 bg-amber-400 rounded-full animate-ping"></span>
                                <p class="text-[10px] text-blue-100 uppercase font-bold tracking-tighter italic">Tombol pulang aktif pukul {{ $jamPulang }}</p>
                            </div>
                        @else
                            <form id="formPulang" action="{{ route('absensi.pulang') }}" method="POST">
                                @csrf
                                <input type="hidden" name="latitude" id="lat-input-pulang">
                                <input type="hidden" name="longitude" id="lng-input-pulang">

                                <button type="button" onclick="submitAbsensi('pulang')" class="w-full bg-gradient-to-r from-orange-500 to-rose-500 text-white font-black py-5 rounded-2xl shadow-lg hover:shadow-orange-500/40 transition-all active:scale-95 flex items-center justify-center gap-3 uppercase tracking-[0.1em] text-sm group">
                                    <span class="group-hover:-translate-y-1 transition-transform">Absen Pulang</span>
                                    <span class="text-xl group-hover:rotate-12 transition-transform">🏠</span>
                                </button>
                            </form>
                        @endif

                    @else
                        {{-- KONDISI 3: SELESAI ATAU STATUS LAIN --}}
                        <div class="bg-slate-500/30 backdrop-blur-md border border-slate-400/30 rounded-2xl py-6">
                            <p class="text-sm font-black text-slate-200 uppercase tracking-widest flex flex-col items-center justify-center gap-2 text-center">
                                <span class="text-3xl mb-2">✨</span> 
                                <span>Selesai untuk hari ini</span>
                                <span class="text-[10px] text-slate-400">Sampai Jumpa Besok!</span>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Bagian Peta -->
        <div class="bg-white rounded-[2.5rem] p-3 shadow-xl border border-slate-100 mb-6">
            <div class="flex items-center justify-between px-4 mb-3">
                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Live GPS Tracking</h4>
                <div id="status-badge" class="px-2 py-1 bg-slate-100 rounded-full text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Connecting...</div>
            </div>
            
            <div id="map-panel" style="height: 250px; width: 100%; border-radius: 1.8rem; z-index: 1;"></div>
            
            <div class="mt-4 p-4 rounded-2xl bg-slate-50 border border-slate-100 text-center">
                <p id="status-text-panel" class="text-xs font-bold text-slate-500 italic">Mencari titik koordinat...</p>
            </div>
        </div>
    </div>
</div>

<script>
    // Pastikan fungsi ini tidak bentrok jika dipanggil berkali-kali
    if (typeof updatePanelClock === 'undefined') {
        function updatePanelClock() {
            const now = new Date();
            const clockEl = document.getElementById('modal-clock');
            const dateEl = document.getElementById('modal-date');
            if(clockEl) clockEl.innerText = now.toLocaleTimeString('id-ID', { hour12: false });
            if(dateEl) dateEl.innerText = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        }
        setInterval(updatePanelClock, 1000);
    }

    let panelMap, panelMarker;
    let pLat = null, pLng = null;

    function initPanelMap() {
        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(
                function(position) {
                    pLat = position.coords.latitude;
                    pLng = position.coords.longitude;

                    // Update input hidden
                    ['masuk', 'pulang'].forEach(tipe => {
                        let latEl = document.getElementById(`lat-input-${tipe}`);
                        let lngEl = document.getElementById(`lng-input-${tipe}`);
                        if(latEl) latEl.value = pLat;
                        if(lngEl) lngEl.value = pLng;
                    });

                    if (!panelMap) {
                        panelMap = L.map('map-panel').setView([pLat, pLng], 17);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(panelMap);
                        panelMarker = L.marker([pLat, pLng]).addTo(panelMap);
                    } else {
                        panelMap.setView([pLat, pLng], 17);
                        panelMarker.setLatLng([pLat, pLng]);
                    }

                    document.getElementById('status-text-panel').innerText = "🛰️ GPS Aktif (Radius 20m Berlaku)";
                    document.getElementById('status-text-panel').className = "text-xs font-bold text-emerald-600 italic";
                    document.getElementById('status-badge').innerText = "ONLINE";
                    document.getElementById('status-badge').className = "px-2 py-1 bg-emerald-100 rounded-full text-[9px] font-bold text-emerald-600 uppercase tracking-tighter";
                },
                function(error) {
                    document.getElementById('status-text-panel').innerText = "❌ GPS Error: " + error.message;
                },
                { enableHighAccuracy: true }
            );
        }
    }

    // Jalankan init map setelah konten dimuat
    setTimeout(initPanelMap, 300);

    function submitAbsensi(tipe) {
        const checkLat = document.getElementById(`lat-input-${tipe}`).value;
        
        if (!checkLat || checkLat === "") {
            Swal.fire({ icon: 'warning', title: 'Lokasi Belum Terdeteksi', text: 'Tunggu ikon GPS di peta muncul sebelum klik absen.' });
            return;
        }

        Swal.fire({
            title: tipe === 'masuk' ? 'Konfirmasi Masuk?' : 'Konfirmasi Pulang?',
            text: "Pastikan Anda berada di area kantor PT Saltek Dumpang Jaya.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            confirmButtonText: 'Ya, Kirim',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Menghitung Jarak...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                document.getElementById(tipe === 'masuk' ? 'formAbsensi' : 'formPulang').submit();
            }
        });
    }
</script>