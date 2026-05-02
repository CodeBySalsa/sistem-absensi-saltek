<div class="p-2">
    <!-- Card Utama dengan Gradasi Biru -->
    <div class="bg-gradient-to-br from-blue-600 via-indigo-700 to-indigo-900 rounded-[2.5rem] shadow-xl p-8 text-white relative overflow-hidden mb-6 border border-white/10">
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        
        <div class="text-center relative z-10">
            <span class="text-[10px] font-black text-blue-200 uppercase tracking-[0.3em] mb-2 block">PT Saltek Dumpang Jaya</span>
            <h2 class="text-2xl font-black tracking-tighter mb-6 uppercase">Presensi Digital</h2>

            <!-- Digital Clock -->
            <div class="bg-white/10 backdrop-blur-md rounded-3xl py-6 mb-6 border border-white/20 shadow-inner">
                <div id="modal-clock" class="text-5xl font-mono font-bold tracking-tighter text-white">00:00:00</div>
                <p class="text-[10px] text-blue-100 mt-2 uppercase font-black tracking-widest" id="modal-date">Memuat Tanggal...</p>
            </div>

            <!-- Tombol Absen -->
            @if(!$cekAbsensi)
                <form id="formAbsensi" action="{{ route('absensi.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="latitude" id="lat">
                    <input type="hidden" name="longitude" id="lng">
                    
                    <button type="button" onclick="submitAbsensi()" id="btn-absen" class="w-full bg-white text-indigo-900 font-black py-4 rounded-2xl shadow-lg hover:bg-blue-50 transition-all active:scale-95 flex items-center justify-center gap-3 uppercase tracking-wider text-sm">
                        <span>Kirim Kehadiran</span>
                        🚀
                    </button>
                </form>
            @else
                <div class="bg-green-500/20 border border-green-400/30 rounded-2xl py-4">
                    <p class="text-xs font-bold text-green-200 uppercase tracking-widest">✅ Absensi Berhasil Dikirim</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Map Section -->
    <div class="bg-white rounded-[2rem] p-2 shadow-sm border border-slate-100">
        <div id="map" style="height: 200px; width: 100%; border-radius: 1.5rem; z-index: 1;"></div>
        <div id="distance-info" class="mt-3 p-3 rounded-xl bg-slate-50 text-center">
            <p id="status-text" class="text-xs font-bold text-slate-700 italic">🛰️ Menghubungkan GPS...</p>
        </div>
    </div>
</div>

<script>
    // 1. Fungsi Jam & Tanggal Digital
    function updateModalClock() {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        
        const clockElement = document.getElementById('modal-clock');
        const dateElement = document.getElementById('modal-date');

        if(clockElement) {
            clockElement.innerText = now.toLocaleTimeString('en-GB');
        }
        if(dateElement) {
            dateElement.innerText = now.toLocaleDateString('id-ID', options);
        }
    }
    setInterval(updateModalClock, 1000);
    updateModalClock();

    // 2. Logika Map & Geolocation
    let map, marker;

    function initMap() {
        const statusText = document.getElementById('status-text');
        
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const userLat = position.coords.latitude;
                    const userLng = position.coords.longitude;

                    document.getElementById('lat').value = userLat;
                    document.getElementById('lng').value = userLng;

                    if (!map) {
                        map = L.map('map').setView([userLat, userLng], 17);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© OpenStreetMap'
                        }).addTo(map);
                        
                        marker = L.marker([userLat, userLng]).addTo(map)
                            .bindPopup("Lokasi Anda")
                            .openPopup();
                    } else {
                        map.setView([userLat, userLng], 17);
                        marker.setLatLng([userLat, userLng]);
                    }

                    statusText.innerText = "🛰️ GPS Terkunci (Lokasi Akurat)";
                    statusText.className = "text-xs font-bold text-green-600 italic";
                },
                function(error) {
                    let pesan = "❌ Gagal mengambil lokasi.";
                    if (error.code == 1) pesan = "❌ Akses GPS Ditolak!";
                    statusText.innerText = pesan;
                    statusText.className = "text-xs font-bold text-red-600 italic";
                },
                { enableHighAccuracy: true }
            );
        }
    }

    // Jalankan initMap saat file di-include
    setTimeout(initMap, 500);

    // 3. Fungsi Submit
    function submitAbsensi() {
        const lat = document.getElementById('lat').value;
        if (!lat) {
            Swal.fire({
                icon: 'warning',
                title: 'GPS Belum Siap',
                text: 'Tunggu sebentar sampai lokasi terdeteksi di peta.',
                confirmButtonColor: '#3b82f6'
            });
            return;
        }

        Swal.fire({
            title: 'Kirim Presensi?',
            text: "Lokasi Anda saat ini akan dicatat ke sistem PT Saltek.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1e40af',
            confirmButtonText: 'Ya, Kirim',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Mengirim...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                document.getElementById('formAbsensi').submit();
            }
        });
    }
</script>