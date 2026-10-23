@extends('layouts.app')
@section('title', 'Ubah Kategori - Sweet Glaze')

@section('content')
<div class="top-bar"><h1 class="page-title">Ubah kategori</h1></div>

<form method="POST" action="{{ route('categories.update', $category) }}" class="card form-card">
    @csrf
    @method('PUT')
    @include('categories._form')
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Simpan perubahan</button>
        <a href="{{ route('categories.index') }}" class="btn">Batal</a>
    </div>
</form>
@endsection