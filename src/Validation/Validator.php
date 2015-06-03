<?php namespace Sebalbisu\Laravel\Validation;

use Illuminate\Validation\Validator as IlluminateValidator;
use Illuminate\Support\MessageBag;

class Validator extends IlluminateValidator
{

    public function __construct($translator = null, array $data = [], array $rules = [], array $messages = [], array $customAttributes = [])
    {
        $translator = $translator ?: app('translator');
 
        parent::__construct($translator, $data, $rules, $messages, $customAttributes);
    }

    public function passes()
    {
        $this->messages = new MessageBag;

        foreach ($this->rules as $attribute => $rules)
        {
            foreach ($rules as $rule)
            {
                // added this modification
                if(isset($this->failedRules[$attribute])) break;

                $this->validate($attribute, $rule);
            }
        }

        foreach ($this->after as $after) { call_user_func($after); }

        return count($this->messages->all()) === 0;
    }

    public function getData($attr = null)
    {
        $data = parent::getData();

        if($attr == null) return $data;

        return isset($data[$attr]) ? $data[$attr] : null;
    }

    /**
     * $rules = [
     *      'ruleA' => 'required|{varA}',
     *      'ruleA' => 'required'
     *  ];
     *  
     *  $this->parseVarsInRules([
     *      'varA' => 'skip',       //convert {varA} in skip
     *  ]);
     *
     *  skip    -> return true in validation
     *  invalid -> return false in validation
     *  array('item1', 'item2', ...) -> parse as 'item1,item2,...'
     *
     *  values:
     *  true        => skip
     *  null        => skip
     *  false       => invalid
     *  []          => invalid
     *  ['ff','ww'] => 'ff,ww'
     *
     */
    public function parseVarsInRules($vars)
    {
        foreach($vars as $key => $value)
        {
            $search = '{'.$key.'}';

            if(is_array($value) && empty($value)) $value = false;
            if($value === '') $value = false;

            $replace = is_array($value) ? implode(',', $value) : $value;

            foreach($this->rules as $attr => $rules)
            {
                for($i = 0; $i < count($rules); $i++)
                {
                    if(strpos($rules[$i], $search))
                    {
                        if($replace === null || $replace === true)
                        {
                            $this->rules[$attr][$i] = 'skip';
                        }
                        elseif ($replace === false)
                        {
                            $this->rules[$attr][$i] = 'invalid';
                        }
                        else
                        {
                            $this->rules[$attr][$i] = 
                                str_replace($search, $replace, $rules[$i]);
                        }
                    }
                }
            }
        }

        return $this;
    }

    public function allSometimes()
    {
        foreach($this->rules as $attr => $rules)
        {
            $this->makeSometimes($attr);
        }

        return $this;
    }

    public function makeSometimes($attr)
    {
        if(!in_array('sometimes', $this->rules[$attr]))
            $this->rules[$attr][] = 'sometimes';

        return $this;
    }

    public function removeRule($attr, $rule)
    {
        if(is_array($attr))
        {
            foreach($input as $attr => $rules)
            {
                foreach($rules as $rule)
                {
                    $this->removeRule($attr, $rule);
                }
            }
        } 
        else
        {
            $rules =& $this->rules[$attr];
            $rules = array_diff($rules, [$rule]);
        }

        return $this;
    }

    protected function getMessage($attr, $rule)
    {
        $msg = parent::getMessage($attr, $rule);

        $lowerRule = snake_case($rule);

        if($msg == "validation.$lowerRule")
            $msg = parent::getMessage($attr, 'in');

        return $msg;
    }
}
