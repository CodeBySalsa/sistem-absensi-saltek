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
        
        {{-- Menu Atas: Profil Baru & Logout --}}
        <div class="flex justify-between items-center mb-6">
            {{-- Tombol Profil Pimpinan --}}
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-1.5 px-3 py-1.5 md:px-5 md:py-2.5 bg-white shadow-sm rounded-xl md:rounded-2xl text-[10px] md:text-xs font-bold text-slate-700 hover:text-blue-600 hover:shadow-md transition-all border border-slate-100 group">
                <span class="text-xs md:text-sm">👤</span>
                <span class="mobile-sub md:text-xs md:font-bold">Profil Pimpinan</span>
            </a>

            {{-- Tombol Logout Khusus Boss --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-1 md:gap-2 px-3 py-1.5 md:px-4 md:py-2.5 bg-red-50 text-red-600 rounded-xl md:rounded-2xl text-[9px] md:text-[10px] font-black uppercase tracking-widest hover:bg-red-100 transition-all border border-red-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 md:h-3.5 md:w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="mobile-sub">Keluar Sistem</span>
                </button>
            </form>
        </div>

        {{-- Banner Premium Eksekutif --}}
        <div class="relative bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white p-4 md:p-12 rounded-[1.5rem] md:rounded-[2.5rem] shadow-xl shadow-indigo-950/10 mb-6 md:mb-10 overflow-hidden flex flex-row justify-between items-center gap-2">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-full blur-3xl opacity-30"></div>
            <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-emerald-500 rounded-full blur-3xl opacity-20"></div>
            
            <div class="relative z-10 w-7/12">
                <span class="px-2 py-0.5 bg-blue-500/20 text-blue-300 text-[6px] md:text-[10px] font-black uppercase tracking-widest rounded-full border border-blue-500/30 inline-block mb-1 md:mb-3 whitespace-nowrap">
                    Pimpinan Access Control
                </span>
                <h1 class="text-sm md:text-3xl lg:text-4xl font-extrabold tracking-tight uppercase leading-tight md:mb-1 mobile-title">
                    Executive Dashboard
                </h1>
                <p class="text-indigo-200/80 font-medium text-[8px] md:text-sm mobile-sub">
                    PT Salttek Dumpang Jaya
                </p>
            </div>
            <div class="relative z-10 w-5/12 md:w-auto bg-white/5 backdrop-blur-md px-2 py-1.5 md:px-6 md:py-4 rounded-xl border border-white/10 text-right shrink-0">
                <p class="text-[6px] md:text-[9px] font-black text-indigo-300 uppercase tracking-widest mb-0.5 whitespace-nowrap">Hari & Tanggal Monitor</p>
                <p class="font-bold text-[8px] md:text-base text-white mobile-sub whitespace-nowrap">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            </div>
        </div>

        {{-- Statistik Ringkas --}}
        <div class="grid grid-cols-3 gap-2 md:gap-6 mb-8 md:mb-10">
            <div class="bg-white p-2 md:p-6 rounded-xl md:rounded-[2rem] shadow-xl shadow-blue-900/5 border border-slate-100 flex items-center gap-1 md:gap-5">
                <div class="text-sm md:text-2xl w-6 h-6 md:w-14 md:h-14 bg-emerald-50 text-emerald-500 rounded-lg flex items-center justify-center shrink-0">✅</div>
                <div class="min-w-0">
                    <p class="text-[6px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none truncate">Hadir</p>
                    <h3 class="text-xs md:text-2xl font-black text-slate-800 leading-tight mt-0.5">{{ $totalHadir }}</h3>
                </div>
            </div>
            <div class="bg-white p-2 md:p-6 rounded-xl md:rounded-[2rem] shadow-xl shadow-blue-900/5 border border-slate-100 flex items-center gap-1 md:gap-5">
                <div class="text-sm md:text-2xl w-6 h-6 md:w-14 md:h-14 bg-amber-50 text-amber-500 rounded-lg flex items-center justify-center shrink-0">✋</div>
                <div class="min-w-0">
                    <p class="text-[6px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none truncate">Izin</p>
                    <h3 class="text-xs md:text-2xl font-black text-slate-800 leading-tight mt-0.5">{{ $totalIzin }}</h3>
                </div>
            </div>
            <div class="bg-white p-2 md:p-6 rounded-xl md:rounded-[2rem] shadow-xl border border-slate-100 flex items-center gap-1 md:gap-5">
                <div class="text-sm md:text-2xl w-6 h-6 md:w-14 md:h-14 bg-rose-50 text-rose-500 rounded-lg flex items-center justify-center shrink-0">🤒</div>
                <div class="min-w-0">
                    <p class="text-[6px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none truncate">Sakit</p>
                    <h3 class="text-xs md:text-2xl font-black text-slate-800 leading-tight mt-0.5">{{ $totalSakit }}</h3>
                </div>
            </div>
        </div>

        {{-- BAGIAN 1: TABEL UTAMA HARIAN --}}
        <div class="mb-12">
            <div class="flex items-center gap-2 mb-4 pl-1">
                <div class="w-1.5 h-4 bg-blue-600 rounded-full"></div>
                <h2 class="text-xs md:text-base font-extrabold text-slate-800 uppercase tracking-tight mobile-sub">Log Kehadiran Hari Ini</h2>
            </div>
            <div class="bg-white rounded-xl md:rounded-[2.5rem] shadow-xl border border-slate-100 overflow-hidden w-full">
                <table class="w-full text-left mobile-table-text md:text-sm">
                    <thead class="bg-slate-900 text-white">
                        <tr>
                            {{-- Diubah menjadi text-left (Rata Kiri) agar lurus rapi --}}
                            <th class="mobile-padding md:p-6 text-[7px] md:text-[10px] font-black uppercase tracking-widest text-left w-3/12">Nama Karyawan</th>
                            <th class="mobile-padding md:p-6 text-[7px] md:text-[10px] font-black uppercase tracking-widest text-center w-2/12">Status</th>
                            <th class="mobile-padding md:p-6 text-[7px] md:text-[10px] font-black uppercase tracking-widest text-center w-2/12">Jam Masuk</th>
                            <th class="mobile-padding md:p-6 text-[7px] md:text-[10px] font-black uppercase tracking-widest text-center w-2/12">Jam Pulang</th>
                            <th class="mobile-padding md:p-6 text-[7px] md:text-[10px] font-black uppercase tracking-widest text-center w-3/12">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($absensiHariIni as $absen)
                        <tr class="hover:bg-slate-50/50 transition-all">
                            {{-- Perbaikan Utama: Class text-center DIHAPUS agar tulisan nama kembali rata kiri yang ideal --}}
                            <td class="mobile-padding md:p-6 font-bold text-slate-800 mobile-truncate text-left">
                                {{ $absen->karyawan->nama_lengkap ?? '-' }}
                            </td>
                            
                            {{-- Kolom 2: Status --}}
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
                            
                            {{-- Kolom 3: Jam Masuk --}}
                            <td class="mobile-padding md:p-6 text-center font-mono text-[7px] md:text-sm font-bold text-blue-600">
                                {{ $absen->jam_masuk ?? '--:--' }}
                            </td>

                            {{-- Kolom 4: Jam Pulang --}}
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
                            
                            {{-- Kolom 5: Keterangan (Rata tengah proporsional mengikuti header) --}}
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
                        <tr class="hover:bg-slate-50/50 transition-all">
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
</body>
</html>