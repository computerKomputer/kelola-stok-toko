<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
class AppServiceProvider extends ServiceProvider
{
 public function boot(): void {
  Gate::define('owner', fn(User $user) => $user->role === 'owner');
  Blade::directive('rupiah', fn($value) => "<?php echo 'Rp '.number_format((float)($value), 0, ',', '.'); ?>");
 }
}
