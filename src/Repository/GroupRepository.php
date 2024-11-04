<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Group;
use App\Entity\Message;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

/**
 * @extends EntityRepository<Group>
 */
class GroupRepository extends EntityRepository
{
    public function findAllQuery(): Query
    {
        return $this->createQueryBuilder('g')
            ->getQuery();
    }

    public function save(Group $message): void
    {
        $this->getEntityManager()->persist($message);
        $this->getEntityManager()->flush();
    }
}
