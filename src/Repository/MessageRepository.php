<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

/**
 * @extends EntityRepository<Message>
 */
class MessageRepository extends EntityRepository
{
    public function findMessagesByGroupId(int $groupId): Query
    {
        return $this->createQueryBuilder('m')
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
