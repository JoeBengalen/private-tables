<?php

namespace JoeBengalen\SlimJsonp;

use Slim\Http\Request;
use Slim\Http\Response;

// http://localhost:8888/api.php/api/v1/test/envelope?callback=test&envelope
// http://localhost:8888/api.php/api/v1/test/envelope?callback=test

/**
 * JsonpMiddleware.
 *
 * If a jsonp callback key is found in the query parameters the response will
 * be wrapped in javascript function call to the given callback.
 */
class JsonpMiddleware
{
    /**
     * @var string
     */
    protected $callbackKey;

    /**
     * @var string|null
     */
    protected $callbackName;

    /**
     * Create JsonpMiddleware.
     *
     * @param string $callbackKey
     */
    public function __construct($callbackKey = 'callback')
    {
        $this->callbackKey = $callbackKey;
    }

    /**
     * Build Response with the callback.
     *
     * @param Response $response
     *
     * @return Response
     */
    protected function buildJsonpResponse(Response $response)
    {
        $content = (string) $response->getBody();
        $contentType = $response->getHeaderLine('Content-Type');

        if (strpos($contentType, 'application/json') === false) {
            $content = '"' . $content . '"';
        }

        $callback = "{$this->callbackName}({$content}));";

        $newResponse = new Response(200);
        $newResponse->getBody()->write($callback);

        return $newResponse
                ->withHeader('Content-Type', 'application/javascript');
    }

    /**
     * Invoke EnvelopeMiddleware.
     *
     * @param Request  $request
     * @param Response $response
     * @param callable $next
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $param = $request->getQueryParam($this->callbackKey);

        if (is_string($param) && !empty($param)) {
            $this->callbackName = $param;
        }

        /* @var $newResponse Response */
        $newResponse = $next($request, $response);

        if ($this->callbackName) {
            $newResponse = $this->buildJsonpResponse($newResponse);
        }

        return $newResponse;
    }
}
