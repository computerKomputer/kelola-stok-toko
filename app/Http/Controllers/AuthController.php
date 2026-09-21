<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth,Hash,RateLimiter};
use Illuminate\Validation\{Rule,ValidationException};
class AuthController {
 public function form(){return view('auth',['register'=>false]);}
 public function registerForm(){return view('auth',['register'=>true]);}
 public function login(Request $r){
  $data=$r->validate(['email'=>'required|email','password'=>'required|string']);
  $key='login:'.hash('sha256',strtolower($data['email']).'|'.$r->ip());
  if(RateLimiter::tooManyAttempts($key,5))throw ValidationException::withMessages(['email'=>'Terlalu banyak percobaan. Coba lagi dalam satu menit.']);
  if(!Auth::attempt($data)){RateLimiter::hit($key,60);throw ValidationException::withMessages(['email'=>'Email atau kata sandi tidak sesuai.']);}
  RateLimiter::clear($key);$r->session()->regenerate();
  return redirect()->intended('/dashboard');
 }
 public function register(Request $r){
  $d=$r->validate(['name'=>'required|string|max:100','email'=>'required|email|max:200|unique:users','password'=>'required|string|min:10|confirmed']);
  $d['role']='customer';$u=User::create($d);Auth::login($u);$r->session()->regenerate();return redirect('/dashboard');
 }
 public function logout(Request $r){Auth::logout();$r->session()->invalidate();$r->session()->regenerateToken();return redirect('/');}
 public function profile(){return view('profile');}
 public function updateProfile(Request $r){
  $d=$r->validate(['name'=>'required|string|max:100','email'=>['required','email','max:200',Rule::unique('users')->ignore($r->user()->id)],'bio'=>'nullable|string|max:1000','current_password'=>'required_with:password|current_password','password'=>'nullable|string|min:10|confirmed']);
  unset($d['current_password']);if(empty($d['password']))unset($d['password']);
  $r->user()->update($d);return back()->with('success','Profil berhasil diperbarui.');
 }
}
