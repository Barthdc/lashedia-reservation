@extends('layouts.app')

@section('content')

<div class="edit-gallery-wrapper">

    <h1 class="edit-gallery-title">
        Edit Gallery
    </h1>

    <form
        action="{{ route('admin.gallery.update', $gallery->id) }}"
        method="POST"
        enctype="multipart/form-data"
        class="edit-gallery-form"
    >
        @csrf
        @method('PUT')

        <input
            type="text"
            name="title"
            value="{{ old('title', $gallery->title) }}"
            placeholder="Judul Gallery"
            required
        >

        <input
            type="file"
            name="image"
            accept="image/*"
        >

        <button type="submit" class="btn-update-gallery">
            Update
        </button>

    </form>

</div>
