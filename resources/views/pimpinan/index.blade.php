<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor Kehadiran - Pimpinan PT Saltek</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 p-6 md:p-10">
    <div class="max-w-5xl mx-auto">
        {{-- Header --}}
        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight uppercase">Monitoring Kehadiran</h1>
                <p class="text-slate-500 font-medium">Laporan real-time PT Saltek Dumpang Jaya</p>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Tanggal Hari Ini</p>
                <p class="font-bold text-blue-600">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
            </div>
        </div>

        {{-- Statistik Ringkas --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="bg-white p-6 rounded-[2rem] shadow-xl shadow-blue-900/5 border border-slate-100 flex items-center gap-5">
                <div class="w-14 h-14 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center text-2xl">✅</div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Hadir</p>
                    <h3 class="text-2xl font-black text-slate-800">{{ $totalHadir }} Orang</h3>
                </div>
            </div>
            <div class="bg-white p-6 rounded-[2rem] shadow-xl shadow-blue-900/5 border border-slate-100 flex items-center gap-5">
                <div class="w-14 h-14 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center text-2xl">✋</div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Izin</p>
                    <h3 class="text-2xl font-black text-slate-800">{{ $totalIzin }} Orang</h3>
                </div>
            </div>
            <div class="bg-white p-6 rounded-[2rem] shadow-xl shadow-blue-900/5 border border-slate-100 flex items-center gap-5">
                <div class="w-14 h-14 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center text-2xl">🤒</div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Sakit</p>
                    <h3 class="text-2xl font-black text-slate-800">{{ $totalSakit }} Orang</h3>
                </div>
            </div>
        </div>

        {{-- Tabel Utama --}}
        <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-100 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-900 text-white">
                    <tr>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest">Nama Karyawan</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-center">Status</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-center">Jam Masuk</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-center">Keterangan/Alasan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($absensiHariIni as $absen)
                    <tr class="hover:bg-slate-50/50 transition-all">
                        <td class="p-6 font-bold text-slate-800">{{ $absen->karyawan->nama_lengkap }}</td>
                        <td class="p-6 text-center">
                            <span class="px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-tighter
                                {{ $absen->status == 'Hadir' ? 'bg-emerald-100 text-emerald-600' : 
                                   ($absen->status == 'Izin' ? 'bg-amber-100 text-amber-600' : 'bg-rose-100 text-rose-600') }}">
                                {{ $absen->status }}
                            </span>
                        </td>
                        <td class="p-6 text-center font-mono text-sm font-bold text-blue-600">
                            {{ $absen->jam_masuk ?? '--:--' }}
                        </td>
                        <td class="p-6 text-center text-sm italic text-slate-500">
                            {{ $absen->keterangan ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-20 text-center text-slate-400 italic">Belum ada data absensi masuk untuk hari ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>