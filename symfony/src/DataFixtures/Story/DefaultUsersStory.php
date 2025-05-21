<?php

namespace App\DataFixtures\Story;

use App\DataFixtures\Factory\UserFactory;
use App\Utils\Roles;
use Zenstruck\Foundry\Story;

final class DefaultUsersStory extends Story
{
    public function build(): void
    {
        UserFactory::createOne([
            'email' => 'super-admin@fixture.com',
            'roles' => [Roles::SUPER_ADMIN->value],
        ]);

        UserFactory::createOne([
            'email' => 'admin@fixture.com',
            'roles' => [Roles::ADMIN->value],
        ]);

        UserFactory::createOne([
            'email' => 'contributor@fixture.com',
            'roles' => [Roles::CONTRIBUTOR->value],
        ]);

        UserFactory::createOne([
            'email' => 'user@fixture.com',
        ]);
    }
}
