<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class IslandServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Blade::directive('react', function ($expression) {
            // Expression is expected to be 'ComponentName', $propsArray
            return "<?php 
                \$args = [$expression];
                \$component = \$args[0];
                \$props = isset(\$args[1]) ? json_encode(\$args[1], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) : '{}';
                echo \"<div data-react-island=\\\"\$component\\\" data-react-props='\$props'></div>\";
            ?>";
        });
    }
}
