@extends('layouts.app')
@section('title', 'Ubah Pemasok - Sweet Glaze')

@section('content')
<div class="top-bar"><h1 class="page-title">Ubah pemasok</h1></div>

<form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="card form-card">
    @csrf
    @method('PUT')
    @include('suppliers._form')
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Simpan perubahan</button>
        <a href="{{ route('suppliers.index') }}" class="btn">Batal</a>
    </div>
</form>
@endsection