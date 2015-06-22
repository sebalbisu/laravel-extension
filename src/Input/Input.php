<?php namespace Sebalbisu\Laravel\Input;

use Sebalbisu\Laravel\Input\Exceptions;
use Sebalbisu\Laravel\Input\Responses;
use Sebalbisu\Laravel\Input\Responses\Response;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Sebalbisu\Laravel\Validation\Factory as ValidationFactory;
use Illuminate\Validation\Validator;
use Sebalbisu\Laravel\Filter\Filter;

abstract class Input {

    static protected $stepsList = ['authorize', 'sanitize', 'handle'];

    static protected $eventDispatcher;

    protected $data;

    protected $validator;

    protected $messages = [];

    protected $steps = [];

    protected $silent;


    public function __construct($data = [])
    {
        $this->setData($data);

        $this->resetState();
    }


    public function getData()
    {
        return $this->data;
    }


    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }


    public function silent($value = true)
    {
        $this->silent = $value;

        return $this;
    }


    public function only($step)
    {
        $this->steps = is_string($step) ? [$step] : $step;

        return $this;
    }


    public function skip($step)
    {
        $skip = is_string($step) ? [$step] : $step;

        $this->steps = array_diff(static::$stepsList, $skip);

        return $this;
    }


    protected function allow()
    {
        return new Responses\Auth(200);
    }


    protected function notFound()
    {
        return new Responses\Auth(404);
    }


    protected function deny()
    {
        return new Responses\Auth(403);
    }


    /**
     * $validator = 'a message'
     * $validator = ['message1', 'message2', ...]
     * $validator = ValidatorClass
     */

    protected function fail($validatorOrMsg = 'Failed validation')
    {
        return new Responses\Validation($validatorOrMsg);
    }


    protected function valid()
    {
        return new Responses\Validation(false);
    }


    protected function result($result)
    {
        return new Responses\Result($result);
    }


    public function getValidator()
    {
        return $this->validator;
    }


    public function run($step = null)
    {
        if($step) $this->only($step);

        while($step = array_shift($this->steps))
        {
            $response = null;

            if(method_exists($this, $step))
            {
                $caller = "_$step";

                $response = method_exists($this, $caller) ?
                    $this->$caller() : 
                    app()->call([$this, $step]);
            
                if($this->shouldEndRun($response)) break;
            }
        }

        return $this->respond($response);
    }


    protected function _authorize()
    {
        $response = app()->call([$this, "authorize"]);
        
        if(!$response instanceof Response) return $this->deny();

        return $response;
    }


    protected function _sanitize()
    {
        $sanitizers = app()->call([$this, "sanitize"]);

        if($sanitizers instanceof Response) return $sanitizers;


        if($sanitizers instanceof Validator)
        {
            $sanitizers = ['validation' => $sanitizers];
        }

        if(isset($sanitizers['filter'])) 
        {
            $this->_filter($sanitizers['filter']);
        }

        if(isset($sanitizers['validation']))
        {
            $response = $this->_validate($sanitizers['validation']);

            if($response->failed()) return $response;
        }

        if(isset($sanitizers['escape'])) 
        {
            $this->_filter($sanitizers['escape']);
        }

        return isset($response) ? $response : null;
    }


    protected function _filter($filters)
    {
        $filter = is_array($filters) ?
            (new Filter($filters)) : $filters;

        $this->data = $filter->filter($this->data);
    }


    protected function _validate($rulesOrValidator)
    {
        $this->validator = is_array($rulesOrValidator) ?
            app('validator')->make($this->data, $rulesOrValidator, $this->messages) :
            $rulesOrValidator;

        return $this->validator->fails() ? 
            $this->fail($this->validator) :
            $this->valid();
    }


    protected function shouldEndRun ($response)
    {
        if($response instanceof Response)
        {
            return ($response->isAuthorizeType() && !$response->isAllowed())
                || ($response->isValidateType() && $response->failed())
                || ($response->isResultType());
        }

        return false;
    }


    protected function respond($response)
    {
        $silent = $this->silent;

        $this->resetState();

        if($silent)
        {
            if(!$response instanceof Response) 
                $response = new Response\Result($response);
        } 
        else 
        {
            if($response instanceof Response)
            {
                if($response->isAuthorizeType() && $response->isNotFound())
                    throw new Exceptions\NotFound();

                if($response->isAuthorizeType() && $response->isAccessDenied())
                    throw new Exceptions\AccessDenied();

                if($response->isValidateType() && $response->failed())
                    throw new Exceptions\Validation($response->getValidator());
            }

            if($response === null) $response = $this;
        }

        return $response;
    }


    protected function resetState()
    {
        $this->steps = static::$stepsList;

        $this->silent = null;
    }


    static public function eventDispatcher()
    {
        return app('input.event-dispatcher');
    }


    protected function fire()
    {
        return call_user_func_array(
            [self::eventDispatcher(), 'fire'], func_get_args());
    }


    static public function build()
    {
        $args = (func_num_args() == 1 && is_array(func_get_arg(0))) ?
            func_get_arg(0) : func_get_args();

        return app()->build(get_called_class(), $args);
    }


    static public function dispatch()
    {
        return call_user_func_array(
            get_called_class() . '::build', func_get_args())->run();
    }


    static public function auth()
    {
        return call_user_func_array(
            get_called_class() . '::build', func_get_args())->run('authorize');
    }


    static public function dispatchHandle()
    {
        return call_user_func_array(
            get_called_class() . '::build', func_get_args())->run('handle');
    }
}
