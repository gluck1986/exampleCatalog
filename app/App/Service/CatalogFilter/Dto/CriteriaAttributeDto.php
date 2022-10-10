<?php

namespace App\Service\CatalogFilter\Dto;

class CriteriaAttributeDto
{
    /**
     * @param list<string> $values
     */
    public function __construct(public readonly int $id, public readonly array $values)
    {
    }
}
