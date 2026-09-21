<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>@yield('title','Cakrawala Computer') · Cakrawala Computer</title>
<meta name="description" content="Cakrawala Computer Baubau: toko komputer, laptop, aksesori, servis perangkat, jaringan, dan software house.">
<link rel="icon" href="/cakrawala-logo.png" type="image/png">
<link rel="stylesheet" href="/app.css"><link rel="stylesheet" href="/cakrawala.css?v=19">
<script src="/app.js?v=11" defer></script>
</head>
<body class="{{ request()->is('manage*','dashboard','orders*','profile') && auth()->check() ? 'workspace' : 'storefront' }}">
@php($isWorkspace = request()->is('manage*','dashboard','orders*','profile') && auth()->check())
@if($isWorkspace)
<aside class="sidebar" id="sidebar">
 <button type="button" class="menu-close" aria-label="Tutup menu">×</button>
 <a href="/dashboard" class="brand">@include('components.brand')</a>
 <nav aria-label="Navigasi dashboard">
 @if(auth()->user()->isStaff())
 <div class="workspace-label">RUANG KERJA</div>
 <a class="{{ request()->is('dashboard')?'selected':'' }}" href="/dashboard"><x-nav-icon name="dashboard"/> Ringkasan</a>
 <div class="workspace-label">TRANSAKSI</div>
 <a class="{{ request()->is('manage/orders*','orders/*')?'selected':'' }}" href="/manage/orders"><x-nav-icon name="orders"/> Pesanan</a>
 <a class="{{ request()->is('manage/sales*')?'selected':'' }}" href="/manage/sales/create"><x-nav-icon name="sale"/> Penjualan langsung</a>
 <a href="/manage/expenses" class="{{ request()->is('manage/expenses*')?'selected':'' }}"><x-nav-icon name="expense"/> Pengeluaran</a>
 @can('owner')<a href="/manage/expense-categories" class="{{ request()->is('manage/expense-categories*')?'selected':'' }}"><x-nav-icon name="expense-category"/> Kategori pengeluaran</a>@endcan
 <div class="workspace-label">INVENTORI</div>
 <a class="{{ request()->is('manage/products*')?'selected':'' }}" href="/manage/products"><x-nav-icon name="product"/> Produk</a>
 <a class="{{ request()->is('manage/product-categories*')?'selected':'' }}" href="/manage/product-categories"><x-nav-icon name="category"/> Kategori produk</a>
 <a class="{{ request()->is('manage/stock*')?'selected':'' }}" href="/manage/stock"><x-nav-icon name="stock"/> Stok barang</a>
 @can('owner')<a class="{{ request()->is('manage/suppliers*')?'selected':'' }}" href="/manage/suppliers"><x-nav-icon name="supplier"/> Supplier</a>@endcan
 <div class="workspace-label">LAPORAN</div>
 <a class="{{ request()->is('manage/reports*')?'selected':'' }}" href="/manage/reports"><x-nav-icon name="report"/> {{ auth()->user()->isOwner()?'Laporan':'Pemasukan Hari Ini' }}</a>
 @endif
 @if(auth()->user()->role!=='customer')
 <div class="workspace-label">KONTEN & PENGATURAN</div>
 <a class="{{ request()->is('manage/articles*')?'selected':'' }}" href="/manage/articles"><x-nav-icon name="article"/> Berita</a>
 @if(auth()->user()->isStaff())
 <a class="{{ request()->is('manage/categories*')?'selected':'' }}" href="/manage/categories"><x-nav-icon name="category"/> Kategori berita</a>
 <a class="{{ request()->is('manage/users*')?'selected':'' }}" href="/manage/users"><x-nav-icon name="users"/> {{ auth()->user()->isOwner()?'Pengguna':'Akun penulis' }}</a>
 @endif

 @can('owner')<a class="{{ request()->is('manage/settings*')?'selected':'' }}" href="/manage/settings"><x-nav-icon name="settings"/> Pengaturan diskon</a>@endcan
 @else
 <div class="workspace-label">RUANG KERJA</div>
 <a href="/products"><x-nav-icon name="product"/> Belanja</a>
 <a class="{{ request()->is('orders*')?'selected':'' }}" href="/orders"><x-nav-icon name="orders"/> Pesanan saya</a>
 <a href="/cart"><x-nav-icon name="cart"/> Keranjang</a>
 @endif
 <a class="{{ request()->is('profile')?'selected':'' }}" href="/profile"><x-nav-icon name="profile"/> Profil saya</a>
 </nav>
 <div class="sidebar-bottom"><a href="/"><x-nav-icon name="external"/> Lihat website</a><div class="account"><span class="avatar">{{ mb_substr(auth()->user()->name,0,1) }}</span><div><strong>{{ auth()->user()->name }}</strong><small>{{ ['owner'=>'Superadmin / Owner','admin'=>'Admin','writer'=>'Penulis berita','customer'=>'Customer'][auth()->user()->role] }}</small></div></div><form method="post" action="/logout">@csrf<button class="text-button">Keluar dari akun →</button></form></div>
</aside>
<div class="main-shell">
<header class="topbar"><button class="menu-toggle secondary" aria-controls="sidebar" aria-expanded="false">☰ Menu</button><div>Workspace <span class="muted">/ @yield('title','Ringkasan')</span></div><div class="top-meta"><span class="online-dot"></span> {{ now()->translatedFormat('d M Y') }} <span class="avatar small">{{ mb_substr(auth()->user()->name,0,1) }}</span></div></header>
<main class="dashboard-content">
@else
<header class="public-header"><a href="/" class="brand">@include('components.brand')</a><nav><a href="/" class="{{ request()->is('/')?'active':'' }}">Beranda</a><a href="/products" class="{{ request()->is('products*')?'active':'' }}">Belanja</a><a href="/blog" class="{{ request()->is('blog*')?'active':'' }}">Artikel & berita</a></nav><div class="header-actions">@auth @if(auth()->user()->role==='customer')<a href="/cart" class="cart-link">Keranjang <b>{{ array_sum(session('cart',[])) }}</b></a>@endif<a href="/dashboard" class="button secondary">Dashboard ↗</a>@else<a href="/login" class="button secondary">Masuk ↗</a>@endauth</div></header>
<main class="public-content">
@endif
@if(session('success'))<div class="notice success" role="status">✓ {{ session('success') }}</div>@endif
@if(config('app.env')==='local')<div class="demo-label">DEMO LOKAL · Produk dan transaksi awal adalah data contoh.</div>@endif
@if($errors->any())<div class="notice error" role="alert"><strong>Periksa kembali isian Anda.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@yield('content')
</main>
@if($isWorkspace)</div>@else<footer class="public-footer"><a href="/" class="brand">@include('components.brand')</a><p>Servis · Toko Komputer · Software House — Baubau</p><span>© {{ date('Y') }} Cakrawala Computer</span></footer>@endif
</body></html>


