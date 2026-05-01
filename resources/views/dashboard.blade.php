<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Monitoring - PT Saltek') }}
        </h2>
    </x-slot>

    {{-- 1. Tambahkan Library SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .swal2-popup { border-radius: 24px !important; font-family: ui-sans-serif, system-ui, -apple-system, sans-serif !important; }
    </style>

    {{-- Logic Cek Absensi Hari Ini --}}
    @php
        $cekAbsensi = \App\Models\Absensi::where('karyawan_id', Auth::user()->karyawan->id ?? 0)
                        ->where('tanggal', date('Y-m-d'))
                        ->first();
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Bagian Khusus Admin --}}
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

            {{-- Stats Cards Admin --}}
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

                {{-- FORM IZIN / SAKIT DENGAN PROTEKSI --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border-b-4 border-amber-500 transition-all hover:shadow-md flex flex-col justify-between">
                    <div>
                        <div class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Form Izin / Sakit</div>
                        
                        @if($cekAbsensi)
                            {{-- Tampilan jika sudah absen/izin --}}
                            <div class="bg-slate-50 rounded-xl p-4 text-center border border-slate-100">
                                <span class="text-2xl">✅</span>
                                <p class="text-[10px] text-slate-500 font-black mt-2 uppercase tracking-tighter">Status Anda Hari Ini:</p>
                                <span class="px-3 py-1 bg-amber-100 text-amber-700 text-[10px] font-black rounded-lg mt-1 inline-block uppercase">
                                    {{ $cekAbsensi->status }}
                                </span>
                                <p class="text-[9px] text-slate-400 mt-2 italic font-medium leading-tight">Pengajuan telah tersimpan.<br>Terima kasih atas konfirmasinya.</p>
                            </div>
                        @else
                            {{-- Tampilan Form jika belum ada data --}}
                            <p class="text-[10px] text-gray-400 mb-4 italic">Silakan isi alasan jika berhalangan hadir hari ini.</p>
                            <div class="mt-2">
                                <form id="formIzinSakit" action="{{ route('absensi.izinSakit') }}" method="POST" class="grid grid-cols-2 gap-2">
                                    @csrf
                                    <input type="hidden" name="status" id="status_input">
                                    <input type="text" name="keterangan" id="keterangan_input" placeholder="Alasan singkat..." required class="col-span-2 text-[10px] border-slate-200 rounded-lg focus:ring-amber-500 focus:border-amber-400 py-2 px-2 shadow-inner bg-slate-50">
                                    
                                    <button type="button" onclick="konfirmasiStatus('Izin')" class="bg-amber-500 hover:bg-amber-600 text-white font-black py-2 rounded-lg text-[10px] uppercase tracking-wider transition-all shadow-md active:scale-95 flex items-center justify-center gap-1">
                                        ✋ <span>Izin</span>
                                    </button>
                                    <button type="button" onclick="konfirmasiStatus('Sakit')" class="bg-rose-500 hover:bg-rose-600 text-white font-black py-2 rounded-lg text-[10px] uppercase tracking-wider transition-all shadow-md active:scale-95 flex items-center justify-center gap-1">
                                        🤒 <span>Sakit</span>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- PANEL ABSENSI DENGAN PROTEKSI --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 flex flex-col justify-center border-b-4 border-green-500 transition-all hover:shadow-md">
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3 text-center font-bold">Panel Absensi</p>
                    
                    @if($cekAbsensi && $cekAbsensi->status == 'Hadir')
                        <div class="text-center mb-3">
                            <span class="px-3 py-1 bg-green-100 text-green-700 text-[10px] font-black rounded-lg uppercase">Sudah Absen Hadir</span>
                        </div>
                    @endif

                    <a href="{{ route('absensi.index') }}" class="flex flex-col items-center justify-center bg-green-600 hover:bg-green-700 text-white font-bold py-6 px-6 rounded-xl shadow-lg uppercase text-xs tracking-widest transition-all active:scale-95">
                        <span class="text-2xl mb-1">🚀</span>
                        <span>{{ $cekAbsensi ? 'Lihat Riwayat' : 'Buka Panel Utama' }}</span>
                    </a>
                </div>
            </div>

            {{-- Selamat Datang --}}
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

            {{-- Tabel Khusus Admin (Tabel Aktivitas & Rekap Tetap Ada) --}}
            @if(Auth::user()->role == 'admin')
                {{-- Bagian Tabel Admin yang kamu punya sebelumnya silakan dipertahankan di sini --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-slate-100 mb-8">
                    <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                        <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">⚡ Aktivitas Absensi Terbaru</h3>
                        <span class="text-[10px] bg-blue-100 text-blue-700 font-black px-3 py-1 rounded-full uppercase tracking-widest border border-blue-200">Batas Waktu: 08:30</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full border-separate border-spacing-0 table-fixed">
                            <thead>
                                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                    <th class="px-6 py-4 text-left w-1/3">Karyawan</th>
                                    <th class="px-6 py-4 text-center">Jam Masuk</th>
                                    <th class="px-6 py-4 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($recentActivities ?? [] as $activity)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-6 py-4 text-sm font-bold text-slate-700 truncate">
                                        {{ $activity->karyawan->nama_lengkap ?? 'Nama Tidak Terdaftar' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-slate-500 text-center uppercase">
                                        {{ $activity->jam_masuk ?? '--:--' }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $isTepatWaktu = $activity->jam_masuk && \Carbon\Carbon::parse($activity->jam_masuk)->format('H:i') <= '08:30';
                                        @endphp
                                        @if($activity->status == 'Izin')
                                            <span class="px-3 py-1 bg-amber-50 text-amber-600 text-[10px] font-black uppercase rounded-lg border border-amber-100">✋ Izin</span>
                                        @elseif($activity->status == 'Sakit')
                                            <span class="px-3 py-1 bg-rose-50 text-rose-600 text-[10px] font-black uppercase rounded-lg border border-rose-100">🤒 Sakit</span>
                                        @elseif($isTepatWaktu || $activity->status == 'Selesai')
                                            <span class="px-3 py-1 bg-green-50 text-green-600 text-[10px] font-black uppercase rounded-lg border border-green-100">✅ Tepat Waktu</span>
                                        @else
                                            <span class="px-3 py-1 bg-red-50 text-red-600 text-[10px] font-black uppercase rounded-lg border border-red-100">❌ Terlambat</span>
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
            @endif
        </div>
    </div>

    {{-- 3. Script Konfirmasi & Notifikasi Sukses --}}
    <script>
        function konfirmasiStatus(pilihan) {
            const keterangan = document.getElementById('keterangan_input').value;

            if (!keterangan) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Alasan Kosong',
                    text: 'Mohon isi alasan singkat terlebih dahulu.',
                    confirmButtonColor: '#3b82f6'
                });
                return;
            }

            Swal.fire({
                title: 'Konfirmasi ' + pilihan,
                text: "Apakah Anda yakin ingin mengirim status " + pilihan + "?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: pilihan === 'Sakit' ? '#f43f5e' : '#f59e0b',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Kirim!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('status_input').value = pilihan;
                    
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Harap tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading() }
                    });

                    document.getElementById('formIzinSakit').submit();
                }
            })
        }

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2500
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "{{ session('error') }}",
                confirmButtonColor: '#f43f5e'
            });
        @endif
    </script>
</x-app-layout>