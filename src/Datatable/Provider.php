<?php namespace Sebalbisu\Laravel\Datatable;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Exception\HttpResponseException;

class Provider extends ServiceProvider {

    public function register()
    {
        $this->app->alias(__NAMESPACE__ . '\Query', 'datatable.query');

        $this->app->alias(__NAMESPACE__ . '\QueryAjax', 'datatable.query.ajax');

        $this->app->resolving(__NAMESPACE__ . '\QueryAjax',

            function($dt, $app) 
            { 
                if(app('request')->ajax() 
                && app('request')->has($dt->getId()))
                {
                    throw new HttpResponseException(response($dt->renderTableBody()));
                }
            }
        );
    }
}
