<?php namespace Sebalbisu\Laravel\Datatable;

use Illuminate\Support\ServiceProvider;

class Provider extends ServiceProvider {

    public function register()
    {
        $this->app->alias(__NAMESPACE__ . '\Query', 'datatable.query');

        $this->app->alias(__NAMESPACE__ . '\QueryAjax', 'datatable.query.ajax');
    }
}
