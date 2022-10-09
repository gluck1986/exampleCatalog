<?php

namespace App\Repository;

use App\Entity\Product;
use App\Repository\Mappers\ProductSolrMapper;
use Solarium\Client;

class ProductRepository
{
    public function __construct(private readonly Client $client, private readonly ProductSolrMapper $mapper)
    {
    }

    /**
     * @param list<Product> $products
     */
    public function write(array $products): bool
    {
        $client = $this->client;
        $update = $client->createUpdate();
        $docs = array_map($this->mapper->domainToDocument(...), $products);
        /** @psalm-suppress PossiblyUndefinedMethod */
        $update->addDocuments($docs);
        /** @psalm-suppress PossiblyUndefinedMethod */
        $update->addCommit();
        $result = $client->update($update);

        /** @psalm-suppress PossiblyUndefinedMethod */
        return $result->getStatus() === 0;
    }
}
