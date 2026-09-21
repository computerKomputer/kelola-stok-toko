@extends('layout')
@section('title','Penjualan langsung')
@section('content')
<div class="page-heading"><div><span class="eyebrow">TRANSAKSI DI TOKO</span><h1>Catat penjualan</h1><p>Satu transaksi untuk seluruh barang dan jasa pelanggan.</p></div></div>
<form id="direct-sale" class="panel form-panel" action="/manage/sales" method="post">@csrf
<input type="hidden" name="checkout_token" value="{{ old('checkout_token',$token) }}">
<section class="sale-section" aria-labelledby="customer-heading">
<div class="sale-section-heading"><span class="sale-step">1</span><div><h2 id="customer-heading">Data pelanggan</h2><p>Isi nama pelanggan dan keterangan pekerjaan.</p></div></div>
<label>Nama Pelanggan <span class="required-hint">Wajib</span><input name="customer_name" value="{{ old('customer_name') }}" placeholder="Contoh: Bapak Andi" required maxlength="100" aria-describedby="customer-help"><small id="customer-help">Ketik nama pelanggan dengan benar dan sesuai data servis.</small></label>
<label>Deskripsi Pengerjaan <span class="optional-hint">Opsional</span><textarea name="work_description" rows="3" placeholder="Contoh: Laptop lambat. Pasang SSD dan instal ulang sistem." aria-describedby="work-help">{{ old('work_description') }}</textarea><small id="work-help">Opsional: isi nomor HP pelanggan.</small></label>
</section>
<section class="sale-section" aria-labelledby="items-heading">
<div class="sale-section-heading"><span class="sale-step">2</span><div><h2 id="items-heading">Barang dan jasa</h2><p>Pilih jenis item, lalu isi produk atau pekerjaan dan jumlahnya.</p></div></div>
<div id="sale-lines">
@foreach(old('items',[['type'=>'product','quantity'=>1]]) as $index=>$line)
@include('admin.sale-line',['index'=>$index,'line'=>$line])
@endforeach
</div>
<template id="sale-line-template">@include('admin.sale-line',['index'=>'ROW','line'=>['type'=>'product','quantity'=>1]])</template>
<button type="button" id="add-sale-line" class="secondary">＋ Tambah barang / jasa</button>
<p class="muted sale-add-help">Gunakan tombol ini jika pelanggan membeli lebih dari satu jenis barang atau jasa.</p>
</section>
<section class="sale-section sale-payment" aria-labelledby="payment-heading">
<div class="sale-section-heading"><span class="sale-step">3</span><div><h2 id="payment-heading">Pembayaran</h2><p>Periksa total dan pilih cara pembayaran yang diterima.</p></div></div>
<label>Pilihan transaksi<select name="payment_mode" id="payment-mode"><option value="full" @selected(old('payment_mode')!=='deposit')>Lunas — barang / pekerjaan sudah diserahkan</option><option value="deposit" @selected(old('payment_mode')==='deposit')>DP — barang disisihkan / pekerjaan diproses</option></select></label><label id="deposit-field" hidden>DP diterima (Rp)<input type="number" name="deposit" id="deposit-amount" min="1" step="1" value="{{ old('deposit') }}"><small>Masukkan uang yang benar-benar diterima sekarang.</small></label><p id="deposit-balance" hidden>Sisa tagihan: <strong id="remaining-total">Rp 0</strong></p><div class="sale-payment-grid"><label>Metode pembayaran<select name="payment_method" required><option value="cash" @selected(old('payment_method')==='cash')>Tunai</option><option value="transfer" @selected(old('payment_method')==='transfer')>Transfer</option></select></label><div class="sale-total-box"><span>Total pembayaran</span><strong id="sale-total" aria-live="polite">Rp 0</strong></div></div>
<p class="muted">Lunas: pembayaran penuh dan barang/pekerjaan sudah diserahkan. DP: stok disisihkan; pelunasan dicatat pada transaksi yang sama.</p>
<button type="submit">Simpan transaksi →</button>
</section></form>
@endsection
