@extends('layouts.app')
@section('title', 'Tambah Pemasok - Sweet Glaze')

@section('content')
<div class="top-bar"><h1 class="page-title">Tambah pemasok</h1></div>

<form method="POST" action="{{ route('suppliers.store') }}" class="card form-card">
    @csrf
    @include('suppliers._form')
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Simpan pemasok</button>
        <a href="{{ route('suppliers.index') }}" class="btn">Batal</a>
    </div>
</form>
@endsection