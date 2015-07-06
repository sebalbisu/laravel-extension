<?php namespace Sebalbisu\Laravel\Ash\Exceptions;

use Illuminate\Http\Exception\HttpResponseException as HttpResponseExceptionIlluminate;

class HttpResponseException extends HttpResponseExceptionIlluminate
{
    
    public function __construct() { }

    protected function makeResponse($event)
    {
        $response = app('ash.event-exception-dispatcher')
            ->fire($event, [], $halt = true);

        if(!$response) throw $this;

        return $response;
    }
}
