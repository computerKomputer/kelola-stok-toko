@extends('layout')
@section('title','Catat pengeluaran')
@section('content')
<div class="page-heading"><div><h1>Catat pengeluaran</h1><p>Catat uang yang sudah dibayarkan dan perubahan stok yang benar-benar terjadi.</p></div><a href="/manage/expenses">← Kembali</a></div>
<form class="panel form-panel" id="expense-form" method="post" action="/manage/expenses">@csrf
<input type="hidden" name="token" value="{{ old('token',(string) Illuminate\Support\Str::uuid()) }}">
<label>Kategori<select name="category" id="expense-category">@foreach(App\Models\ExpenseCategory::labels(true) as $key=>$label)<option value="{{ $key }}" @selected(old('category')===$key)>{{ $label }}</option>@endforeach</select></label>
<div class="form-grid"><label>Tanggal pembayaran<input type="date" name="spent_on" value="{{ old('spent_on',today()->toDateString()) }}" max="{{ today()->toDateString() }}" required></label><label>Nominal dibayarkan (Rp)<input type="number" name="amount" min="0" max="1000000000000" step="1" value="{{ old('amount') }}" required><small>Kerusakan barang tanpa pembayaran baru: isi 0. Nilai barang rusak bukan uang keluar baru.</small></label></div>
<label>Metode pembayaran<select name="method"><option value="cash" @selected(old('method')==='cash')>Tunai</option><option value="transfer" @selected(old('method')==='transfer')>Transfer</option></select></label>
<label>Penerima / tujuan pembayaran<input name="recipient" value="{{ old('recipient') }}" required maxlength="150" placeholder="Contoh: Toko supplier, PLN, atau teknisi"></label>
<label id="expense-supplier">Supplier<select name="supplier_id"><option value="">Pilih supplier</option>@foreach($suppliers as $supplier)<option value="{{ $supplier->id }}" @selected(old('supplier_id')==$supplier->id)>{{ $supplier->name }}</option>@endforeach</select><small>@can('owner') <a href="/manage/suppliers" target="_blank" rel="noopener">Tambah supplier di tab baru</a>, lalu muat ulang formulir sebelum mengisi. @else Minta Superadmin menambahkan supplier jika belum tersedia. @endcan</small></label>
<label>Dampak ke stok<select name="stock_action" id="expense-stock-action"><option value="none" @selected(old('stock_action','none')==='none')>Tidak mengubah stok / sudah dicatat sebelumnya</option><option value="add" @selected(old('stock_action')==='add')>Tambah stok — barang sudah diterima</option>@can('owner') <option value="remove" @selected(old('stock_action')==='remove')>Kurangi stok — barang rusak / digunakan internal</option> @endcan</select></label>
<div id="expense-stock-fields" class="form-grid"><label>Produk<select name="product_id"><option value="">Pilih produk</option>@foreach($products as $product)<option value="{{ $product->id }}" @selected(old('product_id')==$product->id)>{{ $product->name }} · {{ $product->sku }} · tersedia {{ $product->available_stock }}</option>@endforeach</select></label><label>Jumlah unit<input type="number" name="quantity" min="1" max="1000000" value="{{ old('quantity') }}"></label></div>
<p class="muted">Jika Admin sudah menambah stok, pilih Tidak mengubah stok agar tidak terhitung dua kali. Produk baru didaftarkan dahulu di menu Produk. Modal/unit tetap diisi manual oleh Superadmin.</p>
<label>Rincian / nomor nota / alasan<textarea name="note" required maxlength="2000" rows="4">{{ old('note') }}</textarea></label>
<p class="muted">Pengembalian DP dicatat otomatis melalui tombol pembatalan pada detail transaksi, bukan formulir ini.</p>
<button>Simpan pengeluaran</button></form>
@endsection
