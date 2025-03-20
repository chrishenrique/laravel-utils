<?php

namespace ChrisHenrique\LaravelUtils\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class UtilsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Blade::directive('datetime', function ($data) {
            return "<?php if($data) echo ($data)->format('d/m/Y H:i'); ?>";
        });

        Blade::directive('date', function ($data) {
            return "<?php if($data) echo ($data)->format('d/m/Y'); ?>";
        });

        Blade::directive('fromdecimal', function ($number) {
            return "<?php echo number($number)->fromDecimalApp(); ?>";
        });

        Blade::directive('number', function ($number) {
            return "<?php echo number($number); ?>";
        });

        Blade::directive('frommoney', function ($number) {
            return "<?php echo number($number)->fromMoneyApp(); ?>";
        });

        Blade::directive('frominteger', function ($number) {
            return "<?php echo number($number)->fromIntegerApp(); ?>";
        });
    }

    public function register()
    {
        
    }
}