@extends('layout')
@section('title','Buat pembelian')
@section('content')
<div class="page-heading"><div><span class="eyebrow">KHUSUS OWNER</span><h1>Buat pembelian</h1><p>Harga modal mencakup harga beli dan alokasi biaya tambahan.</p></div></div>
<form class="panel form-panel" method="post" action="/manage/purchases">@csrf
<label>Supplier<select name="supplier_id" required>@foreach($suppliers as $s)<option value="{{ $s->id }}" @selected(old('supplier_id')==$s->id)>{{ $s->name }}</option>@endforeach</select><small><a href="/manage/suppliers">Kelola supplier →</a></small></label>
<label>Produk<select name="product_id" required>@foreach($products as $p)<option value="{{ $p->id }}" @selected(old('product_id')==$p->id)>{{ $p->name }}</option>@endforeach</select></label>
<div class="form-grid"><label>Jumlah dipesan<input name="quantity" type="number" value="{{ old('quantity',1) }}" min="1" max="1000000" required></label><label>Harga beli per unit (Rp)<input name="unit_cost" type="number" value="{{ old('unit_cost') }}" min="0" max="1000000000" required></label></div>
<label>Biaya tambahan total (Rp)<input name="extra_cost" type="number" value="{{ old('extra_cost',0) }}" min="0" max="1000000000" required><small>Misalnya ongkir supplier, dibagi merata per unit.</small></label><label>Catatan Owner<textarea name="note" maxlength="500">{{ old('note') }}</textarea></label><button @disabled($suppliers->isEmpty()||$products->isEmpty())>Buat pembelian →</button></form>
@endsection

