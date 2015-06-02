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
            'Sebalbisu\Laravel\Input\Runnable', 
            function($object, $app) { $object->run(); }
        );

        $this->app->resolving(
            'Sebalbisu\Laravel\Input\Request\IFailListening', 
            function($object, $app) { $object->listenInputFail(); }
        );

        $this->app->singleton('input.default-request-events-to-listen', 
            function() {
                return [
                    'Sebalbisu\Laravel\Input\Events\NotFound',
                    'Sebalbisu\Laravel\Input\Events\AccessDenied',
                    'Sebalbisu\Laravel\Input\Events\ValidationFail',
                ];
            }
        );
    }
}
