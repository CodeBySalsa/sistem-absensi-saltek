<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Karyawan - PT Saltek</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen p-6 md:p-10">

    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <a href="{{ route('karyawan.index') }}" class="text-blue-600 text-[10px] font-black hover:text-blue-800 flex items-center gap-2 tracking-[0.2em] group">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                KEMBALI KE DAFTAR
            </a>
            
            <a href="{{ route('dashboard') }}" class="bg-white border border-slate-200 text-slate-600 text-[10px] font-black px-4 py-2 rounded-xl hover:bg-slate-100 transition-all flex items-center gap-2 tracking-[0.1em] shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                DASHBOARD
            </a>
        </div>

        <div class="bg-white p-8 md:p-12 rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-amber-500/5 rounded-full -mr-16 -mt-16 blur-2xl"></div>
            
            <div class="mb-10 text-center relative">
                <div class="inline-block p-4 bg-amber-50 rounded-3xl mb-4 border border-amber-100 shadow-sm shadow-amber-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight uppercase">Edit Data Karyawan</h1>
                <p class="text-slate-500 font-medium mt-2 text-sm">Sedang memperbarui data: <span class="text-blue-600 font-bold">{{ $karyawan->nama_lengkap }}</span></p>
            </div>

            <form action="{{ route('karyawan.update', $karyawan->id) }}" method="POST" class="space-y-7 relative">
                @csrf
                @method('PUT')

                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Nomor Induk Pegawai (NIP)</label>
                    <input type="text" name="nip" value="{{ old('nip', $karyawan->nip) }}" 
                        class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-blue-500 focus:bg-white transition-all outline-none font-bold text-slate-800 placeholder:text-slate-300">
                </div>

                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $karyawan->nama_lengkap) }}" 
                        class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-blue-500 focus:bg-white transition-all outline-none font-bold text-slate-800 placeholder:text-slate-300">
                </div>

                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Jabatan / Posisi</label>
                    <div class="relative">
                        <select name="jabatan" class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-blue-500 focus:bg-white transition-all outline-none font-bold text-slate-800 appearance-none cursor-pointer">
                            <option value="Staff" {{ $karyawan->jabatan == 'Staff' ? 'selected' : '' }}>Staff</option>
                            <option value="Mahasiswa KKN" {{ $karyawan->jabatan == 'Mahasiswa KKN' ? 'selected' : '' }}>Mahasiswa KKN</option>
                            <option value="Teknisi Fiber Optic" {{ $karyawan->jabatan == 'Teknisi Fiber Optic' ? 'selected' : '' }}>Teknisi Fiber Optic</option>
                            <option value="Admin" {{ $karyawan->jabatan == 'Admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Hubungkan ke Akun Login</label>
                    <div class="relative">
                        <select name="user_id" class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-blue-500 focus:bg-white transition-all outline-none font-bold text-slate-800 appearance-none cursor-pointer">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $karyawan->user_id == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-4 pt-6">
                    <button type="submit" class="flex-[2] bg-blue-600 hover:bg-blue-700 text-white font-black py-5 rounded-2xl transition-all shadow-xl shadow-blue-100 uppercase tracking-widest text-[11px] flex justify-center items-center gap-2 active:scale-[0.98]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('karyawan.index') }}" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-black py-5 rounded-2xl transition-all text-center uppercase tracking-widest text-[11px] border border-slate-200">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>