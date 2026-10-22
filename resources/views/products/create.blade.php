@extends('layouts.app')
@section('title', 'Tambah Produk - Sweet Glaze')

@section('content')
<div class="top-bar">
    <h1 class="page-title">Tambah produk</h1>
</div>

<form method="POST" action="{{ route('products.store') }}" class="card form-card">
    @csrf
    @include('products.form')
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Simpan produk</button>
        <a href="{{ route('products.index') }}" class="btn">Batal</a>
    </div>
</form>
@endsection