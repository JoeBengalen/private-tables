<?php

namespace JoeBengalen\HttpRest;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RestParamsMiddleware
{
    /**
     * @var RestParams
     */
    protected $restParams;

    /**
     * @var string
     */
    protected $fieldsKey;

    /**
     * @var string
     */
    protected $orderKey;

    /**
     * @var string|null
     */
    protected $filterKey;

    /**
     * RestParamsMiddleware.
     *
     * @param RestParams  $restParams
     * @param string      $fieldsKey
     * @param string      $orderKey
     * @param string|null $filterKey
     */
    public function __construct(
        RestParams $restParams,
        $fieldsKey = 'fields',
        $orderKey = 'sort',
        $filterKey = null
    ) {
        $this->restParams = $restParams;
        $this->fieldsKey = $fieldsKey;
        $this->orderKey = $orderKey;
        $this->filterKey = $filterKey;
    }

    /**
     * Extract order.
     *
     * @param array $params
     */
    protected function extractOrder(&$params)
    {
        if (isset($params[$this->orderKey]) && is_string($params[$this->orderKey])) {
            $sort = explode(',', $params[$this->orderKey]);

            foreach ($sort as $field) {
                $direction = 'asc';
                if ($field[0] === '-') {
                    $field = ltrim($field, '-');
                    $direction = 'desc';
                }

                $this->restParams->addOrder($field, $direction);
            }

            unset($params[$this->orderKey]);
        }
    }

    /**
     * Extract fields.
     *
     * @param array $params
     */
    protected function extractFields(&$params)
    {
        if (isset($params[$this->fieldsKey]) && is_string($params[$this->fieldsKey])) {
            $fields = explode(',', $params[$this->fieldsKey]);

            foreach ($fields as $field) {
                $this->restParams->addField($field);
            }

            unset($params[$this->fieldsKey]);
        }
    }

    /**
     * Extract filters.
     *
     * @param array $params
     */
    public function extractFilters(&$params)
    {
        if (is_null($this->filterKey)) {
            foreach ($params as $field => $filter) {
                $this->restParams->addFilter($field, $filter);
            }
        } else {
            if (isset($params[$this->filterKey]) && is_string($params[$this->filterKey])) {
                $filters = explode(',', $params[$this->filterKey]);

                foreach ($filters as $filter) {
                    list($field, $filter) = explode(':', $filter);
                    $this->restParams->addFilter($field, $filter);
                }

                unset($params[$this->filterKey]);
            }
        }
    }

    /**
     * Invoke RestParamsMiddleware.
     *
     * @param Request  $request
     * @param Response $response
     * @param callable $next
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $params = $request->getQueryParams();

        $this->extractOrder($params);
        $this->extractFields($params);
        $this->extractFilters($params);

        $newRequest = $request->withAttribute('restParams', $this->restParams);

        return $next($newRequest, $response);
    }
}
