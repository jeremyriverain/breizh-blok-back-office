<?php 

namespace App\Tests\WebTests;

use App\Repository\BoulderAreaRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;

class BoulderAreaBackOfficeTest extends BackOfficeTestCase {
    public function testListBoulderAreas () {
        $this->visitBackOffice(
            userEmail: 'contributor@fixture.com',
        );

        $this->assertIndexFullEntityCount(6);
    }

    public function testSearchBoulderAreas () {
        $this->visitBackOffice(
            userEmail: 'contributor@fixture.com',
        );

        $this->indexSearch(query: 'Cremiou');

        $this->assertIndexFullEntityCount(1);
    }

    public function testShowDetails () {
        $crawler = $this->visitBackOffice(
            userEmail: 'contributor@fixture.com',
        );

        $boulderAreaRepository = static::getContainer()->get(BoulderAreaRepository::class);
        $boulderArea = $boulderAreaRepository->findOneBy(['name' => 'Cremiou']);

        $this->assertNotNull($boulderArea);

        $link = $crawler->filter($this->getIndexEntityActionSelector(Action::DETAIL, $boulderArea->getId()))->link();
        $this->client->click($link);

        $this->assertSelectorTextContains('h1', 'Cremiou');

        $this->assertSelectorCount(3, '.cy-boulders tbody tr');

        $this->assertSelectorTextContains('.cy-boulders tbody tr:nth-child(1)', "L'essai");
        $this->assertSelectorTextContains('.cy-boulders tbody tr:nth-child(2)', 'Monkey');
        $this->assertSelectorTextContains('.cy-boulders tbody tr:nth-child(3)', 'Stone');
    }

    public function testAdminCanDeleteBoulderArea() {
        $this->visitBackOffice(
            userEmail: 'admin@fixture.com',
        );

        $boulderAreaRepository = static::getContainer()->get(BoulderAreaRepository::class);

        $boulderArea = $boulderAreaRepository->find(1);

        $this->assertNotNull($boulderArea); 

        $this->assertSelectorTextContains('table tbody', $boulderArea->getName());

        $this->assertIndexFullEntityCount(6);

        $this->assertIndexEntityActionExists(Action::DELETE, $boulderArea->getId());

        $this->indexDeleteEntity(id: $boulderArea->getId());

        $this->assertIndexFullEntityCount(5);
        $this->assertSelectorTextNotContains('table tbody', $boulderArea->getName());

    }

    public function testContributorCannotUpdateOrDeleteBoulderAreaIfNotCreatedByHim() {
        $this->visitBackOffice(
            userEmail: 'contributor@fixture.com',
        );

        $boulderAreaRepository = static::getContainer()->get(BoulderAreaRepository::class);

        $boulderArea = $boulderAreaRepository->find(1);

        $this->assertNotNull($boulderArea); 

        $this->assertSelectorTextContains('table tbody', $boulderArea->getName());

        $this->assertIndexEntityActionNotExists(Action::DELETE, $boulderArea->getId()); 
        $this->assertIndexEntityActionNotExists(Action::EDIT, $boulderArea->getId()); 
    }

    public function testContributorCanUpdateBoulderAreaCreatedByHim() {

        $userEmail = 'contributor@fixture.com';
        $user = $this->findUser(email: $userEmail);

        $boulderAreaRepository = static::getContainer()->get(BoulderAreaRepository::class);

        $boulderArea = $boulderAreaRepository->find(1);
        $boulderArea->setCreatedBy($user);

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $em->flush();  

        $crawler = $this->visitBackOffice(
            userEmail: $userEmail,
        );

        $this->assertSelectorTextContains('table tbody', $boulderArea->getName());

        $this->assertIndexEntityActionExists(Action::EDIT, $boulderArea->getId()); 

        $link = $crawler->filter($this->getIndexEntityActionSelector(Action::EDIT, $boulderArea->getId()))->link();
        $crawler = $this->client->click($link);

        $this->assertSelectorTextContains('h1','Modifier Secteur');

        $nameInput = $crawler->filter("#".$this->getFormFieldIdValue('name'));
        $this->assertEquals($boulderArea->getName(), $nameInput->attr('value'));
    }

    public function testContributorCanDeleteBoulderAreaCreatedByHim() {

        $userEmail = 'contributor@fixture.com';
        $user = $this->findUser(email: $userEmail);

        $boulderAreaRepository = static::getContainer()->get(BoulderAreaRepository::class);

        $boulderArea = $boulderAreaRepository->find(1);
        $boulderArea->setCreatedBy($user);

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $em->flush();  

        $this->visitBackOffice(
            userEmail: $userEmail,
        );

        $this->assertSelectorTextContains('table tbody', $boulderArea->getName());

        $this->assertIndexFullEntityCount(6);

        $this->assertIndexEntityActionExists(Action::DELETE, $boulderArea->getId());

        $this->indexDeleteEntity(id: $boulderArea->getId());

        $this->assertIndexFullEntityCount(5);
        $this->assertSelectorTextNotContains('table tbody', $boulderArea->getName());

    }

    public function testCannotCreateInvalidBoulderArea() {
        $this->visitBackOffice(
            userEmail: 'contributor@fixture.com',
        );

        $this->client->request('GET', '/admin/fr/boulder-area');

        $this->client->clickLink('Créer Secteur');

        $this->assertSelectorTextContains('h1','Créer "Secteur"');
        
        $this->client->submitForm('Créer', []);

        $this->assertResponseStatusCodeSame(422);

        $this->assertFieldIsInvalid(fieldName: 'name');
    }

    public function testContributorCanCreateDepartment() {
        $this->visitBackOffice(
            userEmail: 'contributor@fixture.com',
        );

        $this->client->request('GET', '/admin/fr/boulder-area');

        $this->assertIndexPageEntityCount(6);

        $this->client->clickLink('Créer Secteur');

        $this->assertSelectorTextContains('h1','Créer "Secteur"');
        
        $this->client->submitForm('Créer', fieldValues: [
            'BoulderArea[name]' => 'foo',
            'BoulderArea[municipality]' => 1
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertIndexPageEntityCount(7);

        $this->assertSelectorTextContains('table tbody', 'foo');
    }
}