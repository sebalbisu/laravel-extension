<?php namespace Sebalbisu\Laravel\Input\Responses; 

class Result extends Response {

    public function isResultType()
    {
        return true;
    }

    public function getResult()
    {
        return $this->result;
    }

}
