@extends('layouts.app')

@section('content')

<div class="form-container">

    <h1>Tambah Stylist</h1>

    <form action="{{ route('admin.stylists.store') }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf

        <input type="text"
               name="name"
               placeholder="Nama Stylist">

        <input type="text"
               name="location"
               placeholder="Lokasi">

        <input type="text"
               name="specialist_1"
               placeholder="Specialist 1">

        <input type="text"
               name="specialist_2"
               placeholder="Specialist 2">

        <input type="text"
               name="specialist_3"
               placeholder="Specialist 3">

        <input type="file" name="image">

        <button type="submit" class="btn-admin">
            Simpan
        </button>

    </form>

</div>

@endsection
