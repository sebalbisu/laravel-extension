<?php namespace Sebalbisu\Laravel\Datatable;

class Query {

    protected $repoCb;

    protected $repoArgs;

    protected $viewResource;

    protected $pagination;

    public function __construct($view, $repoCb, array $repoArgs = [], $pag = [])
    {
        $this->viewResource = $view;

        $this->repoCb = $repoCb;

        $this->repoArgs = $repoArgs;

        $this->pagination = $pag;
    }

    static public function getPagination(array $pag = [])
    {
        $col = \Request::input('order.0.column');

        $inputPag = [
            'limit'    => $limit = \Request::input('length') ?: 10, 
            'offset'   => $offset = \Request::input('start') ?: 0, 
            'page'     => floor($offset / $limit) + 1,
            'order'    => \Request::input("columns.$col.name"),
            'dir'      => \Request::input('order.0.dir', 'asc'),
        ];

        return array_merge($inputPag, $pag);
    }

    public function renderTable($options = [])
    {
        $pag = self::getPagination($this->pagination);

        $args = array_merge($this->repoArgs, ['pag' => $pag]);

        $result = app()->call($this->repoCb, $args);

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
