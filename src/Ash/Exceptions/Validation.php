<?php namespace Sebalbisu\Laravel\Ash\Exceptions;

use Sebalbisu\Laravel\Ash\Events;

class Validation extends HttpResponseException
{
    protected $message = 'Invalid input';

    protected $validator;

    public function __construct($validatorOrMsg)
    {
        $validator = $validatorOrMsg;

        if(is_string($message = $validator))
        {
            $validator = ['other' => $message];
        }

        if(is_array($messages = $validator))
        {
            $validator = app()->validator->make([], []);
            $validator->getMessageBag()->merge($messages);
        }

        $this->message = $validator->getMessageBag()->first();

        $this->validator = $validator;
    }

    public function validator()
    {
        return $this->validator;
    }

    public function errors()
    {
        return $this->validator->errors();
    }

    public function getResponse()
    {
        $event = new Events\ValidationFail($this->validator()); 

        return $this->makeResponse($event);
    }
}
