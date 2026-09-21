<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class UserController {
 public function index(Request $r){return view('admin.users',['users'=>User::when(!$r->user()->isOwner(),fn($q)=>$q->where('role','writer'))->orderBy('name')->paginate(10)]);}
 public function save(Request $r,?User $user=null){
  if(!$r->user()->isOwner())abort_unless(!$user || $user->role==='writer',403);
  $roles=$r->user()->isOwner()?['customer','writer','admin','owner']:['writer'];
  $d=$r->validate(['name'=>'required|string|max:100','email'=>['required','email','max:200',Rule::unique('users')->ignore($user?->id)],'role'=>['required',Rule::in($roles)],'password'=>[$user?'nullable':'required','string','min:10']]);
  if($user && $user->id===$r->user()->id && $d['role']!==$user->role)return back()->withErrors(['role'=>'Anda tidak dapat mengubah peran akun sendiri.']);
  if(empty($d['password']))unset($d['password']);
  if($user){$user->update($d);if(isset($d['password']))$user->forceFill(['remember_token'=>null])->save();}else User::create($d);
  return back()->with('success','Akun pengguna disimpan.');
 }
}
