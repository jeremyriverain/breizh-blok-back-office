<?php

namespace App\Tests\WebTests;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use PHPUnit\Framework\Attributes\DataProvider;

class DepartmentBackOfficeTest extends BackOfficeTestCase
{
    #[DataProvider('viewers')]
    public function testAdminAndContributorCanOnlyViewDepartment($email)
    {
        $this->visitBackOffice(
            userEmail: $email,
        );

        $crawler = $this->client->request('GET', '/admin/fr/department');
        $this->assertResponseStatusCodeSame(200);

        $this->assertSelectorTextContains('table tbody', 'Finistère');
        $this->assertIndexFullEntityCount(1);

        $this->assertIndexEntityActionNotExists(Action::DELETE, 1);
        $this->assertIndexEntityActionNotExists(Action::EDIT, 1);
        $this->assertGlobalActionNotExists(Action::NEW);
        $this->assertIndexEntityActionExists(Action::DETAIL, 1);

        $link = $crawler->filter($this->getIndexEntityActionSelector(Action::DETAIL, 1))->link();
        $this->client->click($link);

        $this->assertSelectorTextContains('h1', 'Finistère');
    }

    public static function viewers(): array
    {
        return [
            ['contributor@fixture.com'],
            ['admin@fixture.com'],
        ];
    }

    public function testSuperAdminCanViewDepartment()
    {
        $this->visitBackOffice(
            userEmail: 'super-admin@fixture.com',
        );

        $crawler = $this->client->request('GET', '/admin/fr/department');
        $this->assertResponseStatusCodeSame(200);

        $this->assertSelectorTextContains('table tbody', 'Finistère');
        $this->assertIndexFullEntityCount(1);

        $this->assertIndexEntityActionExists(Action::DETAIL, 1);

        $link = $crawler->filter($this->getIndexEntityActionSelector(Action::DETAIL, 1))->link();
        $this->client->click($link);

        $this->assertSelectorTextContains('h1', 'Finistère');
    }

    public function testSuperAdminCanDeleteDepartment()
    {
        $this->visitBackOffice(
            userEmail: 'super-admin@fixture.com',
        );

        $this->client->request('GET', '/admin/fr/department');
        $this->assertResponseStatusCodeSame(200);

        $this->assertIndexPageEntityCount(1);

        $this->assertIndexEntityActionExists(Action::DELETE, 1);

        $this->indexDeleteEntity(1);

        $this->assertIndexPageEntityCount(0);
    }

    public function testCannotCreateInvalidDepartment()
    {
        $this->visitBackOffice(
            userEmail: 'super-admin@fixture.com',
        );

        $this->client->request('GET', '/admin/fr/department');

        $this->client->clickLink('Créer Département');

        $this->assertSelectorTextContains('h1', 'Créer "Département"');

        $this->client->submitForm('Créer', []);

        $this->assertResponseStatusCodeSame(422);

        $this->assertFieldIsInvalid(fieldName: 'name');
    }

    public function testCanCreateDepartment()
    {
        $this->visitBackOffice(
            userEmail: 'super-admin@fixture.com',
        );

        $this->client->request('GET', '/admin/fr/department');

        $this->assertIndexPageEntityCount(1);

        $this->client->clickLink('Créer Département');

        $this->assertSelectorTextContains('h1', 'Créer "Département"');

        $this->client->submitForm('Créer', fieldValues: [
            'Department[name]' => 'foo',
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertIndexPageEntityCount(2);

        $this->assertSelectorTextContains('table tbody', 'foo');
    }
}
