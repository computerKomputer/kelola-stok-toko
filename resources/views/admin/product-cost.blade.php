@extends('layout')
@section('title','Edit modal per unit')
@section('content')
<div class="page-heading"><div><span class="eyebrow">KHUSUS OWNER / SUPERADMIN</span><h1>Edit modal per unit</h1><p>{{ $product->name }} · {{ $product->sku }}</p></div><a href="/manage/products" class="text-link">← Kembali</a></div>
<form class="panel form-panel" method="post" action="/manage/products/{{ $product->id }}/cost">
@csrf @method('PUT')
<label>Modal per unit (Rp)<input type="number" name="cost_price" value="{{ old('cost_price',$product->cost_price) }}" min="0" max="1000000000" step="1" required></label>
<label>Alasan pengisian / koreksi<textarea name="note" required maxlength="500">{{ old('note') }}</textarea></label>
<p>Modal ini digunakan untuk persediaan dan penjualan berikutnya. Centang transaksi di bawah jika modal yang sama juga ingin diterapkan pada transaksi tersebut. Omzet dan jumlah stok tidak berubah.</p>
<h2>Terapkan juga pada transaksi terpilih</h2>
<div class="table-wrap"><table><thead><tr><th>Pilih</th><th>Pesanan</th><th>Tanggal</th><th>Jumlah</th><th>Modal/unit saat ini</th></tr></thead><tbody>
@forelse($items as $item)<tr><td><input type="checkbox" name="items[]" value="{{ $item->id }}" @checked(in_array($item->id,old('items',[]))) aria-label="Koreksi modal {{ $item->order->number }}"></td><td>{{ $item->order->number }}</td><td>{{ $item->order->created_at->format('d M Y') }}</td><td>{{ $item->quantity }}</td><td>@if($item->cost_confirmed)@rupiah($item->unit_cost)@else<span class="badge warning">Modal belum lengkap</span>@endif</td></tr>
@empty<tr><td colspan="5">Belum ada transaksi.</td></tr>@endforelse
</tbody></table></div>
<p class="muted">Simpan pilihan pada halaman ini sebelum berpindah halaman transaksi. Isi 0 hanya jika barang memang tanpa biaya modal.</p>
<button type="submit">Simpan modal dan koreksi terpilih</button>
</form>
@include('components.pager',['paginator'=>$items])
<section class="panel table-wrap"><h2>Riwayat koreksi modal</h2><table><thead><tr><th>Waktu / petugas</th><th>Sasaran</th><th>Sebelum</th><th>Sesudah</th><th>Alasan</th></tr></thead><tbody>@forelse($revisions as $revision)<tr><td>{{ $revision->created_at }} · {{ $revision->name }}</td><td>{{ $revision->order_item_id?'Item transaksi #'.$revision->order_item_id:'Modal produk' }}</td><td>@rupiah($revision->old_cost)</td><td>@rupiah($revision->new_cost)</td><td>{{ $revision->note }}</td></tr>@empty<tr><td colspan="5">Belum ada koreksi modal.</td></tr>@endforelse</tbody></table>@include('components.pager',['paginator'=>$revisions])</section>
@endsection
