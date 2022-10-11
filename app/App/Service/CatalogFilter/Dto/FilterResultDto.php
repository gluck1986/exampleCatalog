<?php

namespace App\Service\CatalogFilter\Dto;

class FilterResultDto
{
    /**
     * @param list<FilterAttributeDto> $attributes
     */
    public function __construct(
        public readonly float $costFrom,
        public readonly float $costTo,
        public readonly array $delivery,
        public readonly array $attributes,
    ) {
    }

    public function toArray(): array
    {
        return [
            'costFrom' => $this->costFrom,
            'costTo' => $this->costTo,
            'delivery' => $this->delivery,
            'attr' => array_map(fn(FilterAttributeDto $attr) => $attr->toArray(), $this->attributes)
        ];
    }
}
