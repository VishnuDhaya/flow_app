<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->group(base_path('routes/livewire.php'));
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));

    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));

        Route::prefix('mobile_api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/mobile_api.php'));

        Route::prefix('api/mobile')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/mobile_api_v2.php'));

        Route::prefix('api/cust_mob')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/cust_api.php'));

        Route::prefix('internal_api')
            // ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/internal_api.php'));
        
        Route::prefix('api/partner')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/partner_api.php'));
    }
}
