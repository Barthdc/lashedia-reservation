@extends('layouts.app')
@extends('layouts.admin')

@section('content')

<section class="penata-section">

    <div class="admin-top">
        <h1>Admin Stylist</h1>

        <a href="{{ route('admin.stylists.create') }}" class="btn-admin">
            + Tambah Stylist
        </a>
    </div>

    <div class="penata-container">

        @foreach($stylists as $stylist)

        <div class="penata-card">

            <img src="{{ asset('storage/' . $stylist->image) }}">

            <div class="card-body">

                <h3>{{ $stylist->name }}</h3>

                <p class="lokasi">
                    📍 {{ $stylist->location }}
                </p>

                <div class="tags">
                    <span>{{ $stylist->specialist_1 }}</span>
                    <span>{{ $stylist->specialist_2 }}</span>
                    <span>{{ $stylist->specialist_3 }}</span>
                </div>

                <div class="admin-action">

                    <a href="{{ route('admin.stylists.edit', $stylist->id) }}"
                       class="btn-edit">
                        Edit
                    </a>

                    <form action="{{ route('admin.stylists.destroy', $stylist->id) }}"
                          method="POST">

                        @csrf
                        @method('DELETE')

                        <button class="btn-delete">
                            Hapus
                        </button>

                    </form>

                </div>

            </div>

        </div>

        @endforeach

    </div>

</section>

@endsection
