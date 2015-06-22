<?php namespace Sebalbisu\Laravel\Datatable;

class QueryAjax {

    protected $id;

    protected $data;

    protected $viewResource;

    protected $pagination;

    public function __construct($id, $data, $view, $pag = [])
    {
        $this->id = $id;

        $this->viewResource = $view;

        $this->data = $data;

        $this->pagination = $pag;
    }

    public function renderTable(array $options = [])
    {
        $variables = array_merge($options, [
            'head' => true,
            'body' => false,
            'renderJs' => true,
            'id' => $this->id,
        ]);

        return \view($this->viewResource, $variables)->render();
    }

    public function renderTableBody()
    {
        $pag = self::getPagination($this->pagination);
        $pag['withTotal'] = true;

        $result = is_callable($this->data) ?
            app()->call($this->data, [$pag]) :
            $this->data;

        $content = $this->getTableBody($result['rows']);

        $contentJson = $this->parseTableBodyToJson($content);

        $response = $this->addContentHeaders($contentJson, $result['total']);

        header('Content-Type', 'application/json');

        return $response;
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
                'rows' => $rows,
                'id'   => $this->id,
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
        //put td in ""
        $content = preg_replace('@\s*<td[^>]*>@', '"', $content);
        $content = preg_replace('@\s*</td>@', '",', $content);
        //last td "", to ""
        $content = preg_replace('/,\s*\n+\s*]/', "\n]", $content);
        //last row [], to []
        $content = rtrim($content, " \t\n\r,");
        //deletes all new lines in json
        $content = preg_replace("@\s+@", ' ', $content);

        return $content;
    }

    public function getId()
    {
        return $this->id;
    }
}
