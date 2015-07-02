<?php namespace Sebalbisu\Laravel\Ash\Responses; 

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
