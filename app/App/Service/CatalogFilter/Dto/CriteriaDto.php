<?php

namespace App\Service\CatalogFilter\Dto;

class CriteriaDto
{
    /**
     * @param list<CriteriaAttributeDto> $attributes
     */
    public function __construct(
        public readonly int $group,
        public readonly ?string $name = null,
        public readonly ?float $costFrom = null,
        public readonly ?float $costTo = null,
        public readonly ?int $delivery = null,
        public readonly int $page = 1,
        public readonly int $pageSize = 10,
        public readonly array $attributes = [],
    ) {
    }
}
