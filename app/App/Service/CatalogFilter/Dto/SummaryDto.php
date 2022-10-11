<?php

namespace App\Service\CatalogFilter\Dto;

class SummaryDto
{
    public function __construct(
        public readonly int $page,
        public readonly int $totalPages,
        public readonly int $totalItems,
        public readonly int $pageSize,
    ) {
    }

    public function toArray(): array
    {
        return [
            'page' => $this->page,
            'totalPages' => $this->totalPages,
            'totalItems' => $this->totalItems,
            'pageSize' => $this->pageSize,
        ];
    }
}
