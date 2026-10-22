@extends('layouts.app')
@section('title', 'Tambah Pelanggan - Sweet Glaze')

@section('content')
<div class="top-bar"><h1 class="page-title">Tambah pelanggan</h1></div>

<form method="POST" action="{{ route('customers.store') }}" class="card form-card">
    @csrf
    @include('customers.form')
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Simpan pelanggan</button>
        <a href="{{ route('customers.index') }}" class="btn">Batal</a>
    </div>
</form>
@endsection