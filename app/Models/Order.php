<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Order extends Model {
 protected $guarded=['id'];
 protected function casts():array{return ['paid_at'=>'datetime','fulfilled_at'=>'datetime','completed_at'=>'datetime','staged_sale'=>'boolean'];}
 public function scopePaidToday($query){return $query->where('status','completed')->where('paid_at','>=',today())->where('paid_at','<',today()->addDay());}
 public function payments(){return $this->hasMany(Payment::class);}
 public function getPaidAmountAttribute(){return (int)$this->payments()->sum('amount')+(int)$this->settlement_credit;}
 public function getBalanceAttribute(){return $this->status==='cancelled'?0:max(0,$this->total-$this->paid_amount);}
 public function items(){return $this->hasMany(OrderItem::class);}
 public function user(){return $this->belongsTo(User::class);}
}
