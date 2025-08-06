<?php

namespace App\Tests\WebTests;

use App\Repository\BoulderAreaRepository;
use App\Repository\RockRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;

class RockBackOfficeTest extends BackOfficeTestCase
{
    public function testListRocks()
    {
        $this->visitBackOffice(
            userEmail: 'contributor@fixture.com',
        );

        $this->client->request('GET', '/admin/fr/rock');
        $this->assertResponseStatusCodeSame(200);

        $this->assertIndexFullEntityCount(4);

        $this->assertIndexColumnExists('id');
        $this->assertIndexColumnExists('boulderArea');
        $this->assertIndexColumnExists('boulders');
        $this->assertIndexColumnExists('pictures');
        $this->assertIndexColumnExists('createdAt');
        $this->assertIndexColumnExists('updatedAt');
    }

    public function testSearchRocks()
    {
        $this->visitBackOffice(
            userEmail: 'contributor@fixture.com',
        );

        $this->client->request('GET', '/admin/fr/rock');

        $this->indexSearch(query: 'Cremiou');

        $this->assertIndexFullEntityCount(2);
    }

    public function testShowDetails()
    {
        $this->visitBackOffice(
            userEmail: 'contributor@fixture.com',
        );

        $crawler = $this->client->request('GET', '/admin/fr/rock');

        $link = $crawler->filter($this->getIndexEntityActionSelector(Action::DETAIL, 1))->link();
        $this->client->click($link);

        $this->assertSelectorTextContains('h1', 'Cremiou #1');

        $this->assertSelectorTextContains('.geo-point-field thead th:nth-child(1)', 'Latitude');
        $this->assertSelectorTextContains('.geo-point-field thead th:nth-child(2)', 'Longitude');

        $this->assertSelectorTextContains('.geo-point-field tbody td:nth-child(1)', '48.673149748436');
        $this->assertSelectorTextContains('.geo-point-field tbody td:nth-child(2)', '-4.3580819451625');

        $this->assertSelectorTextContains('.cy-boulders', 'Blocs');
        $this->assertSelectorCount(2, '.cy-boulders li');
        $this->assertSelectorTextContains('.cy-boulders li:nth-child(1)', 'Monkey');
        $this->assertSelectorTextContains('.cy-boulders li:nth-child(2)', 'Stone');
    }

    public function testAdminCanEntirelyManageRock()
    {
        $this->visitBackOffice(
            userEmail: 'admin@fixture.com',
        );

        $this->client->request('GET', '/admin/fr/rock');
        $this->assertResponseStatusCodeSame(200);

        $this->assertIndexFullEntityCount(4);

        $this->assertIndexEntityActionExists(Action::DELETE, 1);
        $this->assertIndexEntityActionExists(Action::EDIT, 1);
        $this->assertGlobalActionExists(Action::NEW);
        $this->assertIndexEntityActionExists(Action::DETAIL, 1);
    }

    public function testAdminCanDeleteRock()
    {
        $this->visitBackOffice(
            userEmail: 'admin@fixture.com',
        );

        $this->client->request('GET', '/admin/fr/rock');
        $this->assertResponseStatusCodeSame(200);
        $this->assertIndexPageEntityCount(4);

        $this->assertIndexEntityActionExists(Action::DELETE, 1);

        $this->indexDeleteEntity(1);

        $this->assertIndexPageEntityCount(3);
    }

    public function testCannotCreateinvalidRock()
    {
        $this->visitBackOffice(
            userEmail: 'admin@fixture.com',
        );

        $this->client->request('GET', '/admin/fr/rock');

        $this->assertIndexPageEntityCount(4);

        $this->client->clickLink('Créer Rocher');

        $this->assertSelectorTextContains('h1', 'Créer "Rocher"');

        $boulderAreaRepository = static::getContainer()->get(BoulderAreaRepository::class);
        $petitParadis = $boulderAreaRepository->findOneBy(['name' => 'Petit paradis']);
        $this->assertNotNull($petitParadis);

        $this->client->submitForm('Créer', []);

        $this->assertResponseStatusCodeSame(422);

        $this->assertInvalidFeedback(fieldName: 'location_latitude', message: 'Cette valeur ne doit pas être vide');
        $this->assertInvalidFeedback(fieldName: 'location_longitude', message: 'Cette valeur ne doit pas être vide');

        $this->client->submitForm('Créer', [
            'Rock[location][latitude]' => '-200',
            'Rock[location][longitude]' => '200',
        ]);

        $this->assertResponseStatusCodeSame(422);

        $this->assertInvalidFeedback(fieldName: 'location_latitude', message: 'Cette valeur doit être comprise entre -90 et 90.');
        $this->assertInvalidFeedback(fieldName: 'location_longitude', message: 'Cette valeur doit être comprise entre -180 et 180.');
    }

    public function testAdminCanCreateRock()
    {
        $this->visitBackOffice(
            userEmail: 'admin@fixture.com',
        );

        $this->client->request('GET', '/admin/fr/rock');

        $this->assertIndexPageEntityCount(4);

        $this->client->clickLink('Créer Rocher');

        $this->assertSelectorTextContains('h1', 'Créer "Rocher"');

        $boulderAreaRepository = static::getContainer()->get(BoulderAreaRepository::class);
        $petitParadis = $boulderAreaRepository->findOneBy(['name' => 'Petit paradis']);
        $this->assertNotNull($petitParadis);

        $this->client->submitForm('Créer', [
            'Rock[boulderArea]' => $petitParadis->getId(),
            'Rock[location][latitude]' => '50.0',
            'Rock[location][longitude]' => '28.0',
        ]);

        $this->assertResponseIsSuccessful();

        $this->client->request('GET', '/admin/fr/rock');

        $this->assertSelectorTextContains('h1', 'Rochers');

        $this->assertIndexPageEntityCount(5);
    }

    public function testContributorCannotUpdateOrDeleteRockIfNotCreatedByHim()
    {
        $this->visitBackOffice(
            userEmail: 'contributor@fixture.com',
        );

        $this->assertSelectorTextContains('table tbody', 1);

        $this->assertIndexEntityActionExists(Action::DETAIL, 1);
        $this->assertIndexEntityActionNotExists(Action::DELETE, 1);
        $this->assertIndexEntityActionNotExists(Action::EDIT, 1);
    }

    public function testContributorCanUpdateRockCreatedByHim()
    {
        $userEmail = 'contributor@fixture.com';
        $user = $this->findUser(email: $userEmail);

        $rockRepository = static::getContainer()->get(RockRepository::class);

        $rock = $rockRepository->find(1);
        $rock->setCreatedBy($user);

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $em->flush();

        $this->visitBackOffice(
            userEmail: $userEmail,
        );

        $crawler = $this->client->request('GET', '/admin/fr/rock');

        $this->assertIndexEntityActionExists(Action::EDIT, 1);

        $link = $crawler->filter($this->getIndexEntityActionSelector(Action::EDIT, 1))->link();
        $this->client->click($link);

        $this->assertSelectorTextContains('h1', 'Modifier Rocher');
    }

    public function testContributorCanDeleteRockCreatedByHim()
    {
        $userEmail = 'contributor@fixture.com';
        $user = $this->findUser(email: $userEmail);

        $rockRepository = static::getContainer()->get(RockRepository::class);

        $rock = $rockRepository->find(1);
        $rock->setCreatedBy($user);

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $em->flush();

        $this->visitBackOffice(
            userEmail: $userEmail,
        );

        $this->client->request('GET', '/admin/fr/rock');

        $this->assertIndexFullEntityCount(4);

        $this->assertIndexEntityActionExists(Action::DELETE, 1);

        $this->indexDeleteEntity(id: 1);

        $this->assertIndexFullEntityCount(3);
    }
}
