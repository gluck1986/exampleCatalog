<?php

namespace App\Action;

use App\Entity\Group;
use App\Repository\GroupRepository;
use HttpSoft\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetGroupsAction
{
    public function __construct(private readonly GroupRepository $groupRepository)
    {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $groups = $this->groupRepository->getAll();

        return new JsonResponse(array_map(fn(Group $g) => $g->toArray(), $groups));
    }
}
