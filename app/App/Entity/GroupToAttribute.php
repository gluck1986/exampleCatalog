<?php

namespace App\Entity;

class GroupToAttribute
{
    public function __construct(private readonly int $groupId, private readonly int $attributeId,)
    {
    }

    public function getGroupId(): int
    {
        return $this->groupId;
    }

    public function getAttributeId(): int
    {
        return $this->attributeId;
    }
}
