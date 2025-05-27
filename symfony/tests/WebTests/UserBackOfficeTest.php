<?php

namespace App\Tests\WebTests;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use PHPUnit\Framework\Attributes\DataProvider;

class UserBackOfficeTest extends BackOfficeTestCase {

   #[DataProvider('viewers')]
    public function testAdminAndContributorCannotAccessUserSection(
        string $email, 
        int $ownId,
        ) {
        $this->visitBackOffice(
            userEmail: $email,
        );

        $this->client->request('GET', '/admin/fr/user');
        $this->assertResponseStatusCodeSame(403);

        $this->client->request('GET', '/admin/fr/user/1');
        $this->assertResponseStatusCodeSame(403);

        $this->client->request('GET', "/admin/fr/user/$ownId/edit");
        $this->assertResponseIsSuccessful();

        $this->client->request('GET', "/admin/fr/user/$ownId");
        $this->assertResponseIsSuccessful();

        $this->client->request('POST', '/admin/fr/user/1/delete');
        $this->assertResponseStatusCodeSame(403);

    }

    public static function viewers(): array
    {
        return [
            ['admin@fixture.com', 2],
            ['contributor@fixture.com', 3],
        ];
    }

    public function testSuperAdminCanEntirelyManageUsers () {
        $this->visitBackOffice(
            userEmail: 'super-admin@fixture.com',
        );

        $this->client->request('GET', '/admin/fr/user');
        $this->assertResponseStatusCodeSame(200);

        $this->assertIndexFullEntityCount(4);

        $this->assertIndexEntityActionExists(Action::DELETE, 1); 
        $this->assertIndexEntityActionExists(Action::EDIT, 1); 
        $this->assertGlobalActionExists(Action::NEW); 
        $this->assertIndexEntityActionExists(Action::DETAIL, 1); 
    }
}