<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Payment extends Model {
 protected $guarded=['id'];
 protected function casts():array{return ['received_at'=>'datetime'];}
 public function order(){return $this->belongsTo(Order::class);}
 public function scopeToday($q){return $q->where('received_at','>=',today())->where('received_at','<',today()->addDay());}
}
