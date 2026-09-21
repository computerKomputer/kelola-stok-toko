<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PurchaseItem extends Model {
 protected $guarded=['id'];
 protected $hidden=['unit_cost'];
 public function product(){return $this->belongsTo(Product::class);}
 public function purchase(){return $this->belongsTo(Purchase::class);}
}
