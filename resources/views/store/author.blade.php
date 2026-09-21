@extends('layout')
@section('title',$author->name)
@section('content')
<div class="catalog-heading"><span class="eyebrow">PENULIS</span><h1>{{ $author->name }}</h1><p>{{ $author->bio ?: 'Berbagi tips dan informasi teknologi bersama Cakrawala Computer.' }}</p></div><div class="article-grid">@forelse($articles as $article)@include('components.article-card')@empty<p class="empty">Belum ada berita dari penulis ini.</p>@endforelse</div>@include('components.pager',['paginator'=>$articles])
@endsection


