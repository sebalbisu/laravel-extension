<?php namespace Sebalbisu\Laravel\Auth;

use Illuminate\Auth\Guard;

class Driver {

    static public function create($app)
    {
        $provider = $app->make(__NAMESPACE__ . '\\UserProvider');

        return new Guard($provider, $app['session.store']);
    }
}
