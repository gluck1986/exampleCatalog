<?php

namespace App\Entity;

class Attribute
{
    public function __construct(
        private readonly ?int $id,
        private readonly string $name,
        private readonly string $value,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
