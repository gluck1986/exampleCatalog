<?php

namespace Routes;

use App\Action\CatalogAction;
use App\Action\GetGroupsAction;
use App\Action\TestAction;
use App\Common\App;
use App\Common\Config\Config;
use App\Common\Factories\ValidatorMiddlewareFactory;
use App\Http\Middleware\BodyParamsMiddleware;
use App\Http\Middleware\ExceptionMiddleware;
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
            $group->middleware(new ExceptionMiddleware());
            $group->middleware(
                ValidatorMiddlewareFactory::make($app->getContainer()->get(Config::class))->factory()
            );
            $group->middleware(new BodyParamsMiddleware());

            $group->post('/catalog', CatalogAction::class);
            $group->get('/groups', GetGroupsAction::class);
        });

    $router->post('/test', TestAction::class)
        ->middleware(new BodyParamsMiddleware());
}
