@extends('layouts.app')
@section('title', 'Ubah Produk - Sweet Glaze')

@section('content')
<div class="top-bar">
    <h1 class="page-title">Ubah produk</h1>
</div>

<form method="POST" action="{{ route('products.update', $product) }}" class="card form-card">
    @csrf
    @method('PUT')
    @include('products._form')
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Simpan perubahan</button>
        <a href="{{ route('products.show', $product) }}" class="btn">Batal</a>
    </div>
</form>
@endsection