<?php namespace Sebalbisu\Laravel\Routing;
 
use Illuminate\Support\ServiceProvider;

class Provider extends ServiceProvider
{
    public function boot()
    {
    }
    
    public function register()
    {
        $this->app->bind(
            'router.web_resource',
            'Sebalbisu\Laravel\Routing\WebResourceRegistrar'
        );
    }

}
