<?php namespace Sebalbisu\Laravel\Input\Request;

use Sebalbisu\Laravel\Input\Events;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

trait FailListenerTrait {

    protected $inputEvents;

    public function onInputFail($urlOrCb)
    {
        if(is_callable($urlOrCb))
        {
            return call_user_func($urlOrCb, $this);
        }
        else
        {
            $this->redirect = $urlOrCb;

            return $this;
        }
    }

    public function listenInputFail()
    {
        $this->inputEvents = $this->inputEvents ?: 
            app('input.default-request-events-to-listen');

        app('input.event-exception-dispatcher')
            ->listen($this->inputEvents, [$this, 'inputFailListener']);
    }

    public function noListenInputFail()
    {
        foreach($this->inputEvents as $event)
        {
            app('input.event-exception-dispatcher')
                ->forget($event);
        }
    }

    public function inputFailListener($event)
    {
        if ($event instanceof Events\NotFound)
        {
            return new NotFoundHttpException();
        }
        elseif ($event instanceof Events\AccessDenied)
        {
            return new AccessDeniedHttpException(403);
        } 
        elseif ($event instanceof Events\ValidationFail)
        {
            $msgs = $event->validator()->errors()->getMessages(); 

            return $this->response($msgs);
        }
    }

}
