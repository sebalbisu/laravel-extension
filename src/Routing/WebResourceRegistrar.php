<?php namespace Sebalbisu\Laravel\Routing;

use Illuminate\Routing\ResourceRegistrar;

/**
 *    Route::group(['prefix' => $resource = 'resource'], function(){
 *        Route::get(''                 , "$resource@index");
 *        Route::get('create'           , "$resource@create");
 *        Route::post(''                , "$resource@store");
 *        Route::get('{id}'             , "$resource@show");
 *        Route::get('{id}/edit'        , "$resource@edit");
 *        Route::post('{id}/update'     , "$resource@update"); 
 *            // route name resource/{id}/update::post -> method post
 *            // route name resource/{id}/update::put -> method put
 *            // route name resource/{id}/update -> method = put
 *        Route::match(['put', 'patch'] , '{id}', "$resource@update");
 *        Route::post('{id}/destroy'    , "$resource@destroy");
 *        Route::delete('{id}'          , "$resource@destroy");
 *            // route name resource/{id}/destroy::post -> method post
 *            // route name resource/{id}/destroy::put -> method put
 *            // route name resource/{id}/destroy -> method = put
 *    });
 *    
 *    Accept routes like /group/resource/{id} => route name 'group.resource'
 *
 *    $options accept 'only', 'except' to restrict methods
 */
class WebResourceRegistrar extends ResourceRegistrar
{

    protected function addResourceUpdate($name, $base, $controller, $options)
    {
        //post
        $uri = $this->getResourceUri($name).'/{'.$base.'}/update';

        $action = $this->getResourceAction($name, $controller, 'update', $options);
        $action['as'] .= '::post';

        $this->router->post($uri, $action);

        //put
        $uri = $this->getResourceUri($name).'/{'.$base.'}';

        $action = $this->getResourceAction($name, $controller, 'update', $options);
        $action['as'] .= '::put';

        $this->router->put($uri, $action);

        return parent::addResourceUpdate($name, $base, $controller, $options);
    }

    protected function addResourceDestroy($name, $base, $controller, $options)
    {
        //post
        $uri = $this->getResourceUri($name).'/{'.$base.'}/destroy';

        $action = $this->getResourceAction($name, $controller, 'destroy', $options);
        $action['as'] .= '::post';

        $this->router->post($uri, $action);

        //put
        $uri = $this->getResourceUri($name).'/{'.$base.'}';

        $action = $this->getResourceAction($name, $controller, 'destroy', $options);
        $action['as'] .= '::put';

        $this->router->put($uri, $action);

        return parent::addResourceDestroy($name, $base, $controller, $options);
    }

    protected function prefixedResource($name, $controller, array $options)
    {
        list($name, $prefix) = $this->getResourcePrefix($name);

        $router = $this->router;

        $callback = function($me) use ($name, $controller, $options, $router)
        {
            //modification to call this class
            (new static($router))->register($name, $controller, $options);
        };

        return $router->group(compact('prefix'), $callback);
    }

}
