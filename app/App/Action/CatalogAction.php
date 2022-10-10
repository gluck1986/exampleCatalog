<?php

namespace App\Action;

use App\Http\Formatter\CatalogFormatter;
use App\Http\InputMappers\CatalogInputMapper;
use App\Service\CatalogFilter\CatalogFilterService;
use HttpSoft\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CatalogAction
{
    public function __construct(
        private readonly CatalogFilterService $service,
        private readonly CatalogInputMapper $inputMapper,
        private readonly CatalogFormatter $formatter
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $criteria = $this->inputMapper->map($request->getParsedBody());
        $resultDto = $this->service->getProductAndFilters($criteria);
        $data = $this->formatter->format($resultDto);

        return new JsonResponse($data);
    }
}
