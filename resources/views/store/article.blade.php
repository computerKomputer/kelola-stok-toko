@extends('layout')
@section('title',$article->title)
@section('content')
<article class="reading"><a class="text-link" href="/blog">← Semua cerita</a><div class="reading-heading"><span class="eyebrow">{{ $article->category }}</span><h1>{{ $article->title }}</h1><p>{{ $article->excerpt }}</p><div class="muted"><a href="/authors/{{ $article->user_id }}">{{ $article->author->name }}</a> · {{ $article->created_at->format('d M Y') }}</div></div><img class="reading-cover" src="{{ $article->image?'/media/'.$article->image:'/illustrations/computer-story.svg' }}" alt="{{ $article->title }}"><div class="reading-body preserve-lines">{{ $article->body }}</div>@if($article->tags)<div class="tags">@foreach(explode(',',$article->tags) as $tag)<span class="badge">{{ trim($tag) }}</span>@endforeach</div>@endif</article>
@endsection

