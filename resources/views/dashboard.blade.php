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
                        <a href="{{ route('karyawan.create') }}" class="bg-white text-blue-900 font-bold py-3 px-6 rounded-2xl hover:bg-blue-50 transition-all shadow-xl active:scale-95 text-sm uppercase tracking-wider">
                            + Tambah Karyawan
                        </a>
                        </div>
                </div>
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white opacity-10 rounded-full blur-3xl"></div>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border-b-4 border-blue-500 transition-all hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Hadir Anda</p>
                            <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $totalHadir }} Hari</h3>
                        </div>
                        <div class="bg-blue-50 p-3 rounded-xl">
                            <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-blue-400 mt-4 font-semibold italic">* Terhitung selama periode KKN di PT Saltek</p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border-b-4 border-orange-500 transition-all hover:shadow-md">
                    <div class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Info Penting</div>
                    <div class="bg-orange-50 border-l-4 border-orange-400 p-3">
                        <p class="text-sm text-orange-800 font-medium">
                            📌 Jangan lupa upload laporan mingguan KKN di portal universitas setiap hari Sabtu.
                        </p>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 flex flex-col justify-center border-b-4 border-green-500 transition-all hover:shadow-md">
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3 text-center">Menu Utama</p>
                    <a href="{{ route('absensi.index') }}" class="group relative flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg shadow-green-200 transition-all active:scale-95">
                        <span class="mr-2 text-center uppercase">Buka Panel Absensi</span>
                        <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                </div>

            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl">
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

        </div>
    </div>
</x-app-layout>