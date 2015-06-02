<?php namespace Sebalbisu\Laravel\Auth;
 
use Illuminate\Support\ServiceProvider;

class Provider extends ServiceProvider
{

    public function boot()
    {
    }
    
    public function register()
    {
        //register a custom driver
        $this->app->extend(
            'auth', 
            function(AuthManager $auth, $app)
            {
                $auth->extend(
                    'Sebalbisu\Laravel\Auth\Driver', 
                    function($app) { return Driver::create($app); }
                );

                return $auth;
            }
        );
    }

}
