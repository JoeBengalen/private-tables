<?php

namespace JoeBengalen\Tables\Api\Action;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Route;
use Slim\Router;

class ListAction
{
    /**
     * @var Router
     */
    protected $router;

    /**
     * Create ListAction.
     *
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Invoke action.
     *
     * @param Request  $request
     * @param Response $response
     * @param Router   $router
     */
    public function __invoke(Request $request, Response $response, Router $router)
    {
        $routes = $this->router->getRoutes();
        $transformer = function (Route $route) {
            return [
                'action' => $route->getName(),
                'method' => implode(', ', $route->getMethods()),
                'pattern' => $route->getPattern(),
            ];
        };

        $actions = array_values(
            array_map($transformer, $routes)
        );

        return $response->withJson($actions, 200, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
