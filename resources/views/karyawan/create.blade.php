<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Karyawan - PT Saltek</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 font-sans p-4 md:p-10">

    <div class="max-w-xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('karyawan.index') }}" class="text-blue-600 text-xs font-bold hover:underline flex items-center gap-2">
                ← KEMBALI KE DAFTAR
            </a>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight mt-4">TAMBAH KARYAWAN</h1>
            <p class="text-slate-500 text-sm font-medium">Lengkapi data untuk menambahkan anggota tim baru di PT Saltek.</p>
        </div>

        <div class="bg-white rounded-3xl shadow-xl border border-slate-200 p-8">
            <form action="{{ route('karyawan.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Hubungkan ke Akun User</label>
                    <select name="user_id" class="w-full bg-slate-50 border border-slate-200 p-4 rounded-2xl text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all appearance-none cursor-pointer">
                        <option value="" disabled selected>-- Pilih Akun Mahasiswa/Karyawan --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} (ID: {{ $user->id }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 px-1">NIP (Nomor Induk Pegawai)</label>
                    <input type="text" name="nip" placeholder="Contoh: 2026001" required
                        class="w-full bg-slate-50 border border-slate-200 p-4 rounded-2xl text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" placeholder="Masukkan nama sesuai KTP..." required
                        class="w-full bg-slate-50 border border-slate-200 p-4 rounded-2xl text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Jabatan</label>
                    <input type="text" name="jabatan" placeholder="Contoh: Admin / Teknisi Fiber Optic" required
                        class="w-full bg-slate-50 border border-slate-200 p-4 rounded-2xl text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Nomor WhatsApp</label>
                    <input type="text" name="no_hp" placeholder="Contoh: 081234567890" 
                        class="w-full bg-slate-50 border border-slate-200 p-4 rounded-2xl text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-5 rounded-2xl shadow-lg shadow-blue-100 transition-all active:scale-95 text-sm uppercase tracking-widest">
                    Simpan Data Karyawan
                </button>
            </form>
        </div>
    </div

</body>
</html>