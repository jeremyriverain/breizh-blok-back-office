<?php

namespace App\DataFixtures;

use App\Controller\Utils\Roles;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        $super_admin = $this->makeUser(('super-admin@fixture.com'));
        $super_admin->setRoles([Roles::SUPER_ADMIN->value]);
        $manager->persist($super_admin);

        $admin = $this->makeUser(('admin@fixture.com'));
        $admin->setRoles([Roles::ADMIN->value]);
        $manager->persist($admin);

        $contributor = $this->makeUser(('contributor@fixture.com'));
        $contributor->setRoles([Roles::CONTRIBUTOR->value]);
        $manager->persist($contributor);

        $user = $this->makeUser(('user@fixture.com'));
        $manager->persist($user);

        $manager->flush();
    }

    public function makeUser(string $email): User
    {
        $user = new User();
        $user->setEmail($email);

        return $user;
    }
}
