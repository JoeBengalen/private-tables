<?php

namespace JoeBengalen\Tables\Api\Action\Error;

use Exception;
use JoeBengalen\Tables\Api\ApiResponder;
use Slim\Http\Request;
use Slim\Http\Response;

class ErrorAction
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
     * @param Exception $exception
     *
     * @return Response
     */
    public function __invoke(
        Request $request,
        Response $response,
        Exception $exception
    ) {
        return $this->responder->error($response, $exception);
    }
}
