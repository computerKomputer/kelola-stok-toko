<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void {
  Schema::table('order_items',function(Blueprint $t){$t->unsignedBigInteger('product_id')->nullable()->change();$t->string('item_type')->default('product');});
  Schema::table('cost_revisions',fn(Blueprint $t)=>$t->unsignedBigInteger('product_id')->nullable()->change());
 }
 public function down():void {Schema::table('order_items',fn(Blueprint $t)=>$t->dropColumn('item_type'));}
};
