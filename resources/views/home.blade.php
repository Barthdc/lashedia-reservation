@extends('layouts.app')

@section('content')

<section class="hero-premium">

    <img
        src="https://picsum.photos/1600/900?beauty"
        class="hero-image"
        alt="Lashedia Beauty Studio"
    >

    <div class="hero-overlay"></div>

    <div class="hero-content container">

        <span class="hero-tag">
            Luxury Bridal & Beauty Studio
        </span>

        <h1>
            Reveal Your
            <br>
            Elegant Beauty
        </h1>

        <p class="hero-desc">
            Makeup premium dengan sentuhan elegan untuk wedding,
            engagement, wisuda, photoshoot, dan event spesial Anda.
        </p>

        <div class="hero-buttons">

            <a href="{{ route('galeri') }}" class="btn-purple">
                Lihat Galeri
            </a>

        </div>

    </div>

</section>


<section class="about-premium container">

    <div class="about-image">
        <img src="https://picsum.photos/700/900?makeup" alt="About Lashedia">
    </div>

    <div class="about-content">

        <span class="section-mini">
            About Lashedia
        </span>

        <h2>
            Elegant Beauty
            <br>
            With Luxury Touch
        </h2>

        <p>
            Lashedia menghadirkan pengalaman makeup premium dengan
            konsep feminin, clean, dan classy untuk setiap momen spesial Anda.
        </p>

        <p>
            Kami mengutamakan detail, kenyamanan, dan hasil akhir yang
            flawless agar setiap customer tampil percaya diri dan memukau.
        </p>

        <div class="about-stats">

            <div>
                <h3>250+</h3>
                <span>Happy Clients</span>
            </div>

            <div>
                <h3>5+</h3>
                <span>Years Experience</span>
            </div>

            <div>
                <h3>4.9</h3>
                <span>Customer Rating</span>
            </div>

        </div>

        <a href="{{ route('tentang-kami') }}" class="btn-pesan">
            Tentang Kami
        </a>

    </div>

</section>


<section class="service-premium">

    <div class="container">

        <div class="section-title">
            <span class="section-mini">
                Our Services
            </span>

            <h2>
                Beauty Services For
                <br>
                Your Special Moment
            </h2>
        </div>

        <div class="service-grid">

            <div class="service-card">
                <h3>Wedding Makeup</h3>
                <p>Riasan elegan dan tahan lama untuk hari pernikahan Anda.</p>
            </div>

            <div class="service-card">
                <h3>Engagement Makeup</h3>
                <p>Tampilan soft glam untuk acara lamaran dan momen intimate.</p>
            </div>

            <div class="service-card">
                <h3>Graduation Makeup</h3>
                <p>Makeup natural flawless untuk momen wisuda yang berkesan.</p>
            </div>

            <div class="service-card">
                <h3>Photoshoot Makeup</h3>
                <p>Look profesional untuk editorial, fashion, dan portrait shoot.</p>
            </div>

        </div>

    </div>

</section>


<section class="cta-premium">

    <div class="cta-card">

        <span class="section-mini">
            Book Your Beauty Moment
        </span>

        <h2>
            Ready to Look Stunning?
        </h2>

        <p>
            Jadwalkan appointment Anda sekarang dan rasakan pengalaman beauty
            service premium bersama Lashedia.
        </p>

        <a href="{{ route('pesan') }}" class="btn-pesan">
            Buat Janji Temu
        </a>

    </div>

</section>

@endsection
