<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor Kehadiran - Pimpinan PT Salttek</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        /* CSS Optimalisasi khusus HP agar tulisan rapi, simetris, dan muat satu layar menyamping */
        @media (max-width: 767px) {
            .mobile-title { font-size: 10px !important; font-weight: 800 !important; }
            .mobile-sub { font-size: 7px !important; }
            .mobile-badge { font-size: 6px !important; padding: 2px 4px !important; }
            .mobile-table-text { font-size: 7.5px !important; }
            .mobile-padding { padding: 6px 4px !important; }
            .mobile-truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 75px; }
        }
    </style>
</head>
<body class="bg-slate-50 p-4 md:p-10">
    {{-- Container utama menggunakan max-w-7xl agar memanjang maksimal di laptop --}}
    <div class="max-w-7xl mx-auto">
        
        {{-- Menu Atas: Penempatan Logo Baru di Sebelah Kiri & Tombol Kontrol Kanan --}}
        <div class="flex justify-between items-center mb-6">
            
            {{-- Sektor Kiri: Logo dengan ukuran yang dibatasi --}}
<div class="flex items-center gap-3">
    <div class="w-14 h-14 md:w-16 md:h-16 bg-white rounded-2xl flex items-center justify-center shadow-md border border-slate-100 p-2 shrink-0">
        <img src="{{ asset('logo.jpeg') }}" alt="Logo PT Salttek" class="w-full h-full object-contain">
    </div>
    <div class="hidden sm:block">
        <h3 class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none">Aplikasi Presensi</h3>
        <h2 class="text-xs font-extrabold text-slate-800 uppercase tracking-tight mt-0.5">PT Salttek Dumpang Jaya</h2>
    </div>
</div>

            {{-- Sektor Kanan: Tombol Menu Asli Bawaan Kamu --}}
            <div class="flex items-center gap-2 md:gap-3 shrink-0">
                {{-- Tombol Profil Pimpinan --}}
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-1.5 px-3 py-2 md:px-5 md:py-2.5 bg-white shadow-sm rounded-xl md:rounded-2xl text-[10px] md:text-xs font-bold text-slate-700 hover:text-blue-600 hover:shadow-md transition-all border border-slate-100 group">
                    <span class="text-xs md:text-sm">👤</span>
                    <span class="mobile-sub md:text-xs md:font-bold">Profil Pimpinan</span>
                </a>

                {{-- Tombol Logout Khusus Boss --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-1 md:gap-2 px-3 py-2 md:px-4 md:py-2.5 bg-red-50 text-red-600 rounded-xl md:rounded-2xl text-[9px] md:text-[10px] font-black uppercase tracking-widest hover:bg-red-100 transition-all border border-red-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 md:h-3.5 md:w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span class="mobile-sub">Keluar Sistem</span>
                    </button>
                </form>
            </div>
        </div>

       {{-- Banner Premium Eksekutif - Pastikan posisi bersih --}}
<div class="relative bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white p-4 md:p-12 rounded-[1.5rem] md:rounded-[2.5rem] shadow-xl shadow-indigo-950/10 mb-6 md:mb-10 overflow-hidden flex flex-row justify-between items-center gap-2">
    {{-- Dekorasi Background --}}
    <div class="absolute -right-10 -top-10 w-40 h-40 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-full blur-3xl opacity-30"></div>
    <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-emerald-500 rounded-full blur-3xl opacity-20"></div>
    
    {{-- Konten Utama --}}
    <div class="relative z-10 w-7/12">
        <span class="px-2 py-0.5 bg-blue-500/20 text-blue-300 text-[6px] md:text-[10px] font-black uppercase tracking-widest rounded-full border border-blue-500/30 inline-block mb-1 md:mb-3">
            Pimpinan Access Control
        </span>
        <h1 class="text-sm md:text-3xl lg:text-4xl font-extrabold tracking-tight uppercase leading-tight mobile-title">
            PT Salttek Dumpang Jaya
        </h1>
        <p class="text-indigo-200/80 font-medium text-[8px] md:text-sm mt-1 md:mt-2 mobile-sub">
            Total Karyawan Terdaftar: <span class="font-bold text-white">{{ $totalKaryawan }} Orang</span>
        </p>
    </div>

    {{-- Tanggal (Satu-satunya elemen tanggal di banner) --}}
    <div class="relative z-10 w-5/12 md:w-auto bg-white/5 backdrop-blur-md px-2 py-1.5 md:px-6 md:py-4 rounded-xl border border-white/10 text-right shrink-0">
        <p class="text-[6px] md:text-[9px] font-black text-indigo-300 uppercase tracking-widest mb-0.5 whitespace-nowrap">Hari & Tanggal</p>
        <p class="font-bold text-[8px] md:text-base text-white mobile-sub whitespace-nowrap">
            {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
        </p>
    </div>
</div>


      {{-- Statistik Ringkas - Kembali 3 Kolom --}}
<div class="grid grid-cols-3 gap-2 md:gap-6 mb-8 md:mb-10">
    
    {{-- Card Hadir --}}
    <a href="#log-kehadiran" class="bg-emerald-50 border border-emerald-100 p-2 md:p-6 rounded-xl md:rounded-[2rem] shadow-lg flex items-center gap-1 md:gap-5 transition-all hover:bg-emerald-100">
        <div class="text-sm w-6 h-6 md:w-14 md:h-14 bg-emerald-500 text-white rounded-lg flex items-center justify-center">✅</div>
        <div>
            <p class="text-[6px] md:text-[10px] font-black text-emerald-800 uppercase">Hadir</p>
            <h3 class="text-xs md:text-2xl font-black text-emerald-900">{{ $totalHadir }}</h3>
        </div>
    </a>

    {{-- Card Izin --}}
    <a href="#log-kehadiran" class="bg-amber-50 border border-amber-100 p-2 md:p-6 rounded-xl md:rounded-[2rem] shadow-lg flex items-center gap-1 md:gap-5 transition-all hover:bg-amber-100">
        <div class="text-sm w-6 h-6 md:w-14 md:h-14 bg-amber-500 text-white rounded-lg flex items-center justify-center">✋</div>
        <div>
            <p class="text-[6px] md:text-[10px] font-black text-amber-800 uppercase">Izin</p>
            <h3 class="text-xs md:text-2xl font-black text-amber-900">{{ $totalIzin }}</h3>
        </div>
    </a>

    {{-- Card Sakit --}}
    <a href="#log-kehadiran" class="bg-rose-50 border border-rose-100 p-2 md:p-6 rounded-xl md:rounded-[2rem] shadow-lg flex items-center gap-1 md:gap-5 transition-all hover:bg-rose-100">
        <div class="text-sm w-6 h-6 md:w-14 md:h-14 bg-rose-500 text-white rounded-lg flex items-center justify-center">🤒</div>
        <div>
            <p class="text-[6px] md:text-[10px] font-black text-rose-800 uppercase">Sakit</p>
            <h3 class="text-xs md:text-2xl font-black text-rose-900">{{ $totalSakit }}</h3>
        </div>
    </a>
</div>

        {{-- BAGIAN 1: TABEL UTAMA HARIAN --}}
<div id="log-kehadiran" class="mb-12 scroll-mt-6">
    <div class="flex items-center gap-2 mb-4 pl-1">
        <div class="w-1.5 h-4 bg-blue-600 rounded-full"></div>
        <h2 class="text-xs md:text-base font-extrabold text-slate-800 uppercase tracking-tight mobile-sub">Log Kehadiran Hari Ini</h2>
    </div>
    <div class="bg-white rounded-xl md:rounded-[2.5rem] shadow-xl border border-slate-100 overflow-hidden w-full">
        <table class="w-full text-left mobile-table-text md:text-sm">
            <thead class="bg-slate-900 text-white">
                <tr>
                    <th class="mobile-padding md:p-6 text-[7px] md:text-[10px] font-black uppercase tracking-widest text-left w-3/12">Nama Karyawan</th>
                    <th class="mobile-padding md:p-6 text-[7px] md:text-[10px] font-black uppercase tracking-widest text-center w-2/12">Status</th>
                    <th class="mobile-padding md:p-6 text-[7px] md:text-[10px] font-black uppercase tracking-widest text-center w-2/12">Jam Masuk</th>
                    <th class="mobile-padding md:p-6 text-[7px] md:text-[10px] font-black uppercase tracking-widest text-center w-2/12">Jam Pulang</th>
                    <th class="mobile-padding md:p-6 text-[7px] md:text-[10px] font-black uppercase tracking-widest text-center w-3/12">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($absensiHariIni as $absen)
                {{-- Baris dengan efek hover, zoom, dan shadow --}}
                <tr class="hover:bg-indigo-50 hover:scale-[1.01] hover:shadow-lg transition-all duration-200 cursor-default">
                    <td class="mobile-padding md:p-6 font-bold text-slate-800 mobile-truncate text-left">
                        {{ $absen->karyawan->nama_lengkap ?? '-' }}
                    </td>
                    
                    <td class="mobile-padding md:p-6 text-center">
                        @if(($absen->status == 'Hadir' || $absen->status == 'Terlambat') && is_null($absen->jam_keluar) && $absen->tanggal < \Carbon\Carbon::today()->toDateString())
                            <span class="px-1.5 py-0.5 rounded text-[6px] md:text-[9px] font-black uppercase bg-slate-100 text-slate-500 border border-slate-200 block md:inline text-center">
                                Lupa
                            </span>
                        @else
                            <span class="px-1.5 py-0.5 md:px-4 md:py-1.5 rounded-full text-[6px] md:text-[9px] font-black uppercase tracking-tighter block md:inline text-center mobile-badge
                                {{ $absen->status == 'Hadir' || $absen->status == 'Selesai' ? 'bg-emerald-100 text-emerald-600' : 
                                   ($absen->status == 'Izin' ? 'bg-amber-100 text-amber-600' : 
                                   ($absen->status == 'Terlambat' ? 'bg-orange-100 text-orange-600' : 'bg-rose-100 text-rose-600')) }}">
                                {{ $absen->status }}
                            </span>
                        @endif
                    </td>
                    
                    <td class="mobile-padding md:p-6 text-center font-mono text-[7px] md:text-sm font-bold text-blue-600">
                        {{ $absen->jam_masuk ?? '--:--' }}
                    </td>

                    <td class="mobile-padding md:p-6 text-center font-mono text-[7px] md:text-sm font-bold">
                        @if(in_array($absen->status, ['Izin', 'Sakit']))
                            <span class="text-slate-400 font-sans font-medium">-</span>
                        @elseif(!is_null($absen->jam_keluar))
                            <span class="text-emerald-600">{{ $absen->jam_keluar }}</span>
                        @else
                            <span class="px-3 py-1 rounded-full text-[6.5px] md:text-[9px] font-black uppercase tracking-tight bg-rose-100 text-rose-600 inline-block text-center mobile-badge">
                                Belum Pulang
                            </span>
                        @endif
                    </td>
                    
                    <td class="mobile-padding md:p-6 text-slate-500 italic mobile-truncate md:whitespace-normal text-center">
                        {{ $absen->keterangan ?? '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-8 md:p-20 text-center text-slate-400 italic font-medium mobile-table-text md:text-sm">Belum ada data absensi masuk untuk hari ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

      {{-- BAGIAN 2: REKAPITULASI BULANAN --}}
<div class="mb-10">
    <div class="flex items-center gap-2 mb-4 pl-1">
        <div class="w-1.5 h-4 bg-indigo-600 rounded-full"></div>
        <h2 class="text-xs md:text-base font-extrabold text-slate-800 uppercase tracking-tight mobile-sub">Rekapitulasi Akumulasi Bulanan</h2>
    </div>
    <div class="bg-white rounded-xl md:rounded-[2.5rem] shadow-xl border border-slate-100 overflow-hidden w-full">
        <table class="w-full text-left mobile-table-text md:text-sm">
            <thead class="bg-indigo-950 text-white">
                <tr>
                    <th class="mobile-padding md:p-6 text-[7px] md:text-[10px] font-black uppercase tracking-widest text-left w-3/12">Nama Karyawan</th>
                    <th class="mobile-padding md:p-6 text-[7px] md:text-[10px] font-black uppercase tracking-widest text-center bg-emerald-950/40 w-3/12">Total Hadir</th>
                    <th class="mobile-padding md:p-6 text-[7px] md:text-[10px] font-black uppercase tracking-widest text-center bg-amber-950/40 w-3/12">Total Izin</th>
                    <th class="mobile-padding md:p-6 text-[7px] md:text-[10px] font-black uppercase tracking-widest text-center bg-rose-950/40 w-3/12">Total Sakit</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($rekapBulanan as $rekap)
                {{-- Efek zoom dan warna terang (highlight) saat disentuh --}}
                <tr class="hover:bg-indigo-50/80 hover:scale-[1.01] hover:shadow-lg transition-all duration-200 cursor-default">
                    <td class="mobile-padding md:p-6 font-bold text-slate-800 mobile-truncate text-left">{{ $rekap->nama_lengkap }}</td>
                    <td class="mobile-padding md:p-6 text-center font-bold text-emerald-600 font-mono text-[7px] md:text-sm bg-emerald-50/20">
                        {{ $rekap->total_hadir }}
                    </td>
                    <td class="mobile-padding md:p-6 text-center font-bold text-amber-600 font-mono text-[7px] md:text-sm bg-amber-50/20">
                        {{ $rekap->total_izin }}
                    </td>
                    <td class="mobile-padding md:p-6 text-center font-bold text-rose-600 font-mono text-[7px] md:text-sm bg-rose-50/20">
                        {{ $rekap->total_sakit }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-8 md:p-20 text-center text-slate-400 italic font-medium mobile-table-text md:text-sm">Tidak ada data rekapitulasi bulanan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
        {{-- Footer Branding --}}
        <div class="mt-14 text-center pb-6">
            <p class="text-[8px] md:text-[10px] font-black text-slate-300 uppercase tracking-[0.3em] mobile-sub md:text-[10px]">Sistem Absensi Digital • PT Salttek Dumpang Jaya</p>
        </div>
    </div>

    {{-- Robot Futuristik Asisten Pimpinan (Desain Sama dengan Karyawan) --}}
    <div class="fixed bottom-5 right-5 z-[9999] flex flex-col items-end gap-2 pointer-events-none group animate-fade-in">
        
        {{-- Gelembung Teks di Atas Robot --}}
        <div id="robot-bubble-pimpinan" class="pointer-events-auto bg-slate-900 text-white text-[10px] md:text-xs font-bold px-4 py-2.5 rounded-2xl shadow-2xl border border-slate-700/50 max-w-[180px] md:max-w-[240px] relative transition-all duration-500 transform scale-0 opacity-0 group-hover:scale-100 group-hover:opacity-100 mb-1 tracking-wide line-clamp-3">
            <span id="robot-text-pimpinan">Dashboard Pimpinan siap dipantau, Boss! 📊🤖</span>
            <div class="absolute bottom-[-6px] right-7 w-3 h-3 bg-slate-900 border-r border-b border-slate-700/50 transform rotate-45"></div>
        </div>

        {{-- Tubuh Robot yang Bisa Diklik --}}
        <div class="pointer-events-auto cursor-pointer relative mr-3 transition-transform duration-300 hover:scale-110 active:scale-95" onclick="tambahPesanPimpinan()">
            <div class="robot-body transition-transform duration-300 active:scale-90">
                {{-- Antena dengan Cahaya Cyan --}}
                <div class="w-1 h-3 bg-slate-400 mx-auto rounded-full relative">
                    <div id="robot-antenna-glow-pimpinan" class="absolute top-0 left-1/2 transform -translate-x-1/2 w-2 h-2 bg-cyan-400 rounded-full shadow-[0_0_8px_#22d3ee] animate-pulse"></div>
                </div>
                {{-- Wajah Robot --}}
                <div class="w-12 h-10 md:w-14 md:h-12 bg-gradient-to-b from-slate-100 to-slate-300 rounded-[1.2rem] shadow-xl border border-white flex items-center justify-center p-1.5 relative overflow-hidden">
                    <div class="w-full h-full bg-slate-950 rounded-[0.6rem] flex items-center justify-center gap-1.5 relative">
                        <div id="eye-left-pimpinan" class="robot-eye w-2.5 h-2.5 bg-cyan-400 rounded-full shadow-[0_0_6px_#22d3ee] transition-all duration-500"></div>
                        <div id="eye-right-pimpinan" class="robot-eye w-2.5 h-2.5 bg-cyan-400 rounded-full shadow-[0_0_6px_#22d3ee] transition-all duration-500"></div>
                    </div>
                </div>
                {{-- Bagian Bawah Robot --}}
                <div class="w-8 h-5 bg-gradient-to-b from-slate-200 to-slate-400 mx-auto rounded-b-xl border-x border-b border-white shadow-md flex justify-center items-center">
                    <div class="w-3 h-1.5 bg-indigo-500 rounded-full animate-pulse"></div>
                </div>
            </div>
        </div>
    </div>
   <style>
    .robot-body { animation: robotFloat 3s ease-in-out infinite; }
    @keyframes robotFloat {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-6px) rotate(1deg); }
    }

    /* KELAS MATA DASAR: SELALU PAKAI INI */
    .robot-eye { 
        width: 10px; height: 10px; 
        background-color: #22d3ee; 
        border-radius: 50%; 
        transition: all 0.3s ease;
        animation: robotBlink 4s infinite; /* ANIMASI KEDIP SELALU JALAN */
    }

    @keyframes robotBlink {
        0%, 90%, 100% { transform: scaleY(1); }
        95% { transform: scaleY(0.1); }
    }

    /* KELAS EKSPRESI (Hanya menimpa bentuk, bukan animasi) */
    .expr-happy {
        height: 8px !important;
        background-color: #34d399 !important;
        box-shadow: 0 0 8px #34d399 !important;
        clip-path: polygon(0% 100%, 0% 40%, 15% 10%, 50% 0%, 85% 10%, 100% 40%, 100% 100%, 80% 100%, 50% 30%, 20% 100%) !important;
    }

    .robot-giggle { animation: giggle 0.5s ease-in-out; }
    @keyframes giggle {
        0%, 100% { transform: scale(1); }
        25% { transform: scale(1.1) translateX(-4px); }
        75% { transform: scale(1.1) translateX(4px); }
    }
</style>
<script>
    // 1. Inisialisasi variabel elemen
    const robotText = document.getElementById("robot-text-pimpinan");
    const robotBubble = document.getElementById("robot-bubble-pimpinan");
    const eyeLeft = document.getElementById("eye-left-pimpinan");
    const eyeRight = document.getElementById("eye-right-pimpinan");

    const pesanOtomatis = [
        "Selamat datang, Pimpinan! Siap pantau hari ini? 📊",
        "Data Presensi hari ini sudah sinkron, Boss! ✅",
        "Rekapitulasi bulanan terpantau aman. 📈",
        "Salttek Dumpang Jaya terpantau disiplin! 🚀",
        "Laporan kehadiran sudah diperbarui. 🤖"
    ];

    let index = 0;

    // Fungsi dasar untuk ekspresi mata (Memastikan mata selalu kedip)
    function setMata(ekspresi) {
        if (!eyeLeft || !eyeRight) return;
        
        // expr-normal selalu ada agar kedip terus
        let baseClass = "robot-eye transition-all duration-500 expr-normal ";
        let ekspresiClass = (ekspresi === "happy") ? "expr-happy" : "";
        
        eyeLeft.className = baseClass + ekspresiClass;
        eyeRight.className = baseClass + ekspresiClass;
    }

    // Fungsi untuk menyembunyikan bubble
    function sembunyikanPesan() {
        if (!robotBubble) return;
        robotBubble.classList.remove("scale-100", "opacity-100");
        robotBubble.classList.add("scale-0", "opacity-0");
        setMata("normal"); // Kembali ke normal (berkedip saja)
    }

    // Fungsi untuk menampilkan pesan (Dipanggil otomatis)
    function tampilkanPesan() {
        if (!robotText || !robotBubble) return;

        // Ganti teks
        robotText.innerText = pesanOtomatis[index];
        index = (index + 1) % pesanOtomatis.length;

        // Tampilkan
        robotBubble.classList.remove("scale-0", "opacity-0");
        robotBubble.classList.add("scale-100", "opacity-100");
        setMata("happy"); // Senyum saat muncul

        // Hilang setelah 4 detik
        setTimeout(sembunyikanPesan, 4000);
    }

    // --- EKSEKUSI OTOMATIS ---
    // Tunggu 2 detik setelah halaman dimuat baru mulai
    setTimeout(() => {
        tampilkanPesan(); // Muncul pertama
        setInterval(tampilkanPesan, 15000); // Muncul lagi setiap 15 detik
    }, 2000);

    // Fungsi tambahan jika tetap ingin diklik manual
    function tambahPesanPimpinan() {
        tampilkanPesan();
    }
</script>
</body>
</html>