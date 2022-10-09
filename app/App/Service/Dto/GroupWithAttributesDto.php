<?php

namespace App\Service\Dto;

class GroupWithAttributesDto
{
    /**
     * @param array<int, AttributeDto> $attributes
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        readonly array $attributes,
    ) {
    }
}
