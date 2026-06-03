@extends('layouts.app')

@section('content')

<style>

/* ===============================
   GALERI PAGE
================================ */

.galeri-page{
    min-height:100vh;

    padding:90px 24px;

    background:
        linear-gradient(
            180deg,
            #fff5f8,
            #ffffff
        );
}

/* HEADER */

.galeri-header{
    text-align:center;

    margin-bottom:50px;
}

.galeri-header h1{
    font-size:52px;

    color:#4b4453;

    font-family:'Playfair Display',serif;

    margin-bottom:12px;
}

.galeri-header p{
    color:#8a7380;

    font-size:15px;
}

/* GRID */

.galeri-grid{
    max-width:1100px;

    margin:auto;

    display:grid;

    grid-template-columns:
        repeat(4,1fr);

    gap:18px;
}

/* CARD */

.galeri-item{
    position:relative;

    overflow:hidden;

    border-radius:18px;

    aspect-ratio:1/1;

    background:white;

    border:1px solid #ffe0ea;

    box-shadow:
        0 10px 24px rgba(247,200,216,.14);

    transition:.3s ease;
}

/* IMAGE */

.galeri-item img{
    width:100%;
    height:100%;

    object-fit:cover;

    display:block;

    cursor:pointer;

    transition:.4s ease;
}

/* HOVER */

.galeri-item:hover{
    transform:translateY(-5px);

    box-shadow:
        0 18px 36px rgba(247,200,216,.24);
}

.galeri-item:hover img{
    transform:scale(1.08);
}

/* INFO */

.galeri-info{
    position:absolute;

    left:12px;
    bottom:12px;

    z-index:10;

    padding:8px 16px;

    border-radius:999px;

    background:
        rgba(255,255,255,.92);

    -webkit-backdrop-filter:blur(10px);
    backdrop-filter:blur(10px);

    box-shadow:
        0 8px 20px rgba(0,0,0,.08);
}

/* TITLE */

.galeri-info h3{
    margin:0;

    color:#4b4453;

    font-size:13px;
    font-weight:700;
}

/* =========================
   LIGHTBOX
========================= */

.galeri-lightbox{
    position:fixed;

    inset:0;

    background:
        rgba(0,0,0,.82);

    display:flex;
    align-items:center;
    justify-content:center;

    opacity:0;
    visibility:hidden;

    transition:.3s ease;

    z-index:99999;

    padding:40px;
}

/* ACTIVE */

.galeri-lightbox.active{
    opacity:1;
    visibility:visible;
}

/* IMAGE */

.lightbox-image{
    width:75vw;

    max-width:1100px;

    max-height:85vh;

    object-fit:contain;

    border-radius:20px;

    box-shadow:
        0 20px 60px rgba(0,0,0,.45);

    transform:scale(.8);

    transition:.35s ease;
}

/* SHOW */

.galeri-lightbox.active .lightbox-image{
    transform:scale(1);
}

/* CLOSE */

.lightbox-close{
    position:absolute;

    top:30px;
    right:40px;

    color:white;

    font-size:42px;

    cursor:pointer;

    transition:.25s ease;
}

.lightbox-close:hover{
    transform:scale(1.1);

    color:#f7c8d8;
}

/* RESPONSIVE */

@media(max-width:992px){

    .galeri-grid{
        grid-template-columns:
            repeat(3,1fr);
    }
}

@media(max-width:768px){

    .galeri-grid{
        grid-template-columns:
            repeat(2,1fr);
    }

    .galeri-header h1{
        font-size:40px;
    }

    .lightbox-image{
        width:92vw;
    }

    .lightbox-close{
        top:20px;
        right:24px;
    }
}

@media(max-width:540px){

    .galeri-grid{
        grid-template-columns:1fr;
    }
}

</style>

<section class="galeri-page">

    <div class="galeri-header">

        <h1>Karya Kami</h1>

        <p>
            Temukan inspirasi makeup dan beauty terbaik Lashedia
        </p>

    </div>

    <div class="galeri-grid">

        @foreach($galleries as $gallery)

            <div class="galeri-item">

                <img
                    src="{{ asset('storage/' . $gallery->image) }}"
                    alt="{{ $gallery->title }}"
                >

                <div class="galeri-info">

                    <h3>
                        {{ $gallery->title }}
                    </h3>

                </div>

            </div>

        @endforeach

    </div>

</section>

<!-- LIGHTBOX -->

<div class="galeri-lightbox" id="lightbox">

    <span class="lightbox-close" id="closeLightbox">
        ✕
    </span>

    <img
        id="lightboxImage"
        class="lightbox-image"
    >

</div>

<script>

const galleryImages =
    document.querySelectorAll('.galeri-item img');

const lightbox =
    document.getElementById('lightbox');

const lightboxImage =
    document.getElementById('lightboxImage');

const closeLightbox =
    document.getElementById('closeLightbox');

/* OPEN */

galleryImages.forEach(img => {

    img.addEventListener('click', () => {

        lightbox.classList.add('active');

        lightboxImage.src = img.src;

    });

});

/* CLOSE BUTTON */

closeLightbox.addEventListener('click', () => {

    lightbox.classList.remove('active');

});

/* CLOSE OUTSIDE */

lightbox.addEventListener('click', (e) => {

    if(e.target === lightbox){

        lightbox.classList.remove('active');

    }

});

</script>

@endsection
