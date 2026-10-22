@extends('layouts.app')
@section('title', 'Tambah Kategori - Sweet Glaze')

@section('content')
<div class="top-bar"><h1 class="page-title">Tambah kategori</h1></div>

<form method="POST" action="{{ route('categories.store') }}" class="card form-card">
    @csrf
    @include('categories.form')
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Simpan kategori</button>
        <a href="{{ route('categories.index') }}" class="btn">Batal</a>
    </div>
</form>
@endsection