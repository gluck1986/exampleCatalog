<?php


namespace App\Common\Factories;

use App\Common\App;
use League\Route\Router;
use function Routes\init;

class RouterFactory
{
    public static function buildRouter(App $app): Router
    {
        $router = new Router();
        init($router, $app);

        return $router;
    }
}
