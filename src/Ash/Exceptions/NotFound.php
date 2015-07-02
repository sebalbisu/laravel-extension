<?php namespace Sebalbisu\Laravel\Ash\Exceptions;

class NotFound extends Ash
{
    protected $message = 'Not Found';

    protected $code = 404;
}
