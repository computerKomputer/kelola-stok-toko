<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Product extends Model {
 protected $guarded=['id','stock','reserved_stock','cost_price','cost_confirmed'];
 protected $hidden=['cost_price','cost_confirmed'];
 protected function casts(): array {return ['active'=>'boolean'];}
 public function getAvailableStockAttribute():int{return max(0,(int)$this->stock-(int)$this->reserved_stock);}
 public function getSalePriceAttribute(): int {return (int) round($this->price*(100-$this->discount)/100);}
 public function getGalleryAttribute(): array {
  $photos=[];
  foreach(['image'=>'Utama','image_left'=>'Tampak kiri','image_right'=>'Tampak kanan'] as $field=>$label){
   if($this->$field)$photos[]=['url'=>'/media/'.$this->$field,'label'=>$label];
  }
  return $photos ?: [['url'=>$this->image_url,'label'=>'Foto produk']];
 }
 public function getImageUrlAttribute(): string {
  if($this->image || $this->image_left || $this->image_right)return '/media/'.($this->image ?: ($this->image_left ?: $this->image_right));
  $category=mb_strtolower($this->category??'');
  $type=match(true){
   str_contains($category,'laptop'),str_contains($category,'desktop')=>'laptop',
   str_contains($category,'monitor')=>'monitor',
   str_contains($category,'printer')=>'printer',
   str_contains($category,'storage'),str_contains($category,'ram')=>'storage',
   str_contains($category,'jaringan')=>'network',
   default=>'accessory',
  };
  return '/illustrations/tech-'.$type.'.svg';
 }
}
