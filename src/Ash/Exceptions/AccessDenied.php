<?php namespace Sebalbisu\Laravel\Ash\Exceptions;

class AccessDenied extends Ash
{
    protected $message = 'Access denied';

    protected $code = 403;
}
