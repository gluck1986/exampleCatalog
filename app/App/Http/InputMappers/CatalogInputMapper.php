<?php

namespace App\Http\InputMappers;

use App\Service\CatalogFilter\Dto\CriteriaAttributeDto;
use App\Service\CatalogFilter\Dto\CriteriaDto;
use Exception;

class CatalogInputMapper
{
    public function map(?array $parsedBody): CriteriaDto
    {
        if (empty($parsedBody)) {
            throw new \Exception('empty data');
        }

        return new CriteriaDto(
            group: $this->getIntValueOrFall($parsedBody, 'group'),
            name: $this->getStringValue($parsedBody, 'name'),
            costFrom: $this->getFloatValue($parsedBody, 'costFrom'),
            costTo: $this->getFloatValue($parsedBody, 'costTo'),
            delivery: $this->getIntValue($parsedBody, 'delivery'),
            page: $this->getIntValue($parsedBody, 'page', 1),
            pageSize: $this->getIntValue($parsedBody, 'page', 10),
            attributes: array_map(fn($arr) => $this->mapAttribute($arr), $this->getArrayValue($parsedBody, 'attr')),
        );
    }

    private function mapAttribute($arr): CriteriaAttributeDto
    {
        return new CriteriaAttributeDto(
            id: $this->getIntValueOrFall($arr, 'id'),
            values: array_values(array_map(fn($val) => (string)$val, $this->getArrayValue($arr, 'values')))
        );
    }

    private function getIntValueOrFall(array $fields, string $key): int
    {
        $value = $fields[$key] ?? throw new Exception("value by `$key` must be");

        return (int)$value;
    }

    private function getIntValue(array $fields, string $key, ?int $default = null): ?int
    {
        return !isset($fields[$key]) ? $default : (int)$fields[$key];
    }

    private function getStringValue(array $fields, string $key, ?string $default = null): ?string
    {
        return !isset($fields[$key]) ? $default : (string)$fields[$key];
    }

    private function getFloatValue(array $fields, string $key, ?float $default = null): ?float
    {
        return !isset($fields[$key]) ? $default : (float)$fields[$key];
    }

    private function getArrayValue(array $fields, string $key, array $default = []): array
    {
        if (!isset($fields[$key])) {
            return $default;
        }
        if (!is_array($fields[$key])) {
            return $default;
        }
        return $fields[$key];
    }
}