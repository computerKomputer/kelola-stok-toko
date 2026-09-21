<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void {
  Schema::table('orders',fn(Blueprint $t)=>$t->unsignedBigInteger('settlement_credit')->default(0));
  Schema::create('sale_cancellations',function(Blueprint $t){$t->id();$t->foreignId('order_id')->unique()->constrained()->restrictOnDelete();$t->foreignId('user_id')->constrained()->restrictOnDelete();$t->foreignId('fee_order_id')->nullable()->constrained('orders')->restrictOnDelete();$t->unsignedBigInteger('paid_before');$t->unsignedBigInteger('fee');$t->unsignedBigInteger('refund');$t->string('method');$t->text('note');$t->timestamps();});
 }
 public function down():void {Schema::dropIfExists('sale_cancellations');Schema::table('orders',fn(Blueprint $t)=>$t->dropColumn('settlement_credit'));}
};
