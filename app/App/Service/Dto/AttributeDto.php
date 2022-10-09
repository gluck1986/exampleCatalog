<?php

namespace App\Service\Dto;

class AttributeDto
{
    /**
     * @param list<string> $values
     */
    public function __construct(public readonly int $id, public readonly string $name, public readonly array $values)
    {
    }
}
