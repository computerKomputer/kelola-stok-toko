<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            $old = DB::table('categories')->where('name', 'Panduan upgrade')->first();
            if ($old) {
                if (DB::table('categories')->where('name', 'Umum')->exists()) {
                    DB::table('categories')->where('id', $old->id)->delete();
                } else {
                    DB::table('categories')->where('id', $old->id)->update(['name' => 'Umum', 'updated_at' => now()]);
                }
            }
            DB::table('articles')->where('category', 'Panduan upgrade')->update(['category' => 'Umum', 'updated_at' => now()]);
        });
    }

    public function down(): void
    {
        // Do not rename user-created "Umum" content when rolling back.
    }
};
