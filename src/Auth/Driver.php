<?php namespace Sebalbisu\Laravel\Auth;

use Illuminate\Auth\Guard;

class Driver {

    static protected $providerName = '\Sebalbisu\Laravel\Auth\UserProvider'; 

    static public function create($app)
    {
        $provider = $app->make(self::$providerName);
        return new Guard($provider, $app['session.store']);
    }
}
