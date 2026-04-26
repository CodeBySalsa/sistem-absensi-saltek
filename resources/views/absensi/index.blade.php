<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Digital - PT Saltek Dumpang Jaya</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 font-sans">

    <div class="max-w-md mx-auto min-h-screen flex flex-col p-4">
        
        <div class="mt-10 mb-8 text-center">
            <h1 class="text-3xl font-bold text-slate-800">PT SALTEK</h1>
            <p class="text-slate-500">Sistem Absensi Digital KKN</p>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-8 text-center border border-slate-200">
            <div class="mb-6">
                <span class="text-sm font-semibold text-blue-600 uppercase tracking-widest">Selamat Datang</span>
                <h2 class="text-xl font-bold text-slate-800 mt-1">Budi Saltek</h2>
            </div>

            <div class="bg-slate-50 rounded-2xl py-6 mb-8 border border-dashed border-slate-300">
                <div id="clock" class="text-4xl font-mono font-bold text-slate-700">00:00:00</div>
                <p class="text-xs text-slate-400 mt-1" id="date">-</p>
            </div>

            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded-lg mb-4 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('absen.masuk') }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-blue-200 transition-all active:scale-95">
                    KLIK UNTUK ABSEN MASUK
                </button>
            </form>
        </div>

        <div class="mt-10">
            <h3 class="font-bold text-slate-700 mb-4">Riwayat Absen Hari Ini</h3>
            <div class="space-y-3">
                @foreach($absensis as $absen)
                <div class="bg-white p-4 rounded-xl flex justify-between items-center shadow-sm">
                    <div>
                        <p class="font-semibold text-slate-800">{{ $absen->karyawan->nama_lengkap ?? 'Karyawan' }}</p>
                        <p class="text-xs text-slate-500">Jam: {{ $absen->jam_masuk }}</p>
                    </div>
                    <span class="bg-green-100 text-green-600 text-xs px-3 py-1 rounded-full font-medium">
                        {{ $absen->status }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    <script>
        function updateClock() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            
            document.getElementById('clock').innerText = now.toLocaleTimeString('id-ID');
            document.getElementById('date').innerText = now.toLocaleDateString('id-ID', options);
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>