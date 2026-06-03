@extends('layouts.app')

@section('content')

<section class="artist-hero">
    <div class="artist-overlay"></div>

    <img src="https://picsum.photos/1600/900?beauty-salon" class="artist-hero-image">

    <div class="artist-hero-content container">
        <span class="artist-tag">Professional Makeup Artist</span>

        <h1>
            Meet Our
            <br>
            Beauty Artists
        </h1>

        <p>
            Tim makeup artist profesional dengan sentuhan feminin,
            elegan, dan luxury beauty untuk setiap momen spesial Anda.
        </p>
    </div>
</section>

<section class="penata-section">

    <div class="penata-header">
        <span class="section-mini">Our Beauty Team</span>

        <h2>
            Discover Professional
            <br>
            Makeup Artists
        </h2>
    </div>

    <div class="stylist-grid">

        {{-- BERNIKE --}}
        <div class="stylist-card">
            <div class="card-inner">

                <div class="card-front">
                    <img src="https://picsum.photos/500/700?makeup-artist-1" class="stylist-img">

                    <div class="card-body">
                        <h3>Bernike</h3>
                        <p class="lokasi">📍 Kabupaten Tangerang</p>

                        <div class="tags">
                            <span>Pengantin</span>
                            <span>Airbrush</span>
                            <span>Editorial</span>
                        </div>

                        <button type="button" class="flip-btn">
                            Lihat Profil
                        </button>
                    </div>
                </div>

                <div class="card-back">
                    <img src="https://picsum.photos/500/700?makeup-artist-1" class="stylist-img">

                    <div class="back-content">
                        <h2>Biografi Bernike</h2>

                        <p>
                            Bernike merupakan makeup artist profesional
                            dengan pengalaman lebih dari 7 tahun dalam
                            bridal makeup, editorial beauty, dan luxury wedding look.
                        </p>

                        <button type="button" class="flip-back-btn">
                            Kembali
                        </button>
                    </div>
                </div>

            </div>
        </div>

        {{-- SALLY --}}
        <div class="stylist-card">
            <div class="card-inner">

                <div class="card-front">
                    <img src="https://picsum.photos/500/700?makeup-artist-2" class="stylist-img">

                    <div class="card-body">
                        <h3>Sally</h3>
                        <p class="lokasi">📍 Kota Tangerang</p>

                        <div class="tags">
                            <span>Glamor</span>
                            <span>Bohemian</span>
                            <span>Asia Selatan</span>
                        </div>

                        <button type="button" class="flip-btn">
                            Lihat Profil
                        </button>
                    </div>
                </div>

                <div class="card-back">
                    <img src="https://picsum.photos/500/700?makeup-artist-2" class="stylist-img">

                    <div class="back-content">
                        <h2>Biografi Sally</h2>

                        <p>
                            Sally dikenal dengan makeup glamor modern,
                            soft feminine touch, dan beauty styling premium
                            untuk wisuda serta prewedding.
                        </p>

                        <button type="button" class="flip-back-btn">
                            Kembali
                        </button>
                    </div>
                </div>

            </div>
        </div>

        {{-- KIKI --}}
        <div class="stylist-card">
            <div class="card-inner">

                <div class="card-front">
                    <img src="https://picsum.photos/500/700?makeup-artist-3" class="stylist-img">

                    <div class="card-body">
                        <h3>Kiki</h3>
                        <p class="lokasi">📍 Jakarta</p>

                        <div class="tags">
                            <span>Natural</span>
                            <span>Beach Wedding</span>
                            <span>Waterproof</span>
                        </div>

                        <button type="button" class="flip-btn">
                            Lihat Profil
                        </button>
                    </div>
                </div>

                <div class="card-back">
                    <img src="https://picsum.photos/500/700?makeup-artist-3" class="stylist-img">

                    <div class="back-content">
                        <h2>Biografi Kiki</h2>

                        <p>
                            Kiki memiliki spesialisasi natural bridal makeup
                            dan waterproof makeup untuk outdoor wedding serta beach wedding.
                        </p>

                        <button type="button" class="flip-back-btn">
                            Kembali
                        </button>
                    </div>
                </div>

            </div>
        </div>

    </div>

</section>

<script>
document.querySelectorAll('.flip-btn').forEach((btn) => {
    btn.addEventListener('click', function () {
        this.closest('.stylist-card').classList.add('active');
    });
});

document.querySelectorAll('.flip-back-btn').forEach((btn) => {
    btn.addEventListener('click', function () {
        this.closest('.stylist-card').classList.remove('active');
    });
});
</script>

@endsection
