<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Daftar Karyawan - PT Saltek</title>

    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-slate-50 p-6 md:p-10">

    <div class="max-w-6xl mx-auto">

       

        <div class="mb-6 flex justify-start">

            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 rounded-2xl text-[11px] font-black text-slate-600 hover:bg-slate-100 hover:shadow-md transition-all uppercase tracking-widest shadow-sm">

                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />

                </svg>

                Kembali ke Dashboard

            </a>

        </div>



        <div class="bg-white p-8 md:p-10 rounded-[2.5rem] shadow-xl shadow-slate-200/60 border border-slate-100">

            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">

                <div>

                    <h1 class="text-3xl font-black text-slate-800 tracking-tight uppercase leading-none">Daftar Karyawan PT Saltek</h1>

                    <p class="text-sm text-slate-500 font-medium mt-3">Manajemen data anggota KKN dan staf operasional.</p>

                </div>

                <a href="{{ route('karyawan.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-black px-8 py-4 rounded-2xl transition-all shadow-lg shadow-blue-100 uppercase tracking-widest">

                    + Tambah Karyawan

                </a>

            </div>

           

            @if(session('success'))

                <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 text-sm font-bold rounded-2xl flex items-center gap-3">

                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">

                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />

                    </svg>

                    {{ session('success') }}

                </div>

            @endif



            <div class="overflow-hidden rounded-3xl border border-slate-100 shadow-sm">

                <table class="w-full border-collapse bg-white">

                    <thead>

                        <tr class="bg-slate-900 text-white">

                            <th class="p-5 text-left text-[10px] font-black uppercase tracking-[0.2em]">NIP</th>

                            <th class="p-5 text-left text-[10px] font-black uppercase tracking-[0.2em]">Nama Lengkap</th>

                            <th class="p-5 text-left text-[10px] font-black uppercase tracking-[0.2em]">Jabatan</th>

                            <th class="p-5 text-left text-[10px] font-black uppercase tracking-[0.2em]">No. HP</th>

                            <th class="p-5 text-center text-[10px] font-black uppercase tracking-[0.2em] border-l border-slate-800">Aksi</th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        @foreach ($karyawans as $k)

                        <tr class="hover:bg-slate-50 transition-colors">

                            <td class="p-5 text-sm font-medium text-slate-500 tracking-tighter">{{ $k->nip }}</td>

                            <td class="p-5 text-sm font-extrabold text-slate-800">{{ $k->nama_lengkap }}</td>

                            <td class="p-5">

                                <span class="px-4 py-1.5 bg-blue-50 text-blue-600 text-[9px] font-black uppercase rounded-full border border-blue-100/50">

                                    {{ $k->jabatan }}

                                </span>

                            </td>

                            <td class="p-5 text-sm font-medium text-slate-600">{{ $k->no_hp }}</td>

                           

                            <td class="p-5 text-center border-l border-slate-50">

                                <div class="flex justify-center items-center gap-3">

                                    <a href="{{ route('karyawan.edit', $k->id) }}" class="p-2.5 bg-amber-50 text-amber-600 rounded-xl hover:bg-amber-100 transition-all border border-amber-100" title="Edit">

                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">

                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />

                                        </svg>

                                    </a>



                                    <form action="{{ route('karyawan.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data {{ $k->nama_lengkap }}?');" class="inline">

                                        @csrf

                                        @method('DELETE')

                                        <button type="submit" class="p-2.5 bg-red-50 text-red-600 rounded-xl hover:bg-red-100 transition-all border border-red-100" title="Hapus">

                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">

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

</body>

</html>