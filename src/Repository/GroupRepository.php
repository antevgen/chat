<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Group;
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
}
