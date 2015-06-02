<?php namespace Sebalbisu\Laravel\Input\Events;

class ValidationFail {

    protected $validator;

    public function __construct($validator)
    {
        $this->validator = $validator;
    }

    public function validator()
    {
        return $this->validator;
    }
}
