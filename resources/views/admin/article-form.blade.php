@extends('layout')
@section('title',$article->exists?'Edit berita':'Tulis berita')
@section('content')
<div class="page-heading"><div><span class="eyebrow">BAGIKAN CERITA ANDA</span><h1>{{ $article->exists?'Edit berita':'Tulis berita' }}</h1><p>Berita langsung tampil di website setelah dipublikasikan.</p></div><div class="page-heading-actions">@if($article->exists)<a href="/blog/{{ $article->slug }}" class="text-link" target="_blank" rel="noopener">Lihat halaman ↗</a>@endif<a href="/manage/articles" class="text-link">← Kembali</a></div></div>
<form class="panel form-panel" method="post" enctype="multipart/form-data" action="/manage/articles{{ $article->exists?'/'.$article->id:'' }}">@csrf @if($article->exists)@method('PUT')@endif
<label>Judul berita<input name="title" value="{{ old('title',$article->title) }}" required maxlength="180"></label><div class="form-grid"><label>Kategori<select name="category" required>@foreach($categories as $c)<option @selected(old('category',$article->category)===$c->name)>{{ $c->name }}</option>@endforeach</select></label><label>Tag<input name="tags" value="{{ old('tags',$article->tags) }}" maxlength="200" placeholder="Pisahkan dengan koma"></label></div>
<label>Ringkasan<textarea name="excerpt" maxlength="350" required>{{ old('excerpt',$article->excerpt) }}</textarea></label><label>Isi berita<textarea class="article-editor" name="body" rows="16" maxlength="50000" required>{{ old('body',$article->body) }}</textarea><small>Gunakan baris kosong untuk memisahkan paragraf. Isi ditampilkan sebagai teks.</small></label>
<label>Gambar sampul<input type="file" name="image" accept="image/jpeg,image/png,image/webp"><small>JPG, PNG, atau WebP. Maksimal 3 MB. Kosongkan untuk mempertahankan gambar.</small></label><button @disabled($categories->isEmpty())>{{ $article->exists?'Simpan & perbarui berita':'Publikasikan sekarang' }} ↗</button>
</form>
@endsection

