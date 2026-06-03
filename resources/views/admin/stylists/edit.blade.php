@extends('layouts.app')

@section('content')

<div class="form-container">

    <h1>Edit Stylist</h1>

    <form action="{{ route('admin.stylists.update', $stylist->id) }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf
        @method('PUT')

        <input type="text"
               name="name"
               value="{{ $stylist->name }}">

        <input type="text"
               name="location"
               value="{{ $stylist->location }}">

        <input type="text"
               name="specialist_1"
               value="{{ $stylist->specialist_1 }}">

        <input type="text"
               name="specialist_2"
               value="{{ $stylist->specialist_2 }}">

        <input type="text"
               name="specialist_3"
               value="{{ $stylist->specialist_3 }}">

        <input type="file" name="image">

        <button type="submit" class="btn-admin">
            Update
        </button>

    </form>

</div>

@endsection
