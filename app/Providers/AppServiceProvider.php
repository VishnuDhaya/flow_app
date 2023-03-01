<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use DB;
use Event;
use Log;

use App\Repositories\SQL\CommonRepositorySQL;
use App\Repositories\SQL\MarketRepositorySQL;
use App\Services\Support\CacheService;
use Illuminate\Contracts\Routing\UrlGenerator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        
     //Schema::defaultStringLength(191);
        if(env('ENFORCE_SSL', false)) {
            $url->forceScheme('https');
        }

        DB::listen(function($query) {
            Log::info(
                $query->sql,
                $query->bindings,
                $query->time
            );
        });

        //CacheService::flush();
        //CacheService::load();
        $mainPath = database_path('migrations');
        $directories = glob($mainPath . '/*' , GLOB_ONLYDIR);
        $paths = array_merge([$mainPath], $directories);
        
        $this->loadMigrationsFrom($paths);
        
    }
    

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
