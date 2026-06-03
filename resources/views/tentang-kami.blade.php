@extends('layouts.app')

@section('content')

<style>

/* ===============================
   TENTANG PAGE
================================ */

.tentang-page{
    min-height:100vh;

    background:
        linear-gradient(
            180deg,
            #fff5f8,
            #ffffff
        );

    padding-bottom:80px;
}

/* ===============================
   HERO
================================ */

.tentang-hero{
    padding:110px 24px 80px;

    text-align:center;

    background:
        linear-gradient(
            180deg,
            #fff1f6,
            #ffffff
        );
}

.tentang-hero span{
    display:inline-flex;

    margin-bottom:16px;

    padding:8px 18px;

    border-radius:999px;

    background:white;

    color:#d96b93;

    font-size:13px;
    font-weight:700;

    border:1px solid #ffe0ea;

    box-shadow:
        0 8px 20px rgba(247,200,216,.14);
}

.tentang-hero h1{
    font-size:58px;

    line-height:1.15;

    margin-bottom:18px;

    color:#4b4453;

    font-family:'Playfair Display',serif;
}

.tentang-hero p{
    max-width:720px;

    margin:auto;

    color:#8a7380;

    font-size:17px;
    line-height:1.8;
}

/* ===============================
   CONTENT
================================ */

.tentang-wrapper{
    max-width:1180px;

    margin:70px auto 0;

    padding:0 24px;

    display:grid;

    grid-template-columns:1fr 1fr;

    gap:60px;

    align-items:center;
}

/* IMAGE */

.tentang-image{
    position:relative;
}

.tentang-image img{
    width:100%;

    height:560px;

    object-fit:cover;

    border-radius:36px;

    border:1px solid #ffe0ea;

    box-shadow:
        0 22px 55px rgba(247,200,216,.24);
}

.tentang-badge{
    position:absolute;

    left:28px;
    bottom:28px;

    background:rgba(255,255,255,.92);

    -webkit-backdrop-filter:blur(10px);
    backdrop-filter:blur(10px);

    padding:16px 22px;

    border-radius:22px;

    box-shadow:
        0 12px 28px rgba(0,0,0,.10);
}

.tentang-badge h3{
    margin:0;

    color:#4b4453;

    font-size:22px;

    font-family:'Playfair Display',serif;
}

.tentang-badge p{
    margin:4px 0 0;

    color:#8a7380;

    font-size:13px;
}

/* TEXT */

.tentang-right h2{
    font-size:44px;

    line-height:1.2;

    margin-bottom:22px;

    color:#4b4453;

    font-family:'Playfair Display',serif;
}

.tentang-desc{
    color:#8a7380;

    font-size:16px;

    line-height:1.9;

    margin-bottom:18px;
}

/* FEATURES */

.tentang-features{
    display:grid;

    grid-template-columns:repeat(2,1fr);

    gap:16px;

    margin:32px 0;
}

.feature-box{
    background:white;

    border:1px solid #ffe0ea;

    border-radius:22px;

    padding:20px;

    box-shadow:
        0 10px 24px rgba(247,200,216,.12);
}

.feature-box h4{
    margin-bottom:8px;

    color:#4b4453;

    font-size:16px;
}

.feature-box p{
    color:#8a7380;

    font-size:13px;

    line-height:1.6;
}

/* ===============================
   KONTAK CARD
================================ */

.kontak-card{
    margin-top:34px;

    background:white;

    border:1px solid #ffe0ea;

    border-radius:30px;

    padding:30px;

    box-shadow:
        0 16px 40px rgba(247,200,216,.16);
}

.kontak-card h3{
    font-size:30px;

    margin-bottom:20px;

    color:#4b4453;

    font-family:'Playfair Display',serif;
}

.kontak-list{
    display:flex;

    flex-direction:column;

    gap:14px;
}

.kontak-item{
    display:flex;

    align-items:center;

    gap:12px;

    padding:14px 18px;

    border-radius:18px;

    background:#fff5f8;

    color:#4b4453;

    font-size:14px;
    font-weight:600;

    border:1px solid #ffe0ea;
}

.kontak-icon{
    width:34px;
    height:34px;

    display:flex;
    align-items:center;
    justify-content:center;

    border-radius:50%;

    background:
        linear-gradient(
            135deg,
            #f7c8d8,
            #f3a8c4
        );

    color:white;

    flex-shrink:0;
}

/* ===============================
   CTA
================================ */

.tentang-cta{
    max-width:980px;

    margin:80px auto 0;

    padding:0 24px;
}

.cta-box{
    text-align:center;

    background:
        linear-gradient(
            135deg,
            #fff1f6,
            #ffffff
        );

    border:1px solid #ffe0ea;

    border-radius:38px;

    padding:55px 36px;

    box-shadow:
        0 20px 50px rgba(247,200,216,.18);
}

.cta-box h2{
    font-size:42px;

    color:#4b4453;

    margin-bottom:14px;

    font-family:'Playfair Display',serif;
}

.cta-box p{
    color:#8a7380;

    max-width:620px;

    margin:0 auto 28px;

    line-height:1.8;
}

.cta-btn{
    display:inline-flex;

    align-items:center;
    justify-content:center;

    padding:14px 28px;

    border-radius:999px;

    text-decoration:none;

    background:
        linear-gradient(
            135deg,
            #f7c8d8,
            #f3a8c4
        );

    color:#1f1f1f;

    font-size:14px;
    font-weight:700;

    transition:.25s ease;

    box-shadow:
        0 12px 28px rgba(247,200,216,.28);
}

.cta-btn:hover{
    transform:translateY(-2px);

    color:white;
}

/* ===============================
   RESPONSIVE
================================ */

@media(max-width:900px){

    .tentang-wrapper{
        grid-template-columns:1fr;

        gap:40px;
    }

    .tentang-hero h1{
        font-size:44px;
    }

    .tentang-right h2{
        font-size:36px;
    }

    .tentang-image img{
        height:420px;
    }
}

@media(max-width:600px){

    .tentang-hero{
        padding:80px 18px 60px;
    }

    .tentang-hero h1{
        font-size:36px;
    }

    .tentang-wrapper{
        margin-top:45px;

        padding:0 18px;
    }

    .tentang-features{
        grid-template-columns:1fr;
    }

    .kontak-card{
        padding:24px;
    }

    .cta-box h2{
        font-size:32px;
    }
}

</style>

<div class="tentang-page">

    {{-- HERO --}}
    <section class="tentang-hero">


        <h1>
            Tentang Lashedia
        </h1>

        <p>
            Lashedia adalah layanan makeup artist profesional yang menghadirkan riasan elegan,
            flawless, dan sesuai karakter setiap klien untuk berbagai acara spesial.
        </p>

    </section>

    {{-- KONTEN --}}
    <section class="tentang-wrapper">

        <div class="tentang-image">

            <img
                src="https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=900&q=80"
                alt="Tentang Lashedia"
            >

            <div class="tentang-badge">

                <h3>
                    Beauty with Confidence
                </h3>

                <p>
                    Professional Makeup Service
                </p>

            </div>

        </div>

        <div class="tentang-right">

            <h2>
                Riasan Profesional untuk Momen Terbaik Anda
            </h2>

            <p class="tentang-desc">
                Lashedia lahir dari kecintaan terhadap dunia kecantikan dan seni tata rias.
                Kami percaya bahwa makeup bukan hanya tentang mempercantik wajah, tetapi juga
                membangun rasa percaya diri dan menonjolkan pesona alami setiap individu.
            </p>

            <p class="tentang-desc">
                Dengan sentuhan profesional, produk berkualitas, serta teknik makeup yang
                disesuaikan dengan kebutuhan klien, Lashedia siap membantu Anda tampil
                memukau untuk acara wisuda, lamaran, pernikahan, photoshoot, pesta, maupun
                kebutuhan beauty treatment lainnya.
            </p>

            <div class="tentang-features">

                <div class="feature-box">

                    <h4>
                        Makeup Elegan
                    </h4>

                    <p>
                        Hasil riasan soft, clean, dan menyesuaikan karakter wajah.
                    </p>

                </div>

                <div class="feature-box">

                    <h4>
                        Produk Berkualitas
                    </h4>

                    <p>
                        Menggunakan produk yang nyaman dan cocok untuk berbagai kebutuhan.
                    </p>

                </div>

                <div class="feature-box">

                    <h4>
                        Pelayanan Ramah
                    </h4>

                    <p>
                        Konsultasi gaya makeup sesuai acara dan preferensi klien.
                    </p>

                </div>

                <div class="feature-box">

                    <h4>
                        Hasil Profesional
                    </h4>

                    <p>
                        Tampilan rapi, tahan lama, dan siap untuk dokumentasi.
                    </p>

                </div>

            </div>

            <div class="kontak-card">

                <h3>
                    Hubungi Kami
                </h3>

                <div class="kontak-list">

                    <div class="kontak-item">
                        <span class="kontak-icon">☎</span>
                        085717772201
                    </div>

                    <div class="kontak-item">
                        <span class="kontak-icon">✉</span>
                        LashediaMUA@gmail.com
                    </div>

                    <div class="kontak-item">
                        <span class="kontak-icon">◎</span>
                        @bernikeledibeth
                    </div>

                </div>

            </div>

        </div>

    </section>

    {{-- CTA --}}
    <section class="tentang-cta">

        <div class="cta-box">

            <h2>
                Siap Tampil Lebih Percaya Diri?
            </h2>

            <p>
                Jadwalkan reservasi makeup Anda bersama Lashedia dan dapatkan tampilan terbaik
                untuk momen spesial Anda.
            </p>

           <a
    href="{{ url('/pesan-sekarang') }}"
    class="cta-btn"
>
    Pesan Sekarang
</a>
        </div>

    </section>

</div>

@endsection
