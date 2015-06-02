<?php namespace Sebalbisu\Laravel\Filter;

use Illuminate\Support\ServiceProvider;

class Provider extends ServiceProvider {

    public function register()
    {
        $this->app->singleton('filter.plugin_manager', function($app)
        {
            return $app->build('Zend\Filter\FilterPluginManager');
        });
    }
}
