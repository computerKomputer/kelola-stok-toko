@extends('layout')
@section('title','Pengaturan diskon')
@section('content')
<div class="page-heading"><div><span class="eyebrow">KENDALI OWNER</span><h1>Pengaturan diskon</h1><p>Tentukan batas diskon yang bisa diberikan Admin.</p></div></div><form class="panel form-panel" method="post" action="/manage/settings">@csrf @method('PUT')<label>Batas diskon Admin (%)<input type="number" name="limit" value="{{ old('limit',$limit) }}" min="0" max="100" required></label><p class="muted">Owner dapat memberi diskon hingga 100%. Perubahan batas berlaku pada perubahan diskon berikutnya; diskon produk yang sudah ada tetap berlaku.</p><button>Simpan pengaturan</button></form>
@endsection

