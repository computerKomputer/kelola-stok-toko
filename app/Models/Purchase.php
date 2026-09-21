<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Purchase extends Model {
 protected $guarded=['id'];
 protected $hidden=['extra_cost'];
 public function items(){return $this->hasMany(PurchaseItem::class);}
 public function supplier(){return $this->belongsTo(Supplier::class);}
}
