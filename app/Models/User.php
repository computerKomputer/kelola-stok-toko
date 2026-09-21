<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
class User extends Authenticatable {
 protected $fillable=['name','email','password','role','bio'];
 protected $hidden=['password','remember_token'];
 protected function casts(): array {return ['password'=>'hashed'];}
 public function isOwner(): bool {return $this->role==='owner';}
 public function isStaff(): bool {return in_array($this->role,['admin','owner'],true);}
}
