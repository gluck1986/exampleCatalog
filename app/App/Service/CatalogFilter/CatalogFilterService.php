<?php

namespace App\Service\CatalogFilter;

use App\Entity\Attribute;
use App\Repository\GroupToAttributeRepository;
use App\Service\CatalogFilter\Dto\CatalogWithFilterDto;
use App\Service\CatalogFilter\Dto\CriteriaDto;
use App\Service\CatalogFilter\Mapper\CatalogFilterResultMapper;
use Exception;
use Solarium\Client;
use Solarium\Component\FacetSet;
use Solarium\Core\Query\Result\ResultInterface;
use Solarium\QueryType\Select\Query\Query;
use Solarium\QueryType\Select\Result\Result;

class CatalogFilterService
{
    /**
     * @var list<Attribute>
     */
    private array $attributes = [];

    public function __construct(
        private readonly GroupToAttributeRepository $groupToAttributeRepository,
        private readonly CatalogFilterResultMapper $catalogFilterResultMapper,
        private readonly Client $client,
    ) {
    }

    /**
     * @throws Exception
     */
    public function getProductAndFilters(CriteriaDto $criteria): CatalogWithFilterDto
    {
        $this->attributes = $this->groupToAttributeRepository->getAttributesByGroup($criteria->group);
        $attrIdToFieldMap = $this->getAttrIdToFieldMap();
        $rawResult = $this->getProductAndFiltersRaw($attrIdToFieldMap, $criteria);

        return $this->mapResult($rawResult, $criteria);
    }

    /**
     * @return array<int, string>
     * @throws Exception
     */
    private function getAttrIdToFieldMap(): array
    {
        $result = [];
        foreach ($this->attributes as $attr) {
            $attrId = $attr->getId() ?? throw new Exception('attribute id must be');
            $result[$attrId] = $this->getAttributeFieldName($attrId);
        }
        return $result;
    }

    /**
     * @psalm-suppress MoreSpecificReturnType
     * @param array<int, string> $attrIdToFieldMap
     * @psalm-return Result&ResultInterface
     */
    private function getProductAndFiltersRaw(array $attrIdToFieldMap, CriteriaDto $criteria): ResultInterface
    {
        $attrIdToCriteriaValuesMap = $this->getAttrIdToCriteriaValuesMap($criteria);
        $client = $this->client;
        /** @var Query $query */
        $query = $client->createQuery(Client::QUERY_SELECT);

        $facetSet = $query->getFacetSet();
        $query->createFilterQuery('group_i')->setTags(['group'])->setQuery('group_i:' . $criteria->group);

        if ($criteria->name) {
            $query->setQuery("name_s:*$criteria->name*");
        }
        $this->queryDelivery($criteria, $query, $facetSet);
        $this->costQuery($criteria, $query);
        $this->attributesQuery($attrIdToFieldMap, $attrIdToCriteriaValuesMap, $query, $facetSet);

        $query->setRows($criteria->pageSize);
        $query->setStart($criteria->pageSize * ($criteria->page - 1));

        /** @psalm-suppress LessSpecificReturnStatement*/
        return $client->execute($query);
    }

    private function getTagName(int $attrId): string
    {
        return "attr$attrId";
    }

    private function getAttributeFieldName(int $attributeId): string
    {
        return sprintf('attr_%s_s', $attributeId);
    }

    /**
     * @param array<int, list<string>> $attrIdToCriteriaValuesMap
     */
    private function hasNotEmptyValues(array $attrIdToCriteriaValuesMap, int $attrId): bool
    {
        return !empty($attrIdToCriteriaValuesMap[$attrId])
            && count($attrIdToCriteriaValuesMap[$attrId]) > 0;
    }

    /**
     * @return array<int, list<string>>
     */
    private function getAttrIdToCriteriaValuesMap(CriteriaDto $criteria): array
    {
        $result = [];
        foreach ($criteria->attributes as $attr) {
            $result[$attr->id] = $attr->values;
        }
        return $result;
    }

    private function queryDelivery(CriteriaDto $criteria, Query $query, FacetSet $facetSet): void
    {
        if (null !== $criteria->delivery) {
            $query->createFilterQuery('delivery_i')
                ->setTags(['delivery'])
                ->setQuery('delivery_i:' . $criteria->delivery);

            /** @psalm-suppress PossiblyUndefinedMethod, MixedMethodCall */
            $facetSet->createFacetField('delivery_i')
                ->setField('delivery_i')->getLocalParameters()->addExcludes(['delivery']);
        } else {
            /** @psalm-suppress PossiblyUndefinedMethod, MixedMethodCall */
            $facetSet->createFacetField('delivery_i')
                ->setField('delivery_i');
        }
    }

    private function costQuery(CriteriaDto $criteria, Query $query): void
    {
        if (null !== $criteria->costFrom || null !== $criteria->costTo) {
            $query->createFilterQuery('cost_d')
                ->setQuery(sprintf(
                    'cost_d:[%s TO %s]',
                    $criteria->costFrom ?? '*',
                    $criteria->costTo ?? '*'
                ))->setTags(['cost']);
            $stats = $query->getStats();
            $stats->createField('{!min=true max=true ex=cost}cost_d');
        } else {
            $stats = $query->getStats();
            $stats->createField('{!min=true max=true}cost_d');
        }
    }

    /**
     * @param array<int, string> $attrIdToFieldMap
     * @param array<int, list<string>> $attrIdToCriteriaValuesMap
     */
    private function attributesQuery(
        array    $attrIdToFieldMap,
        array    $attrIdToCriteriaValuesMap,
        Query    $query,
        FacetSet $facetSet
    ): void {
        foreach ($attrIdToFieldMap as $attrId => $fieldName) {
            if ($this->hasNotEmptyValues($attrIdToCriteriaValuesMap, $attrId)) {
                $queryPart = sprintf(
                    '%s:("%s")',
                    $fieldName,
                    implode('" OR "', $attrIdToCriteriaValuesMap[$attrId])
                );
                $query->createFilterQuery($fieldName)
                    ->setTags([$this->getTagName($attrId)])
                    ->setQuery($queryPart);

                /** @psalm-suppress PossiblyUndefinedMethod, MixedMethodCall */
                $facetSet->createFacetField($fieldName)
                    ->setField($fieldName)->getLocalParameters()->addExcludes([$this->getTagName($attrId)]);
                ;
            } else {
                /** @psalm-suppress PossiblyUndefinedMethod, MixedMethodCall */
                $facetSet->createFacetField($fieldName)
                    ->setField($fieldName);
            }
        }
    }

    /**
     * @psalm-param Result&ResultInterface $rawResult
     * @param Result|ResultInterface $rawResult
     * @param CriteriaDto $criteriaDto
     * @return CatalogWithFilterDto
     * @throws Exception
     */
    private function mapResult(Result|ResultInterface $rawResult, CriteriaDto $criteriaDto): CatalogWithFilterDto
    {
        return $this->catalogFilterResultMapper->mapResult($rawResult, $this->attributes, $criteriaDto);
    }
}
