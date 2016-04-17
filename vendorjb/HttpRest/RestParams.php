<?php

namespace JoeBengalen\HttpRest;

class RestParams
{
    /**
     * @var string[]
     */
    protected $fields = [];

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var array
     */
    protected $order = [];

    /**
     * Add field.
     *
     * @param string $field
     *
     * @return self
     */
    public function addField($field)
    {
        $this->fields[] = $field;

        return $this;
    }

    /**
     * Add filter.
     *
     * @param string $field
     * @param string $filter
     *
     * @return self
     */
    public function addFilter($field, $filter)
    {
        $this->filters[$field] = $filter;

        return $this;
    }

    /**
     * Add order.
     *
     * @param string $field
     * @param string $direction
     *
     * @return self
     */
    public function addOrder($field, $direction)
    {
        $this->order[$field] = $direction;

        return $this;   
    }

    /**
     * Get fields.
     *
     * @return string[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Get filters.
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Get order.
     *
     * @return array
     */
    public function getOrder()
    {
        return $this->order;
    }
}
