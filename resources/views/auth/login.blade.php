<x-guest-layout>
    {{-- LOGO PT SALTTEK DUMPANG JAYA DI LETAKKAN DI ATAS MENU KEMBALI --}}
    <div class="flex flex-col items-center justify-center mb-6 text-center">
        <div class="w-20 h-20 bg-white rounded-2xl flex items-center justify-center shadow-md border border-slate-100 p-1.5 transition-transform duration-300">
            <img src="{{ asset('logo pt salttek dumpang jaya.jpeg') }}" alt="Logo PT Salttek" class="w-full h-full object-contain">
        </div>
        <h2 class="text-lg font-extrabold text-slate-800 tracking-tight uppercase mt-3">
            PT SALTTEK <span class="text-blue-600">DUMPANG JAYA</span>
        </h2>
        <p class="text-[11px] text-slate-400 mt-0.5 font-medium">
            Silakan masuk untuk memonitor absensi digital Anda
        </p>
    </div>

    <div class="mb-6">
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-400 hover:text-blue-600 transition-colors group">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke Beranda
        </a>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>