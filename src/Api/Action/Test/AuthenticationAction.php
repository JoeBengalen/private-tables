<?php

namespace JoeBengalen\Tables\Api\Action\Test;

use Slim\Http\Request;
use Slim\Http\Response;

class AuthenticationAction
{
    /**
     * Invoke AuthenticationAction.
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response)
    {
        $authenticated = $request->getAttribute('authenticated');

        $data = "Authenticated as: {$authenticated}";

        return $response->write($data);
    }
}
