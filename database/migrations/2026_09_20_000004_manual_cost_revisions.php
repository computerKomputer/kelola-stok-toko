<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Schema,DB};
return new class extends Migration {
 public function up():void {
  Schema::table('products',fn(Blueprint $t)=>$t->boolean('cost_confirmed')->default(false));
  Schema::table('order_items',fn(Blueprint $t)=>$t->boolean('cost_confirmed')->default(false));
  DB::table('products')->where('cost_price','>',0)->update(['cost_confirmed'=>true]);
  DB::table('order_items')->where('unit_cost','>',0)->update(['cost_confirmed'=>true]);
  Schema::create('cost_revisions',function(Blueprint $t){$t->id();$t->foreignId('user_id')->constrained();$t->foreignId('product_id')->constrained();$t->foreignId('order_item_id')->nullable()->constrained();$t->unsignedBigInteger('old_cost');$t->unsignedBigInteger('new_cost');$t->text('note');$t->timestamps();});
 }
 public function down():void {Schema::dropIfExists('cost_revisions');Schema::table('order_items',fn(Blueprint $t)=>$t->dropColumn('cost_confirmed'));Schema::table('products',fn(Blueprint $t)=>$t->dropColumn('cost_confirmed'));}
};
