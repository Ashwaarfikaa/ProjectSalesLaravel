@extends('layouts.app')
@section('title', 'Ubah Pelanggan - Sweet Glaze')

@section('content')
<div class="top-bar"><h1 class="page-title">Ubah pelanggan</h1></div>

<form method="POST" action="{{ route('customers.update', $customer) }}" class="card form-card">
    @csrf
    @method('PUT')
    @include('customers._form')
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Simpan perubahan</button>
        <a href="{{ route('customers.show', $customer) }}" class="btn">Batal</a>
    </div>
</form>
@endsection