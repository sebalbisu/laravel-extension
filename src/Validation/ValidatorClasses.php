<?php namespace Sebalbisu\Laravel\Validation;

abstract class ValidatorClasses extends Validator
{

    public function __construct($data = [], $rules = [], $messages = [], $customAttributes = [])
    {
        $rules = array_merge($this->rules, $rules);

        $messages = array_merge($this->customMessages, $messages);

        parent::__construct($translator = null, $data, $rules, $messages, $customAttributes);

        //the same config as Factory:

        $factory = app('validator');

        $this->setContainer(app());
        $this->addExtensions($factory->getExtensions());
        $this->addReplacers($factory->getReplacers());
        $this->setFallbackMessages($factory->getFallbackMessages());
        $this->setPresenceVerifier($factory->getPresenceVerifier());
    }
}
