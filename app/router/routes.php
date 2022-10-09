<?php

namespace Routes;

use App\Action\TestAction;
use App\Common\App;
use App\Common\Config\Config;
use App\Common\Factories\ValidatorMiddlewareFactory;
use App\Http\Middleware\BodyParamsMiddleware;
use League\Route\RouteGroup;
use League\Route\Router;
use League\Route\Strategy\ApplicationStrategy;

function init(Router $router, App $app)
{
    $appStrategy = new ApplicationStrategy();
    $appStrategy->setContainer($app->getContainer());
    $router
        ->setStrategy($appStrategy)
        ->group('', function (RouteGroup $group) use ($app) {
            $group->middleware(new BodyParamsMiddleware());
            $group->middleware(
                ValidatorMiddlewareFactory::make($app->getContainer()->get(Config::class))->factory()
            );

            $group->post('/catalog', TestAction::class)
                ->middleware(new BodyParamsMiddleware());
            ;
        });

    $router->post('/test', TestAction::class)
        ->middleware(new BodyParamsMiddleware());
}
