<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Group;
use App\Repository\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

class GroupService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly GroupRepository $groupRepository,
    ) {
    }

    public function getPaginatedGroups(int $page = 1, int $limit = 10): array
    {
        $adapter = new QueryAdapter($this->groupRepository->findAllQuery());
        $pagerfanta = new Pagerfanta($adapter);

        $pagerfanta->setMaxPerPage($limit);
        $pagerfanta->setCurrentPage($page);

        $groups = array_map(
            fn($group) => $group->toArray(),
            iterator_to_array($pagerfanta->getCurrentPageResults())
        );

        return [
            'data' => $groups,
            'meta' => [
                'current_page' => $pagerfanta->getCurrentPage(),
                'per_page' => $pagerfanta->getMaxPerPage(),
                'total' => $pagerfanta->getNbResults(),
                'total_pages' => $pagerfanta->getNbPages(),
            ]
        ];
    }

    public function createGroup(string $name): Group
    {
        $group = new Group();
        $group->setName($name);
        $this->entityManager->persist($group);
        $this->entityManager->flush();

        return $group;
    }
}
