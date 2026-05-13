<div class="p-2">
    <style>
        /* CSS Tombol Fokus agar muncul di depan peta */
        .leaflet-fokus-btn {
            background: #4f46e5 !important;
            color: white !important;
            border: none !important;
            width: 42px !important;
            height: 42px !important;
            border-radius: 12px !important;
            font-size: 18px !important;
            font-weight: bold !important;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3) !important;
            cursor: pointer !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .leaflet-fokus-btn:hover {
            transform: scale(1.05);
        }
    </style>

    <div class="max-h-[80vh] overflow-y-auto pr-1 custom-scrollbar">
        <!-- Card Utama Presensi PT Saltek Dumpang Jaya -->
        <div class="bg-gradient-to-br from-indigo-600 via-blue-700 to-slate-900 rounded-[2.5rem] shadow-2xl p-8 text-white relative overflow-hidden mb-6 border border-white/10">
            <div class="text-center relative z-10">
                <div class="flex items-center justify-center gap-2 mb-2">
                    <span class="text-[10px] font-black text-blue-100 uppercase tracking-[0.4em]">PT Saltek Dumpang Jaya</span>
                </div>
                <h2 class="text-3xl font-black tracking-tighter mb-6 uppercase italic">Presensi Digital</h2>

                <div class="bg-white/10 backdrop-blur-xl rounded-[2rem] py-8 mb-6 border border-white/20">
                    <div id="modal-clock" class="text-6xl font-mono font-bold text-white">00:00:00</div>
                    <div id="modal-date" class="text-[11px] text-blue-100 uppercase font-black mt-2">Memuat...</div>
                </div>

                <div class="relative z-20">
                    @if(!$cekAbsensi)
                        <form id="formAbsensi" action="{{ route('absensi.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="latitude" id="lat-input-masuk">
                            <input type="hidden" name="longitude" id="lng-input-masuk">
                            <button type="button" onclick="submitAbsensi('masuk')" class="w-full bg-white text-indigo-900 font-black py-5 rounded-2xl shadow-xl uppercase text-sm">Kirim Kehadiran 🚀</button>
                        </form>
                    @elseif(($cekAbsensi->status == 'Hadir' || $cekAbsensi->status == 'Terlambat') && $cekAbsensi->jam_keluar == null)
                        <form id="formPulang" action="{{ route('absensi.pulang') }}" method="POST">
                            @csrf
                            <input type="hidden" name="latitude" id="lat-input-pulang">
                            <input type="hidden" name="longitude" id="lng-input-pulang">
                            <button type="button" onclick="submitAbsensi('pulang')" class="w-full bg-gradient-to-r from-orange-500 to-rose-500 text-white font-black py-5 rounded-2xl shadow-lg uppercase text-sm">Absen Pulang 🏠</button>
                        </form>
                    @else
                        <div class="bg-slate-500/30 rounded-2xl py-6 text-center">
                            <span class="text-sm font-black text-slate-200 uppercase">Selesai Untuk Hari Ini ✨</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Bagian Peta -->
        <div class="bg-white rounded-[2.5rem] p-3 shadow-xl border border-slate-100 mb-6">
            <div class="flex items-center justify-between px-4 mb-3">
                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Live GPS Tracking</h4>
                <div id="status-badge" class="px-2 py-1 bg-slate-100 rounded-full text-[9px] font-bold text-slate-400">Connecting...</div>
            </div>
            
            <div id="map-container-wrapper" class="relative w-full overflow-hidden" style="border-radius: 1.8rem; height: 250px;">
                <div id="map-panel" style="height: 100%; width: 100%; z-index: 1;"></div>
            </div>
            
            <div class="mt-4 p-4 rounded-2xl bg-slate-50 text-center">
                <p id="status-text-panel" class="text-xs font-bold text-slate-500 italic">Mencari titik koordinat...</p>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. Logika Jam Digital
    function updatePanelClock() {
        const now = new Date();
        const clockEl = document.getElementById('modal-clock');
        const dateEl = document.getElementById('modal-date');

        if(clockEl) {
            clockEl.innerText = now.toLocaleTimeString('id-ID', {
                hour12: false
            });
        }

        if(dateEl) {
            dateEl.innerText = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }
    }

    setInterval(updatePanelClock, 1000);

    let panelMap, panelMarker;
    let pLat = null, pLng = null;

    // 2. Fungsi Fokus Kembali
    function manualRecenter() {
        if (panelMap && pLat && pLng) {
            panelMap.setView([pLat, pLng], 17, {
                animate: true
            });
        }
    }

    // 3. Inisialisasi Peta
    function initPanelMap() {

        if (navigator.geolocation) {

            navigator.geolocation.watchPosition(

                function(position) {

                    pLat = position.coords.latitude;
                    pLng = position.coords.longitude;

                    document.querySelectorAll('[id^="lat-input-"]').forEach(el => el.value = pLat);
                    document.querySelectorAll('[id^="lng-input-"]').forEach(el => el.value = pLng);

                    if (!panelMap) {

                        panelMap = L.map('map-panel').setView([pLat, pLng], 17);

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; OpenStreetMap'
                        }).addTo(panelMap);

                        panelMarker = L.marker([pLat, pLng]).addTo(panelMap);

                        // =========================
                        // TOMBOL FOKUS LOKASI
                        // =========================
                        const focusButton = L.control({
                            position: 'bottomright'
                        });

                        focusButton.onAdd = function(map) {

                            const div = L.DomUtil.create('div');

                            div.innerHTML = `
                                <button 
                                    class="leaflet-fokus-btn"
                                    onclick="manualRecenter()"
                                    title="Kembali ke lokasi saya">
                                    📍
                                </button>
                            `;

                            return div;
                        };

                        focusButton.addTo(panelMap);

                    } else {

                        panelMarker.setLatLng([pLat, pLng]);

                    }

                    document.getElementById('status-text-panel').innerText = "🛰️ GPS Aktif (Terdeteksi)";
                    document.getElementById('status-badge').innerText = "ONLINE";

                    document.getElementById('status-badge').className =
                        "px-2 py-1 bg-emerald-100 rounded-full text-[9px] font-bold text-emerald-600";

                },

                function(error) {

                    document.getElementById('status-text-panel').innerText =
                        "❌ GPS Error: " + error.message;

                },

                {
                    enableHighAccuracy: true
                }
            );
        }
    }

    // Tunggu modal render
    setTimeout(initPanelMap, 600);

    // 4. Submit Absensi
    function submitAbsensi(tipe) {

        Swal.fire({
            title: 'Kirim Presensi?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            confirmButtonText: 'Ya, Kirim'

        }).then((result) => {

            if (result.isConfirmed) {

                document.getElementById(
                    tipe === 'masuk' ? 'formAbsensi' : 'formPulang'
                ).submit();

            }
        });
    }
</script>