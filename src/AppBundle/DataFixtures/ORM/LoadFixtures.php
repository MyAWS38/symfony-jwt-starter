<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use UserBundle\Entity\User;

class LoadFixtures implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $users = [];

        // Create admin user.
        $user = new User();
        $user->setActive(true);
        $user->setEmail('admin@example.com');
        $user->setName('Admin');
        $user->setPlainPassword('secret123');
        $user->addRole('ROLE_ADMIN');
        $manager->persist($user);
        $manager->flush();
        $users['admin'] = $user;

        // Create un-privileged user.
        $user = new User();
        $user->setActive(true);
        $user->setEmail('user@example.com');
        $user->setName('User');
        $user->setPlainPassword('secret123');
        $manager->persist($user);
        $manager->flush();
        $users['user'] = $user;
    }
}
