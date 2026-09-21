@extends('layout')
@section('title','Koleksi produk')
@section('content')
<div class="catalog-heading"><span class="eyebrow">TEMUKAN PILIHAN ANDA</span><h1>Komputer & aksesori,<br><em>untuk setiap kebutuhan.</em></h1><p>Laptop, PC, printer, dan perlengkapan untuk bekerja, belajar, serta berkarya.</p></div>
<form class="filter-bar" method="get"><label class="search-field"><span class="sr-only">Cari produk</span><input name="q" value="{{ request('q') }}" placeholder="Cari produk pilihan Anda…"></label><label><span class="sr-only">Kategori</span><select name="category"><option value="">Semua kategori</option>@foreach($categories as $category)<option @selected(request('category')===$category)>{{ $category }}</option>@endforeach</select></label><button>Cari produk →</button><a class="text-link" href="/products">Reset</a></form>
<p class="muted">{{ $products->total() }} produk ditemukan</p>
<div class="product-grid">@forelse($products as $product)@include('components.product-card')@empty<div class="empty">Tidak ada produk yang sesuai. Coba kata pencarian lain.</div>@endforelse</div>
@include('components.pager',['paginator'=>$products])
@endsection


