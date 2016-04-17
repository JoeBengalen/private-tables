<?php

namespace JoeBengalen\HttpAuthentication;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class BasicAuthenticationMiddleware
{
    /**
     * @var string
     */
    protected $realm;

    /**
     * @var callable
     */
    protected $validator;

    /**
     * Create AuthenticationMiddleware.
     *
     * @param callable $validator
     * @param string   $realm
     */
    public function __construct(callable $validator, $realm = __CLASS__)
    {
        $this->validator = $validator;
        $this->realm = $realm;
    }

    /**
     * Check if HTTP basic credentials are provided.
     *
     * @param Request $request
     *
     * @return bool
     */
    protected function credentialsProvided(Request $request)
    {
        $serverParams = $request->getServerParams();

        if (!isset($serverParams['PHP_AUTH_USER'])) {
            return false;
        }

        if (!isset($serverParams['PHP_AUTH_PW'])) {
            return false;
        }

        return true;
    }

    /**
     * Check if the credentials are valid.
     *
     * @param string $username
     * @param string $password
     *
     * @return mixed|null
     */
    protected function validateCredentials($username, $password)
    {
        return call_user_func($this->validator, $username, $password);
    }

    /**
     * Response with Unauthorized (401).
     *
     * @param Response $response
     *
     * @return Response
     */
    protected function respondUnauthorized(Response $response)
    {
        return $response
                ->withStatus(401)
                ->withHeader(
                    'WWW-Authenticate',
                    'Basic realm="' . $this->realm . '"'
                );
    }

    /**
     * Invoke AuthenticationMiddleware.
     *
     * @param Request  $request
     * @param Response $response
     * @param callable $next
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        if (!$this->credentialsProvided($request)) {
            return $this->respondUnauthorized($response);
        }

        $serverParams = $request->getServerParams();

        $username = $serverParams['PHP_AUTH_USER'];
        $password = $serverParams['PHP_AUTH_PW'];

        $authenticated = $this->validateCredentials($username, $password);

        if (is_null($authenticated)) {
            return $this->respondUnauthorized($response);
        }

        $newRequest = $request->withAttribute('authenticated', $authenticated);

        return $next($newRequest, $response);
    }
}
