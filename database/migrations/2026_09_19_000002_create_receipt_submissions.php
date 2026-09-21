<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void {Schema::create('receipt_submissions',function(Blueprint $t){$t->id();$t->uuid('token')->unique();$t->foreignId('purchase_id')->constrained()->cascadeOnDelete();$t->timestamp('created_at');});}
 public function down():void {Schema::dropIfExists('receipt_submissions');}
};
