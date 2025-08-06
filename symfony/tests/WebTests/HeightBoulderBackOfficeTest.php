<?php

namespace App\Tests\WebTests;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use PHPUnit\Framework\Attributes\DataProvider;

class HeightBoulderBackOfficeTest extends BackOfficeTestCase
{
    #[DataProvider('viewers')]
    public function testAdminAndContributorCannotAccessHeightBoulderSection($email)
    {
        $this->visitBackOffice(
            userEmail: $email,
        );

        $this->client->request('GET', '/admin/fr/height-boulder');
        $this->assertResponseStatusCodeSame(403);

        $this->client->request('GET', '/admin/fr/height-boulder/1');
        $this->assertResponseStatusCodeSame(403);
    }

    public static function viewers(): array
    {
        return [
            ['contributor@fixture.com'],
            ['admin@fixture.com'],
        ];
    }

    public function testSuperAdminCanEntirelyManageHeightBoulder()
    {
        $this->visitBackOffice(
            userEmail: 'super-admin@fixture.com',
        );

        $this->client->request('GET', '/admin/fr/height-boulder');
        $this->assertResponseStatusCodeSame(200);

        $this->assertIndexFullEntityCount(3);

        $this->assertIndexEntityActionExists(Action::DELETE, 1);
        $this->assertIndexEntityActionExists(Action::EDIT, 1);
        $this->assertGlobalActionExists(Action::NEW);
        $this->assertIndexEntityActionExists(Action::DETAIL, 1);
    }

    public function testSuperAdminCanDeleteHeightBoulder()
    {
        $this->visitBackOffice(
            userEmail: 'super-admin@fixture.com',
        );

        $this->client->request('GET', '/admin/fr/height-boulder');
        $this->assertResponseStatusCodeSame(200);

        $this->assertIndexPageEntityCount(3);

        $this->assertIndexEntityActionExists(Action::DELETE, 1);

        $this->indexDeleteEntity(1);

        $this->assertIndexPageEntityCount(2);
    }

    public function testCannotCreateInvalidHeightBoulder()
    {
        $this->visitBackOffice(
            userEmail: 'super-admin@fixture.com',
        );

        $this->client->request('GET', '/admin/fr/height-boulder');

        $this->client->clickLink('Créer Hauteur');

        $this->assertSelectorTextContains('h1', 'Créer "Hauteur"');

        $this->client->submitForm('Créer', []);

        $this->assertResponseStatusCodeSame(422);

        $this->assertFieldIsInvalid(fieldName: 'min');

        $this->client->submitForm('Créer', fieldValues: [
            'HeightBoulder[min]' => 0,
            'HeightBoulder[max]' => 3,
        ]);

        $this->assertResponseStatusCodeSame(422);

        $this->assertInvalidFeedback(fieldName: 'min', message: 'La combinaison des propriétés min et max existe déjà');
    }

    public function testCanCreateHeightBoulder()
    {
        $this->visitBackOffice(
            userEmail: 'super-admin@fixture.com',
        );

        $this->client->request('GET', '/admin/fr/height-boulder');

        $this->assertIndexFullEntityCount(3);

        $this->client->clickLink('Créer Hauteur');

        $this->assertSelectorTextContains('h1', 'Créer "Hauteur"');

        $this->client->submitForm('Créer', fieldValues: [
            'HeightBoulder[min]' => 0,
            'HeightBoulder[max]' => 4,
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertIndexFullEntityCount(4);
    }
}
