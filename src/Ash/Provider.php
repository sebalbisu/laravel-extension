<?php namespace Sebalbisu\Laravel\Ash;

use Illuminate\Support\ServiceProvider;
use Illuminate\Events\Dispatcher as EventDispatcher;

class Provider extends ServiceProvider {

    public function register()
    {
        $this->app->singleton('ash.event-dispatcher', function($app)
        {
            return new EventDispatcher(app());
        });

        $this->app->singleton('ash.event-exception-dispatcher', function($app)
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
                $object->listenAshFail(); 
            }
        );

        $this->app->singleton('ash.default-request-events-to-listen', 
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
