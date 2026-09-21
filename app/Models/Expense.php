<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Expense extends Model {
 protected $guarded=['id'];
 public const CATEGORIES=['purchase'=>'Pembelian stok / sparepart','operational'=>'Operasional toko','payroll'=>'Gaji & komisi','internal'=>'Kerusakan / biaya internal'];
 public function product(){return $this->belongsTo(Product::class);}
 public function user(){return $this->belongsTo(User::class);}
 public function supplier(){return $this->belongsTo(Supplier::class);}
 protected function casts():array{return ['spent_on'=>'date'];}
}
