<?php namespace Sebalbisu\Laravel\Input;

use Illuminate\Support\ServiceProvider;
use Illuminate\Events\Dispatcher as EventDispatcher;

class Provider extends ServiceProvider {

    public function register()
    {
        $this->app->singleton('input.event-dispatcher', function($app)
        {
            return new EventDispatcher(app());
        });

        $this->app->singleton('input.event-exception-dispatcher', function($app)
        {
            return new EventDispatcher(app());
        });

        $this->app->resolving(
            __NAMESPACE__ . '\Runnable', 

            function($object, $app) 
            { 
                $object->run();
            }
        );

        $this->app->resolving(
            __NAMESPACE__ . '\Request\IFailListening', 

            function($object, $app) 
            { 
                $object->listenInputFail(); 
            }
        );

        $this->app->singleton('input.default-request-events-to-listen', 
            function() {
                return [
                    __NAMESPACE__ . '\Events\NotFound',
                    __NAMESPACE__ . '\Events\AccessDenied',
                    __NAMESPACE__ . '\Events\ValidationFail',
                ];
            }
        );
    }
}
