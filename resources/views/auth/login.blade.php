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


<style>


    <style>

/* EFEK CAHAYA HALUS DI BACKGROUND */
body::before{
    content:'';

    position:fixed;

    top:-150px;
    right:-150px;

    width:400px;
    height:400px;

    background:
    radial-gradient(
        circle,
        rgba(59,130,246,0.15),
        transparent 70%
    );

    z-index:0;
}

body::after{
    content:'';

    position:fixed;

    bottom:-150px;
    left:-150px;

    width:400px;
    height:400px;

    background:
    radial-gradient(
        circle,
        rgba(96,165,250,0.12),
        transparent 70%
    );

    z-index:0;
}

/* CARD ADA EFEK KACA HALUS */
.w-full.sm\:max-w-md{
    backdrop-filter:blur(10px);

    position:relative;

    z-index:2;
}

/* ICON / LOGO */
img{
    filter:
    drop-shadow(0 6px 15px rgba(37,99,235,0.15));

    animation:logoFloat 4s ease-in-out infinite;
}

/* LOGO FLOAT */
@keyframes logoFloat{
    0%{
        transform:translateY(0px);
    }

    50%{
        transform:translateY(-5px);
    }

    100%{
        transform:translateY(0px);
    }
}

/* INPUT HOVER */
input:hover{
    border-color:#93c5fd !important;
}

/* BUTTON SHINE */
button{
    position:relative;

    overflow:hidden;
}

button::before{
    content:'';

    position:absolute;

    top:0;
    left:-100%;

    width:100%;
    height:100%;

    background:
    linear-gradient(
        120deg,
        transparent,
        rgba(255,255,255,0.3),
        transparent
    );

    transition:0.7s;
}

button:hover::before{
    left:100%;
}

/* LINK ANIMATION */
a{
    position:relative;
}

a::after{
    content:'';

    position:absolute;

    left:0;
    bottom:-2px;

    width:0%;

    height:2px;

    background:#2563eb;

    transition:0.3s;
}

a:hover::after{
    width:100%;
}



@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

body{
    font-family:'Poppins',sans-serif;

    background:
    linear-gradient(
        135deg,
        #0f172a,
        #1e3a8a,
        #312e81
    ) !important;

    overflow:hidden;

    position:relative;

    background-size:400% 400%;

    animation:bgMove 12s ease infinite;
}

/* BACKGROUND GERAK */
@keyframes bgMove{
    0%{
        background-position:0% 50%;
    }

    50%{
        background-position:100% 50%;
    }

    100%{
        background-position:0% 50%;
    }
}

/* BULATAN GLOW */
body::before,
body::after{
    content:'';

    position:absolute;

    width:350px;
    height:350px;

    border-radius:50%;

    filter:blur(120px);

    z-index:0;
}

body::before{
    background:#3b82f6;

    top:-120px;
    left:-120px;

    animation:float1 8s infinite alternate;
}

body::after{
    background:#8b5cf6;

    bottom:-120px;
    right:-120px;

    animation:float2 10s infinite alternate;
}

/* ANIMASI */
@keyframes float1{
    from{
        transform:translateY(0px);
    }

    to{
        transform:translateY(50px);
    }
}

@keyframes float2{
    from{
        transform:translateY(0px);
    }

    to{
        transform:translateY(-50px);
    }
}

/* CARD LOGIN */
.w-full.sm\:max-w-md{
    position:relative;

    z-index:10;

    background:rgba(255,255,255,0.15) !important;

    backdrop-filter:blur(18px);

    border-radius:30px !important;

    border:1px solid rgba(255,255,255,0.15);

    box-shadow:
    0 10px 40px rgba(0,0,0,0.25);

    overflow:hidden;

    animation:
    fadeUp 1s ease,
    floatingCard 5s ease-in-out infinite;
}

/* GLOW CARD */
.w-full.sm\:max-w-md::after{
    content:'';

    position:absolute;

    inset:-2px;

    border-radius:30px;

    background:
    linear-gradient(
        135deg,
        rgba(96,165,250,0.25),
        rgba(139,92,246,0.2),
        rgba(6,182,212,0.2)
    );

    z-index:-1;

    filter:blur(20px);
}

/* GARIS ATAS */
.w-full.sm\:max-w-md::before{
    content:'';

    position:absolute;

    top:0;
    left:0;

    width:100%;
    height:5px;

    background:
    linear-gradient(
        90deg,
        #60a5fa,
        #8b5cf6,
        #06b6d4
    );
}

/* CARD ANIMATION */
@keyframes fadeUp{
    from{
        opacity:0;
        transform:translateY(30px);
    }

    to{
        opacity:1;
        transform:translateY(0);
    }
}

@keyframes floatingCard{
    0%{
        transform:translateY(0px);
    }

    50%{
        transform:translateY(-6px);
    }

    100%{
        transform:translateY(0px);
    }
}

/* LOGO */
img{
    animation:logoFloat 4s ease-in-out infinite;

    filter:
    drop-shadow(0 0 10px rgba(255,255,255,0.3));

    transition:0.5s;
}

img:hover{
    transform:
    rotate(6deg)
    scale(1.08);
}

@keyframes logoFloat{
    0%{
        transform:translateY(0px);
    }

    50%{
        transform:translateY(-8px);
    }

    100%{
        transform:translateY(0px);
    }
}

/* EMAIL & PASSWORD ONLY */
input[type="email"],
input[type="password"]{

    border-radius:14px !important;

    transition:0.3s;

    backdrop-filter:blur(10px);

    background:
    rgba(255,255,255,0.08) !important;

    border:
    1px solid rgba(255,255,255,0.08) !important;

    color:white !important;
}

/* HOVER */
input[type="email"]:hover,
input[type="password"]:hover{

    border-color:#93c5fd !important;
}

/* FOCUS */
input[type="email"]:focus,
input[type="password"]:focus{

    transform:translateY(-1px);

    box-shadow:
    0 0 15px rgba(96,165,250,0.4) !important;
}

/* REMEMBER ME */
input[type="checkbox"]{

    appearance:auto !important;

    width:16px;
    height:16px;

    cursor:pointer;

    accent-color:#3b82f6;

    transform:none !important;

    box-shadow:none !important;
}

/* BUTTON */
button{
    border-radius:14px !important;

    background:
    linear-gradient(
        135deg,
        #3b82f6,
        #8b5cf6
    ) !important;

    transition:0.3s;

    position:relative;

    overflow:hidden;
}

/* BUTTON SHINE */
button::before{
    content:'';

    position:absolute;

    top:0;
    left:-100%;

    width:100%;
    height:100%;

    background:
    linear-gradient(
        120deg,
        transparent,
        rgba(255,255,255,0.4),
        transparent
    );

    transition:0.7s;
}

button:hover::before{
    left:100%;
}

button:hover{
    transform:translateY(-3px);

    box-shadow:
    0 10px 20px rgba(59,130,246,0.4);
}

/* LINK */
a{
    position:relative;

    transition:0.3s;
}

a::after{
    content:'';

    position:absolute;

    left:0;
    bottom:-2px;

    width:0%;
    height:2px;

    background:#60a5fa;

    transition:0.3s;
}

a:hover{
    color:#60a5fa !important;
}

a:hover::after{
    width:100%;
}

/* PARTICLE BINTANG */
body{
    position:relative;
}

/* TITIK-TITIK BERKILAU */
body .particles{
    position:fixed;

    top:0;
    left:0;

    width:100%;
    height:100%;

    pointer-events:none;

    z-index:1;
}

/* PARTICLE */
body .particles span{
    position:absolute;

    width:4px;
    height:4px;

    background:rgba(255,255,255,0.7);

    border-radius:50%;

    animation:particleMove linear infinite;
}

/* ANIMASI PARTICLE */
@keyframes particleMove{
    0%{
        transform:
        translateY(0px)
        scale(1);

        opacity:0;
    }

    20%{
        opacity:1;
    }

    100%{
        transform:
        translateY(-100vh)
        scale(0);

        opacity:0;
    }
}

/* CARD HOVER LEBIH PREMIUM */
.w-full.sm\:max-w-md:hover{
    transform:
    translateY(-5px)
    scale(1.01);

    box-shadow:
    0 20px 50px rgba(0,0,0,0.3),
    0 0 40px rgba(59,130,246,0.15);
}

/* BORDER BERKILAU */
.w-full.sm\:max-w-md{
    position:relative;
}

.w-full.sm\:max-w-md::before{
    background-size:300% 300% !important;

    animation:borderGlow 5s ease infinite;
}

@keyframes borderGlow{
    0%{
        background-position:0% 50%;
    }

    50%{
        background-position:100% 50%;
    }

    100%{
        background-position:0% 50%;
    }
}

/* INPUT GLASS */
input{
    backdrop-filter:blur(10px);

    background:
    rgba(255,255,255,0.08) !important;
}

/* INPUT ANIMATION */
input:focus{
    transform:
    translateY(-1px);

    transition:0.3s;
}

/* BUTTON PULSE */
button{
    animation:pulseButton 3s infinite;
}

@keyframes pulseButton{
    0%{
        box-shadow:
        0 0 0 0 rgba(59,130,246,0.4);
    }

    70%{
        box-shadow:
        0 0 0 15px rgba(59,130,246,0);
    }

    100%{
        box-shadow:
        0 0 0 0 rgba(59,130,246,0);
    }
}

/* LOGO LIGHT */
img{
    position:relative;
}

img::after{
    content:'';

    position:absolute;

    inset:0;

    border-radius:50%;

    background:
    radial-gradient(
        circle,
        rgba(255,255,255,0.2),
        transparent
    );
}


/* INPUT TEXT */
input[type="email"],
input[type="password"]{
    border-radius:14px !important;

    transition:0.3s;

    backdrop-filter:blur(10px);

    background:
    rgba(255,255,255,0.08) !important;

    border:
    1px solid rgba(255,255,255,0.08) !important;

    color:white !important;
}
</style>

