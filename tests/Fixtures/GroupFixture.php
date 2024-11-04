<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use App\Entity\Group;
use App\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class GroupFixture extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        $group1 = new Group();
        $group1->setName('Group 1');

        $group2 = new Group();
        $group2->setName('Group 2');

        $manager->persist($group1);
        $manager->persist($group2);
        $manager->flush();
    }

    public function loadGroupsWithMember(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername('admin');
        $user->setEmail('admin@test.com');

        $group1 = new Group();
        $group1->setName('Group 1');
        $group1->addMember($user);

        $group2 = new Group();
        $group2->setName('Group 2');

        $manager->persist($user);
        $manager->persist($group1);
        $manager->persist($group2);
        $manager->flush();
    }
}
