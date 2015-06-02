<?php namespace Sebalbisu\Laravel\Validation\Extensions;

class Invalid
{
    static protected $message = "the field :attribute is invalid";

    static public function register($rule, $factory)
    {
        $factory->extend($rule, get_called_class().'@validator', self::$message);
        //$factory->replacer($rule, get_called_class().'@replacer');
    }

    public function validator()
    {
        return false;
    }

    public function replacer()
    {
        //
    }
}
