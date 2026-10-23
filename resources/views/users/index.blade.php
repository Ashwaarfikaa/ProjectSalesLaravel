@extends('layouts.app')
@section('title', 'Pengguna - Sweet Glaze')

@section('content')
<div class="top-bar">
    <div>
        <h1 class="page-title">Pengguna</h1>
        <p class="page-subtitle">Kelola akun dan role Admin atau Staff.</p>
    </div>
    <a href="{{ route('users.create') }}" class="btn btn-primary">Tambah pengguna</a>
</div>

<div class="card table-card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Nama</th><th>Email</th><th>Role</th><th></th></tr></thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td><strong>{{ $user->name }}</strong> @if ($user->id === auth()->id()) <span class="muted">(Anda)</span> @endif</td>
                        <td>{{ $user->email }}</td>
                        <td><span class="badge {{ $user->isAdmin() ? '' : 'badge-warning' }}">{{ $user->roleLabel() }}</span></td>
                        <td class="actions">
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-edit btn-sm">Ubah</a>
                            @if ($user->id !== auth()->id())
                                <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Hapus pengguna ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{ $users->links() }}
@endsection