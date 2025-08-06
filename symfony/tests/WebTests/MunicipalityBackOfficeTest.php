<?php

namespace App\Tests\WebTests;

use App\Repository\MunicipalityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use PHPUnit\Framework\Attributes\DataProvider;

class MunicipalityBackOfficeTest extends BackOfficeTestCase
{
    #[DataProvider('viewers')]
    public function testAdminAndContributorCanOnlyViewMunicipality($email)
    {
        $this->visitBackOffice(
            userEmail: $email,
        );

        $crawler = $this->client->request('GET', '/admin/fr/municipality');
        $this->assertResponseStatusCodeSame(200);

        $municipalityRepository = static::getContainer()->get(MunicipalityRepository::class);
        $municipality = $municipalityRepository->find(1);

        $this->assertSelectorTextContains('table tbody', $municipality->getName());
        $this->assertIndexFullEntityCount(2);

        $this->assertIndexEntityActionNotExists(Action::DELETE, $municipality->getId());
        $this->assertIndexEntityActionNotExists(Action::EDIT, $municipality->getId());
        $this->assertGlobalActionNotExists(Action::NEW);
        $this->assertIndexEntityActionExists(Action::DETAIL, $municipality->getId());

        $link = $crawler->filter($this->getIndexEntityActionSelector(Action::DETAIL, $municipality->getId()))->link();
        $this->client->click($link);

        $this->assertSelectorTextContains('h1', $municipality->getName());
    }

    public static function viewers(): array
    {
        return [
            ['contributor@fixture.com'],
            ['admin@fixture.com'],
        ];
    }

    public function testSuperAdminCanEntirelyManageMunicipality()
    {
        $this->visitBackOffice(
            userEmail: 'super-admin@fixture.com',
        );

        $this->client->request('GET', '/admin/fr/municipality');
        $this->assertResponseStatusCodeSame(200);

        $this->assertIndexFullEntityCount(2);

        $this->assertIndexEntityActionExists(Action::DELETE, 1);
        $this->assertIndexEntityActionExists(Action::EDIT, 1);
        $this->assertGlobalActionExists(Action::NEW);
        $this->assertIndexEntityActionExists(Action::DETAIL, 1);
    }

    public function testSuperAdminCanDeleteMunicipality()
    {
        $this->visitBackOffice(
            userEmail: 'super-admin@fixture.com',
        );

        $this->client->request('GET', '/admin/fr/municipality');
        $this->assertResponseStatusCodeSame(200);

        $this->assertIndexPageEntityCount(2);

        $this->assertIndexEntityActionExists(Action::DELETE, 1);

        $this->indexDeleteEntity(1);

        $this->assertIndexPageEntityCount(1);
    }

    public function testCannotCreateInvalidMunicipality()
    {
        $this->visitBackOffice(
            userEmail: 'super-admin@fixture.com',
        );

        $this->client->request('GET', '/admin/fr/municipality');

        $this->client->clickLink('Créer Commune');

        $this->assertSelectorTextContains('h1', 'Créer "Commune"');

        $this->client->submitForm('Créer', []);

        $this->assertResponseStatusCodeSame(422);

        $this->assertFieldIsInvalid(fieldName: 'name');

        $this->client->submitForm('Créer', fieldValues: [
            'Municipality[name]' => 'Kerlouan',
            'Municipality[department]' => '1',
        ]);

        $this->assertResponseStatusCodeSame(422);

        $this->assertInvalidFeedback(fieldName: 'name', message: 'Cette valeur est déjà utilisée');
    }

    public function testCanCreateMunicipality()
    {
        $this->visitBackOffice(
            userEmail: 'super-admin@fixture.com',
        );

        $this->client->request('GET', '/admin/fr/municipality');

        $this->assertIndexFullEntityCount(2);
        $this->assertSelectorTextNotContains('table tbody', 'foo');

        $this->client->clickLink('Créer Commune');

        $this->assertSelectorTextContains('h1', 'Créer "Commune"');

        $this->client->submitForm('Créer', [
            'Municipality[name]' => 'foo',
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertIndexFullEntityCount(3);

        $this->assertSelectorTextContains('table tbody', 'foo');
    }

    public function testCanShowDetailsAboutMunicipality()
    {
        $this->visitBackOffice(
            userEmail: 'admin@fixture.com',
        );

        $this->client->request('GET', '/admin/fr/municipality/2');

        $this->assertSelectorTextContains('h1', 'Kerlouan');

        $this->assertSelectorCount(5, '.cy-boulderAreas tbody tr');
        $this->assertSelectorTextContains('.cy-boulderAreas tbody tr:nth-child(1)', 'Bivouac');
        $this->assertSelectorTextContains('.cy-boulderAreas tbody tr:nth-child(2)', 'Cremiou');
    }
}
