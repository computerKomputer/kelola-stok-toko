<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Schema,DB};
return new class extends Migration {
 public function up():void {
  Schema::table('products',fn(Blueprint $t)=>$t->unsignedInteger('reserved_stock')->default(0));
  Schema::table('orders',function(Blueprint $t){$t->boolean('staged_sale')->default(false);$t->timestamp('fulfilled_at')->nullable();$t->timestamp('completed_at')->nullable()->index();});
  Schema::create('payments',function(Blueprint $t){$t->id();$t->foreignId('order_id')->constrained()->restrictOnDelete();$t->foreignId('user_id')->constrained()->restrictOnDelete();$t->string('token',100)->unique();$t->bigInteger('amount');$t->string('method');$t->text('note')->nullable();$t->timestamp('received_at')->index();$t->timestamps();});
  DB::table('orders')->where('status','completed')->orderBy('id')->chunkById(200,function($orders){foreach($orders as $o){DB::table('orders')->where('id',$o->id)->update(['completed_at'=>$o->updated_at,'fulfilled_at'=>$o->updated_at]);DB::table('payments')->insert(['order_id'=>$o->id,'user_id'=>$o->user_id,'token'=>'legacy-'.$o->id,'amount'=>$o->total,'method'=>$o->payment_method==='transfer'?'transfer':'cash','note'=>'Migrasi pembayaran transaksi lama','received_at'=>$o->paid_at??$o->updated_at,'created_at'=>now(),'updated_at'=>now()]);}});
 }
 public function down():void {Schema::dropIfExists('payments');Schema::table('orders',fn(Blueprint $t)=>$t->dropColumn(['staged_sale','fulfilled_at','completed_at']));Schema::table('products',fn(Blueprint $t)=>$t->dropColumn('reserved_stock'));}
};
