@extends('layout')
@section('title','Modal / biaya transaksi')
@section('content')
<div class="page-heading"><div><h1>Modal / biaya transaksi</h1><p>{{ $item->order->number }} · {{ $item->product_name }} · {{ $item->quantity }} unit</p></div><a href="/orders/{{ $item->order_id }}">← Kembali</a></div>
<form class="panel form-panel" method="post" action="/manage/sale-items/{{ $item->id }}/cost">@csrf @method('PUT')
<label>Modal / biaya langsung per unit (Rp)<input name="unit_cost" type="number" min="0" max="1000000000" step="1" required value="{{ old('unit_cost',$item->unit_cost) }}"></label>
<label>Alasan pengisian / koreksi<textarea name="note" required maxlength="500">{{ old('note') }}</textarea></label>
<p>Isi 0 jika memang tanpa biaya langsung. Nilai ini berlaku hanya untuk baris transaksi ini. Omzet dan stok tetap sama.</p><button>Simpan biaya</button></form>
<section class="panel table-wrap"><h2>Riwayat biaya</h2><table><thead><tr><th>Waktu / petugas</th><th>Sebelum</th><th>Sesudah</th><th>Alasan</th></tr></thead><tbody>@forelse($revisions as $rev)<tr><td>{{ $rev->created_at }} · {{ $rev->name }}</td><td>@rupiah($rev->old_cost)</td><td>@rupiah($rev->new_cost)</td><td>{{ $rev->note }}</td></tr>@empty<tr><td colspan="4">Belum ada koreksi.</td></tr>@endforelse</tbody></table>@include('components.pager',['paginator'=>$revisions])</section>
@endsection
