<?php

namespace App\Service\CatalogFilter\Mapper;

use App\Entity\Attribute;
use App\Entity\Product;
use App\Service\CatalogFilter\Dto\CatalogWithFilterDto;
use App\Service\CatalogFilter\Dto\CriteriaDto;
use App\Service\CatalogFilter\Dto\FilterAttributeDto;
use App\Service\CatalogFilter\Dto\FilterResultDto;
use App\Service\CatalogFilter\Dto\SummaryDto;
use App\Service\Dto\AttributeDto;
use Exception;
use Solarium\Component\Result\Facet\Field;
use Solarium\Core\Query\Result\ResultInterface;
use Solarium\QueryType\Select\Result\Document;
use Solarium\QueryType\Select\Result\Result;

class CatalogFilterResultMapper
{
    /**
     * @param Result|ResultInterface $rawResult
     * @param list<Attribute> $knowAttributes
     * @return CatalogWithFilterDto
     * @throws Exception
     */
    public function mapResult(Result|ResultInterface $rawResult, array $knowAttributes, CriteriaDto $criteriaDto): CatalogWithFilterDto
    {
        $attributesMap = $this->attributesIndexById($knowAttributes);
        [$min, $max] = $this->mapCost($rawResult);
        $delivery = $this->mapDelivery($rawResult);
        $attributes = $this->mapAttributes($rawResult, $attributesMap);

        $filter = new FilterResultDto(
            costFrom: $min,
            costTo: $max,
            delivery: $delivery,
            attributes: $attributes,
        );

        $totalPages = $criteriaDto->pageSize === 0 ? 0: (int)ceil($rawResult->getNumFound() / $criteriaDto->pageSize);

        $summary = new SummaryDto(
            page: $criteriaDto->page,
            totalPages: $totalPages,
            totalItems: $rawResult->getNumFound(),
            pageSize: $criteriaDto->pageSize,
        );
        $products = $this->mapProducts($rawResult, $attributesMap);

        return new CatalogWithFilterDto(
            products: $products,
            filter: $filter,
            summary: $summary,
        );
    }

    private function mapCost(Result|ResultInterface $rawResult): array
    {
        $statsResult = $rawResult->getStats();
        $statsItem = $statsResult->getResult('cost_d');

        return [$statsItem->getMin(), $statsItem->getMax()];
    }

    /**
     * @param Result|ResultInterface $rawResult
     * @return list<int>
     */
    private function mapDelivery(Result|ResultInterface $rawResult): array
    {
        $result = [];
        $facet = $rawResult->getFacetSet();
        foreach ($facet->getFacet('delivery_i') as $value => $count) {
            if ($count > 0) {
                $result[] = (int)$value;
            }
        }
        return $result;
    }

    /**
     * @param Result|ResultInterface $rawResult
     * @param array<int, Attribute> $attributesMap
     * @return list<FilterAttributeDto>
     */
    private function mapAttributes(Result|ResultInterface $rawResult, array $attributesMap): array
    {
        $result = [];
        $facets = $rawResult->getFacetSet()->getFacets();
        foreach ($facets as $name => $facet) {
            if (!$facet instanceof Field) {
                continue;
            }
            $id = $this->parseFieldToAttributeId($name);
            if (null === $id) {
                continue;
            }
            $attributeEntity = $attributesMap[$id] ?? null;
            if (null === $attributeEntity) {
                continue;
            }
            $result[] = new FilterAttributeDto(
                id: $attributeEntity->getId(),
                name: $attributeEntity->getName(),
                values: array_keys(array_filter($facet->getValues())),
            );
        }
        return $result;
    }

    private function parseFieldToAttributeId(string $field): ?int
    {
        $match=[];
        if (preg_match('/attr_([\d]{1,})_s/', $field, $match) === 1) {
            return empty($match[1]) ? null : (int)$match[1];
        }
        return null;
    }

    /**
     * @param list<Attribute> $attributes
     * @return array<int, Attribute>
     */
    private function attributesIndexById(array $attributes): array
    {
        $result = [];
        foreach ($attributes as $attribute) {
            $result[$attribute->getId()] = $attribute;
        }
        return $result;
    }

    /**
     * @param Result|ResultInterface $rawResult
     * @param array<int, Attribute> $attributesMap
     * @return list<Product>
     * @throws Exception
     */
    private function mapProducts(Result|ResultInterface $rawResult, array $attributesMap): array
    {
        $result = [];
        /** @var Document $document */
        foreach ($rawResult->getDocuments() as $document) {
            $fields = $document->getFields();
            $result[] = new Product(
                guid: $this->getStringValue($fields, 'id'),
                groupId: $this->getIntValue($fields, 'group_i'),
                name: $this->getStringValue($fields, 'name_s'),
                cost: $this->getFloatValue($fields, 'cost_d'),
                descr: $this->getStringValue($fields, 'descr_txt'),
                delivery: $this->getIntValue($fields, 'delivery_i'),
                attr: $this->mapProductAttributes($fields, $attributesMap),
            );
        }
        return $result;
    }

    private function getIntValue(array $fields, string $key): int
    {
        $value = $fields[$key] ?? throw new Exception("value by `$key` must be");
        if (is_array($value)) {
            $candidat = array_values($value)[0] ?? throw new Exception("value by `$key` must be");
            return (int)$candidat;
        }
        return (int)$value;
    }

    private function getStringValue(array $fields, string $key): string
    {
        $value = $fields[$key] ?? throw new Exception("value by `$key` must be");
        if (is_array($value)) {
            $candidat = array_values($value)[0] ?? throw new Exception("value by `$key` must be");
            return (string)$candidat;
        }
        return (string)$value;
    }

    private function getFloatValue(array $fields, string $key): float
    {
        $value = $fields[$key] ?? throw new Exception("value by `$key` must be");
        if (is_array($value)) {
            $candidat = array_values($value)[0] ?? throw new Exception("value by `$key` must be");
            return (float)$candidat;
        }
        return (float)$value;
    }

    /**
     * @param array $fields
     * @param array<int, Attribute> $attributesMap
     * @return list<Attribute>
     * @throws Exception
     */
    private function mapProductAttributes(array $fields, array $attributesMap): array
    {
        $result = [];
        foreach ($fields as $field => $values) {
            $attrId = $this->parseFieldToAttributeId($field);
            if (null === $attrId) {
                continue;
            }
            $attribute = $attributesMap[$attrId];
            if (null === $attribute) {
                continue;
            }
            $result[] = new Attribute(
                id: $attribute->getId(),
                name: $attribute->getName(),
                value: $this->getStringValue(['val'=>$values], 'val'),
            );
        }

        return $result;
    }
}