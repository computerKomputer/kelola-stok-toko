<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Schema,DB};
return new class extends Migration {
 public function up():void {
  Schema::create('expense_categories',function(Blueprint $t){$t->id();$t->string('code')->unique();$t->string('name',100)->unique();$t->boolean('active')->default(true);$t->timestamps();});
  foreach(['purchase'=>'Pembelian stok / sparepart','operational'=>'Operasional toko','payroll'=>'Gaji & komisi','internal'=>'Kerusakan / biaya internal'] as $code=>$name) DB::table('expense_categories')->insert(['code'=>$code,'name'=>$name,'active'=>true,'created_at'=>now(),'updated_at'=>now()]);
 }
 public function down():void {Schema::dropIfExists('expense_categories');}
};
