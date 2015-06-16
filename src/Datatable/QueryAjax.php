<?php namespace Sebalbisu\Laravel\Datatable;

class QueryAjax {

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

    public function renderTable(array $options = [])
    {
        $variables = array_merge($options, ['head' => true, 'body' => false, 'renderJs' => true ]);

        return \view($this->viewResource, $variables)->render();
    }

    public function renderTableBody()
    {
        $pag = self::getPagination($this->pagination);
        $pag['withTotal'] = true;

        $args = array_merge($this->repoArgs, ['pag' => $pag]);

        $result = app()->call($this->repoCb, $args);

        $content = $this->getTableBody($result['rows']);
        $contentJson = $this->parseTableBodyToJson($content);

        $response = $this->addContentHeaders($contentJson, $result['total']);

        header('Content-Type', 'application/json');

        return $response;
    }

    protected function addContentHeaders($content, $total)
    {
        return '{
            "recordsTotal": ' . $total . ',
            "recordsFiltered": ' . $total . ',
            "data": [ ' . $content . ' ]
        }';
    }

    public function getTableBody($rows)
    {
        $This = $this;

        return \view($this->viewResource, 
            [
                'head' => false, 
                'body' => true, 
                'rows' => $rows
            ])
            ->render();
    }

    public function parseTableBodyToJson($content)
    {
        $replacers = 
        [
            '<tbody>'  => '',
            '</tbody>' => '',
            '<tr>'     => '[',
            '</tr>'    => '],',
            '"'        => '\"',
        ];

        $content = str_replace(
            array_keys($replacers), array_values($replacers), $content
        );
        $content = preg_replace('@<td>[\s\n]*(.*)[\s\n]*</td>@', '"\1",', $content);
        $content = preg_replace('/,\s*\n+\s*]/', "\n]", $content);
        $content = rtrim($content, " \t\n\r,");

        return $content;
    }
}
