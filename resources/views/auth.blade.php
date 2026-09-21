@extends('layout')
@section('title',$register?'Daftar akun':'Masuk')
@section('content')
<div class="auth-layout"><section class="auth-intro"><span class="eyebrow">CAKRAWALA COMPUTER</span><h1>Teknologi tepat.<br>Usaha melesat.</h1><p>Belanja perangkat komputer dan kelola operasional toko dalam satu tempat.</p><img src="/illustrations/computer-hero.svg" alt="Ilustrasi perangkat komputer Cakrawala"></section><section class="panel auth-panel"><span class="eyebrow">{{ $register?'MULAI DARI SINI':'SENANG BERTEMU LAGI' }}</span><h2>{{ $register?'Buat akun Anda':'Masuk ke akun' }}</h2><p class="muted">{{ $register?'Daftar sebagai customer untuk mulai belanja.':'Gunakan email dan kata sandi Anda.' }}</p><form method="post" action="{{ $register?'/register':'/login' }}">@csrf
@if($register)<label>Nama lengkap<input name="name" value="{{ old('name') }}" autocomplete="name" required maxlength="100"></label>@endif
<label>Email<input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required></label>
<label>Kata sandi<input type="password" name="password" autocomplete="{{ $register?'new-password':'current-password' }}" required @if($register) minlength="10" @endif></label>
@if($register)<label>Ulangi kata sandi<input type="password" name="password_confirmation" autocomplete="new-password" required minlength="10"></label><small class="muted">Gunakan minimal 10 karakter.</small>@endif
<button class="button full">{{ $register?'Buat akun':'Masuk' }} →</button></form><p class="auth-switch">{{ $register?'Sudah punya akun?':'Belum punya akun?' }} <a href="{{ $register?'/login':'/register' }}">{{ $register?'Masuk':'Daftar sekarang' }}</a></p></section></div>
@endsection


