<?php namespace Sebalbisu\Laravel\Input\Request;

interface IFailListening {

    public function listenInputFail();

    public function noListenInputFail();

}
