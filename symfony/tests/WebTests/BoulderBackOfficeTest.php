<?php

namespace App\Tests\WebTests;

use App\Controller\BoulderCrudController;
use App\Controller\DashboardController;
use App\Repository\BoulderAreaRepository;
use App\Repository\BoulderRepository;
use App\Repository\HeightBoulderRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use PHPUnit\Framework\Attributes\DataProvider;

class BoulderBackOfficeTest extends BackOfficeTestCase
{
    public function testListRocks()
    {
        $this->visitBackOffice(
            userEmail: 'contributor@fixture.com',
        );

        $this->client->request('GET', '/admin/fr/boulder');
        $this->assertResponseStatusCodeSame(200);

        $this->assertIndexFullEntityCount(5);
    }

    public function testFilterBouldersByBoulderArea()
    {
        $this->visitBackOffice(
            userEmail: 'contributor@fixture.com',
        );

        $boulderAreaRepository = static::getContainer()->get(BoulderAreaRepository::class);
        $menezHam = $boulderAreaRepository->findOneBy(['name' => 'Menez Ham']);

        $this->assertNotNull($menezHam);

        $this->client->request(
            'GET',
            '/admin/fr/boulder?filters[boulderArea]='.$menezHam->getId()
        );

        $this->assertIndexFullEntityCount(1);

        $this->assertSelectorTextContains('table tbody tr:nth-child(1)', 'Menez Ham');
    }

    public function testFilterUrbanBoulders()
    {
        $this->visitBackOffice(
            userEmail: 'contributor@fixture.com',
        );

        $boulderRepository = static::getContainer()->get(BoulderRepository::class);
        $urbanBoulders = $boulderRepository->findBy(['isUrban' => true]);

        $this->assertCount(1, $urbanBoulders);
        $this->client->request(
            'GET',
            '/admin/fr/boulder?filters[isUrban]=1'
        );

        $this->assertIndexFullEntityCount(1);

        $this->assertSelectorTextContains(
            'table tbody tr:nth-child(1)',
            $urbanBoulders[0]->getName()
        );
    }

    public function testFilterHeightBoulder()
    {
        $this->visitBackOffice(
            userEmail: 'contributor@fixture.com',
        );

        $heightBoulderRepository = static::getContainer()->get(HeightBoulderRepository::class);
        $heightBoulder = $heightBoulderRepository->findOneBy(['max' => 3]);
        $this->assertNotNull($heightBoulder);

        $crawler = $this->client->request(
            'GET',
            $this->generateFilterRenderUrl(
                dashboardFqcn: DashboardController::class,
                controllerFqcn: BoulderCrudController::class
            )
        );

        $form = $crawler->filter('form[name="filters"]')->form();

        $form['filters'] = [
            'height' => [
                'comparison' => '=',
                'value' => $heightBoulder->getId(),
            ],
        ];

        $this->client->submit($form);

        $this->assertIndexFullEntityCount(1);

        $this->assertSelectorTextContains(
            'table tbody tr:nth-child(1)',
            'Stone'
        );
    }

    public function testSearchBoulders()
    {
        $this->visitBackOffice(
            userEmail: 'contributor@fixture.com',
        );

        $this->client->request(
            'GET',
            '/admin/fr/boulder'
        );

        $this->indexSearch(query: 'Stone');

        $this->assertIndexFullEntityCount(1);

        $this->assertSelectorTextContains(
            'table tbody tr:nth-child(1)',
            'Stone'
        );
    }

    public function testShowDetails()
    {
        $this->visitBackOffice(
            userEmail: 'contributor@fixture.com',
        );

        $crawler = $this->client->request(
            'GET',
            '/admin/fr/boulder'
        );

        $boulderRepository = static::getContainer()->get(BoulderRepository::class);
        $boulder = $boulderRepository->findOneBy(['name' => 'Stone']);

        $this->assertNotNull($boulder);

        $link = $crawler->filter($this->getIndexEntityActionSelector(Action::DETAIL, $boulder->getId()))->link();
        $this->client->click($link);

        $this->assertSelectorTextContains('h1', 'Stone');

        $this->assertSelectorTextContains('.cy-height .field-label', 'Hauteur');
        $this->assertSelectorTextContains('.cy-height .field-value', 'Moins de 3m');
    }

    public function testContributorCannotUpdateOrDeleteBoulderIfNotCreatedByHim()
    {
        $this->visitBackOffice(
            userEmail: 'contributor@fixture.com',
        );

        $this->client->request(
            'GET',
            '/admin/fr/boulder'
        );

        $boulderRepository = static::getContainer()->get(BoulderRepository::class);

        $boulder = $boulderRepository->find(1);

        $this->assertNotNull($boulder);

        $this->assertSelectorTextContains('table tbody', $boulder->getName());

        $this->assertGlobalActionExists(Action::NEW);
        $this->assertIndexEntityActionNotExists(Action::DELETE, $boulder->getId());
        $this->assertIndexEntityActionNotExists(Action::EDIT, $boulder->getId());
    }

    public function testContributorCanUpdateBoulderCreatedByHim()
    {
        $userEmail = 'contributor@fixture.com';
        $user = $this->findUser(email: $userEmail);

        $boulderRepository = static::getContainer()->get(BoulderRepository::class);

        $boulder = $boulderRepository->find(1);
        $boulder->setCreatedBy($user);

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $em->flush();

        $this->visitBackOffice(
            userEmail: $userEmail,
        );

        $crawler = $this->client->request(
            'GET',
            '/admin/fr/boulder'
        );

        $this->assertSelectorTextContains('table tbody', $boulder->getName());

        $this->assertIndexEntityActionExists(Action::EDIT, $boulder->getId());

        $link = $crawler->filter($this->getIndexEntityActionSelector(Action::EDIT, $boulder->getId()))->link();
        $crawler = $this->client->click($link);

        $this->assertSelectorTextContains('h1', 'Modifier Bloc');

        $this->assertFormFieldNotExists('isDisabled');
    }

    public function testAdminCanUpdateAnyBoulder()
    {
        $userEmail = 'admin@fixture.com';

        $this->visitBackOffice(
            userEmail: $userEmail,
        );

        $crawler = $this->client->request(
            'GET',
            '/admin/fr/boulder'
        );

        $link = $crawler->filter($this->getIndexEntityActionSelector(Action::EDIT, 1))->link();
        $crawler = $this->client->click($link);

        $this->assertSelectorTextContains('h1', 'Modifier Bloc');

        $this->assertFormFieldNotExists('isDisabled');
    }

    public function testContributorCanUpdateLineOfItsOwnBoulders()
    {
        $userEmail = 'contributor@fixture.com';
        $user = $this->findUser(email: $userEmail);

        $boulderRepository = static::getContainer()->get(BoulderRepository::class);

        $boulder = $boulderRepository->find(1);

        $this->assertNotNull($boulder->getRock());

        $boulder->setCreatedBy($user);

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $em->flush();

        $this->visitBackOffice(
            userEmail: $userEmail,
        );

        $crawler = $this->client->request(
            'GET',
            '/admin/fr/boulder'
        );

        $this->assertSelectorTextContains('table tbody', $boulder->getName());

        $this->assertIndexEntityActionExists('drawLine', $boulder->getId());

        $link = $crawler->filter($this->getIndexEntityActionSelector('drawLine', $boulder->getId()))->link();
        $crawler = $this->client->click($link);

        $this->assertSelectorTextContains('h1', 'Ligne du bloc');
    }

    public function testContributorCanDeleteBoulderCreatedByHim()
    {
        $userEmail = 'contributor@fixture.com';
        $user = $this->findUser(email: $userEmail);

        $boulderRepository = static::getContainer()->get(BoulderRepository::class);

        $boulder = $boulderRepository->find(1);
        $boulder->setCreatedBy($user);

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $em->flush();

        $this->visitBackOffice(
            userEmail: $userEmail,
        );

        $this->client->request(
            'GET',
            '/admin/fr/boulder'
        );

        $this->assertSelectorTextContains('table tbody', $boulder->getName());

        $this->assertIndexFullEntityCount(5);

        $this->assertIndexEntityActionExists(Action::DELETE, $boulder->getId());

        $this->indexDeleteEntity(id: $boulder->getId());

        $this->assertIndexFullEntityCount(4);
        $this->assertSelectorTextNotContains('table tbody', $boulder->getName());
    }

    public function testUpdatedFieldIsFilledIfBoulderIsModified()
    {
        $this->visitBackOffice(
            userEmail: 'super-admin@fixture.com',
        );

        $crawler = $this->client->request(
            'GET',
            '/admin/fr/boulder'
        );

        $link = $crawler->filter($this->getIndexEntityActionSelector(Action::DETAIL, 1))->link();
        $crawler = $this->client->click($link);

        $this->assertSelectorTextContains('h1', 'Stone');

        $this->assertSelectorTextContains('.cy_updated_by .field-label', 'Mis à jour par');
        $this->assertSelectorTextContains('.cy_updated_by .field-value', 'Aucun');

        $this->client->clickLink('Modifier');

        $this->assertSelectorTextContains('h1', 'Modifier Bloc');

        $this->client->submitForm('Sauvegarder les modifications',
            fieldValues: ['Boulder[name]' => 'foo']
        );

        $this->assertResponseIsSuccessful();

        $crawler = $this->client->request(
            'GET',
            '/admin/fr/boulder'
        );

        $link = $crawler->filter($this->getIndexEntityActionSelector(Action::DETAIL, 1))->link();
        $crawler = $this->client->click($link);

        $this->assertSelectorTextContains('h1', 'foo');

        $this->assertSelectorTextContains('.cy_updated_by .field-label', 'Mis à jour par');
        $this->assertSelectorTextContains('.cy_updated_by .field-value', 'super-admin@fixture.com');
    }

    public function testCannotCreateInvalidBoulder()
    {
        $this->visitBackOffice(
            userEmail: 'contributor@fixture.com',
        );

        $this->client->request('GET', '/admin/fr/boulder');

        $this->client->clickLink('Créer Bloc');

        $this->assertSelectorTextContains('h1', 'Créer "Bloc"');

        $this->client->submitForm('Créer', []);

        $this->assertResponseStatusCodeSame(422);

        $this->assertInvalidFeedback(
            fieldName: 'name',
            message: 'Cette valeur ne doit pas être vide'
        );
    }

    public function testCanCreateBoulder()
    {
        $this->visitBackOffice(
            userEmail: 'admin@fixture.com',
        );

        $this->client->request('GET', '/admin/fr/boulder');

        $this->assertIndexFullEntityCount(5);
        $this->assertSelectorTextNotContains('table tbody', 'bar');

        $this->client->clickLink('Créer Bloc');

        $this->assertSelectorTextContains('h1', 'Créer "Bloc"');

        $this->client->submitForm('Créer', fieldValues: [
            'Boulder[name]' => 'bar',
            'Boulder[rock]' => 1,
        ]);

        $this->assertResponseIsSuccessful();

        $this->client->request('GET', '/admin/fr/boulder');

        $this->assertIndexFullEntityCount(6);
        $this->assertSelectorTextContains('table tbody', 'bar');
    }

    public function testAdminCanDeleteBoulder()
    {
        $this->visitBackOffice(
            userEmail: 'admin@fixture.com',
        );

        $this->client->request('GET', '/admin/fr/boulder');

        $boulderRepository = static::getContainer()->get(BoulderRepository::class);

        $boulder = $boulderRepository->find(1);

        $this->assertNotNull($boulder);

        $this->assertSelectorTextContains('table tbody', $boulder->getName());

        $this->assertIndexFullEntityCount(5);

        $this->assertIndexEntityActionExists(Action::DELETE, $boulder->getId());

        $this->indexDeleteEntity(id: $boulder->getId());

        $this->assertIndexFullEntityCount(4);
        $this->assertSelectorTextNotContains('table tbody', $boulder->getName());
    }

    public function testCannotDrawLineIfNoPictureAssociatedToTheRock()
    {
        $this->visitBackOffice(
            userEmail: 'admin@fixture.com',
        );

        $crawler = $this->client->request('GET', '/admin/fr/boulder');

        $boulderRepository = static::getContainer()->get(BoulderRepository::class);

        $boulder = $boulderRepository->find(3);

        $this->assertCount(0, $boulder->getRock()->getPictures());

        $this->assertIndexEntityActionExists('drawLine', $boulder->getId());

        $link = $crawler->filter($this->getIndexEntityActionSelector('drawLine', 3))->link();
        $crawler = $this->client->click($link);

        $this->assertSelectorTextContains('h1', 'Ligne du bloc');

        $this->assertSelectorTextContains(
            'p',
            "Vous ne pouvez pas dessiner la ligne de bloc car aucune photo n'est associée au rocher."
        );
    }

    #[DataProvider('viewers')]
    public function testAdminAndContributorCannotDisableBoulder(
        string $email,
    ) {
        $this->visitBackOffice(
            userEmail: $email,
        );

        $this->client->request('GET', '/admin/fr/boulder');

        $this->assertBooleanFieldIsNotRenderedAsSwitch('isDisabled');

        $this->client->clickLink('Créer Bloc');

        $this->assertFormFieldNotExists('isDisabled');
    }

    public static function viewers(): array
    {
        return [
            ['admin@fixture.com'],
            ['contributor@fixture.com'],
        ];
    }

    public function testSuperAdminCanDisableBoulder()
    {
        $userEmail = 'super-admin@fixture.com';

        $this->visitBackOffice(
            userEmail: $userEmail,
        );

        $crawler = $this->client->request(
            'GET',
            '/admin/fr/boulder'
        );

        $this->assertBooleanFieldIsRenderedAsSwitch('isDisabled');

        $link = $crawler->filter($this->getIndexEntityActionSelector(Action::EDIT, 1))->link();
        $crawler = $this->client->click($link);

        $this->assertSelectorTextContains('h1', 'Modifier Bloc');

        $this->assertFormFieldExists('isDisabled');
    }
}
