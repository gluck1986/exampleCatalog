<?php

namespace App\Repository\Mappers;

use App\Entity\Product;
use Solarium\Client;
use Solarium\Core\Query\DocumentInterface;

class ProductSolrMapper
{
    public function __construct(private readonly Client $client)
    {
    }

    public function domainToDocument(Product $product): DocumentInterface
    {
        $update = $this->client->createUpdate();

        /** @psalm-suppress PossiblyUndefinedMethod */
        $doc = $update->createDocument();
        /** @psalm-suppress MixedPropertyAssignment  */
        $doc->id = $product->getGuid();
        /** @psalm-suppress MixedPropertyAssignment  */
        $doc->name_s = $product->getName();
        /** @psalm-suppress MixedPropertyAssignment  */
        $doc->cost_d = $product->getCost();
        /** @psalm-suppress MixedPropertyAssignment  */
        $doc->descr_txt = $product->getDescr();
        /** @psalm-suppress MixedPropertyAssignment  */
        $doc->delivery_i = $product->getDelivery();
        /** @psalm-suppress MixedPropertyAssignment  */
        $doc->group_i = $product->getGroupId();

        foreach ($product->getAttr() as $attr) {
            $attrId = is_null($attr->getId()) ? throw new \Exception("attr must have id") : (string)$attr->getId();
            /** @psalm-suppress MixedPropertyAssignment  */
            $doc->{'attr_' . $attrId . '_s'} = $attr->getValue();
        }

        if (!($doc instanceof DocumentInterface)) {
            throw new \Exception("\$doc must be instance of " . DocumentInterface::class);
        }

        return $doc;
    }
}
