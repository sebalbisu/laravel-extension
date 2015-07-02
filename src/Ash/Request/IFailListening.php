<?php namespace Sebalbisu\Laravel\Ash\Request;

interface IFailListening {

    public function listenAshFail();

    public function noListenAshFail();

}
