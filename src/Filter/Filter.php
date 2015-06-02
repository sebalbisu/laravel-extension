<?php namespace Sebalbisu\Laravel\Filter;

use Zend\Filter\FilterChain;
use Zend\Filter\Callback as FilterCallback;

class Filter {

    protected $filtersInput;

    protected $parsedFilters;

    public function __construct(array $filtersInput)
    {
        $this->filtersInput = $this->parseFilters($filtersInput);
    }

    protected function parsefilters($filtersInput)
    {
        $globalFilters = [];
        if(isset($filtersInput['*']))
        {
            $globalFilters = $filtersInput['*'];
            unset($filtersInput['*']);
        }

        foreach($filtersInput as $field => $filters)
        {
            $totalFilters = array_merge($filters, $globalFilters);
            $chain = new FilterChain();
            $chain->setPluginManager(app('filter.plugin_manager'));

            foreach($totalFilters as $filter)
            {
                $filter = is_string($filter) ? [$filter] : $filter;

                $method = 'filter' . ucfirst($filter[0]);
                if(method_exists($this, $method))
                {
                    $priority = isset($filter[2]) ? $filter[2] : null;
                    $filter = ['callback', 'callback' => [$this, $method]];

                    if($priority) $filter[2] = $priority;
                }

                call_user_func_array([$chain, 'attachByName'], $filter);
            }

            $fields = explode('|', $field);
            foreach($fields as $field)
            {
                if(isset($this->parsedFilters[$field]))
                {
                    $this->parsedFilters[$field]->merge($chain);
                } else {
                    $this->parsedFilters[$field] =  $chain;
                }
            }
        }
    }

    public function filter($data)
    {
        foreach($data as $field => $value)
        {
            if(isset($this->parsedFilters[$field]))
                $data[$field] = $this->parsedFilters[$field]->filter($value);
        }

        return $data;
    }
}
