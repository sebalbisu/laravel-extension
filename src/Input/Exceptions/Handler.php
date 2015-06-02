<?php namespace Sebalbisu\Laravel\Input\Exceptions;

use Sebalbisu\Laravel\Input\Events;

class Handler {

    public function render($request, &$e)
    {
        switch($e)
        {
            case $e instanceof NotFound:
                $event = new Events\NotFound(); 
                $response = self::eventDispatcher()->fire($event, [], $halt = true);
                if($response) $e = $response;
                break;

            case $e instanceof AccessDenied:
                $event = new Events\AccessDenied();
                $response = self::eventDispatcher()->fire($event, [], $halt = true);

                if($response) $e = $response;
                break;

            case $e instanceof Validation:
                $event = new Events\ValidationFail($e->validator()); 
                $response = self::eventDispatcher()->fire($event, [], $halt = true);
                if($response) return $response;
                break;

            default:
                break;
        }
    }

    static public function eventDispatcher(EventDispatcher $dispatcher = null)
    {
        return app('input.event-exception-dispatcher');
    }
}
