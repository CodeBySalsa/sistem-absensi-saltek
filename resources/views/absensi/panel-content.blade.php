<div class="p-2">
    <!-- PEMBUNGKUS SCROLL: Agar modal bisa di-scroll ke atas/bawah -->
    <div class="max-h-[75vh] overflow-y-auto pr-1 custom-scrollbar">
        
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

                <!-- Logika Tombol Absen -->
                <div class="relative z-20">
                    @if(!$cekAbsensi)
                        <form id="formAbsensi" action="{{ route('absensi.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="latitude" class="lat-input">
                            <input type="hidden" name="longitude" class="lng-input">
                            
                            <button type="button" onclick="submitAbsensi('masuk')" id="btn-absen" class="w-full bg-white text-indigo-900 font-black py-4 rounded-2xl shadow-lg hover:bg-blue-50 transition-all active:scale-95 flex items-center justify-center gap-3 uppercase tracking-wider text-sm">
                                <span>Kirim Kehadiran</span> 🚀
                            </button>
                        </form>
                    @elseif($cekAbsensi->status == 'Hadir' || $cekAbsensi->status == 'Terlambat')
                        @php
                            $jamSekarang = \Carbon\Carbon::now()->format('H:i');
                            $jamPulang = '16:00'; 
                        @endphp

                        @if($jamSekarang < $jamPulang)
                            <div class="bg-green-500/20 border border-green-400/30 rounded-2xl py-4 mb-2">
                                <p class="text-xs font-bold text-green-200 uppercase tracking-widest">✅ Absen {{ $cekAbsensi->status }} Berhasil</p>
                            </div>
                            <p class="text-[9px] text-blue-200 uppercase font-bold tracking-tighter italic">* Tombol pulang aktif otomatis pukul {{ $jamPulang }}</p>
                        @else
                            <form id="formPulang" action="{{ route('absensi.pulang', $cekAbsensi->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="latitude" class="lat-input">
                                <input type="hidden" name="longitude" class="lng-input">

                                <button type="button" onclick="submitAbsensi('pulang')" class="w-full bg-orange-500 text-white font-black py-4 rounded-2xl shadow-lg hover:bg-orange-600 transition-all active:scale-95 flex items-center justify-center gap-3 uppercase tracking-wider text-sm">
                                    <span>Absen Pulang</span> 🏠
                                </button>
                            </form>
                        @endif
                    @elseif($cekAbsensi->status == 'Selesai')
                        <div class="bg-slate-500/20 border border-slate-400/30 rounded-2xl py-4">
                            <p class="text-xs font-bold text-slate-200 uppercase tracking-widest">✨ Tugas Selesai. Selamat Istirahat!</p>
                        </div>
                    @else
                        <div class="bg-amber-500/20 border border-amber-400/30 rounded-2xl py-4">
                            <p class="text-xs font-bold text-amber-200 uppercase tracking-widest">ℹ️ Status: {{ $cekAbsensi->status }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Map Section -->
        <div class="bg-white rounded-[2rem] p-2 shadow-sm border border-slate-100 mb-4">
            <div id="map" style="height: 220px; width: 100%; border-radius: 1.5rem; z-index: 1;"></div>
            <div id="distance-info" class="mt-3 p-3 rounded-xl bg-slate-50 text-center">
                <p id="status-text" class="text-xs font-bold text-slate-700 italic">🛰️ Menghubungkan GPS...</p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling scrollbar agar lebih tipis dan rapi */
    .custom-scrollbar::-webkit-scrollbar {
        width: 5px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
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
                    statusText.className = "text-xs font-bold text-green-600 italic";
                },
                function(error) {
                    statusText.innerText = "❌ Akses GPS Ditolak/Gagal.";
                    statusText.className = "text-xs font-bold text-red-600 italic";
                },
                { enableHighAccuracy: true }
            );
        }
    }
    setTimeout(initMap, 500);

    function submitAbsensi(tipe) {
        if (!currentLat || !currentLng) {
            Swal.fire({ icon: 'warning', title: 'GPS Belum Siap', text: 'Tunggu lokasi terdeteksi di peta.', confirmButtonColor: '#3b82f6' });
            return;
        }

        const config = {
            title: tipe === 'masuk' ? 'Kirim Presensi Masuk?' : 'Kirim Presensi Pulang?',
            text: "Pastikan Anda berada di area PT Saltek.",
            formId: tipe === 'masuk' ? 'formAbsensi' : 'formPulang'
        };

        Swal.fire({
            title: config.title,
            text: config.text,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: tipe === 'masuk' ? '#1e40af' : '#f97316',
            confirmButtonText: 'Ya, Kirim',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Mengirim...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                document.getElementById(config.formId).submit();
            }
        });
    }
</script>