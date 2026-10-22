@extends('layouts.app')
@section('title', 'Tambah Pengguna - Sweet Glaze')

@section('content')
<div class="top-bar"><h1 class="page-title">Tambah pengguna</h1></div>

<form method="POST" action="{{ route('users.store') }}" class="card form-card">
    @csrf
    @include('users.form')
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Simpan pengguna</button>
        <a href="{{ route('users.index') }}" class="btn">Batal</a>
    </div>
</form>
@endsection