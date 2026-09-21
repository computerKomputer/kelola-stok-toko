<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\{Category,Setting};
class DatabaseSeeder extends Seeder {
 public function run(): void {
  foreach(['Inspirasi','Panduan','Kabar toko'] as $name)Category::firstOrCreate(compact('name'));
  Setting::firstOrCreate(['key'=>'admin_discount_limit'],['value'=>'10']);
 }
}
