<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Schema,DB};
return new class extends Migration {
 public function up():void {
  Schema::table('orders',function(Blueprint $t){$t->timestamp('paid_at')->nullable()->index();});
  // Legacy completed orders were explicitly confirmed as paid in the old workflow.
  DB::table('orders')->where('status','completed')->update(['paid_at'=>DB::raw('updated_at')]);
 }
 public function down():void {Schema::table('orders',fn(Blueprint $t)=>$t->dropColumn('paid_at'));}
};
