<?php
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
Artisan::command('app:create-owner',function(){
 $name=$this->ask('Nama Owner');$email=$this->ask('Email Owner');$password=$this->secret('Kata sandi (minimal 10 karakter)');
 $v=validator(compact('name','email','password'),['name'=>'required|max:100','email'=>'required|email|unique:users','password'=>'required|min:10']);
 if($v->fails()){$this->error($v->errors()->first());return 1;}
 User::create(compact('name','email','password')+['role'=>'owner']);$this->info('Akun Owner berhasil dibuat.');
})->purpose('Membuat akun Owner tanpa akun atau kata sandi bawaan');
