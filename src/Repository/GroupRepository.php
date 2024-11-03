<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Group;
use Doctrine\ORM\EntityRepository;

/**
 * @extends EntityRepository<Group>
 */
class GroupRepository extends EntityRepository
{
    public function findAll(): array
    {
        return $this->createQueryBuilder('g')
            ->getQuery()
            ->getArrayResult();
    }
}
