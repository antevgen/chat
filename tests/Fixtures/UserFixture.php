<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use App\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        $user1 = new User();
        $user1->setUsername('editor');
        $user1->setEmail('editor@no-reply.com');

        $user2 = new User();
        $user2->setUsername('administrator');
        $user2->setEmail('administrator@no-reply.com');

        $manager->persist($user1);
        $manager->persist($user2);
        $manager->flush();
    }
}
