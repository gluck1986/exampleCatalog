<?php
/** @var \App\Common\App $app */

use App\Service\CatalogFilter\CatalogFilterService;
use App\Service\CatalogFilter\Dto\CriteriaAttributeDto;
use App\Service\CatalogFilter\Dto\CriteriaDto;


$app = require_once __DIR__ . '/App/autoload.php';
/** @var CatalogFilterService $generator */
$catalogFilter = $app->getContainer()->get(CatalogFilterService::class);
$catalogFilter->getProductAndFilters(
    new CriteriaDto(
        group: 1,
        pageSize: 5,
        page: 1,
        attributes: [
         //   new CriteriaAttributeDto(1, ['lakin',]),
            //new CriteriaAttributeDto(2, ['1']),
        ],
    delivery: 1, /*costFrom: 0, costTo: 1000,*/)
);

