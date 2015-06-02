<?php namespace Sebalbisu\Laravel\Validation;

use Illuminate\Support\ServiceProvider;
use Illuminate\Events\Dispatcher as EventDispatcher;

class Provider extends ServiceProvider {

    protected $extensions = 
    [
        'invalid' => __NAMESPACE__ . '\Extensions\Invalid',
        'skip' => __NAMESPACE__ . '\Extensions\Skip',
        'time' => __NAMESPACE__ . '\Extensions\Time',
    ];

    public function boot()
    {
        $factory = app('validator');

        $factory->resolver(function($t, $d, $r, $c) 
        {
           return new Validator($t, $d, $r, $c);
        });

        //register extensions in factory:
        
        foreach($this->extensions as $rule => $class)
        {
            $class::register($rule, $factory);
        } 
    }

    public function register()
    {
        $this->app->singleton('validator', function($app)
        {
            $validator = new Factory($app['translator'], $app);

            if (isset($app['validation.presence']))
            {
                $validator->setPresenceVerifier($app['validation.presence']);
            }

            return $validator;
        });
    }
}
