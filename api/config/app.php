<?php

declare(strict_types=1);

use DI\Container;
use Slim\App;
use Slim\Factory\AppFactory;

return static function (Container $container): App {
    $app = AppFactory::createFromContainer($container);
    (require __DIR__ . '/routes.php')($app);
    return $app;
};