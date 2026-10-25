<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - SummitGear Outdoor</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/summitgear.css') }}">
</head>
<body>

<main class="guest-wrap">
    <div class="top-bar" style="justify-content:center; text-align:center">
        <div>
            <h1 class="page-title">🏔 SummitGear Outdoor</h1>
            <p class="page-subtitle">Inventory &amp; Sales Management System</p>
        </div>
    </div>

    <form action="{{ route('login.submit') }}" method="POST" class="card login-card" style="max-width:430px; margin:0 auto">
        @csrf

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul style="margin:0; padding-left:16px">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="form-group">
            <label for="email">Alamat email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                   autofocus placeholder="admin@summitgear.com">
        </div>

        <div class="form-group">
            <label for="password">Kata sandi</label>
            <input type="password" id="password" name="password" required
                   placeholder="••••••••">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary" style="width:100%">Masuk</button>
        </div>

        <div class="demo-box">
            <strong>Akun demo</strong><br>
            &bull; Admin/Owner: <code>admin@summitgear.com</code> / <code>password</code><br>
            &bull; Staff Kasir: <code>staff@summitgear.com</code> / <code>password</code>
        </div>
    </form>

    <p class="muted" style="text-align:center; margin-top:16px">
        UTS — Web Programming 2 · Integrated Laravel Project
    </p>
</main>
</body>
</html>
