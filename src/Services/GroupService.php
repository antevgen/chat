<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class GroupService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }
}
