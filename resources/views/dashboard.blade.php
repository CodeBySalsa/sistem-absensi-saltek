<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Monitoring - PT Saltek') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(Auth::user()->role == 'admin')
            <div class="mb-8 bg-gradient-to-r from-blue-800 to-indigo-900 overflow-hidden shadow-lg sm:rounded-3xl p-8 text-white relative">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-center">
                    <div class="text-center md:text-left mb-6 md:mb-0">
                        <h3 class="text-2xl font-black tracking-tight uppercase">Panel Kontrol Administrator</h3>
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
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex items-center gap-5">
                    <div class="h-12 w-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white text-xl shadow-lg shadow-blue-100">👥</div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Anggota</p>
                        <h3 class="text-2xl font-black text-slate-800">{{ $totalKaryawan ?? 0 }}</h3>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex items-center gap-5">
                    <div class="h-12 w-12 bg-green-500 rounded-2xl flex items-center justify-center text-white text-xl shadow-lg shadow-green-100">✅</div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Hadir Hari Ini</p>
                        <h3 class="text-2xl font-black text-slate-800">{{ $hadirHariIni ?? 0 }}</h3>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex items-center gap-5">
                    <div class="h-12 w-12 bg-amber-500 rounded-2xl flex items-center justify-center text-white text-xl shadow-lg shadow-amber-100">⚠️</div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Izin / Sakit</p>
                        <h3 class="text-2xl font-black text-slate-800">{{ $izinSakit ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border-b-4 border-blue-500 transition-all hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Hadir Anda</p>
                            <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $totalHadir }} Hari</h3>
                        </div>
                        <div class="bg-blue-50 p-3 rounded-xl text-blue-500">📅</div>
                    </div>
                    <p class="text-xs text-blue-400 mt-4 font-semibold italic">* Terhitung periode KKN di PT Saltek</p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border-b-4 border-orange-500 transition-all hover:shadow-md">
                    <div class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Info Penting</div>
                    <div class="bg-orange-50 border-l-4 border-orange-400 p-3">
                        <p class="text-sm text-orange-800 font-medium italic">📌 Jangan lupa upload laporan mingguan KKN!</p>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 flex flex-col justify-center border-b-4 border-green-500 transition-all hover:shadow-md">
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3 text-center">Menu Utama</p>
                    <a href="{{ route('absensi.index') }}" class="flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg uppercase text-sm tracking-widest transition-all active:scale-95">
                        Buka Panel Absensi
                    </a>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl mb-8 border border-slate-100">
                <div class="p-8 text-gray-900">
                    <div class="flex items-center space-x-4">
                        <div class="h-14 w-14 bg-slate-100 rounded-full flex items-center justify-center text-2xl shadow-inner border border-slate-200">
                            {{ Auth::user()->role == 'admin' ? '🛡️' : '👨‍💻' }}
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">
                                Selamat datang kembali, 
                                <span class="font-bold text-blue-600 uppercase text-[10px] tracking-widest ml-1 border border-blue-200 px-2 py-0.5 rounded-full">
                                    {{ Auth::user()->role }}
                                </span>
                            </p>
                            <h4 class="text-xl font-bold text-gray-800 tracking-tight">{{ Auth::user()->name }}!</h4>
                        </div>
                    </div>
                </div>
            </div>

            @if(Auth::user()->role == 'admin')
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-slate-100 mb-8">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">⚡ Aktivitas Absensi Terbaru</h3>
                    <span class="text-[10px] bg-blue-100 text-blue-700 font-black px-3 py-1 rounded-full uppercase tracking-widest">Batas Waktu: 08:30</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full border-separate border-spacing-0">
                        <thead>
                            <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                <th class="px-16 py-4 text-left">Karyawan</th>
                                <th class="px-8 py-4 text-center">Jam Masuk</th>
                                <th class="px-8 py-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($recentActivities ?? [] as $activity)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-16 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 bg-blue-50 rounded-full flex items-center justify-center text-[10px] font-bold text-blue-600 shadow-sm">
                                            {{ strtoupper(substr($activity->karyawan->nama ?? '?', 0, 1)) }}
                                        </div>
                                        <span class="text-sm font-bold text-slate-700">
                                            {{ $activity->karyawan->nama ?? 'Nama Tidak Terdaftar' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-8 py-4 text-sm font-medium text-slate-500 text-center">
                                    {{ $activity->jam_masuk ?? '--:--' }}
                                </td>
                                <td class="px-8 py-4 text-center">
                                    @if($activity->jam_masuk && \Carbon\Carbon::parse($activity->jam_masuk)->format('H:i') <= '08:30')
                                        <span class="px-3 py-1 bg-green-50 text-green-600 text-[10px] font-black uppercase rounded-lg border border-green-100">Tepat Waktu</span>
                                    @else
                                        <span class="px-3 py-1 bg-red-50 text-red-600 text-[10px] font-black uppercase rounded-lg border border-red-100">Terlambat</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-8 py-10 text-center text-slate-400 text-sm italic">Belum ada aktivitas absensi hari ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-slate-100">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">📈 Laporan Performa Karyawan (Bulan Ini)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full border-separate border-spacing-0">
                        <thead>
                            <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                <th class="px-16 py-4 text-left">Nama Karyawan</th>
                                <th class="px-8 py-4 text-center">Hadir</th>
                                <th class="px-8 py-4 text-center">Izin</th>
                                <th class="px-8 py-4 text-center">Sakit</th>
                                <th class="px-8 py-4 text-center bg-blue-50/30">Total Absen</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($rekapBulanan ?? [] as $rekap)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-16 py-4 text-sm font-bold text-slate-700">
                                    {{ $rekap->nama }}
                                </td>
                                <td class="px-8 py-4 text-center">
                                    <span class="px-3 py-1 bg-green-50 text-green-600 text-xs font-bold rounded-lg border border-green-100">
                                        {{ $rekap->total_hadir ?? 0 }} Hari
                                    </span>
                                </td>
                                <td class="px-8 py-4 text-center">
                                    <span class="px-3 py-1 bg-amber-50 text-amber-600 text-xs font-bold rounded-lg">
                                        {{ $rekap->total_izin ?? 0 }} Kali
                                    </span>
                                </td>
                                <td class="px-8 py-4 text-center">
                                    <span class="px-3 py-1 bg-rose-50 text-rose-600 text-xs font-bold rounded-lg">
                                        {{ $rekap->total_sakit ?? 0 }} Kali
                                    </span>
                                </td>
                                <td class="px-8 py-4 text-center font-black text-slate-800 bg-blue-50/10">
                                    {{ ($rekap->total_izin ?? 0) + ($rekap->total_sakit ?? 0) }} Hari
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-8 py-10 text-center text-slate-400 text-sm italic">Belum ada data performa untuk bulan ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>