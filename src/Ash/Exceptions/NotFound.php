<?php namespace Sebalbisu\Laravel\Ash\Exceptions;

use Sebalbisu\Laravel\Ash\Events;

class NotFound extends HttpResponseException
{
    protected $message = 'Not Found';

    protected $code = 404;

    public function getResponse()
    {
        return $this->makeResponse(new Events\NotFound);
    }
}
