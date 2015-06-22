<?php namespace Sebalbisu\Laravel\Input\Exceptions;

class AccessDenied extends Input
{
    protected $message = 'Access denied';

    protected $code = 403;
}
