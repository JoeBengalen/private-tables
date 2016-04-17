<?php

use Aura\Sql\ExtendedPdo;
use function DI\get;
use function DI\object;
use function DI\string;

return [
    'status' => 'test', // development | test
    'database.dsn' => string('sqlite:{root}/db/data/{status}.db'),
    'database.username' => null,
    'database.password' => null,

    ExtendedPdo::class => object(ExtendedPdo::class)->constructor(
        get('database.dsn'),
        get('database.username'),
        get('database.password')
    ),

    'root' => dirname(__DIR__),
];