<?php

namespace JoeBengalen\Tables\Api\Action\Error;

use JoeBengalen\Tables\Api\ApiResponder;
use Slim\Http\Request;
use Slim\Http\Response;

class NotFoundAction
{
    /**
     * @var ApiResponder
     */
    protected $responder;

    /**
     * Create NotFoundAction.
     *
     * @param ApiResponder $responder
     */
    public function __construct(ApiResponder $responder)
    {
        $this->responder = $responder;
    }

    /**
     * Invoke NotFoundAction
     *
     * @param  Request  $request
     * @param  Response $response
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response)
    {
        return $this->responder->notFound($response);
    }
}
