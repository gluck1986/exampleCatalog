<?php

namespace App\Service\CatalogFilter\Mapper;

use App\Entity\Attribute;
use App\Entity\Product;

class ProductMapper
{
    public function toArray(Product $product): array
    {
        return [
            'guid' => $product->getGuid(),
            'name' => $product->getName(),
            'cost' => $product->getCost(),
            'descr' => $product->getDescr(),
            'delivery' => $product->getDelivery(),
            'attr' => array_map($this->attributeToArray(...), $product->getAttr())
        ];
    }

    public function attributeToArray(Attribute $attribute): array
    {
        return [
            'id' => $attribute->getId(),
            'name' => $attribute->getName(),
            'value' => $attribute->getValue(),
        ];
    }
}
