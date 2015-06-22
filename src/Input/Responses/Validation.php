<?php namespace Sebalbisu\Laravel\Input\Responses; 

class Validation extends Response {

    protected $validator;

    public function isValidateType() 
    { 
        return true; 
    }

    public function passed()
    {
        return !$this->failed();
    }

    public function failed()
    {
        return ($this->getValidator() AND $this->getValidator()->errors()->has());
    }

    public function getValidator()
    {
        if(isset($this->validator)) return $this->validator;

        if(!$this->result) return $this->validator = false;

        $validator = $this->result;

        if(is_string($message = $validator))
        {
            $validator = ['other' => $message];
        }

        if(is_array($messages = $validator))
        {
            $validator = app()->validator->make([], []);
            $validator->getMessageBag()->merge($messages);
        }

        return $this->validator = $validator;
    }


    public function __sleep()
    {
        return ['result'];
    }
}
