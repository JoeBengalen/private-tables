<?php

use Aura\Filter\FilterFactory;
use DI\Container;
use DI\Factory\RequestedEntry;
use JoeBengalen\HttpAuthentication\BasicAuthenticationMiddleware;
use JoeBengalen\Tables\Api\Action\Error\ErrorAction;
use JoeBengalen\Tables\Api\Action\Error\NotAllowedAction;
use JoeBengalen\Tables\Api\Action\Error\NotFoundAction;
use JoeBengalen\Tables\Api\Action\Error\PhpErrorAction;
use JoeBengalen\Tables\Api\Filter\FieldFilter;
use JoeBengalen\Tables\Api\Filter\TableFilter;
use Slim\Router;
use function DI\factory;
use function DI\get;
use function DI\object;

$filterFactory = factory(
    function (Container $container, RequestedEntry $entry) {
        $factory = $container->get(FilterFactory::class);
        return $factory->newSubjectFilter($entry->getName());
    }
);

return [
    'settings.displayErrorDetails' => false,

    'authentication.username' => 'admin',
    'authentication.password' => 'Admin!23',

    'errorHandler' =>get(ErrorAction::class),
    'phpErrorHandler' =>get(PhpErrorAction::class),
    'notFoundHandler' =>get(NotFoundAction::class),
    'notAllowedHandler' =>get(NotAllowedAction::class),

    TableFilter::class => $filterFactory,
    FieldFilter::class => $filterFactory,

    Router::class => get('router'),

    BasicAuthenticationMiddleware::class => object()
        ->constructor(get('authenticater'), 'custom_realm'),

    'authenticater' => factory(
        function (Container $container) {
            $user = [
                'username' => $container->get('authentication.username'),
                'password' => $container->get('authentication.password'),
            ];

            return function ($username, $password) use ($user) {
                if ($username === $user['username']
                    && $password === $user['password']
                ) {
                    return $username;
                }

            };
        }
    ),
];
