<?php

namespace App\Service\CatalogFilter\Dto;

use App\Entity\Product;

class CatalogWithFilterDto
{
    /**
     * @param list<Product> $products
     */
    public function __construct(
        public readonly array $products,
        public readonly FilterResultDto $filter,
        public readonly SummaryDto $summary,
    ) {
    }
}
