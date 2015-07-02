<?php namespace Sebalbisu\Laravel\Ash\Responses; 

abstract class Response {

    protected $result;

    public function __construct($result)
    {
        $this->result = $result;
    }

    public function isAuthorizeType() { return false; }

    public function isValidateType() { return false; }

    public function isResultType() { return false; }

}
