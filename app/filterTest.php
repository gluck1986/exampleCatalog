<?php
/** @var \App\Common\App $app */

use App\Service\CatalogFilter\CatalogFilter;
use App\Service\CatalogFilter\Dto\CriteriaAttributeDto;
use App\Service\CatalogFilter\Dto\CriteriaDto;


$app = require_once __DIR__ . '/App/autoload.php';
/** @var CatalogFilter $generator */
$catalogFilter = $app->getContainer()->get(CatalogFilter::class);
$catalogFilter->getProductAndFilters(
    new CriteriaDto(
        group: 1,
        pageSize: 1,
        attributes: [
            new CriteriaAttributeDto(1, ['and', 'plc']),
        ],
    /*delivery: 1, costFrom: 0, costTo: 1000,*/)
);

