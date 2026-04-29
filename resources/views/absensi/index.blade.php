<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Digital - PT Saltek Dumpang Jaya</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-100 font-sans">

    <div class="max-w-md mx-auto min-h-screen flex flex-col p-4">
        
        <div class="mt-6 mb-8 flex justify-between items-start px-2">
            <div>
                <a href="{{ route('dashboard') }}" class="text-blue-600 text-xs font-bold hover:underline flex items-center">
                    ← KEMBALI KE DASHBOARD
                </a>
                <h1 class="text-3xl font-bold text-slate-800 tracking-tight mt-2">PT SALTEK</h1>
                <p class="text-slate-500 text-sm">Sistem Absensi Digital KKN</p>
            </div>
            
            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                @csrf
                <button type="submit" class="bg-red-50 text-[10px] text-red-500 px-3 py-1 rounded-full border border-red-100 hover:bg-red-500 hover:text-white transition-all font-bold">
                    LOGOUT
                </button>
            </form>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-8 text-center border border-slate-200 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-indigo-600"></div>
            
            <div class="mb-6">
                <span class="text-sm font-semibold text-blue-600 uppercase tracking-widest italic">Status Anda:</span>
                <h2 class="text-xl font-bold text-slate-800 mt-1 uppercase tracking-tight">{{ Auth::user()->name }}</h2>
            </div>

            <div class="bg-slate-50 rounded-2xl py-6 mb-8 border border-dashed border-slate-300">
                <div id="clock" class="text-4xl font-mono font-bold text-slate-700 tracking-widest">00:00:00</div>
                <p class="text-xs text-slate-400 mt-1 font-medium" id="date">-</p>
            </div>

            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded-xl mb-4 text-sm font-medium border border-green-200 animate-bounce">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 text-red-700 p-3 rounded-xl mb-4 text-sm font-medium border border-red-200">
                    {{ session('error') }}
                </div>
            @endif

            @php
                $karyawan = Auth::user()->karyawan;
                $absenHariIni = $karyawan ? $absensis->where('karyawan_id', $karyawan->id)->where('tanggal', date('Y-m-d'))->first() : null;
                $jamSekarang = (int)date('H');
            @endphp

            @if(!$karyawan)
                <div class="bg-yellow-50 text-yellow-700 p-4 rounded-2xl text-sm border border-yellow-200 font-medium">
                    ⚠️ Akun kamu belum terhubung ke data Karyawan. Hubungi Admin.
                </div>
            @elseif(!$absenHariIni)
                <form action="{{ route('absensi.store') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-5 rounded-2xl shadow-lg shadow-blue-200 transition-all active:scale-95 text-lg">
                        ABSEN MASUK SEKARANG
                    </button>
                </form>
            @elseif($absenHariIni && !$absenHariIni->jam_pulang)
                @if($jamSekarang >= 17)
                    <form action="{{ route('absensi.update', $absenHariIni->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-5 rounded-2xl shadow-lg shadow-orange-200 transition-all active:scale-95 text-lg">
                            ABSEN PULANG SEKARANG
                        </button>
                    </form>
                @else
                    <div class="bg-blue-50 border border-blue-100 p-5 rounded-2xl text-center">
                        <p class="text-blue-700 font-bold text-sm italic">✓ Anda Sudah Absen Masuk</p>
                        <p class="text-blue-500 text-[10px] mt-1 uppercase tracking-widest font-black">
                            Tombol Pulang Aktif Jam 17:00
                        </p>
                    </div>
                @endif
            @else
                <div class="bg-slate-100 text-slate-500 py-5 rounded-2xl font-bold border border-slate-200 flex flex-col items-center">
                    <span class="text-2xl mb-1">✅</span>
                    TUGAS HARI INI SELESAI
                </div>
            @endif
        </div>

        <div class="mt-10 mb-10">
            <h3 class="font-bold text-slate-700 mb-4 px-1 flex items-center">
                <span class="w-2 h-6 bg-blue-600 rounded-full mr-3"></span>
                Riwayat Aktivitas Terakhir
            </h3>
            
            <div class="space-y-3">
                @forelse($absensis as $absen)
                @php
                    $isYesterday = $absen->tanggal < date('Y-m-d');
                    $noPulang = !$absen->jam_pulang && $isYesterday;
                @endphp
                <div class="bg-white p-4 rounded-3xl flex justify-between items-center shadow-sm border {{ $noPulang ? 'border-red-100' : 'border-slate-100' }} hover:border-blue-200 transition-all group">
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-2xl {{ $noPulang ? 'bg-red-500' : 'bg-gradient-to-br from-blue-500 to-indigo-600' }} flex items-center justify-center text-white font-black text-sm shadow-sm group-hover:scale-110 transition-transform">
                            {{ strtoupper(substr(trim($absen->karyawan->nama_lengkap ?? 'K'), 0, 1)) }}
                        </div>
                        
                        <div class="ml-4">
                            <p class="font-black text-slate-800 tracking-tight leading-none text-sm mb-1">
                                {{ $absen->karyawan->nama_lengkap ?? 'User Baru' }}
                            </p>
                            <p class="text-[9px] text-slate-400 font-bold mb-2 uppercase">{{ \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('d F Y') }}</p>
                            <div class="flex gap-2">
                                <span class="text-[9px] bg-blue-50 text-blue-600 px-2 py-0.5 rounded-md font-bold italic">
                                    IN: {{ $absen->jam_masuk }}
                                </span>
                                @if($absen->jam_pulang)
                                <span class="text-[9px] bg-orange-50 text-orange-600 px-2 py-0.5 rounded-md font-bold italic">
                                    OUT: {{ $absen->jam_pulang }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        @if($absen->jam_pulang)
                            <span class="bg-blue-600 text-white shadow-md shadow-blue-100 text-[8px] px-3 py-1.5 rounded-xl font-black uppercase tracking-tighter">
                                SELESAI
                            </span>
                        @elseif($noPulang)
                            <span class="bg-red-50 text-red-600 border border-red-100 text-[8px] px-3 py-1.5 rounded-xl font-black uppercase tracking-tighter">
                                TANPA KET. PULANG
                            </span>
                        @else
                            <span class="bg-green-500 text-white shadow-md shadow-green-100 text-[8px] px-3 py-1.5 rounded-xl font-black uppercase tracking-tighter">
                                HADIR
                            </span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center bg-slate-50 border border-dashed border-slate-200 rounded-3xl py-10">
                    <p class="text-slate-400 text-sm italic font-medium">Belum ada aktivitas.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        function updateClock() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            
            document.getElementById('clock').innerText = `${hours}:${minutes}:${seconds}`;
            document.getElementById('date').innerText = now.toLocaleDateString('id-ID', options);
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>