<?php

namespace Routes;

use App\Action\TestAction;
use App\Common\App;
use App\Http\Middleware\BodyParamsMiddleware;
use Laminas\Diactoros\ResponseFactory;
use League\Route\RouteGroup;
use League\Route\Router;
use League\Route\Strategy\ApplicationStrategy;
use League\Route\Strategy\JsonStrategy;

function init(Router $router, App $app)
{
    $router
        ->setStrategy(new ApplicationStrategy())
        ->group('', function (RouteGroup $group) use ($app) {
            $group->middleware(new BodyParamsMiddleware());
            $group->post('/test', new TestAction($app->getSolrClient(), $app->getPdo()));
        })->setStrategy(new JsonStrategy(new ResponseFactory()));
}
