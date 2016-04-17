<?php

namespace JoeBengalen\Tables\Api\Action\Test;

use Slim\Http\Request;
use Slim\Http\Response;

class CompressAction
{
    /**
     * Invoke CompressAction.
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response)
    {
        $data = '
            <h1>Test page</h1><br/>
            <h1>Test page</h1><br/>
            <h1>Test page</h1><br/>
            <h1>Test page</h1><br/>
            <h1>Test page</h1><br/>
            <h1>Test page</h1><br/>
            <h1>Test page</h1><br/>
            <h1>Test page</h1><br/>
            <h1>Test page</h1><br/>
            <h1>Test page</h1><br/>
            <h1>Test page</h1><br/>
            <h1>Test page</h1><br/>
            ';

        return $response->write($data);
    }
}
