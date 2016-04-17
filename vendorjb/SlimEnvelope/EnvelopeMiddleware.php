<?php

namespace JoeBengalen\SlimEnvelope;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * EnvelopeMiddleware.
 *
 * Wraps the response in a json object which has the status code, response
 * headers and the actual content. The http response code will always be 200. To
 * know the actual result you have to use the result given in the json data.
 */
class EnvelopeMiddleware
{
    /**
     * @var string
     */
    protected $envelopeKey;

    /**
     * @var string[]
     */
    protected $headerBlacklist = [
        'Content-Type',
    ];

    /**
     * @var bool
     */
    protected $useEnvelope = false;

    /**
     * Create EnvelopeMiddleware.
     *
     * @param string $envelopeKey
     */
    public function __construct($envelopeKey = 'envelope')
    {
        $this->envelopeKey = $envelopeKey;
    }

    /**
     * Filter the blacklisted headers.
     *
     * @param array $headers
     *
     * @return array
     */
    protected function filterHeaders(array $headers)
    {
        return array_filter(
            $headers,
            function ($header) {
                return !in_array(
                    $header,
                    $this->headerBlacklist
                );
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Create the envelope.
     *
     * @param int   $status
     * @param array $headers
     * @param mixed $content
     *
     * @return array
     */
    protected function createEnvelope($status, array $headers, $content)
    {
        return [
            'status' => $status,
            'headers' => $headers,
            'content' => $content,
        ];
    }

    /**
     * Build Response with and envelope.
     *
     * @param Response $response
     *
     * @return Response
     */
    protected function buildEnvelopeResponse(Response $response)
    {
        $content = (string) $response->getBody();
        $contentType = $response->getHeaderLine('Content-Type');

        if (strpos($contentType, 'application/json') !== false) {
            $content = json_decode($content);
        }

        $envelope = $this->createEnvelope(
            $response->getStatusCode(),
            $this->filterHeaders($response->getHeaders()),
            $content
        );

        return (new Response())->withJson($envelope, 200, JSON_PRETTY_PRINT);
    }

    /**
     * Invoke EnvelopeMiddleware.
     *
     * @param Request  $request
     * @param Response $response;
     * @param callable $next
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $param = $request->getQueryParam($this->envelopeKey);

        if (!is_null($param)) {
            $this->useEnvelope = true;
        }

        /* @var $newResponse Response */
        $newResponse = $next($request, $response);

        if ($this->useEnvelope) {
            $newResponse = $this->buildEnvelopeResponse($newResponse);
        }

        return $newResponse;
    }
}
