<?php

namespace App\Action;

use HttpSoft\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Solarium\Client;

class TestAction
{

    public function __construct(private readonly Client $client)
    {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
//        $client = $this->client;
//
//        $update = $client->createUpdate();
//        $doc1 = $update->createDocument();
//        $doc1->id = 123;
//        $doc1->name_s = 'testdoc-1';
//        $doc1->price_i = 364;
//        $doc1->attr_weight = '500';
//        // add the documents and a commit command to the update query
//        $update->addDocuments([$doc1]);
//        $update->addCommit();
//
//// this executes the query and returns the result
//        $result = $client->update($update);


        return new JsonResponse([/*$result->getStatus(), $result->getData()*/]);
    }
}
