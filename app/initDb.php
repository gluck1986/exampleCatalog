<?php

/** @var \App\Common\App $app */

use App\Service\DataGenerator;

$app = require_once __DIR__ . '/App/autoload.php';
/** @var DataGenerator $generator */
$generator = $app->getContainer()->get(DataGenerator::class);


$generator->generate();
//print_r("hello world");
