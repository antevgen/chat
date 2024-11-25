<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;

/**
 * @extends EntityRepository<Message>
 */
class MessageRepository extends EntityRepository
{
    public function findMessagesByGroupId(int $groupId): Query
    {
        return $this->createQueryBuilder('m')
            ->leftJoin(User::class, 'u', Join::WITH, 'm.user = u.id')
            ->andWhere('m.group = :groupId')
            ->setParameter('groupId', $groupId)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery();
    }

    public function save(Message $message): void
    {
        $this->getEntityManager()->persist($message);
        $this->getEntityManager()->flush();
    }
}
