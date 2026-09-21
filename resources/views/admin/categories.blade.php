@extends('layout')
@section('title','Kategori berita')
@section('content')
<div class="page-heading"><div><span class="eyebrow">ORGANISASI KONTEN</span><h1>Kategori berita</h1><p>Kelompokkan cerita agar mudah ditemukan pembaca.</p></div></div><form class="panel inline-form" action="/manage/categories" method="post">@csrf<label>Nama kategori<input name="name" required maxlength="80" value="{{ old('name') }}"></label><button>＋ Tambah kategori</button></form><div class="panel">@forelse($categories as $c)<div class="summary-line"><strong>{{ $c->name }}</strong><form method="post" action="/manage/categories/{{ $c->id }}" data-confirm="Hapus kategori ini?">@csrf @method('DELETE')<button class="text-button danger">Hapus</button></form></div>@empty<p class="empty">Belum ada kategori.</p>@endforelse @include('components.pager',['paginator'=>$categories])</div>
@endsection

