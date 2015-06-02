<?php namespace Sebalbisu\Laravel\Input;

use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Sebalbisu\Laravel\Input\Exceptions\NotFound as ExceptionNotFound;
use Sebalbisu\Laravel\Input\Exceptions\AccessDenied as ExceptionAccessDenied;
use Sebalbisu\Laravel\Input\Exceptions\Validation as ExceptionValidation;
use Sebalbisu\Laravel\Validation\Factory as ValidationFactory;
use Sebalbisu\Laravel\Filter\Filter;

trait InputTrait {

    static protected $eventDispatcher;

    protected $data;

    protected $input;

    protected $inputAfter;

    protected $validator;

    protected $messages = [];

    protected $skip = [
        'init'       => false,
        'input'      => false,
        'auth'       => false,
        'filter'     => false,
        'validation' => false,
        'escape'     => false,
        'inputAfter' => false,
        'handle'     => false,
    ];

    public function __construct($data = [])
    {
        $this->setData($data);
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

    public function run()
    {
        if(!$this->skip['init']
        && method_exists($this, 'init'))
        {
            call_user_func_array([$this, 'init'], func_get_args());
        }

        if(!$this->skip['input']
        && method_exists($this, 'input'))
        {
            $this->input = app()->call([$this, 'input']) ? [];

            foreach($this->input as $input)
            {
                $this->data = $input->setData($this->data)->run();
            }
        }

        if(!$this->skip['auth']
        && method_exists($this, 'authorize')
        && !app()->call([$this, 'authorize']))
        {
            $this->deny();
        }

        if(!$this->skip['sanitize']
        && method_exists($this, 'sanitize'))
        {
            $sanitizers = app()->call([$this, 'sanitize']);

            if(is_null($sanitizers)) $sanitizers = [];

            if(!$this->skip['filter']
            && isset($sanitizers['filter'])
            ) 
            {
                $this->_filter($sanitizers['filter']);
            }

            if(!$this->skip['validation']
            && isset($sanitizers['validation']))
            {
                $this->_validate($sanitizers['validation']);
            }

            if(!$this->skip['escape']
            && isset($sanitizers['escape'])) 
            {
                $this->_filter($sanitizers['escape']);
            }
        }

        if(!$this->skip['inputAfter']
        && method_exists($this, 'inputAfter'))
        {
            $this->inputAfter = app()->call([$this, 'inputAfter']) ? [];

            foreach($this->inputAfter as $input)
            {
                $this->data = $input->setData($this->data)->run();
            }
        }

        return (!this->skip['handle'] && method_exists($this, 'handle')) ? 
            app()->call([$this, 'handle']) :
            $this;
    }

    public function skip($key, $value)
    {
        if(is_array($key))
        {
            while($params = array_shift($key))
            {
                return $this->skip($params[0], $params[1]);
            }
        }

        $this->skip[$key] = (bool)$value;

        return $this;
    }

    protected function notFound()
    {
        throw new ExceptionNotFound();
    }

    protected function deny()
    {
        throw new ExceptionAccessDenied();
    }

    /**
     * $validator = 'a message'
     * $validator = ['message1', 'message2', ...]
     * $validator = ValidatorClass
     */
    protected function fail($validatorOrMsg)
    {
        throw new ExceptionValidation($validatorOrMsg);
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

        if($this->validator->fails())
        {
            $this->fail($this->validator);
        }
    }

    public function getValidator()
    {
        return $this->validator;
    }

    public function getInputs($i = null)
    {
        return ($i !== null) $this->input[$i] : $this->input;
    }

    public function getInputsAfter($i = null)
    {
        return ($i !== null) $this->inputAfter[$i] : $this->inputAfter;
    }

    static public function eventDispatcher(EventDispatcher $dispatcher = null)
    {
        return app('input.event-dispatcher');
    }

    static public function build()
    {
        $args = (func_num_args() == 1 && is_array(func_get_arg(0))) ?
            func_get_arg(0) : func_get_args();

        return app()->build(get_called_class(), $args);
    }

    static public function perform()
    {
        return static::build(func_get_args())->run();
    }
}
