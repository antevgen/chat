<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Psr\Log\InvalidArgumentException;

class UserService
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function getPaginatedList(int $page = 1, int $limit = 10): array
    {
        $adapter = new QueryAdapter($this->userRepository->findAllQuery());
        $pagerfanta = new Pagerfanta($adapter);

        $pagerfanta->setMaxPerPage($limit);
        $pagerfanta->setCurrentPage($page);

        $users = array_map(
            fn($user) => $user->toArray(),
            iterator_to_array($pagerfanta->getCurrentPageResults())
        );

        return [
            'data' => $users,
            'meta' => [
                'current_page' => $pagerfanta->getCurrentPage(),
                'per_page' => $pagerfanta->getMaxPerPage(),
                'total' => $pagerfanta->getNbResults(),
                'total_pages' => $pagerfanta->getNbPages(),
            ]
        ];
    }

    public function createUser(string $username, string $email): User
    {
        if ($this->userRepository->findOneBy(['email' => $email])) {
            throw new InvalidArgumentException("Email already exists.");
        }

        if ($this->userRepository->findOneBy(['username' => $username])) {
            throw new InvalidArgumentException("Username already exists.");
        }

        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $this->userRepository->save($user);

        return $user;
    }

    public function getUser(int $userId): User
    {
        return $this->userRepository->find($userId);
    }
}
