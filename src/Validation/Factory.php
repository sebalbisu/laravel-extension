<?php namespace Sebalbisu\Laravel\Validation;

use Illuminate\Validation\Factory as FactoryIlluminate;

class Factory extends FactoryIlluminate
{
    public function getExtensions()
    {
        return $this->extensions;
    }

    public function getReplacers()
    {
        return $this->replacers;
    }

    public function getFallbackMessages()
    {
        return $this->fallbackMessages;
    }
}
