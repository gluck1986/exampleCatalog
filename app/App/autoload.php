<?php

use App\Common\App;
use App\Common\Factories\ConfigFactory;

$basePath = dirname(__DIR__);

/** @psalm-suppress UnresolvableInclude */
require_once $basePath . '/vendor/autoload.php';

$config = ConfigFactory::make($basePath);
$request = Laminas\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);
$app = new App($config, $request);

return $app;
