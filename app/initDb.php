<?php

/** @var \App\Common\App $app */
$app = require_once __DIR__ . '/App/autoload.php';
$solr = $app->getSolrClient();

print_r("hello world");

