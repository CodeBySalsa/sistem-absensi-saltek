<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Absensi Digital - PT SALTTEK DUMPANG JAYA</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body {
    font-family: 'Plus Jakarta Sans', sans-serif;
    overflow-x: hidden;
    overflow-y: auto; /* Izinkan scroll jika benar-benar tidak muat */
    background: linear-gradient(135deg, #eef4ff, #f8fbff);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

        /* ===================================== */
        /* AURORA BACKGROUND */
        /* ===================================== */

        body::before{
            content:'';
            position:fixed;
            inset:-20%;

            background:
            radial-gradient(circle at 20% 20%, rgba(59,130,246,0.18), transparent 30%),
            radial-gradient(circle at 80% 30%, rgba(99,102,241,0.18), transparent 35%),
            radial-gradient(circle at 50% 80%, rgba(6,182,212,0.16), transparent 35%);

            filter:blur(80px);

            animation:auroraMove 18s ease-in-out infinite alternate;

            z-index:-5;
        }

        @keyframes auroraMove{

            0%{
                transform:
                translateX(-40px)
                translateY(-20px)
                rotate(0deg);
            }

            100%{
                transform:
                translateX(40px)
                translateY(20px)
                rotate(8deg);
            }
        }

        /* ===================================== */
        /* MAIN BG */
        /* ===================================== */

        .animated-bg{
            position:fixed;
            inset:0;
            overflow:hidden;
            pointer-events:none;
            z-index:0;
        }

        /* ===================================== */
        /* FLOATING BUBBLES */
        /* ===================================== */

.bubble{
    position:absolute;
    bottom:-200px;
    border-radius:50%;

    background:
    radial-gradient(
        circle at 30% 30%,
        rgba(255,255,255,0.7),
        rgba(139,92,246,0.35),
        rgba(59,130,246,0.28)
    );

    border:1px solid rgba(255,255,255,0.25);

    backdrop-filter:blur(10px);

    box-shadow:
    inset 0 0 20px rgba(255,255,255,0.45),
    0 0 35px rgba(139,92,246,0.35),
    0 0 60px rgba(59,130,246,0.25);

    animation:bubbleMove linear infinite;
}

/* VARIASI WARNA BUBBLE */

.x1{
    background:
    radial-gradient(circle at 30% 30%,
    rgba(255,255,255,0.8),
    rgba(99,102,241,0.45),
    rgba(59,130,246,0.3));
}

.x2{
    background:
    radial-gradient(circle at 30% 30%,
    rgba(255,255,255,0.8),
    rgba(168,85,247,0.45),
    rgba(99,102,241,0.3));
}

.x3{
    background:
    radial-gradient(circle at 30% 30%,
    rgba(255,255,255,0.8),
    rgba(59,130,246,0.45),
    rgba(6,182,212,0.3));
}

.x4{
    background:
    radial-gradient(circle at 30% 30%,
    rgba(255,255,255,0.8),
    rgba(139,92,246,0.45),
    rgba(59,130,246,0.3));
}

.x5{
    background:
    radial-gradient(circle at 30% 30%,
    rgba(255,255,255,0.8),
    rgba(99,102,241,0.45),
    rgba(168,85,247,0.3));
}

        .x1{
            width:130px;
            height:130px;
            left:5%;
            animation-duration:18s;
        }

        .x2{
            width:80px;
            height:80px;
            left:25%;
            animation-duration:12s;
            animation-delay:2s;
        }

        .x3{
            width:150px;
            height:150px;
            right:10%;
            animation-duration:20s;
        }

        .x4{
            width:60px;
            height:60px;
            right:30%;
            animation-duration:10s;
            animation-delay:1s;
        }

        .x5{
            width:100px;
            height:100px;
            left:50%;
            animation-duration:15s;
        }

        @keyframes bubbleMove{

            0%{
                transform:
                translateY(0)
                scale(0.8)
                rotate(0deg);

                opacity:0;
            }

            20%{
                opacity:.7;
            }

            100%{
                transform:
                translateY(-130vh)
                scale(1.3)
                rotate(360deg);

                opacity:0;
            }
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
            0 0 20px #6ea8ff,
            0 0 35px #8b5cf6;

            animation:sparkle 3s ease-in-out infinite;
        }

        .s1{
            top:15%;
            left:12%;
        }

        .s2{
            top:25%;
            right:18%;
            animation-delay:1s;
        }

        .s3{
            bottom:20%;
            left:20%;
            animation-delay:2s;
        }

        .s4{
            bottom:30%;
            right:25%;
            animation-delay:1.5s;
        }

        @keyframes sparkle{

            0%,100%{
                opacity:.2;
                transform:
                scale(.5)
                rotate(0deg);
            }

            50%{
                opacity:1;
                transform:
                scale(1.5)
                rotate(180deg);
            }
        }

        /* ===================================== */
        /* MICRO PARTICLES */
        /* ===================================== */

        .micro-particles{
            position:fixed;
            inset:0;
            pointer-events:none;
            z-index:0;
        }

        .micro-particles span{
            position:absolute;

            width:5px;
            height:5px;

            border-radius:999px;

            background:rgba(255,255,255,0.6);

            box-shadow:
            0 0 10px rgba(255,255,255,0.6),
            0 0 20px rgba(59,130,246,0.35);

            animation:microFloat linear infinite;
        }

        .micro-particles span:nth-child(1){
            top:15%;
            left:10%;
            animation-duration:12s;
        }

        .micro-particles span:nth-child(2){
            top:35%;
            left:85%;
            animation-duration:15s;
        }

        .micro-particles span:nth-child(3){
            top:75%;
            left:20%;
            animation-duration:11s;
        }

        .micro-particles span:nth-child(4){
            top:60%;
            left:70%;
            animation-duration:17s;
        }

        .micro-particles span:nth-child(5){
            top:40%;
            left:45%;
            animation-duration:13s;
        }

        @keyframes microFloat{

            0%{
                transform:translateY(0px);
                opacity:0;
            }

            30%{
                opacity:1;
            }

            100%{
                transform:translateY(-120px);
                opacity:0;
            }
        }

        /* ===================================== */
        /* CARD */
        /* ===================================== */

        .login-wrapper{
            position:relative;
            z-index:10;

            animation:softZoom 1.2s ease;
        }

        @keyframes softZoom{

            from{
                opacity:0;
                transform:scale(.92);
            }

            to{
                opacity:1;
                transform:scale(1);
            }
        }

        .login-card{
        
            position:relative;

            overflow:hidden;

            border-radius:40px;

            padding:32px; /* Disesuaikan agar lebih kompak */

            background:rgba(255,255,255,0.95);

            backdrop-filter:blur(20px);

            border:1px solid rgba(255,255,255,0.7);

            box-shadow:
            0 25px 70px rgba(59,130,246,0.18),
            0 0 60px rgba(99,102,241,0.15);

            animation:cardFloat 6s ease-in-out infinite;
        }
        /* Tambahkan ini di bawah CSS .login-card */
            @media (max-height: 700px) {
                .login-card {
                    padding: 20px !important; /* Perkecil padding jika layar pendek */
                }
                .logo-box {
                    width: 100px !important;
                    height: 100px !important;
                    margin-bottom: 20px !important;
                }
                h1 { font-size: 24px !important; }
                .desc { margin-top: 10px !important; font-size: 12px !important; }
                .mt-10 { margin-top: 20px !important; }
            }

        @keyframes cardFloat{

            0%,100%{
                transform:translateY(0px);
            }

            50%{
                transform:translateY(-8px);
            }
        }

        /* BORDER ANIMATION */

        .login-card::before{
            content:'';

            position:absolute;
            inset:0;

            padding:2px;

            border-radius:40px;

            background:linear-gradient(
                130deg,
                #3b82f6,
                #6366f1,
                #06b6d4,
                #3b82f6
            );

            background-size:300% 300%;

            animation:borderMove 6s linear infinite;

            -webkit-mask:
                linear-gradient(#fff 0 0) content-box,
                linear-gradient(#fff 0 0);

            -webkit-mask-composite:xor;
                    mask-composite:exclude;

            z-index:-1;
        }

        @keyframes borderMove{

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

        /* ===================================== */
        /* LOGO */
        /* ===================================== */

        .logo-box{
            position:relative;

            width:130px;
            height:130px;

            margin:auto;

            border-radius:32px;

            background:white;

            display:flex;
            align-items:center;
            justify-content:center;

            overflow:hidden;

            box-shadow:
            0 15px 40px rgba(59,130,246,0.18);

            margin-bottom:30px;
        }

        .logo-box::before{
            content:'';

            position:absolute;
            inset:-6px;

            border-radius:38px;

            background:conic-gradient(
                from 0deg,
                #3b82f6,
                #06b6d4,
                #6366f1,
                #3b82f6
            );

            animation:spinGlow 5s linear infinite;

            z-index:-1;

            filter:blur(14px);
        }

        @keyframes spinGlow{

            100%{
                transform:rotate(360deg);
            }
        }

        .logo-box img{
            width:100%;
            height:100%;
            object-fit:contain;
            transition:1s ease;
        }

        .logo-box:hover img{
            transform:scale(1.08) rotate(3deg);
        }

        /* ===================================== */
        /* TITLE */
        /* ===================================== */

        h1{
            position:relative;

            font-size:32px;
            font-weight:800;

            line-height:1.2;

            text-transform:uppercase;

            color:#1e293b;

            text-align:center;

            text-shadow:
            0 0 15px rgba(59,130,246,0.12);

            animation:titleFloat 5s ease-in-out infinite;
        }

        @keyframes titleFloat{

            0%,100%{
                transform:translateY(0px);
            }

            50%{
                transform:translateY(-4px);
            }
        }

        .gradient-text{
            background:
            linear-gradient(
                90deg,
                #2563eb,
                #6366f1,
                #06b6d4,
                #2563eb
            );

            background-size:200% auto;

            color:transparent;

            -webkit-background-clip:text;

            animation:textWave 4s linear infinite;
        }

        @keyframes textWave{

            100%{
                background-position:200% center;
            }
        }

        /* ===================================== */
        /* DESCRIPTION */
        /* ===================================== */

        .desc{
            margin-top:18px;

            text-align:center;

            color:#64748b;

            font-size:14px;

            line-height:1.8;
        }

        /* ===================================== */
        /* BUTTON */
        /* ===================================== */

        .btn{
            width:100%;

            display:flex;
            align-items:center;
            justify-content:center;

            padding:18px;

            border-radius:20px;

            font-weight:800;

            text-decoration:none;

            transition:.4s ease;

            position:relative;

            overflow:hidden;
        }

        .btn-primary{
            background:#2563eb;
            color:white;

            box-shadow:
            0 15px 30px rgba(37,99,235,0.25);
        }

        .btn-primary:hover{
            transform:
            translateY(-4px)
            scale(1.02);

            background:#1d4ed8;

            box-shadow:
            0 20px 40px rgba(37,99,235,0.4);
        }

        .btn-secondary{
            background:white;

            border:2px solid #e2e8f0;

            color:#475569;
        }

        .btn-secondary:hover{
            border-color:#93c5fd;

            transform:
            translateY(-4px)
            scale(1.02);
        }

        /* ===================================== */
        /* DIVIDER */
        /* ===================================== */

        .divider{
            display:flex;
            align-items:center;
            gap:14px;

            margin:25px 0;
        }

        .divider div{
            flex:1;
            height:1px;
            background:#e2e8f0;
        }

        .divider span{
            font-size:11px;
            color:#94a3b8;
            font-weight:700;
            text-transform:uppercase;
            letter-spacing:3px;
        }

        /* ===================================== */
        /* FOOTER */
        /* ===================================== */

        .footer{
            margin-top:40px;
            padding-top:25px;

            border-top:1px solid #f1f5f9;

            text-align:center;

            font-size:10px;

            color:#cbd5e1;

            font-weight:800;

            letter-spacing:5px;

            text-transform:uppercase;
        }

    </style>
</head>

<body>

    <div class="animated-bg">

        <span class="bubble x1"></span>
        <span class="bubble x2"></span>
        <span class="bubble x3"></span>
        <span class="bubble x4"></span>
        <span class="bubble x5"></span>

        <span class="spark s1">✦</span>
        <span class="spark s2">✦</span>
        <span class="spark s3">✦</span>
        <span class="spark s4">✦</span>

    </div>

    <div class="micro-particles">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
    </div>

    <div class="min-h-screen flex items-center justify-center p-6 relative">

        <div class="login-wrapper w-full max-w-sm md:max-w-md">

            <div class="login-card">

                <div class="logo-box">
    <img src="{{ asset('logo.jpeg') }}" alt="Logo PT Salttek">
</div>

                <h1>
                    PT SALTTEK <br>
                    <span class="gradient-text">
                        DUMPANG JAYA
                    </span>
                </h1>

                <p class="desc">
                    Sistem Monitoring Absensi Digital <br>
                    & Manajemen Karyawan Terintegrasi.
                </p>

                <div class="mt-10 space-y-4">

                    @if (Route::has('login'))

                        @auth

                            <a href="{{ url('/dashboard') }}" class="btn btn-primary">
                                MASUK KE DASHBOARD
                            </a>

                        @else

                            <a href="{{ route('login') }}" class="btn btn-primary">
                                LOGIN SEKARANG
                            </a>

                            @if (Route::has('register'))

                                <div class="divider">
                                    <div></div>
                                    <span>Atau</span>
                                    <div></div>
                                </div>

                                <a href="{{ route('register') }}" class="btn btn-secondary">
                                    Daftar Akun Baru
                                </a>

                            @endif

                        @endauth

                    @endif

                </div>

                <div class="footer">
                    MEDAN • SUMATERA UTARA • 2026
                </div>

            </div>

        </div>

    </div>

</body>
</html>