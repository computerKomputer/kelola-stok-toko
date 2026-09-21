<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
 Schema::create('users',function(Blueprint $t){$t->id();$t->string('name');$t->string('email')->unique();$t->string('password');$t->string('role')->default('customer')->index();$t->text('bio')->nullable();$t->rememberToken();$t->timestamps();});
 Schema::create('categories',function(Blueprint $t){$t->id();$t->string('name')->unique();$t->timestamps();});
 Schema::create('products',function(Blueprint $t){$t->id();$t->string('sku')->unique();$t->string('name');$t->string('category');$t->text('description');$t->string('image')->nullable();$t->unsignedBigInteger('price');$t->unsignedTinyInteger('discount')->default(0);$t->unsignedBigInteger('cost_price')->default(0);$t->unsignedInteger('stock')->default(0);$t->unsignedInteger('min_stock')->default(5);$t->boolean('active')->default(true);$t->timestamps();});
 Schema::create('suppliers',function(Blueprint $t){$t->id();$t->string('name');$t->string('phone');$t->text('address')->nullable();$t->timestamps();});
 Schema::create('purchases',function(Blueprint $t){$t->id();$t->string('number')->unique();$t->foreignId('supplier_id')->constrained()->restrictOnDelete();$t->string('status')->default('ordered');$t->unsignedBigInteger('extra_cost')->default(0);$t->text('note')->nullable();$t->timestamps();});
 Schema::create('purchase_items',function(Blueprint $t){$t->id();$t->foreignId('purchase_id')->constrained()->cascadeOnDelete();$t->foreignId('product_id')->constrained()->restrictOnDelete();$t->unsignedInteger('quantity');$t->unsignedInteger('received')->default(0);$t->unsignedBigInteger('unit_cost');$t->timestamps();});
 Schema::create('orders',function(Blueprint $t){$t->id();$t->string('number')->unique();$t->string('checkout_token',36)->unique();$t->foreignId('user_id')->constrained()->restrictOnDelete();$t->string('customer_name');$t->string('phone');$t->text('address');$t->string('status')->default('pending')->index();$t->string('payment_method')->default('cod');$t->unsignedBigInteger('total');$t->timestamps();});
 Schema::create('order_items',function(Blueprint $t){$t->id();$t->foreignId('order_id')->constrained()->cascadeOnDelete();$t->foreignId('product_id')->constrained()->restrictOnDelete();$t->string('product_name');$t->unsignedInteger('quantity');$t->unsignedBigInteger('unit_price');$t->unsignedBigInteger('unit_cost');$t->timestamps();});
 Schema::create('stock_movements',function(Blueprint $t){$t->id();$t->foreignId('product_id')->constrained()->restrictOnDelete();$t->foreignId('user_id')->constrained()->restrictOnDelete();$t->integer('quantity');$t->unsignedInteger('balance');$t->string('type');$t->string('reference');$t->text('note')->nullable();$t->timestamps();});
 Schema::create('articles',function(Blueprint $t){$t->id();$t->foreignId('user_id')->constrained()->restrictOnDelete();$t->string('title');$t->string('slug')->unique();$t->string('category');$t->string('tags')->nullable();$t->text('excerpt');$t->longText('body');$t->string('image')->nullable();$t->unsignedInteger('views')->default(0);$t->timestamps();});
 Schema::create('settings',function(Blueprint $t){$t->id();$t->string('key')->unique();$t->text('value');$t->timestamps();});
 }
 public function down(): void {foreach(['settings','articles','stock_movements','order_items','orders','purchase_items','purchases','suppliers','products','categories','users'] as $table) Schema::dropIfExists($table);}
};
