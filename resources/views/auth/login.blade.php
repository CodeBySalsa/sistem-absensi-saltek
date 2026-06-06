<x-guest-layout>

<div class="effects">

    <span class="spark s1">✦</span>
    <span class="spark s2">✦</span>
    <span class="spark s3">✦</span>
    <span class="spark s4">✦</span>
    <span class="spark s5">✦</span>
    <span class="spark s6">✦</span>
    <span class="spark s7">✦</span>
    <span class="spark s8">✦</span>

    <span class="bubble b1"></span>
    <span class="bubble b2"></span>
    <span class="bubble b3"></span>
    <span class="bubble b4"></span>
    <span class="bubble b5"></span>
    <span class="bubble b6"></span>
    <span class="bubble b7"></span>

</div>

<div class="login-wrapper">

    <div class="login-card">

        <div class="logo-box">
    <img src="{{ asset('logo.jpeg') }}" alt="Logo PT Salttek">
</div>
        <h1>
            PT SALTTEK
            <span>DUMPANG JAYA</span>
        </h1>

        <p class="subtitle">
            Sistem Monitoring Absensi Digital & Manajemen Karyawan Modern
        </p>

        <a href="{{ url('/') }}" class="back-btn">
            ← Kembali ke Beranda
        </a>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="input-group">

                <label>Email</label>

                <div class="input-box">

                    <span class="icon">
                        ✉️
                    </span>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Masukkan email"
                        required
                        autofocus
                    >

                </div>

                <x-input-error :messages="$errors->get('email')" class="mt-2" />

            </div>

            <div class="input-group">

                <label>Password</label>

                <div class="input-box">

                    <span class="icon">
                        🔒
                    </span>

                    <input
                        id="password"
                        type="password"
                        name="password"
                        placeholder="Masukkan password"
                        required
                    >

                    <span class="toggle-password">
                        👁
                    </span>

                </div>

                <x-input-error :messages="$errors->get('password')" class="mt-2" />

            </div>

            <div class="remember-box">

                <label>

                    <input type="checkbox" name="remember">

                    <span>Remember me</span>

                </label>

            </div>

            <div class="login-footer">

                @if (Route::has('password.request'))

                    <a href="{{ route('password.request') }}" class="forgot-link">
                        Forgot your password?
                    </a>

                @endif

                <button type="submit" class="login-btn">
                    LOG IN
                </button>

            </div>

        </form>

    </div>

</div>

</x-guest-layout>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

/* Trik Utama: Mematikan total background putih luar bawaan layout Guest */
div.min-h-screen.bg-gray-100,
div.min-h-screen.flex.flex-col,
.min-h-screen {
    background: transparent !important;
    background-image: none !important;
    box-shadow: none !important;
    padding: 0 !important;
}

/* Menghilangkan paksa kotak putih panjang luar bawaan laravel breeze/jetstream */
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

body{
    overflow: hidden !important; /* Kunci layar biar GA BISA DI-SCROLL sama sekali */
    height: 100vh;
    width: 100vw;
    position: fixed;

    background:
    linear-gradient(
        135deg,
        #eef4ff,
        #f8fbff
    ) !important;

    font-family:
    'Plus Jakarta Sans',
    sans-serif;
}

/* ===================================== */
/* AURORA BACKGROUND */
/* ===================================== */

body::before{
    content:'';

    position:fixed;

    width:700px;
    height:700px;

    left:-250px;
    top:-100px;

    border-radius:50%;

    background:
    radial-gradient(
        circle,
        rgba(59,130,246,0.55),
        transparent 70%
    );

    filter:blur(90px);

    animation:auroraBlue 10s ease-in-out infinite alternate;

    z-index:0;
}

body::after{
    content:'';

    position:fixed;

    width:750px;
    height:750px;

    right:-250px;
    bottom:-200px;

    border-radius:50%;

    background:
    radial-gradient(
        circle,
        rgba(168,85,247,0.55),
        transparent 70%
    );

    filter:blur(100px);

    animation:auroraPurple 12s ease-in-out infinite alternate;

    z-index:0;
}

@keyframes auroraBlue{

    0%{
        transform:
        translateX(0)
        translateY(0);
    }

    100%{
        transform:
        translateX(60px)
        translateY(40px);
    }
}

@keyframes auroraPurple{

    0%{
        transform:
        translateX(0)
        translateY(0);
    }

    100%{
        transform:
        translateX(-60px)
        translateY(-40px);
    }
}

/* ===================================== */
/* EFFECTS */
/* ===================================== */

.effects{
    position:fixed;
    inset:0;

    overflow:hidden;

    pointer-events:none;

    z-index:1;
}

/* ===================================== */
/* SPARKLES */
/* ===================================== */

.spark{
    position:absolute;

    color:white;

    font-size:22px;

    text-shadow:
    0 0 10px #fff,
    0 0 20px #60a5fa,
    0 0 40px #8b5cf6,
    0 0 60px #7c3aed;

    animation:sparkleMove 4s ease-in-out infinite;
}

.s1{
    top:10%;
    left:8%;
}

.s2{
    top:18%;
    right:12%;
    animation-delay:1s;
}

.s3{
    top:35%;
    left:18%;
    animation-delay:2s;
}

.s4{
    top:62%;
    right:14%;
    animation-delay:1.5s;
}

.s5{
    bottom:18%;
    left:14%;
    animation-delay:2.5s;
}

.s6{
    bottom:15%;
    right:20%;
    animation-delay:3s;
}

.s7{
    top:50%;
    left:50%;
    animation-delay:1s;
}

.s8{
    top:75%;
    right:35%;
    animation-delay:2s;
}

@keyframes sparkleMove{

    0%,100%{
        opacity:.2;

        transform:
        scale(.5)
        rotate(0deg)
        translateY(0px);
    }

    50%{
        opacity:1;

        transform:
        scale(1.8)
        rotate(180deg)
        translateY(-10px);
    }
}

/* ===================================== */
/* BUBBLES */
/* ===================================== */

.bubble{
    position:absolute;

    bottom:-180px;

    border-radius:50%;

    background:
    radial-gradient(
        circle at 30% 30%,
        rgba(255,255,255,0.75),
        rgba(99,102,241,0.40),
        rgba(59,130,246,0.28)
    );

    backdrop-filter:blur(10px);

    border:
    1px solid rgba(255,255,255,0.35);

    box-shadow:
    inset 0 0 18px rgba(255,255,255,0.45),
    0 0 35px rgba(59,130,246,0.28),
    0 0 60px rgba(139,92,246,0.22);

    animation:bubbleFloat linear infinite;
}

.b1{
    width:140px;
    height:140px;
    left:5%;
    animation-duration:18s;
}

.b2{
    width:80px;
    height:80px;
    left:20%;
    animation-duration:13s;
    animation-delay:2s;
}

.b3{
    width:170px;
    height:170px;
    right:8%;
    animation-duration:22s;
}

.b4{
    width:60px;
    height:60px;
    right:28%;
    animation-duration:10s;
    animation-delay:1s;
}

.b5{
    width:120px;
    height:120px;
    left:50%;
    animation-duration:16s;
}

.b6{
    width:90px;
    height:90px;
    right:45%;
    animation-duration:14s;
    animation-delay:3s;
}

.b7{
    width:50px;
    height:50px;
    left:75%;
    animation-duration:9s;
}

@keyframes bubbleFloat{

    0%{
        transform:
        translateY(0)
        scale(.8)
        rotate(0deg);

        opacity:0;
    }

    15%{
        opacity:.8;
    }

    100%{
        transform:
        translateY(-140vh)
        scale(1.4)
        rotate(360deg);

        opacity:0;
    }
}

/* ===================================== */
/* WRAPPER & CARD COMPACT */
/* ===================================== */

.login-wrapper{
    position:fixed;
    inset:0;
    width:100vw;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:15px 20px;
    z-index:9999 !important; /* Dipaksa naik ke paling atas agar layout luar tidak mengganggu */
}

.login-card{
    position:relative;
    width:100%;
    max-width:400px;
    padding:24px 28px;
    border-radius:24px;
    background: rgba(255,255,255,0.28) !important;
    backdrop-filter:blur(20px);
    -webkit-backdrop-filter:blur(20px);
    border: 1px solid rgba(255,255,255,0.6);
    box-shadow:
    0 25px 70px rgba(59,130,246,0.15),
    0 0 50px rgba(139,92,246,0.12);
    overflow:hidden;
    z-index:10;
}

form{
    width:100%;
    display:flex;
    flex-direction:column;
    gap:12px;
}

.login-card::before{
    content:'';
    position:absolute;
    inset:-2px;
    border-radius:22px;
    padding:2px;
    background:
    linear-gradient(
        130deg,
        #3b82f6,
        #7c3aed,
        #06b6d4,
        #3b82f6
    );
    background-size:300% 300%;
    animation:borderGlow 6s linear infinite;
    -webkit-mask:
        linear-gradient(#fff 0 0) content-box,
        linear-gradient(#fff 0 0);
    -webkit-mask-composite:xor;
            mask-composite:exclude;
    z-index:-1;
}

/* ===================================== */
/* LOGO & TEXT */
/* ===================================== */

/* Update CSS untuk logo agar mengikuti bentuk asli (seperti landing page) */
.logo-box {
    width: 100px; /* Sesuaikan ukuran dengan landing page */
    height: 100px;
    margin: 0 auto 10px;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden; 
    /* Gunakan border-radius yang lebih kecil agar bentuknya tetap persegi dengan sudut melengkung */
    border-radius: 12px; 
    background: transparent; /* Hilangkan background putih jika tidak perlu */
}

.logo-box img {
    width: 100%;
    height: 100%;
    /* Gunakan contain agar proporsi logo asli tetap terjaga */
    object-fit: contain; 
     transition:1s ease;
}

.login-card h1{
    text-align:center;
    font-size:18px;
    font-weight:800;
    color:#1e293b;
    margin-bottom:4px;
}

.login-card h1 span{
    display:block;
    background:
    linear-gradient(
        90deg,
        #2563eb,
        #7c3aed
    );
    -webkit-background-clip:text;
    color:transparent;
}

.subtitle{
    text-align:center;
    font-size:11px;
    color:#64748b;
    margin-bottom:14px;
    line-height:1.5;
}

.back-btn{
    display:block;
    text-align:center;
    margin-bottom:14px;
    text-decoration:none;
    color:#64748b;
    font-size:11px;
    font-weight:600;
    transition:.3s;
}

.back-btn:hover{
    color:#7c3aed;
}

/* ===================================== */
/* INPUT COMPACT */
/* ===================================== */

.input-group{
    margin-bottom:0px;
}

.input-group label{
    display:block;
    margin-bottom:5px;
    font-size:12px;
    font-weight:700;
    color:#1e293b;
}

.input-box{
    width:100%;
    height:44px;
    border-radius:12px;
    background:
    linear-gradient(
        90deg,
        rgba(255,255,255,0.65),
        rgba(214,234,255,0.78)
    );
    display:flex;
    align-items:center;
    gap:10px;
    padding:0 14px;
    border: 1px solid rgba(255,255,255,0.6);
    transition:.3s;
}

.input-box:hover{
    transform:translateY(-1px);
}

.input-box:focus-within{
    box-shadow: 0 0 0 3px rgba(124,58,237,0.10);
}

.icon{
    font-size:16px;
}

.input-box input{
    flex:1;
    border:none !important;
    outline:none !important;
    background:transparent !important;
    font-size:13px;
    color:#334155;
}

.input-box input::placeholder{
    color:#94a3b8;
}

.toggle-password{
    display:flex;
    align-items:center;
    justify-content:center;
    min-width:30px;
    cursor:pointer;
    font-size:16px;
    transition:.3s;
}

/* ===================================== */
/* REMEMBER & FOOTER */
/* ===================================== */

.remember-box{
    margin-top:2px;
}

.remember-box label{
    display:flex;
    align-items:center;
    gap:8px;
    color:#475569;
    font-size:12px;
}

.remember-box input{
    width:14px;
    height:14px;
    accent-color:#7c3aed;
}

.login-footer{
    margin-top:8px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:12px;
}

.forgot-link{
    color:#64748b;
    text-decoration:none;
    font-size:12px;
    transition:.3s;
}

.forgot-link:hover{
    color:#7c3aed;
}

/* ===================================== */
/* BUTTON COMPACT */
/* ===================================== */

.login-btn{
    border:none;
    min-width:110px;
    height:42px;
    padding:0 20px;
    border-radius:12px;
    background:
    linear-gradient(
        130deg,
        #2563eb,
        #9333ea
    );
    color:white;
    font-weight:700;
    font-size:13px;
    letter-spacing:1px;
    cursor:pointer;
    transition:.3s;
    box-shadow: 0 8px 20px rgba(124,58,237,0.25);
}

.login-btn:hover{
    transform:translateY(-2px);
    box-shadow: 0 12px 25px rgba(124,58,237,0.35);
}

/* ===================================== */
/* MOBILE */
/* ===================================== */

@media(max-width:768px){
    .login-wrapper {
        position: relative;
        height: auto;
    }
    body {
        overflow: auto !important;
        position: relative;
    }
    .login-card{
        max-width:95%;
        padding:20px 20px;
    }

    .login-footer{
        flex-direction:column;
        align-items:stretch;
    }

    .login-btn{
        width:100%;
    }
}

</style>

<script>

const togglePassword =
document.querySelector('.toggle-password');

const password =
document.querySelector('#password');

togglePassword.addEventListener('click', function(){

    if(password.type === 'password'){

        password.type = 'text';
        this.innerHTML = '🙈';

    }else{

        password.type = 'password';
        this.innerHTML = '👁';
    }

});

</script>