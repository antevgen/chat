<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use App\Entity\Group;
use App\Entity\Message;
use App\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class MessageFixture extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername('admin');
        $user->setEmail('admin@test.com');

        $group = new Group();
        $group->setName('Group 1');
        $group->addMember($user);

        $message = new Message();
        $message->setSubject('Message 1');
        $message->setContent('Content 1');
        $message->setGroup($group);
        $message->setUser($user);


        $manager->persist($user);
        $manager->persist($group);
        $manager->persist($message);
        $manager->flush();
    }
}
