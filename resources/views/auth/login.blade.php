@extends('layouts.app')
@section('title', 'Masuk - Sweet Glaze')

@section('content')
<div class="card login-card">
    <div class="login-brand">
        <div class="brand-mark" aria-hidden="true">🍩</div>
        <h1>Sweet Glaze</h1>
        <p>Masuk untuk mengelola stok, penjualan, atau katalog menu.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}">
        @csrf
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
        </div>
        <div class="form-group">
            <label for="password">Kata sandi</label>
            <input type="password" id="password" name="password" autocomplete="current-password" required>
        </div>
        <div class="form-group">
            <label class="check"><input type="checkbox" name="remember" value="1"> Ingat saya</label>
        </div>
        <button type="submit" class="btn btn-primary">Masuk</button>
    </form>

    <div class="demo-box">
        <strong>Akun demo</strong>
        Admin: <code onclick="fillEmail('admin@sweetglaze.test')" style="cursor:pointer; text-decoration:underline;">admin@sweetglaze.test</code><br>
        Staff: <code onclick="fillEmail('staff@sweetglaze.test')" style="cursor:pointer; text-decoration:underline;">staff@sweetglaze.test</code><br>
        Pelanggan: <code onclick="fillEmail('pelanggan@sweetglaze.test')" style="cursor:pointer; text-decoration:underline;">pelanggan@sweetglaze.test</code><br>
        Kata sandi: <code>password</code>
    </div>
</div>

<script>
function fillEmail(email) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = 'password';
}
</script>
@endsection