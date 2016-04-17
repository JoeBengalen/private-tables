<?php

namespace JoeBengalen\Tables\Api\Action;

use Slim\Http\Request;
use Slim\Http\Response;

class HelloAction
{
    /**
     * Invoke action.
     *
     * @param Request  $request
     * @param Response $response
     */
    public function __invoke(Request $request, Response $response)
    {
        $data['message'] = 'Hello! You have reached the api';

        return $response->withJson($data, null, JSON_PRETTY_PRINT);
    }
}
