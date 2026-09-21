<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80)->unique();
            $table->timestamps();
        });

        // Preserve categories already used by existing products.
        foreach (DB::table('products')->where('category', '<>', '')->distinct()->pluck('category') as $name) {
            DB::table('product_categories')->insertOrIgnore([
                'name' => $name, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_categories');
    }
};
