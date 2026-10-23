@extends('layouts.app')
@section('title', 'Ubah Pengguna - Sweet Glaze')

@section('content')
<div class="top-bar"><h1 class="page-title">Ubah pengguna</h1></div>

<form method="POST" action="{{ route('users.update', $user) }}" class="card form-card">
    @csrf
    @method('PUT')
    @include('users._form')
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Simpan perubahan</button>
        <a href="{{ route('users.index') }}" class="btn">Batal</a>
    </div>
</form>
@endsection