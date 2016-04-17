<?php

namespace JoeBengalen\Tables\Api\Action\Test;

use Slim\Http\Request;
use Slim\Http\Response;

class EnvelopeAction
{
    /**
     * Invoke EnvelopeAction.
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response)
    {
        $data = [
            'id' => 1,
            'name' => 'test1',
        ];

        return $response
                ->withJson($data, 404, JSON_PRETTY_PRINT)
                ->withHeader('X-CustomHeader', 'dummy');
    }
}
