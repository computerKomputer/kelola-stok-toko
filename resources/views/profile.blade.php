@extends('layout')
@section('title','Profil saya')
@section('content')
<div class="page-heading"><div><span class="eyebrow">AKUN ANDA</span><h1>Profil saya</h1><p>Perbarui identitas dan kata sandi akun.</p></div></div>
<form class="panel form-panel" method="post" action="/profile">@csrf @method('PUT')
<label>Nama lengkap<input name="name" value="{{ old('name',auth()->user()->name) }}" required></label>
<label>Email<input type="email" name="email" value="{{ old('email',auth()->user()->email) }}" required></label>
<label>Bio singkat<textarea name="bio" maxlength="1000">{{ old('bio',auth()->user()->bio) }}</textarea></label>
<h3>Ubah kata sandi</h3><p class="muted">Kosongkan jika tidak ingin mengubah kata sandi.</p>
<label>Kata sandi saat ini<input type="password" name="current_password" autocomplete="current-password"></label>
<div class="form-grid"><label>Kata sandi baru<input type="password" name="password" minlength="10" autocomplete="new-password"></label><label>Ulangi kata sandi baru<input type="password" name="password_confirmation" minlength="10" autocomplete="new-password"></label></div><button>Simpan profil</button>
</form>
@endsection

