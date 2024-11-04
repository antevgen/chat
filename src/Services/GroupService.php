<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Group;
use App\Entity\User;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

class GroupService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly GroupRepository $groupRepository,
        private readonly UserRepository $userRepository,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
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

    public function getGroup(int $groupId): ?Group
    {
        return $this->groupRepository->find($groupId);
    }

    public function isExistingMember(Group $group, mixed $userId): bool
    {
        return $group->getMembers()->filter(function (User $user) use ($userId) {
            return $user->getId() === $userId;
        })->count() > 0;
    }

    public function addMember(Group $group, mixed $userId): void
    {
        /** @var User $user */
        $user = $this->userRepository->find($userId);
        $group->addMember($user);

        $this->entityManager->flush();
    }
}
