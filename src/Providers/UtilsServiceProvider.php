<?php

namespace ChrisHenrique\LaravelUtils\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class UtilsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Blade::directive('fromdecimal', function ($data) {
            return "<?php echo Number::fromDecimalApp($data); ?>";
        });

        Blade::directive('frommoney', function ($data) {
            return "<?php echo Number::fromMoneyApp($data); ?>";
        });

        Blade::directive('frominteger', function ($data) {
            return "<?php echo Number::fromIntegerApp($data); ?>";
        });
    }

    public function register()
    {
        
    }
}