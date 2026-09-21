@if($paginator->hasPages())
@php($start = max(1, $paginator->currentPage() - 2))
@php($end = min($paginator->lastPage(), $paginator->currentPage() + 2))
<nav class="pagination" aria-label="Halaman hasil">
 <span>{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }}</span>
 <div class="pagination-links">
  @if($paginator->previousPageUrl())
   <a href="{{ $paginator->previousPageUrl() }}" aria-label="Halaman sebelumnya">←</a>
  @endif
  @if($start > 1)
   <a href="{{ $paginator->url(1) }}">1</a>
   @if($start > 2)<span aria-hidden="true">…</span>@endif
  @endif
  @for($page = $start; $page <= $end; $page++)
   @if($page === $paginator->currentPage())<span class="current" aria-current="page">{{ $page }}</span>@else<a href="{{ $paginator->url($page) }}">{{ $page }}</a>@endif
  @endfor
  @if($end < $paginator->lastPage())
   @if($end < $paginator->lastPage()-1)<span aria-hidden="true">…</span>@endif
   <a href="{{ $paginator->url($paginator->lastPage()) }}">{{ $paginator->lastPage() }}</a>
  @endif
  @if($paginator->nextPageUrl())
   <a href="{{ $paginator->nextPageUrl() }}" aria-label="Halaman berikutnya">→</a>
  @endif
 </div>
</nav>
@endif
