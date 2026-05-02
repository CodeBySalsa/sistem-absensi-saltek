<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard Monitoring - PT Saltek') }}
            </h2>
            <div class="flex items-center gap-3">
                <div id="countdown-area" class="text-sm font-bold text-white bg-slate-900 px-4 py-2 rounded-2xl border border-slate-700 shadow-sm">
                    <span class="text-[10px] text-slate-400 uppercase tracking-widest mr-1">Batas Absen:</span>
                    <span id="timer" class="font-mono text-yellow-400">--:--:--</span>
                </div>
                
                <div id="realtime-clock" class="text-sm font-bold text-blue-600 bg-blue-50 px-4 py-2 rounded-2xl border border-blue-100 shadow-sm">
                    {{-- Jam otomatis muncul di sini --}}
                </div>
            </div>
        </div>
    </x-slot>

    {{-- Library Tambahan --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <style>
        .swal2-popup { border-radius: 24px !important; font-family: ui-sans-serif, system-ui, -apple-system, sans-serif !important; }
        .animate-pulse-slow { animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: .7; } }
        #map { height: 200px; width: 100%; border-radius: 1.5rem; margin-top: 1rem; z-index: 1; }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Indikator Lampu Kehadiran --}}
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 mb-6">
                <div class="flex items-center gap-6">
                    <div class="relative">
                        @if($cekAbsensi && ($cekAbsensi->status == 'Hadir' || $cekAbsensi->status == 'Selesai'))
                            <div class="w-14 h-14 bg-green-500 rounded-full shadow-[0_0_20px_rgba(34,197,94,0.6)] animate-pulse"></div>
                            <div class="absolute -top-1 -right-1 bg-white rounded-full p-1 shadow-sm text-xs">✅</div>
                        @elseif(now()->format('H:i') > '08:30')
                            <div class="w-14 h-14 bg-red-500 rounded-full shadow-[0_0_20px_rgba(239,68,68,0.6)] animate-pulse"></div>
                            <div class="absolute -top-1 -right-1 bg-white rounded-full p-1 shadow-sm text-xs">❌</div>
                        @else
                            <div class="w-14 h-14 bg-yellow-400 rounded-full shadow-[0_0_20px_rgba(250,204,21,0.6)] animate-pulse"></div>
                            <div class="absolute -top-1 -right-1 bg-white rounded-full p-1 shadow-sm text-xs">⏳</div>
                        @endif
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Status Real-time Anda</p>
                        <h3 class="text-xl font-black text-slate-800 uppercase tracking-tighter">
                            @if($cekAbsensi && ($cekAbsensi->status == 'Hadir' || $cekAbsensi->status == 'Selesai'))
                                Presensi Berhasil Dikirim
                            @elseif(now()->format('H:i') > '08:30')
                                Batas Waktu Terlewati (Terlambat)
                            @else
                                Menunggu Absensi Masuk
                            @endif
                        </h3>
                    </div>
                </div>
            </div>
            
            {{-- Bagian Khusus Admin --}}
            @if(Auth::user()->role == 'admin')
            <div class="mb-8 bg-gradient-to-r from-blue-800 to-indigo-900 overflow-hidden shadow-lg sm:rounded-3xl p-8 text-white relative">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-center">
                    <div class="text-center md:text-left mb-6 md:mb-0">
                        <div class="flex items-center justify-center md:justify-start gap-2 mb-2">
                            <span class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></span>
                            <h3 class="text-2xl font-black tracking-tight uppercase">Panel Kontrol Administrator</h3>
                        </div>
                        <p class="text-blue-200 mt-1 font-medium">Kelola data karyawan KKN dan monitoring sistem PT Saltek.</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('karyawan.index') }}" class="bg-blue-600 border border-blue-400 text-white font-bold py-3 px-6 rounded-2xl hover:bg-blue-700 transition-all shadow-xl active:scale-95 text-sm uppercase tracking-wider">
                            Lihat Data Karyawan
                        </a>
                        <a href="{{ route('karyawan.create') }}" class="bg-white text-blue-900 font-bold py-3 px-6 rounded-2xl hover:bg-blue-50 transition-all shadow-xl active:scale-95 text-sm uppercase tracking-wider">
                            + Tambah Karyawan
                        </a>
                    </div>
                </div>
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white opacity-10 rounded-full blur-3xl"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex items-center gap-5 transition-transform hover:scale-[1.02]">
                    <div class="h-12 w-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white text-xl shadow-lg shadow-blue-100">👥</div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Anggota</p>
                        <h3 class="text-2xl font-black text-slate-800">{{ $totalKaryawan ?? 0 }}</h3>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex items-center gap-5 transition-transform hover:scale-[1.02]">
                    <div class="h-12 w-12 bg-green-500 rounded-2xl flex items-center justify-center text-white text-xl shadow-lg shadow-green-100">✅</div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Hadir Hari Ini</p>
                        <h3 class="text-2xl font-black text-slate-800">{{ $hadirHariIni ?? 0 }}</h3>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex items-center gap-5 transition-transform hover:scale-[1.02]">
                    <div class="h-12 w-12 bg-amber-500 rounded-2xl flex items-center justify-center text-white text-xl shadow-lg shadow-amber-100">⚠️</div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Izin / Sakit</p>
                        <h3 class="text-2xl font-black text-slate-800">{{ $izinSakit ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            @endif

            {{-- Stats Cards Umum/User --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border-b-4 border-blue-500 transition-all hover:shadow-md flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Hadir Anda</p>
                            <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $totalHadir ?? 0 }} Hari</h3>
                        </div>
                        <div class="bg-blue-50 p-3 rounded-xl text-blue-500 text-xl">📅</div>
                    </div>
                    <p class="text-xs text-blue-400 mt-4 font-semibold italic">* Terhitung periode KKN di PT Saltek</p>
                </div>

                {{-- FORM IZIN / SAKIT --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border-b-4 border-amber-500 transition-all hover:shadow-md flex flex-col justify-between">
                    <div>
                        <div class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Form Izin / Sakit</div>
                        @if($cekAbsensi)
                            <div class="bg-slate-50 rounded-xl p-4 text-center border border-slate-100">
                                <span class="text-2xl">✅</span>
                                <p class="text-[10px] text-slate-500 font-black mt-2 uppercase tracking-tighter">Status Anda Hari Ini:</p>
                                <span class="px-3 py-1 bg-amber-100 text-amber-700 text-[10px] font-black rounded-lg mt-1 inline-block uppercase italic">
                                    {{ $cekAbsensi->status }}
                                </span>
                            </div>
                        @else
                            <form id="formIzinSakit" action="{{ route('absensi.izinSakit') }}" method="POST" class="grid grid-cols-2 gap-2 mt-2">
                                @csrf
                                <input type="hidden" name="status" id="status_input">
                                <input type="text" name="keterangan" id="keterangan_input" placeholder="Alasan singkat..." required class="col-span-2 text-[10px] border-slate-200 rounded-lg py-2 px-2 bg-slate-50">
                                <button type="button" onclick="konfirmasiStatus('Izin')" class="bg-amber-500 hover:bg-amber-600 text-white font-black py-2 rounded-lg text-[10px] uppercase shadow-md active:scale-95">✋ Izin</button>
                                <button type="button" onclick="konfirmasiStatus('Sakit')" class="bg-rose-500 hover:bg-rose-600 text-white font-black py-2 rounded-lg text-[10px] uppercase shadow-md active:scale-95">🤒 Sakit</button>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- PANEL ABSENSI (MODAL VERSION) --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 flex flex-col justify-center border-b-4 border-green-500 transition-all hover:shadow-md">
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3 text-center font-bold">Panel Absensi</p>
                    <button onclick="openAbsensiModal()" class="w-full bg-gradient-to-br from-blue-500 via-indigo-600 to-indigo-800 hover:from-indigo-700 hover:to-blue-600 text-white font-bold py-6 px-6 rounded-2xl shadow-lg shadow-indigo-100 transition-all active:scale-95 flex flex-col items-center justify-center">
                        <span class="text-2xl mb-1">🚀</span>
                        <span class="uppercase tracking-widest text-[11px]">{{ $cekAbsensi ? 'Lihat Riwayat' : 'Buka Panel Utama' }}</span>
                    </button>
                </div>
            </div>

            {{-- Table Admin Sections --}}
            @if(Auth::user()->role == 'admin')
            <div class="grid grid-cols-1 gap-6">
                {{-- Aktivitas Absensi Terbaru --}}
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-6 border-b border-slate-50 flex justify-between items-center">
                        <h3 class="font-black text-slate-800 uppercase tracking-tight flex items-center gap-2">
                            <span>⚡</span> Aktivitas Absensi Terbaru
                        </h3>
                        <span class="text-[10px] bg-blue-50 text-blue-600 px-3 py-1 rounded-full font-bold uppercase">Batas Waktu: 08:30</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/50">
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Karyawan</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Jam Masuk</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($recentActivities as $activity)
                                <tr>
                                    <td class="px-6 py-4 text-sm font-bold text-slate-700">
                                        {{-- MEMANGGIL RELASI KARYAWAN --}}
                                        {{ $activity->karyawan->nama_lengkap ?? 'Nama Tidak Terdaftar' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-mono text-slate-500">{{ $activity->jam_masuk }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase {{ $activity->status == 'Hadir' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                            {{ $activity->status }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-10 text-center text-slate-400 italic text-sm">Belum ada aktivitas absensi hari ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Rekapitulasi Seluruh Karyawan --}}
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-6 border-b border-slate-50">
                        <h3 class="font-black text-slate-800 uppercase tracking-tight flex items-center gap-2">
                            <span>📊</span> Rekapitulasi Seluruh Karyawan ({{ $namaBulan }})
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/50">
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Karyawan</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Hadir</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Izin</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Sakit</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($rekapBulanan as $rekap)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-bold text-slate-700">
                                        {{-- LANGSUNG MEMANGGIL NAMA_LENGKAP DARI OBJEK KARYAWAN --}}
                                        {{ $rekap->nama_lengkap ?? 'Nama Tidak Terdaftar' }}
                                    </td>
                                    <td class="px-6 py-4 text-center font-mono font-bold text-green-600">{{ $rekap->total_hadir }}</td>
                                    <td class="px-6 py-4 text-center font-mono font-bold text-amber-600">{{ $rekap->total_izin }}</td>
                                    <td class="px-6 py-4 text-center font-mono font-bold text-rose-600">{{ $rekap->total_sakit }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-slate-400 italic text-sm">Data rekapitulasi belum tersedia.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>

    {{-- STRUCTURE MODAL ABSENSI --}}
    <div id="absensiModal" class="fixed inset-0 z-[999] hidden">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeAbsensiModal()"></div>
        <div class="relative flex items-end justify-center min-h-screen p-0 sm:p-4 pointer-events-none">
            <div id="modalContent" class="bg-slate-50 w-full max-w-md rounded-t-[2.5rem] sm:rounded-[2.5rem] shadow-2xl overflow-hidden transform translate-y-full transition-transform duration-500 ease-out pointer-events-auto">
                <div class="flex justify-center py-3">
                    <div class="w-12 h-1.5 bg-slate-300 rounded-full"></div>
                </div>
                <button onclick="closeAbsensiModal()" class="absolute top-5 right-6 text-slate-400 hover:text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                <div class="p-6">
                    @include('absensi.panel-content')
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Notifikasi Berhasil
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        // Modal Logic
        function openAbsensiModal() {
            const modal = document.getElementById('absensiModal');
            const content = document.getElementById('modalContent');
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('translate-y-full');
                content.classList.add('translate-y-0');
            }, 10);
            if (typeof initMap === 'function') setTimeout(initMap, 600);
        }

        function closeAbsensiModal() {
            const content = document.getElementById('modalContent');
            content.classList.add('translate-y-full');
            content.classList.remove('translate-y-0');
            setTimeout(() => {
                document.getElementById('absensiModal').classList.add('hidden');
            }, 500);
        }

        // FUNGSI COUNTDOWN & CLOCK
        function startCountdown() {
            const target = new Date();
            target.setHours(8, 30, 0);
            setInterval(() => {
                const now = new Date();
                let diff = target - now;
                if (diff < 0) {
                    document.getElementById('timer').innerText = "WAKTU HABIS";
                    document.getElementById('timer').classList.replace('text-yellow-400', 'text-red-500');
                    return;
                }
                const hours = Math.floor(diff / (1000 * 60 * 60));
                const mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const secs = Math.floor((diff % (1000 * 60)) / 1000);
                document.getElementById('timer').innerText = `${String(hours).padStart(2, '0')}:${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
            }, 1000);
        }

        function updateClock() {
            const now = new Date();
            const clockEl = document.getElementById('realtime-clock');
            if(clockEl) {
                clockEl.innerText = `🕒 ${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}:${String(now.getSeconds()).padStart(2, '0')}`;
            }
        }

        setInterval(updateClock, 1000);
        updateClock();
        startCountdown();

        // Konfirmasi Status Izin/Sakit
        function konfirmasiStatus(pilihan) {
            const keterangan = document.getElementById('keterangan_input').value;
            if (!keterangan) {
                Swal.fire({ icon: 'warning', title: 'Alasan Kosong', text: 'Mohon isi alasan singkat terlebih dahulu.', confirmButtonColor: '#3b82f6' });
                return;
            }
            Swal.fire({
                title: 'Konfirmasi ' + pilihan,
                text: "Kirim status " + pilihan + " hari ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: pilihan === 'Sakit' ? '#f43f5e' : '#f59e0b',
                confirmButtonText: 'Ya, Kirim!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('status_input').value = pilihan;
                    document.getElementById('formIzinSakit').submit();
                }
            })
        }
    </script>
</x-app-layout>