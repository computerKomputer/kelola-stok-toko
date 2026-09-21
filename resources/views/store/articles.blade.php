@extends('layout')
@section('title','Cerita & berita')
@section('content')
<div class="catalog-heading"><span class="eyebrow">CAKRAWALA INSIGHT</span><h1>Kenali teknologinya.<br><em>Maksimalkan manfaatnya.</em></h1><p>Tips komputer, panduan upgrade, dan kabar terbaru Cakrawala Computer.</p></div>
<form class="filter-bar"><input name="q" value="{{ request('q') }}" placeholder="Cari berita…" aria-label="Cari berita"><select name="category" aria-label="Kategori berita"><option value="">Semua kategori</option>@foreach($categories as $c)<option @selected(request('category')===$c)>{{ $c }}</option>@endforeach</select><button>Cari →</button><a href="/blog">Reset</a></form>
<div class="article-grid">@forelse($articles as $article)@include('components.article-card')@empty<p class="empty">Belum ada berita yang sesuai pencarian.</p>@endforelse</div>@include('components.pager',['paginator'=>$articles])
@endsection


