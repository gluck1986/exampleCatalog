<?php

namespace App\Http\Formatter;

use App\Service\CatalogFilter\Dto\CatalogWithFilterDto;
use App\Service\CatalogFilter\Mapper\ProductMapper;

class CatalogFormatter
{
    public function __construct(private readonly ProductMapper $productMapper)
    {
    }

    public function format(CatalogWithFilterDto $dto): array
    {
        return [
            'products' => array_map([$this->productMapper, 'toArray'], $dto->products),
            'filters' => $dto->filter->toArray(),
            'summary' => $dto->summary->toArray(),
        ];
    }
}