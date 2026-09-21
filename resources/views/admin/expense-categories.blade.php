@extends('layout')
@section('title','Kategori pengeluaran')
@section('content')
<div class="expense-category-page">
<div class="page-heading"><div><span class="eyebrow">KONTEN & PENGATURAN</span><h1>Kategori pengeluaran</h1><p>Kelola pilihan kategori yang digunakan saat mencatat pengeluaran.</p></div><a class="button secondary" href="/manage/expenses">← Kembali ke pengeluaran</a></div>
<section class="panel category-create">
 <div><h2>Tambah kategori</h2><p class="muted">Kategori baru langsung tersedia untuk Admin dan Superadmin.</p></div>
 <form method="post" action="/manage/expense-categories" class="category-create-form">
 @csrf
 <label>Nama kategori baru<input name="name" value="{{ old('name') }}" maxlength="100" required placeholder="Contoh: Transportasi atau Internet"></label><button>＋ Tambah kategori</button>
 </form>
</section>
<section class="panel category-list">
 <div class="category-list-heading"><div><h2>Daftar kategori</h2><p class="muted">Ubah nama atau status, lalu simpan pada baris yang sama.</p></div><span class="badge neutral">{{ $categories->total() }} kategori</span></div>
 @foreach($categories as $category)
 <form class="category-edit-row" method="post" action="/manage/expense-categories/{{ $category->id }}">
 @csrf @method('PUT')
 <label class="category-name">Nama kategori<input name="name" value="{{ $category->name }}" maxlength="100" required aria-label="Nama kategori {{ $category->name }}"><small>{{ $category->expenses_count }} catatan pengeluaran</small></label>
 <label>Status<select name="active" aria-label="Status {{ $category->name }}"><option value="1" @selected($category->active)>Aktif</option><option value="0" @selected(!$category->active)>Nonaktif</option></select></label>
 <button class="secondary" aria-label="Simpan perubahan {{ $category->name }}">Simpan perubahan</button>
 </form>
 @endforeach
 <div class="category-list-pager">@include('components.pager',['paginator'=>$categories])</div>
 <div class="category-help">Kategori nonaktif tidak muncul saat pencatatan baru. Riwayat pengeluaran tetap tersimpan.</div>
</section>
<section class="category-system"><span class="category-system-icon" aria-hidden="true">↩</span><div><h2>Pengembalian DP <span class="badge">Otomatis</span></h2><p>Dicatat melalui pembatalan transaksi. Kategori ini dikelola oleh sistem dan tidak dapat diubah.</p></div></section>
<p class="category-footnote">Perubahan nama kategori tidak mengubah stok. Kategori baru tidak memengaruhi stok; penambahan stok tetap melalui kategori pembelian dan pengurangan melalui kategori biaya internal.</p>
</div>
@endsection
