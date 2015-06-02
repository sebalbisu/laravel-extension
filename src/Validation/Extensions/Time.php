<?php namespace Sebalbisu\Laravel\Validation\Extensions;

class Time
{
    static protected $message =  ':attribute is not a valid time (hh:mm:ss)';

    static public function register($rule, $factory)
    {
        $factory->extend($rule, get_called_class().'@validator', self::$message);
    }

    protected function validator($attr, $value, $params)
    {
        if ($value instanceof DateTime) return true;

        if (strtotime($value) === false) return false;

        return true;
    }

    public function replacer()
    {
        //
    }
}
