<?php namespace Sebalbisu\Laravel\Datatable;

class Query {

    protected $data;

    protected $viewResource;

    protected $pagination;

    public function __construct($data, $view)
    {
        $this->viewResource = $view;

        $this->data = $data;
    }

    public function renderTable($options = [])
    {
        $result = is_callable($this->data) ?
            app()->call($this->data) :
            $this->data;

        $variables = array_merge($options, 
        [
            'head' => true, 
            'body' => true, 
            'renderJs' => true,
            'rows' => $result,
        ]);

        return \view($this->viewResource, $variables)->render();
    }
}
