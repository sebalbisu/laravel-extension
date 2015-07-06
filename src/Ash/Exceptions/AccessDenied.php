<?php namespace Sebalbisu\Laravel\Ash\Exceptions;

use Sebalbisu\Laravel\Ash\Events;

class AccessDenied extends HttpResponseException
{
    protected $message = 'Access denied';

    protected $code = 403;

    public function getResponse()
    {
        return $this->makeResponse(new Events\AccessDenied);
    }
}
