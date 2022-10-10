<?php

namespace App\Service\CatalogFilter\Dto;

class FilterAttributeDto
{
    /**
     * @param list<string> $values
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly array $values,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'values' => $this->values
        ];
    }
}