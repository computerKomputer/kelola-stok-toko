<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void {Schema::create('expenses',function(Blueprint $t){$t->id();$t->string('token',36)->unique();$t->foreignId('user_id')->constrained()->restrictOnDelete();$t->string('category');$t->unsignedBigInteger('amount');$t->string('method');$t->date('spent_on')->index();$t->string('recipient');$t->text('note');$t->foreignId('supplier_id')->nullable()->constrained()->restrictOnDelete();$t->foreignId('product_id')->nullable()->constrained()->restrictOnDelete();$t->integer('stock_change')->default(0);$t->timestamps();});}
 public function down():void {Schema::dropIfExists('expenses');}
};
