<x-guest-layout>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<div class="register-wrapper">

    <div class="bg-bubble bubble-left"></div>
    <div class="bg-bubble bubble-right"></div>

    <div class="sparkles">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
    </div>

    <div class="wave wave-top"></div>
    <div class="wave wave-bottom"></div>

    <div class="register-card">

        <div class="logo-box">
            <img src="{{ asset('logo pt salttek dumpang jaya.jpeg') }}" alt="Logo PT Salttek">
        </div>

        <h1>CREATE ACCOUNT</h1>
        <p class="subtitle">Sistem Absensi Digital Modern</p>

        <a href="{{ url('/') }}" class="back-btn">
            ← Kembali ke Beranda
        </a>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="input-group">
                <label>Name</label>

                <div class="input-box">
                    <span>👤</span>

                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Enter your name"
                        required
                        autofocus
                    >
                </div>

                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="input-group">
                <label>Email</label>

                <div class="input-box">
                    <span>✉️</span>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Enter your email"
                        required
                    >
                </div>

                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="input-group">
                <label>Password</label>

                <div class="input-box">
                    <span>🔒</span>

                    <input
                        id="password"
                        type="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                    >

                <small class="toggle-password">👁</small>
                </div>

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="input-group">
                <label>Confirm Password</label>

                <div class="input-box">
                    <span>🔒</span>

                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        placeholder="Confirm your password"
                        required
                    >

                    <small class="toggle-password">👁</small>
                </div>

                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <button type="submit" class="register-btn">
                REGISTER ACCOUNT
            </button>

            <div class="login-link">
                Already have an account?
                <a href="{{ route('login') }}">Login here</a>
            </div>

        </form>

    </div>

</div>

</x-guest-layout>

<style>

/* ======================================================== */
/* FIX: OVERRIDE UNTUK MENGHILANGKAN KOTAK PUTIH LAYOUT LUAR */
/* ======================================================== */
div.min-h-screen.bg-gray-100,
div.min-h-screen.flex.flex-col,
.min-h-screen {
    background: transparent !important;
    background-image: none !important;
    box-shadow: none !important;
    padding: 0 !important;
}

div.w-full.sm\:max-w-md.mt-6.px-6.py-4.bg-white,
div.sm\:max-w-md,
.bg-white.shadow-md {
    background: transparent !important;
    box-shadow: none !important;
    border: none !important;
    padding: 0 !important;
    max-w: 100% !important;
    width: auto !important;
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

html,
body{
    width:100%;
    min-height:100vh;
    overflow: hidden !important; /* FIX: Layar dikunci total biar TIDAK BISA DI-SCROLL */
    font-family:'Poppins',sans-serif;
    background:#f4f7ff;
}

/* =============================== */
/* FORCE LARAVEL BREEZE */
/* =============================== */

body{
    display:block !important;
    position: fixed; /* FIX: Mengunci posisi dokumen */
}

main{
    width:100% !important;
    min-height:100vh !important;

    display:flex !important;
    justify-content:center !important;
    align-items:center !important;

    background:transparent !important;

    padding:15px 20px !important; /* FIX: Menyesuaikan padding agar pas dengan layar laptop */

    position:relative;
    overflow:hidden;
}

/* =============================== */
/* WRAPPER */
/* =============================== */

.register-wrapper{
    position:fixed; /* FIX: Dibikin fixed memenuhi layar */
    inset:0;

    width:100vw;
    height:100vh;

    display:flex;
    justify-content:center;
    align-items:center;

    overflow:hidden;

    z-index:9999 !important; /* FIX: Naik ke lapisan paling atas */
}

/* =============================== */
/* CARD */
/* =============================== */
.register-card{
    position:relative;

    width:100%;
    max-width:440px; /* FIX: Dipersempit setara card login agar proposional dan tidak meluber ke bawah */

    padding:24px 30px; /* FIX: Dibuat kompak agar pas dalam satu layar penuh */

    border-radius:32px;

    background:rgba(255,255,255,0.72);

    backdrop-filter:blur(20px);
    -webkit-backdrop-filter:blur(20px);

    border:1px solid rgba(255,255,255,0.9);

    box-shadow:
    0 20px 60px rgba(99,102,241,0.15);

    z-index:10;
}
/* =============================== */
/* LOGO */
/* =============================== */

.logo-box{
    width:75px; /* FIX: Ukuran logo disesuaikan lebih minimalis agar hemat ruang vertical */
    height:75px;

    margin:0 auto 12px;

    border-radius:20px;

    background:white;

    display:flex;
    justify-content:center;
    align-items:center;

    box-shadow:
    0 10px 30px rgba(96,98,255,0.15);
}

.logo-box img{
    width:50px;
}

/* =============================== */
/* TITLE */
/* =============================== */
.register-card h1{
    text-align:center;

    font-size:22px; /* FIX: Sesuai request untuk diperkecil ukurannya */

    line-height:1.1;

    margin-bottom:4px;

    font-weight:800;

    letter-spacing:-1px;

    background:linear-gradient(
        90deg,
        #2563eb,
        #9333ea
    );

    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
}

.subtitle{
    text-align:center;

    color:#7b8ba7;

    font-size:12px; /* FIX: Dikecilkan sedikit agar kompak */

    margin-bottom:12px;
}

/* =============================== */
/* BACK BUTTON */
/* =============================== */

.back-btn{
    display:block; /* FIX: Centered text block */
    text-align: center;

    margin-bottom:16px;

    text-decoration:none;

    color:#64748b;
    font-size: 12px;

    font-weight:600;

    transition:.3s;
}

.back-btn:hover{
    color:#5b5df5;
}

/* =============================== */
/* INPUT */
/* =============================== */

.input-group{
    margin-bottom:10px; /* FIX: Jarak dirapatkan agar muat satu layar tanpa scroll */
}

.input-group label{
    display:block;

    margin-bottom:4px;

    font-size:12px; /* FIX: Compact label */

    font-weight:700;

    color:#1e293b;
}

.input-box{
    width:100%;

    height:42px; /* FIX: Tinggi kotak input dioptimasi agar pas */

    border-radius:12px;

    border:2px solid #e8edff;

    background:white;

    display:flex;
    align-items:center;

    gap:10px;

    padding:0 14px;

    transition:.3s;

    box-shadow:
    0 5px 18px rgba(112,125,255,0.07);
}

.input-box:hover{
    transform:translateY(-1px);
}

.input-box:focus-within{
    border-color:#7c3aed;

    box-shadow:
    0 0 0 5px rgba(124,58,237,0.10),
    0 10px 25px rgba(124,58,237,0.14);
}

.input-box span{
    font-size:16px;
}

.input-box small{
    font-size:15px;
    opacity:.6;
    cursor:pointer;
}

.input-box input{
    flex:1;

    width:100%;

    border:none !important;
    outline:none !important;

    background:transparent !important;

    font-size:13px;

    color:#334155;
}

.input-box input::placeholder{
    color:#94a3b8;
}

/* =============================== */
/* BUTTON */
/* =============================== */

.register-btn{
    width:100%;

    height:42px; /* FIX: Penyesuaian tinggi tombol */

    border:none;

    border-radius:12px;

    margin-top:8px;

    font-size:14px;

    font-weight:700;

    color:white;

    cursor:pointer;

    background:
    linear-gradient(
        90deg,
        #2563eb,
        #9333ea
    );

    box-shadow:
    0 12px 30px rgba(124,58,237,0.28);

    transition:.35s;
}

.register-btn:hover{
    transform:translateY(-2px);

    box-shadow:
    0 18px 40px rgba(124,58,237,0.35);
}

/* =============================== */
/* LOGIN */
/* =============================== */

.login-link{
    text-align:center;

    margin-top:12px;

    color:#7c8aa5;
    font-size: 12px;
}

.login-link a{
    color:#5b34f2;

    font-weight:700;

    text-decoration:none;
}

/* =============================== */
/* BUBBLE */
/* =============================== */

.bg-bubble{
    position:fixed;

    border-radius:999px;

    filter:blur(60px);

    z-index:0;
}

.bubble-left{
    width:320px;
    height:320px;

    left:-90px;
    top:100px;

    background:
    radial-gradient(
        circle,
        rgba(79,104,255,0.95),
        rgba(79,104,255,0)
    );
}

.bubble-right{
    width:340px;
    height:340px;

    right:-100px;
    bottom:80px;

    background:
    radial-gradient(
        circle,
        rgba(185,90,255,0.95),
        rgba(185,90,255,0)
    );
}

/* =============================== */
/* WAVES */
/* =============================== */

.wave{
    position:fixed;

    width:520px;
    height:520px;

    border-radius:50%;

    border:1.5px solid rgba(170,180,255,0.22);

    z-index:0;
}

.wave-top{
    top:-250px;
    right:-250px;
}

.wave-bottom{
    bottom:-260px;
    left:-260px;
}

/* =============================== */
/* SPARKLES */
/* =============================== */

.sparkles{
    position:fixed;

    inset:0;

    pointer-events:none;

    z-index:1;
}

.sparkles span{
    position:absolute;

    width:7px;
    height:7px;

    border-radius:50%;

    background:white;

    box-shadow:
    0 0 10px white,
    0 0 20px white,
    0 0 35px white;

    animation:sparkle 4s infinite ease-in-out;
}

.sparkles span:nth-child(1){ top:12%; left:10%; }
.sparkles span:nth-child(2){ top:20%; right:15%; }
.sparkles span:nth-child(3){ top:35%; left:18%; }
.sparkles span:nth-child(4){ top:60%; right:12%; }
.sparkles span:nth-child(5){ bottom:18%; left:15%; }
.sparkles span:nth-child(6){ bottom:12%; right:22%; }
.sparkles span:nth-child(7){ top:72%; left:28%; }
.sparkles span:nth-child(8){ top:40%; right:30%; }

@keyframes sparkle{

    0%,100%{
        opacity:.2;
        transform:
        scale(1)
        translateY(0px);
    }

    50%{
        opacity:1;

        transform:
        scale(1.8)
        translateY(-6px);
    }
}

/* =============================== */
/* MOBILE RESPONSIVE RESET */
/* =============================== */

@media(max-width:768px){
    html, body {
        overflow: auto !important;
        position: relative;
    }
    .register-wrapper {
        position: relative;
        height: auto;
        padding: 20px 10px;
    }
    .register-card{
        padding:24px 20px;
        max-width: 100%;
    }

    .register-card h1{
        font-size:26px;
    }

    .input-box{
        height:46px;
    }

    .register-btn{
        height:46px;
        font-size:16px;
    }

    .logo-box{
        width:80px;
        height:80px;
    }

    .logo-box img{
        width:55px;
    }
}

.toggle-password{
    cursor:pointer;
    transition:.3s;
}

.toggle-password:hover{
    transform:scale(1.15);
}

.toggle-password{
    cursor:pointer;
    z-index:10;
    position:relative;
}

.toggle-password{
    cursor:pointer;
    user-select:none;
}

</style>

<script>

document.querySelectorAll('.toggle-password').forEach(function(button){

    button.addEventListener('click', function(){

        const input =
        this.parentElement.querySelector('input');

        if(input.type === 'password'){

            input.type = 'text';
            this.innerHTML = '🙈';

        }else{

            input.type = 'password';
            this.innerHTML = '👁';

        }

    });

});

</script>