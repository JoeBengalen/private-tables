<?php

namespace JoeBengalen\Tables\Api\Action\Test;

use JoeBengalen\HttpRest\RestParams;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// http://localhost:8888/api.php/api/v1/test/restparams?sort=-name&fields=name,id&name=test

class RestParamsAction
{
    /**
     * Invoke RestParamsAction.
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response)
    {
        /* @var $restParams RestParams */
        $restParams = $request->getAttribute('restParams');

        $data = [
            'fields' => $restParams->getFields(),
            'filters' => $restParams->getFilters(),
            'order' => $restParams->getOrder(),
        ];

        return $response->withJson($data, 200, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
