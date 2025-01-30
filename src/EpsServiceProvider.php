<?php 
namespace Sixersoft\EPSPayment;

use Illuminate\Support\ServiceProvider;

class EpsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('eps', function () {
            return new EPS();
        });

        $this->mergeConfigFrom(__DIR__ . '/Config/eps.php', 'eps');
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/Config/eps.php' => config_path('eps.php'),
        ], 'config');
    }
}
