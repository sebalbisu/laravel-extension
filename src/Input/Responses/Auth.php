<?php namespace Sebalbisu\Laravel\Input\Responses; 

class Auth extends Response {

    public function isAuthorizeType()
    {
        return true;
    }

    public function isAllowed()
    {
        return $this->result === 200;
    }

    public function isNotFound()
    {
        return $this->result === 404;
    }

    public function isAccessDenied()
    {
        return $this->result === 403;
    }
}
