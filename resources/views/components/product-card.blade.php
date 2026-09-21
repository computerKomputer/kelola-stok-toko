<article class="product-card">
<a href="/products/{{ $product->id }}" class="product-visual tone-{{ $product->id%4 }}">@if($product->image)<img src="/media/{{ $product->image }}" alt="{{ $product->name }}" loading="lazy">@else<img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy">@endif @if($product->discount)<span class="sale-badge">−{{ $product->discount }}%</span>@endif<span class="product-arrow">↗</span></a>
<div class="product-info"><small>{{ $product->category }}</small><a href="/products/{{ $product->id }}"><h3>{{ $product->name }}</h3></a><div class="price-row"><strong>@rupiah($product->sale_price)</strong>@if($product->discount)<del>@rupiah($product->price)</del>@endif</div><span class="availability {{ $product->available_stock?'':'sold-out' }}">{{ $product->available_stock?'● Tersedia':'Stok habis' }}</span></div>
</article>

