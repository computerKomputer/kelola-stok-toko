@extends('layout')
@section('title','Supplier')
@section('content')
<div class="page-heading"><div><span class="eyebrow">MITRA TOKO</span><h1>Supplier</h1><p>Informasi pemasok hanya dapat diakses Owner.</p></div></div>
<details class="panel" @if($suppliers->isEmpty()) open @endif><summary>＋ Tambah supplier</summary><form method="post" action="/manage/suppliers" class="form-grid">@csrf<label>Nama supplier<input name="name" required maxlength="150"></label><label>Telepon<input name="phone" required maxlength="30"></label><label>Alamat<textarea name="address" maxlength="500"></textarea></label><div class="align-end"><button>Simpan supplier</button></div></form></details>
@foreach($suppliers as $s)<details class="panel"><summary>{{ $s->name }} <span class="muted">· {{ $s->phone }}</span></summary><form class="form-grid" method="post" action="/manage/suppliers/{{ $s->id }}">@csrf @method('PUT')<label>Nama<input name="name" value="{{ $s->name }}" required></label><label>Telepon<input name="phone" value="{{ $s->phone }}" required></label><label>Alamat<textarea name="address">{{ $s->address }}</textarea></label><div class="align-end"><button>Simpan perubahan</button></div></form><form method="post" action="/manage/suppliers/{{ $s->id }}" data-confirm="Hapus supplier ini?">@csrf @method('DELETE')<button class="text-button danger">Hapus supplier</button></form></details>@endforeach @include('components.pager',['paginator'=>$suppliers])
@endsection

