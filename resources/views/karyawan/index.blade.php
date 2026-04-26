<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Karyawan - PT Saltek</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10">
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Daftar Karyawan PT Saltek</h1>
        
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-blue-600 text-white">
                    <th class="p-3 text-left">NIP</th>
                    <th class="p-3 text-left">Nama Lengkap</th>
                    <th class="p-3 text-left">Jabatan</th>
                    <th class="p-3 text-left">No. HP</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($karyawans as $k)
                <tr class="border-bottom border-gray-200 hover:bg-gray-50">
                    <td class="p-3">{{ $k->nip }}</td>
                    <td class="p-3 font-semibold">{{ $k->nama_lengkap }}</td>
                    <td class="p-3 text-gray-600">{{ $k->jabatan }}</td>
                    <td class="p-3">{{ $k->no_hp }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>