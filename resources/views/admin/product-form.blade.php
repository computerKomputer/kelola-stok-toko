@extends('layout')
@section('title',$product->exists?'Edit produk':'Tambah produk')
@section('content')
<div class="page-heading"><div><span class="eyebrow">KATALOG TOKO</span><h1>{{ $product->exists?'Edit produk':'Tambah produk' }}</h1><p>Informasi yang jelas membantu pelanggan memilih.</p></div><a href="/manage/products" class="text-link">← Kembali</a></div>
<form class="panel form-panel" method="post" enctype="multipart/form-data" action="/manage/products{{ $product->exists?'/'.$product->id:'' }}">@csrf @if($product->exists)@method('PUT')@endif
<div class="form-grid"><label>Nama produk<input name="name" value="{{ old('name',$product->name) }}" required maxlength="150"></label><label>SKU / kode produk<input name="sku" value="{{ old('sku',$product->sku) }}" required maxlength="50"></label></div>
<label>Kategori produk
<select name="category" required @disabled($categories->isEmpty())>
<option value="">Pilih kategori produk</option>
@foreach($categories as $category)<option value="{{ $category->name }}" @selected(old('category',$product->category)===$category->name)>{{ $category->name }}</option>@endforeach
</select>
<small>@if($categories->isEmpty())Belum ada kategori. @endif<a class="text-link" href="/manage/product-categories">Kelola / tambah kategori produk →</a></small>
</label><label>Deskripsi<textarea name="description" required rows="5">{{ old('description',$product->description) }}</textarea></label>
<div class="form-grid"><label>Harga jual (Rp)<input type="number" name="price" value="{{ old('price',$product->price) }}" min="1" max="1000000000" required></label><label>Diskon (%)<input type="number" name="discount" value="{{ old('discount',$product->discount??0) }}" min="0" max="100" required>@unless(auth()->user()->isOwner())<small>Batas perubahan diskon Anda: {{ $discountLimit }}%.</small>@endunless</label></div>
<label>Batas stok minimum<input type="number" name="min_stock" value="{{ old('min_stock',$product->min_stock??5) }}" min="0" required></label>
<div class="product-photo-fields">
 @foreach(['image'=>'Foto utama / tampak atas','image_left'=>'Tampak kiri','image_right'=>'Tampak kanan'] as $field=>$label)
 <label>{{ $label }} {{ $field==='image'?'*':'(opsional)' }}
  <img class="product-photo-preview" data-photo-preview="{{ $field }}" @if($product->$field) src="/media/{{ $product->$field }}" @else hidden @endif alt="Pratinjau {{ $label }}">
  <input type="file" name="{{ $field }}" accept="image/jpeg,image/png,image/webp" data-photo-input="{{ $field }}" @required($field==='image' && !$product->image)>
  <small>{{ $product->$field?'Pilih file baru untuk mengganti foto ini.':'JPG, PNG, atau WebP. Maksimal 3 MB.' }}</small>
 </label>
 @endforeach
</div>
<label class="checkbox"><input name="active" type="checkbox" value="1" @checked(old('active',$product->exists?$product->active:true))> Tampilkan di toko online</label><p class="muted">Admin dapat menambah produk tanpa mengisi harga modal. Stok awal 0; tambahkan barang melalui penyesuaian stok. @can('owner')Harga modal diisi secara manual oleh Superadmin.@endcan</p><button @disabled($categories->isEmpty())>Simpan produk →</button>
</form>
@endsection

