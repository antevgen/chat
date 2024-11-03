<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Group;
use App\Repository\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;

class GroupService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly GroupRepository $groupRepository,
    ) {
    }

    public function getAllGroups(): array
    {
        return $this->groupRepository->findAll();
    }

    public function createGroup(mixed $name): Group
    {
        $group = new Group();
        $group->setName($name);
        $this->entityManager->persist($group);
        $this->entityManager->flush();

        return $group;
    }
}
