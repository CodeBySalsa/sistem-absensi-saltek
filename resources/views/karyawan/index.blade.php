<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Karyawan - PT Saltek</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* CSS Optimalisasi Khusus HP agar tabel bisa digeser halus kesamping dan tidak gepeng */
        @media (max-width: 767px) {
            .mobile-title { font-size: 11px !important; font-weight: 800 !important; }
            .mobile-sub { font-size: 7.5px !important; }
            .mobile-table-text { font-size: 7.5px !important; }
            .mobile-padding { padding: 8px 6px !important; }
            .mobile-truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 90px; }
        }

        /* EFEK PREMIUM TAMBAHAN UNTUK TABEL */
        .premium-table {
            border-separate: separate !important;
            border-spacing: 0 8px !important; /* Memberikan efek baris mengambang */
        }
        
        .premium-table tbody tr {
            box-shadow: 0 4px 10px rgba(15, 23, 42, 0.015);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .premium-table tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 20px rgba(99, 102, 241, 0.05);
        }
    </style>
</head>
<body class="bg-slate-50 p-4 md:p-10">
    <div class="max-w-7xl mx-auto space-y-4 md:space-y-6">

        {{-- Navigasi Kembali ke Dashboard --}}
        <div class="flex justify-start">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-1.5 md:px-5 md:py-2.5 bg-white border border-slate-200 rounded-xl md:rounded-2xl text-[9px] md:text-[11px] font-black text-slate-600 hover:bg-slate-100 hover:text-blue-600 hover:border-blue-200 hover:shadow-md transition-all uppercase tracking-widest shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 md:h-4 md:w-4 text-blue-600 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Kembali ke Dashboard
            </a>
        </div>

        {{-- Kontainer Utama Dashboard --}}
        <div class="bg-white p-4 md:p-10 rounded-[1.5rem] md:rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100/80">
            
            {{-- Judul Atas & Tombol Tambah Karyawan Terkunci Selalu Menyamping Rapi --}}
            <div class="flex flex-row justify-between items-center mb-6 md:mb-10 gap-2 pb-5 border-b border-slate-100">
                <div class="w-7/12 md:w-auto">
                    <h1 class="text-xs sm:text-2xl md:text-3xl font-black tracking-tight uppercase leading-tight whitespace-nowrap overflow-hidden text-ellipsis bg-gradient-to-r from-slate-900 to-indigo-900 bg-clip-text text-transparent">
                        Daftar Karyawan PT Saltek
                    </h1>
                    <p class="text-[7px] md:text-sm text-slate-400 font-medium mt-0.5 md:mt-2 whitespace-nowrap overflow-hidden text-ellipsis">
                        Manajemen data anggota staf operasional.
                    </p>
                </div>
                <div class="w-5/12 md:w-auto flex justify-end shrink-0">
                    <a href="{{ route('karyawan.create') }}" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-[7px] md:text-[11px] font-black px-3 py-2 md:px-8 md:py-4 rounded-lg md:rounded-2xl transition-all shadow-lg shadow-blue-500/20 hover:shadow-blue-500/30 hover:-translate-y-0.5 uppercase tracking-wider md:tracking-widest whitespace-nowrap">
                        + Tambah Karyawan
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm font-bold rounded-2xl flex items-center gap-3 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Pembungkus tabel luar melengkung rapi dengan pelindung luapan data --}}
            <div class="overflow-hidden rounded-xl md:rounded-3xl border border-slate-100 w-full">
                
                {{-- Container internal scrollable agar data di HP memanjang luas horizontal --}}
                <div class="w-full overflow-x-auto">
                    <table class="w-full premium-table bg-white mobile-table-text md:text-sm min-w-[600px] md:min-w-0">
                        <thead class="bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-950 text-white">
                            <tr>
                                <th class="mobile-padding md:p-5 text-left text-[7px] md:text-[10px] font-extrabold uppercase tracking-[0.2em] w-2/12 rounded-l-xl md:rounded-l-2xl">NIP</th>
                                <th class="mobile-padding md:p-5 text-left text-[7px] md:text-[10px] font-extrabold uppercase tracking-[0.2em] w-3/12">Nama Lengkap</th>
                                <th class="mobile-padding md:p-5 text-left text-[7px] md:text-[10px] font-extrabold uppercase tracking-[0.2em] w-2/12">Jabatan</th>
                                <th class="mobile-padding md:p-5 text-left text-[7px] md:text-[10px] font-extrabold uppercase tracking-[0.2em] w-3/12">No. WhatsApp</th>
                                <th class="mobile-padding md:p-5 text-center text-[7px] md:text-[10px] font-extrabold uppercase tracking-[0.2em] border-l border-slate-800/50 w-2/12 rounded-r-xl md:rounded-r-2xl">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-slate-600 font-semibold divide-y divide-slate-50">
                            @foreach ($karyawans as $k)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                {{-- NIP --}}
                                <td class="mobile-padding md:p-5 font-mono text-slate-400 tracking-normal text-left rounded-l-xl md:rounded-l-2xl">{{ $k->nip }}</td>
                                
                                {{-- Nama Lengkap (Rata kiri lurus ideal) --}}
                                <td class="mobile-padding md:p-5 font-bold text-slate-800 text-left mobile-truncate">{{ $k->nama_lengkap }}</td>
                                
                                {{-- Jabatan --}}
                                <td class="mobile-padding md:p-5 text-left">
                                    <span class="px-2 py-0.5 md:px-4 md:py-1.5 bg-indigo-50 text-indigo-600 text-[6px] md:text-[9.5px] font-black uppercase rounded-md md:rounded-full border border-indigo-100/40 inline-block mobile-badge whitespace-nowrap tracking-wide shadow-sm shadow-indigo-100/20">
                                        {{ $k->jabatan }}
                                    </span>
                                </td>
                                
                                {{-- No WhatsApp (Dibuat tegak/normal agar formal dan elegan) --}}
                                <td class="mobile-padding md:p-5 text-blue-600 font-bold tracking-wide text-left font-mono not-italic hover:text-blue-700">
                                    {{ $k->no_hp ?? '-' }}
                                </td>

                                {{-- Aksi Edit & Hapus --}}
                                <td class="mobile-padding md:p-5 text-center border-l border-slate-100 rounded-r-xl md:rounded-r-2xl">
                                    <div class="flex justify-center items-center gap-1.5 md:gap-3">
                                        <a href="{{ route('karyawan.edit', $k->id) }}" class="p-1 md:p-2.5 bg-amber-50 text-amber-600 rounded-md md:rounded-xl hover:bg-amber-400 hover:text-amber-950 transition-all border border-amber-100 flex items-center justify-center shrink-0 shadow-sm shadow-amber-100" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 md:h-4 md:w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>

                                        <form action="{{ route('karyawan.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data {{ $k->nama_lengkap }}?');" class="inline flex items-center justify-center shrink-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 md:p-2.5 bg-rose-50 text-rose-600 rounded-md md:rounded-xl hover:bg-rose-500 hover:text-white transition-all border border-rose-100 flex items-center justify-center shadow-sm shadow-rose-100" title="Hapus">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 md:h-4 md:w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</body>
</html>