<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Setting extends Model {
 protected $guarded=['id'];
 public static function valueOf(string $key, string $default=''): string {return (string)(static::where('key',$key)->value('value') ?? $default);}
}
