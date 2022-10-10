<?php

namespace App\Repository\Mappers;

use App\Entity\Attribute;

class AttributeMapper
{
    public function rowToDomain(array $row): Attribute
    {
        return new Attribute(
            id: empty($row['id']) ? null : (int)$row['id'],
            name: empty($row['name']) ? '' : (string)$row['name'],
            value: empty($row['value']) ? '' : (string)$row['value'],
        );
    }
}
