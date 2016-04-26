<?php

namespace JoeBengalen\Tables\Api\Action;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ErrorAction
{
    /**
     * Invoke action.
     *
     * @param Request  $request
     * @param Response $response
     */
    public function __invoke(Request $request, Response $response)
    {
        throw new Exception('This is an example error message');
    }
}
