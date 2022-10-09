<?php

namespace App\Entity;

class Product
{
    /**
     * @param list<Attribute> $attr
     */
    public function __construct(
        private readonly string $guid,
        private readonly int $groupId,
        private readonly string $name,
        private readonly float $cost,
        private readonly string $descr,
        private readonly int $delivery,
        private readonly array $attr,
    ) {
    }

    public function getGroupId(): int
    {
        return $this->groupId;
    }

    public function getGuid(): string
    {
        return $this->guid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCost(): float
    {
        return $this->cost;
    }

    public function getDescr(): string
    {
        return $this->descr;
    }

    /**
     * @return list<Attribute>
     */
    public function getAttr(): array
    {
        return $this->attr;
    }

    public function getDelivery(): int
    {
        return $this->delivery;
    }
}
