<?php

namespace JoeBengalen\Tables\Api\Action\Error;

use JoeBengalen\Tables\Api\ApiResponder;
use Slim\Http\Request;
use Slim\Http\Response;

class NotAllowedAction
{
    /**
     * @var ApiResponder
     */
    protected $responder;

    /**
     * Create ErrorAction.
     *
     * @param ApiResponder $responder
     */
    public function __construct(ApiResponder $responder)
    {
        $this->responder = $responder;
    }

    /**
     * Invoke ErrorAction
     *
     * @param Request   $request
     * @param Response  $response
     * @param string[]  $allowedMethods
     *
     * @return Response
     */
    public function __invoke(
        Request $request,
        Response $response,
        array $allowedMethods
    ) {
        if ($request->getMethod() === 'OPTIONS') {
            return $this->responder->options($response, $allowedMethods);
        }

        return $this->responder->notAllowed($response, $allowedMethods);
    }
}
