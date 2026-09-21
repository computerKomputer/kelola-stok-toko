<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class OrderItem extends Model {
 protected $guarded=['id'];
 protected $hidden=['unit_cost','cost_confirmed'];
 public function order(){return $this->belongsTo(Order::class);}
}
