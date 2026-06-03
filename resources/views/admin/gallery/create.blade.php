@extends('layouts.app')

@section('content')

<style>

/* ====================================
   CREATE GALLERY PAGE
==================================== */

.gallery-create-section{
    min-height:100vh;

    display:flex;
    align-items:center;
    justify-content:center;

    padding:60px 20px;

    background:
        linear-gradient(
            180deg,
            #fff5f8,
            #ffffff
        );
}

/* CARD */

.gallery-create-card{
    width:100%;
    max-width:520px;

    background:white;

    padding:42px;

    border-radius:32px;

    border:1px solid #ffe0ea;

    box-shadow:
        0 16px 40px rgba(247,200,216,.18);
}

/* TITLE */

.gallery-create-title{
    font-size:46px;

    line-height:1.15;

    margin-bottom:12px;

    text-align:center;

    color:#4b4453;

    font-family:'Playfair Display',serif;
}

.gallery-create-subtitle{
    text-align:center;

    color:#8a7380;

    margin-bottom:34px;

    font-size:15px;
}

/* FORM */

.gallery-form{
    display:flex;
    flex-direction:column;

    gap:22px;
}

/* INPUT GROUP */

.gallery-group{
    display:flex;
    flex-direction:column;

    gap:10px;
}

/* LABEL */

.gallery-group label{
    font-size:14px;
    font-weight:700;

    color:#4b4453;
}

/* INPUT TEXT */

.gallery-input{
    width:100%;

    height:56px;

    padding:0 18px;

    border-radius:18px;

    border:1px solid #f7c8d8;

    background:#fffafb;

    font-size:14px;

    transition:.25s ease;

    font-family:'DM Sans',sans-serif;
}

.gallery-input:focus{
    outline:none;

    background:white;

    border-color:#f3a8c4;

    box-shadow:
        0 0 0 5px rgba(247,200,216,.20);
}

/* FILE INPUT */

.gallery-file{
    width:100%;

    padding:16px;

    border-radius:18px;

    border:1px dashed #f3a8c4;

    background:#fffafb;

    font-size:14px;

    cursor:pointer;

    color:#8a7380;
}

/* BUTTON */

.gallery-btn{
    width:100%;

    height:56px;

    border:none;

    border-radius:18px;

    background:
        linear-gradient(
            135deg,
            #f7c8d8,
            #f3a8c4
        );

    color:#1f1f1f;

    font-size:15px;
    font-weight:700;

    cursor:pointer;

    transition:.25s ease;

    font-family:'DM Sans',sans-serif;

    box-shadow:
        0 12px 28px rgba(247,200,216,.28);
}

.gallery-btn:hover{
    transform:translateY(-2px);

    color:white;

    opacity:.95;
}

/* RESPONSIVE */

@media(max-width:600px){

    .gallery-create-card{
        padding:34px 24px;
    }

    .gallery-create-title{
        font-size:34px;
    }
}

</style>

<div class="gallery-create-section">

    <div class="gallery-create-card">

        <h1 class="gallery-create-title">
            Tambah Gallery
        </h1>

        <p class="gallery-create-subtitle">
            Upload hasil makeup terbaik untuk gallery Lashedia
        </p>

        <form
            action="{{ route('admin.gallery.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="gallery-form"
        >

            @csrf

            <div class="gallery-group">

                <label>
                    Judul Gallery
                </label>

                <input
                    type="text"
                    name="title"
                    placeholder="Masukkan judul gallery"
                    class="gallery-input"
                >

            </div>

            <div class="gallery-group">

                <label>
                    Upload Gambar
                </label>

                <input
                    type="file"
                    name="image"
                    class="gallery-file"
                >

            </div>

            <button
                type="submit"
                class="gallery-btn"
            >
                Simpan Gallery
            </button>

        </form>

    </div>

</div>

@endsection
