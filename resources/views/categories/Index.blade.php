@extends('layouts.app')
@section('title', 'Kategori - Sweet Glaze')

@section('content')
<div class="top-bar">
    <div>
        <h1 class="page-title">Kategori</h1>
        <p class="page-subtitle">Kelompokkan produk agar mudah dicari.</p>
    </div>
    <a href="{{ route('categories.create') }}" class="btn btn-primary">Tambah kategori</a>
</div>

<div class="card table-card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Nama kategori</th><th class="num">Jumlah produk</th><th></th></tr></thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td><strong>{{ $category->name }}</strong></td>
                        <td class="num">{{ $category->products_count }}</td>
                        <td class="actions">
                            <a href="{{ route('categories.edit', $category) }}" class="btn btn-edit btn-sm">Ubah</a>
                            <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="empty">Belum ada kategori. Tambahkan kategori pertama Anda.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $categories->links() }}
@endsection