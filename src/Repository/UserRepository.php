<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

/**
 * @extends EntityRepository<User>
 */
class UserRepository extends EntityRepository
{
    public function findAllQuery(): Query
    {
        return $this->createQueryBuilder('g')
            ->getQuery();
    }

    public function save(User $message): void
    {
        $this->getEntityManager()->persist($message);
        $this->getEntityManager()->flush();
    }
}
