<?php namespace Sebalbisu\Laravel\Ash\Request;

use Sebalbisu\Laravel\Ash\Events;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

trait FailListenerTrait {

    protected $ashEvents;

    public function onAshFail($urlOrCb)
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

    public function listenAshFail()
    {
        $this->ashEvents = $this->ashEvents ?: 
            app('ash.default-request-events-to-listen');

        app('ash.event-exception-dispatcher')
            ->listen($this->ashEvents, [$this, 'ashFailListener']);
    }

    public function noListenAshFail()
    {
        foreach($this->ashEvents as $event)
        {
            app('ash.event-exception-dispatcher')
                ->forget($event);
        }
    }

    public function ashFailListener($event)
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
