<?php namespace Sebalbisu\Laravel\Ash\Events;

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
