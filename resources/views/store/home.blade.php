@extends('layout')
@section('title','Toko & servis komputer di Baubau')
@section('content')
<section class="cc-hero">
<div class="cc-hero-copy"><span class="eyebrow"><span class="cc-dot"></span> PARTNER TEKNOLOGI ANDA DI BAUBAU</span><h1>Teknologi tepat.<br><span>Aktivitas makin<br>hebat.</span></h1><p>Dari laptop untuk bekerja, PC untuk berkarya, hingga servis perangkat. Temukan kebutuhan teknologi Anda di Cakrawala Computer.</p><div class="hero-actions"><a class="button" href="/products">Jelajahi produk ↗</a><a class="cc-hero-secondary" href="#layanan">Lihat layanan →</a></div><div class="cc-hero-notes"><span>01 &nbsp; KOMPUTER & AKSESORI</span><span>02 &nbsp; SERVIS & SOLUSI IT</span></div></div>
<div class="cc-hero-art"><div class="cc-orbit"></div><img src="/illustrations/computer-hero.svg" alt="Ilustrasi laptop, monitor, dan perangkat komputer Cakrawala"><span class="cc-art-label"><span class="cc-dot"></span> YOUR NEXT SETUP STARTS HERE</span><div class="cc-spec"><span>WORK. CREATE. CONNECT.</span><strong>Siap untuk<br>langkah berikutnya.</strong></div></div>
</section>
<div class="benefit-strip"><span>⌘ &nbsp; Komputer & laptop</span><span>⚙ &nbsp; Servis & upgrade</span><span>◎ &nbsp; Jaringan & WiFi</span><span>⌁ &nbsp; Website & software</span></div>
<section class="section"><div class="section-heading"><div><span class="eyebrow">TEMUKAN KEBUTUHAN ANDA</span><h2>Belanja berdasarkan kategori</h2></div><a class="text-link" href="/products">Semua produk ↗</a></div><div class="cc-category-grid">@foreach($categories as $category)<a href="{{ url('/products').'?'.http_build_query(['category'=>$category]) }}"><span class="cc-category-icon" aria-hidden="true">⌘</span><strong>{{ $category }}</strong><span>↗</span></a>@endforeach</div></section>
<section class="section"><div class="section-heading"><div><span class="eyebrow">PILIHAN CAKRAWALA</span><h2>Lengkapi setup Anda</h2></div><a class="text-link" href="/products">Lihat katalog ↗</a></div><div class="product-grid">@forelse($products as $product)@include('components.product-card')@empty<div class="empty">Produk sedang disiapkan. Hubungi kami untuk kebutuhan komputer Anda.</div>@endforelse</div></section>
<section class="cc-services section" id="layanan"><div class="section-heading"><div><span class="eyebrow">LEBIH DARI TOKO KOMPUTER</span><h2>Perangkat bermasalah?<br>Kami siap membantu.</h2></div><p>Solusi untuk perangkat pribadi,<br>kebutuhan kantor, dan bisnis Anda.</p></div>
<div class="cc-service-grid">
<article><span>01 / SERVICE</span><h3>Servis komputer & laptop</h3><p>Troubleshooting hardware dan software, instalasi sistem, serta perawatan perangkat.</p><a href="https://wa.me/{{ config('store.whatsapp') }}" target="_blank" rel="noopener">Konsultasi servis ↗</a></article>
<article><span>02 / CCTV &amp; IOT</span><h3>CCTV &amp; perangkat IoT</h3><p>Konsultasi pemasangan kamera pengawas dan perangkat pintar untuk rumah, kantor, atau usaha.</p><a href="https://wa.me/{{ config('store.whatsapp') }}" target="_blank" rel="noopener">Tanya solusi CCTV &amp; IoT ↗</a></article>
<article><span>03 / CONNECTIVITY</span><h3>Jaringan & rakit PC</h3><p>Instalasi LAN/WiFi, konfigurasi router, rakit PC custom, dan upgrade komponen.</p><a href="https://wa.me/{{ config('store.whatsapp') }}" target="_blank" rel="noopener">Diskusikan kebutuhan ↗</a></article>
</div></section>
<section class="cc-software"><div><span class="eyebrow">CAKRAWALA SOFTWARE HOUSE</span><h2>Bisnis berkembang.<br>Sistem ikut melangkah.</h2><p>Website, toko online, dan aplikasi bisnis untuk membantu operasional Anda.</p><a href="https://wa.me/{{ config('store.whatsapp') }}" target="_blank" rel="noopener" class="button">Konsultasi pengembangan ↗</a></div><div class="cc-code-art" aria-hidden="true"><div><i></i><i></i><i></i><span>cakrawala / solutions</span></div><pre>&lt;your-business&gt;
  website
  inventory
  point-of-sale
  mobile-app
&lt;/your-business&gt;</pre><span class="cc-code-badge">BUILT FOR YOUR BUSINESS</span></div></section>
<section class="section"><div class="section-heading"><div><span class="eyebrow">INSIGHT & UPDATE</span><h2>Kabar dari dunia teknologi</h2></div><a class="text-link" href="/blog">Semua artikel ↗</a></div><div class="article-grid">@forelse($articles as $article)@include('components.article-card')@empty<p class="empty">Artikel teknologi segera hadir.</p>@endforelse</div></section>
<section class="cc-contact section" id="kontak"><div><span class="eyebrow">MARI TERHUBUNG</span><h2>Datang, cerita,<br>temukan solusinya.</h2><a class="button" href="https://wa.me/{{ config('store.whatsapp') }}" target="_blank" rel="noopener">Hubungi via WhatsApp ↗</a></div><div><h3>Cakrawala Computer</h3><p>{{ config('store.address') }}</p><p>{{ config('store.hours') }}</p><a href="tel:+{{ config('store.whatsapp') }}">{{ config('store.phone') }}</a><br><a href="mailto:{{ config('store.email') }}">{{ config('store.email') }}</a></div></section>
@endsection

