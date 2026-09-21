<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ExpenseCategory extends Model {
 protected $guarded=['id'];
 protected function casts():array{return ['active'=>'boolean'];}
 public function expenses(){return $this->hasMany(Expense::class,'category','code');}
 public static function labels(bool $activeOnly=false):array{return static::query()->when($activeOnly,fn($q)=>$q->where('active',true))->orderBy('id')->pluck('name','code')->all();}
}
