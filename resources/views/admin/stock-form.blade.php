@extends('layout')
@section('title','Edit stok')
@section('content')
<div class="page-heading"><div><span class="eyebrow">PENYESUAIAN PERSEDIAAN</span><h1>Edit stok</h1><p>{{ $product->name }} · {{ $product->sku }}</p></div><div class="page-heading-actions">@if($product->active)<a href="/products/{{ $product->id }}" class="text-link" target="_blank" rel="noopener">Lihat produk ↗</a>@endif<a href="/manage/stock" class="text-link">← Kembali</a></div></div>
<form class="panel form-panel" method="post" action="/manage/stock/{{ $product->id }}">
@csrf @method('PUT')
<input type="hidden" name="original_stock" value="{{ old('original_stock',$product->stock) }}">
<label>Stok saat ini (unit)<input id="current-stock" type="number" value="{{ $product->stock }}" readonly></label>
<label>Jenis perubahan<select id="stock-operation" name="operation"><option value="add">Tambah stok</option>@can('owner')<option value="subtract" @selected(old('operation')==='subtract')>Kurangi stok</option>@endcan</select></label><label>Jumlah perubahan (unit)<input id="stock-addition" type="number" name="addition" value="{{ old('addition') }}" min="1" max="1000000" step="1" placeholder="Contoh: 2" required></label><p>Stok setelah perubahan: <strong><output id="stock-total" aria-live="polite">{{ $product->stock }}</output> unit</strong></p>
<label>Alasan perubahan<textarea name="note" required maxlength="500" rows="3" placeholder="Contoh: 1 barang dikembalikan pelanggan, kondisi layak jual">{{ old('note') }}</textarea></label>
<p class="muted">Masukkan jumlah perubahan dengan angka positif, bukan jumlah akhir. Pengurangan stok hanya untuk Superadmin. Perubahan otomatis dicatat dalam riwayat beserta nama petugas. Penyesuaian ini hanya mengubah stok; pengembalian uang pelanggan belum dicatat melalui formulir ini.</p>
<button type="submit">Simpan stok</button>
</form>
@endsection
