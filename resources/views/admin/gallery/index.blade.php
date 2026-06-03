@extends('layouts.admin')

@section('content')

<style>
    .gallery-admin-wrapper{
        padding:40px 0;
    }

    .admin-topbar{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:20px;
        margin-bottom:36px;
    }

    .admin-topbar h1{
        font-size:54px;
        line-height:1;
        margin:0;
        color:#4b4453;
        font-family:'Playfair Display', serif;
        font-weight:800;
    }

    .admin-action-btn{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        padding:16px 28px;
        border-radius:999px;
        background:#f7c8d8;
        color:#2f2935;
        text-decoration:none;
        font-size:15px;
        font-weight:800;
        border:none;
        cursor:pointer;
        transition:all .25s ease;
        box-shadow:0 10px 24px rgba(247,200,216,.32);
    }

    .admin-action-btn:hover{
        background:#f3a8c4;
        color:white;
        transform:translateY(-2px);
        box-shadow:0 16px 34px rgba(247,200,216,.48);
    }

    .gallery-grid{
        display:grid;
        grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
        gap:28px;
    }

    .gallery-card{
        background:white;
        border-radius:28px;
        overflow:hidden;
        border:1px solid #ffe0ea;
        box-shadow:0 12px 28px rgba(247,200,216,.14);
        transition:all .3s ease;
    }

    .gallery-card:hover{
        transform:translateY(-6px);
        box-shadow:0 18px 40px rgba(247,200,216,.24);
    }

    .gallery-card img{
        width:100%;
        height:220px;
        object-fit:cover;
        display:block;
    }

    .gallery-body{
        padding:22px;
    }

    .gallery-body h3{
        font-size:20px;
        color:#4b4453;
        margin:0 0 18px 0;
        font-family:'DM Sans',sans-serif;
        font-weight:800;
    }

    .gallery-action{
        display:flex;
        align-items:center;
        gap:12px;
        flex-wrap:wrap;
    }

    .gallery-delete-form{
        margin:0;
        padding:0;
        display:inline-flex;
    }

    .btn-gallery{
        min-width:82px;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        padding:12px 20px;
        border:none;
        border-radius:999px;
        text-decoration:none;
        font-size:14px;
        font-weight:800;
        cursor:pointer;
        transition:all .25s ease;
        line-height:1;
    }

    .btn-gallery:active{
        transform:scale(.96);
    }

    .btn-edit{
        background:#f7c8d8;
        color:#2f2935;
        box-shadow:0 8px 20px rgba(247,200,216,.34);
    }

    .btn-edit:hover{
        background:#f3a8c4;
        color:white;
        transform:translateY(-2px);
        box-shadow:0 14px 28px rgba(247,200,216,.48);
    }

    .btn-delete{
        background:#4b4453;
        color:white;
        box-shadow:0 8px 20px rgba(75,68,83,.22);
    }

    .btn-delete:hover{
        background:#2f2935;
        color:white;
        transform:translateY(-2px);
        box-shadow:0 14px 28px rgba(75,68,83,.35);
    }

    .empty-gallery{
        grid-column:1 / -1;
        background:white;
        border:1px solid #ffe0ea;
        border-radius:24px;
        padding:40px;
        text-align:center;
        color:#6b6572;
        font-weight:700;
        box-shadow:0 12px 28px rgba(247,200,216,.12);
    }

    @media(max-width:768px){
        .admin-topbar{
            flex-direction:column;
            align-items:flex-start;
        }

        .admin-topbar h1{
            font-size:38px;
        }

        .admin-action-btn{
            width:100%;
        }

        .gallery-card img{
            height:180px;
        }

        .gallery-action{
            gap:10px;
        }

        .btn-gallery{
            flex:1;
        }

        .gallery-delete-form{
            flex:1;
        }

        .gallery-delete-form .btn-gallery{
            width:100%;
        }
    }
</style>

<div class="gallery-admin-wrapper">

    <div class="admin-topbar">

        <h1>Gallery Admin</h1>

        <a
            href="{{ route('admin.gallery.create') }}"
            class="admin-action-btn"
        >
            + Tambah Gallery
        </a>

    </div>

    <div class="gallery-grid">

        @forelse($galleries as $gallery)

            <div class="gallery-card">

                <img
                    src="{{ asset('storage/'.$gallery->image) }}"
                    alt="{{ $gallery->title }}"
                >

                <div class="gallery-body">

                    <h3>{{ $gallery->title }}</h3>

                    <div class="gallery-action">

                        <a
                            href="{{ route('admin.gallery.edit', $gallery->id) }}"
                            class="btn-gallery btn-edit"
                        >
                            Edit
                        </a>

                        <form
                            action="{{ route('admin.gallery.destroy', $gallery->id) }}"
                            method="POST"
                            class="gallery-delete-form"
                            onsubmit="return confirm('Yakin ingin menghapus gallery ini?')"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="btn-gallery btn-delete"
                            >
                                Hapus
                            </button>

                        </form>

                    </div>

                </div>

            </div>

        @empty

            <div class="empty-gallery">
                Belum ada gallery.
            </div>

        @endforelse

    </div>

</div>

@endsection
