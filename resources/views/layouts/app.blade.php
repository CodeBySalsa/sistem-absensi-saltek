<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        {{-- Penambahan: Agar custom style dari dashboard bisa masuk ke sini --}}
        @stack('styles')
    </head>
    {{-- Perubahan: Mengganti bg-gray-50 menjadi bg-slate-50 agar lebih senada dengan dashboard baru --}}
    <body class="font-sans antialiased bg-slate-50 text-slate-900">
        <div class="min-h-screen">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                {{-- Perubahan: Menambahkan logic transparan jika di dashboard agar gradasi dashboard tidak terpotong --}}
                <header class="{{ Request::routeIs('dashboard') ? 'bg-transparent' : 'bg-white shadow-sm border-b border-gray-100' }}">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        {{-- Penambahan: Tempat script dari dashboard berkumpul --}}
        @stack('scripts')
    </body>
</html>