<?php

namespace App\Tests\WebTests;

use App\Repository\UserRepository;
use App\Utils\Roles;
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

        $this->client->request('GET', "/admin/fr/user/new");
        $this->assertResponseStatusCodeSame(403);

        $this->client->request('GET', "/admin/fr/user/$ownId/edit");
        $this->assertResponseIsSuccessful();
        $this->assertFormFieldNotExists('roles');
        $this->assertFormFieldExists('email');

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

        $this->assertIndexEntityActionExists(Action::DELETE, 2); 
        $this->assertIndexEntityActionExists(Action::EDIT, 2); 
        $this->assertGlobalActionExists(Action::NEW); 
        $this->assertIndexEntityActionExists(Action::DETAIL, 2); 
    }

    public function testSuperAdminCanDeleteUser () {
        $this->visitBackOffice(
            userEmail: 'super-admin@fixture.com',
        );

        $this->client->request('GET', '/admin/fr/user');
        $this->assertResponseStatusCodeSame(200);

        $this->assertIndexPageEntityCount(4);

        $this->assertIndexEntityActionExists(Action::DELETE, 2); 

        $this->indexDeleteEntity(2);

        $this->assertIndexPageEntityCount(3);
    }

    public function testCannotCreateInvalidUser() {
        $this->visitBackOffice(
            userEmail: 'super-admin@fixture.com',
        );

        $this->client->request('GET', '/admin/fr/user');

        $this->client->clickLink('Créer Utilisateur');

        $this->assertSelectorTextContains('h1','Créer "Utilisateur"');
        
        $this->client->submitForm('Créer', []);

        $this->assertResponseStatusCodeSame(422);

        $this->assertFieldIsInvalid(fieldName: 'email');
    }

    public function testCanCreateUser() {

        $this->visitBackOffice(
            userEmail: 'super-admin@fixture.com',
        );

        $this->client->request('GET', '/admin/fr/user');

        $this->client->clickLink('Créer Utilisateur');

        $this->assertSelectorTextContains('h1','Créer "Utilisateur"');

        $this->assertFormFieldExists('email');

        $this->assertFormFieldExists('roles');

        $this->client->submitForm('Créer', [
            'User[email]' => 'test@fixture.com',
            'User[roles][0]' => true,
            'User[roles][1]' => true,
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertIndexFullEntityCount(5);

        $userRepository = static::getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneBy(['email' => 'test@fixture.com']);

        $this->assertNotNull($user);

        $this->assertCount(2, $user->getRoles());
        $this->assertContains(Roles::CONTRIBUTOR->value, $user->getRoles());
        $this->assertContains(Roles::USER->value, $user->getRoles());
    }
}