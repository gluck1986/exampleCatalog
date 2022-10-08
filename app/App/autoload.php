<?php

use App\Common\App;
use App\Common\Factories\ConfigFactory;

$basePath = $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__);

require_once dirname(__DIR__) . "/vendor/autoload.php";

$config = ConfigFactory::build($basePath);
$request = Laminas\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);
$app = new App($config, $request);

return $app;
