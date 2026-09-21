@extends('layout')
@section('title','Kategori produk')
@section('content')
<div class="page-heading">
    <div><span class="eyebrow">KATALOG TOKO</span><h1>Kategori produk</h1><p>Buat kategori sekali, lalu semua petugas tinggal memilihnya saat mengisi produk.</p></div>
    <a href="/manage/products/create" class="button secondary">＋ Tambah produk</a>
</div>
<form class="panel inline-form product-category-create" action="/manage/product-categories" method="post">
    @csrf
    <label>Nama kategori produk<input name="name" value="{{ old('name') }}" maxlength="80" required placeholder="Contoh: Aksesori"></label>
    <button>＋ Tambah kategori</button>
</form>
<section class="panel table-wrap">
    <table>
        <thead><tr><th>Kategori produk</th><th>Jumlah produk</th><th>Aksi</th></tr></thead>
        <tbody>
        @forelse($categories as $category)
            <tr>
                <td><strong>{{ $category->name }}</strong></td>
                <td>{{ $category->products_count }} produk</td>
                <td>
                    @if($category->products_count)
                        <span class="muted">Sedang digunakan</span>
                    @else
                        <form action="/manage/product-categories/{{ $category->id }}" method="post" data-confirm="Hapus kategori produk ini?">
                            @csrf @method('DELETE')
                            <button class="text-button danger">Hapus</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="3" class="empty">Belum ada kategori produk. Tambahkan kategori pertama melalui formulir di atas.</td></tr>
        @endforelse
        </tbody>
    </table>
    @include('components.pager',['paginator'=>$categories])
</section>
@endsection

