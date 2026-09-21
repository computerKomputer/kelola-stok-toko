<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Article extends Model {
 protected $guarded=['id','views'];
 public function author(){return $this->belongsTo(User::class,'user_id');}
}
