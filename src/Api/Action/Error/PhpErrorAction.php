<?php

namespace JoeBengalen\Tables\Api\Action\Error;

use JoeBengalen\Tables\Api\ApiResponder;
use Slim\Http\Request;
use Slim\Http\Response;
use Throwable;

class PhpErrorAction
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
     * @param Throwable $error
     *
     * @return Response
     */
    public function __invoke(
        Request $request,
        Response $response,
        Throwable $error
    ) {
        return $this->responder->phpError($response, $error);
    }
}
