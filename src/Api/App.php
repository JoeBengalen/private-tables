<?php

namespace JoeBengalen\Tables\Api;

use DI\Bridge\Slim\App as PhpDiApp;
use DI\ContainerBuilder;

class App extends PhpDiApp
{
    protected function configureContainer(ContainerBuilder $builder)
    {
        $builder->addDefinitions('../config/base.php');
        $builder->addDefinitions('../config/api.php');
    }
}
