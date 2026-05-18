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
    </style>
</head>
<body class="bg-slate-50 p-4 md:p-10">
    <div class="max-w-7xl mx-auto space-y-4 md:space-y-6">

        {{-- Navigasi Kembali ke Dashboard --}}
        <div class="flex justify-start">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-1.5 md:px-5 md:py-2.5 bg-white border border-slate-200 rounded-xl md:rounded-2xl text-[9px] md:text-[11px] font-black text-slate-600 hover:bg-slate-100 hover:shadow-md transition-all uppercase tracking-widest shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 md:h-4 md:w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Kembali ke Dashboard
            </a>
        </div>

        {{-- Kontainer Utama Dashboard --}}
        <div class="bg-white p-4 md:p-10 rounded-[1.5rem] md:rounded-[2.5rem] shadow-xl shadow-slate-200/60 border border-slate-100">
            
            {{-- PERBAIKAN: Judul Atas & Tombol Tambah Karyawan Terkunci Selalu Menyamping Rapi --}}
            <div class="flex flex-row justify-between items-center mb-6 md:mb-10 gap-2 pb-4 border-b border-slate-50">
                <div class="w-7/12 md:w-auto">
                    <h1 class="text-xs sm:text-2xl md:text-3xl font-black text-slate-800 tracking-tight uppercase leading-tight whitespace-nowrap overflow-hidden text-ellipsis">
                        Daftar Karyawan PT Saltek
                    </h1>
                    <p class="text-[7px] md:text-sm text-slate-500 font-medium mt-0.5 md:mt-2 whitespace-nowrap overflow-hidden text-ellipsis">
                        Manajemen data anggota KKN dan staf operasional.
                    </p>
                </div>
                <div class="w-5/12 md:w-auto flex justify-end shrink-0">
                    <a href="{{ route('karyawan.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-[7px] md:text-[11px] font-black px-2.5 py-2 md:px-8 md:py-4 rounded-lg md:rounded-2xl transition-all shadow-lg shadow-blue-100 uppercase tracking-wider md:tracking-widest whitespace-nowrap">
                        + Tambah Karyawan
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 text-sm font-bold rounded-2xl flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            {{-- PERBAIKAN: Pembungkus tabel luar melengkung rapi dengan pelindung luapan data --}}
            <div class="overflow-hidden rounded-xl md:rounded-3xl border border-slate-100 shadow-sm w-full">
                
                {{-- Container internal scrollable agar data di HP memanjang luas horizontal --}}
                <div class="w-full overflow-x-auto">
                    <table class="w-full border-collapse bg-white mobile-table-text md:text-sm min-w-[600px] md:min-w-0">
                        <thead class="bg-slate-900 text-white">
                            <tr>
                                <th class="mobile-padding md:p-5 text-left text-[7px] md:text-[10px] font-black uppercase tracking-[0.2em] w-2/12">NIP</th>
                                <th class="mobile-padding md:p-5 text-left text-[7px] md:text-[10px] font-black uppercase tracking-[0.2em] w-3/12">Nama Lengkap</th>
                                <th class="mobile-padding md:p-5 text-left text-[7px] md:text-[10px] font-black uppercase tracking-[0.2em] w-2/12">Jabatan</th>
                                <th class="mobile-padding md:p-5 text-left text-[7px] md:text-[10px] font-black uppercase tracking-[0.2em] w-3/12">No. WhatsApp</th>
                                <th class="mobile-padding md:p-5 text-center text-[7px] md:text-[10px] font-black uppercase tracking-[0.2em] border-l border-slate-800 w-2/12">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-600 font-medium">
                            @foreach ($karyawans as $k)
                            <tr class="hover:bg-slate-50 transition-colors">
                                {{-- NIP --}}
                                <td class="mobile-padding md:p-5 font-mono text-slate-500 tracking-tighter text-left">{{ $k->nip }}</td>
                                
                                {{-- Nama Lengkap (Rata kiri lurus ideal) --}}
                                <td class="mobile-padding md:p-5 font-extrabold text-slate-800 text-left mobile-truncate">{{ $k->nama_lengkap }}</td>
                                
                                {{-- Jabatan --}}
                                <td class="mobile-padding md:p-5 text-left">
                                    <span class="px-2 py-0.5 md:px-4 md:py-1.5 bg-blue-50 text-blue-600 text-[6px] md:text-[9px] font-black uppercase rounded-md md:rounded-full border border-blue-100/50 inline-block mobile-badge whitespace-nowrap">
                                        {{ $k->jabatan }}
                                    </span>
                                </td>
                                
                                {{-- No WhatsApp --}}
                                <td class="mobile-padding md:p-5 text-blue-600 font-bold italic tracking-tight text-left font-mono">
                                    {{ $k->no_hp ?? '-' }}
                                </td>

                                {{-- Aksi Edit & Hapus --}}
                                <td class="mobile-padding md:p-5 text-center border-l border-slate-50">
                                    <div class="flex justify-center items-center gap-1.5 md:gap-3">
                                        <a href="{{ route('karyawan.edit', $k->id) }}" class="p-1 md:p-2.5 bg-amber-50 text-amber-600 rounded-md md:rounded-xl hover:bg-amber-100 transition-all border border-amber-100 flex items-center justify-center shrink-0" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 md:h-4 md:w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>

                                        <form action="{{ route('karyawan.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data {{ $k->nama_lengkap }}?');" class="inline flex items-center justify-center shrink-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 md:p-2.5 bg-red-50 text-red-600 rounded-md md:rounded-xl hover:bg-red-100 transition-all border border-red-100 flex items-center justify-center" title="Hapus">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 md:h-4 md:w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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