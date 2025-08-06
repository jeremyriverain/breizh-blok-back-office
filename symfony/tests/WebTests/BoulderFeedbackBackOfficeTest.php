<?php

namespace App\Tests\WebTests;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use PHPUnit\Framework\Attributes\DataProvider;

class BoulderFeedbackBackOfficeTest extends BackOfficeTestCase
{
    #[DataProvider('viewers')]
    public function testAdminAndContributorCannotAccessBoulderFeedbackSection(
        string $email,
        int $ownId,
    ) {
        $this->visitBackOffice(
            userEmail: $email,
        );

        $this->client->request('GET', '/admin/fr/boulder-feedback');
        $this->assertResponseStatusCodeSame(403);

        $this->client->request('GET', '/admin/fr/boulder-feedback/1');
        $this->assertResponseStatusCodeSame(403);

        $this->client->request('GET', '/admin/fr/boulder-feedback/new');
        $this->assertResponseStatusCodeSame(403);

        $this->client->request('POST', '/admin/fr/boulder-feedback/1/delete');
        $this->assertResponseStatusCodeSame(403);
    }

    public static function viewers(): array
    {
        return [
            ['admin@fixture.com', 2],
            ['contributor@fixture.com', 3],
        ];
    }

    public function testSuperAdminCanViewAndDeleteBoulderFeedback()
    {
        $this->visitBackOffice(
            userEmail: 'super-admin@fixture.com',
        );

        $this->client->request('GET', '/admin/fr/boulder-feedback');
        $this->assertResponseStatusCodeSame(200);

        $this->assertIndexFullEntityCount(2);

        $this->assertIndexEntityActionExists(Action::DELETE, 1);
        $this->assertIndexEntityActionExists(Action::DETAIL, 1);
        $this->assertIndexEntityActionNotExists(Action::EDIT, 1);
        $this->assertGlobalActionNotExists(Action::NEW);
    }

    public function testSuperAdminCanDeleteBoulderFeedback()
    {
        $this->visitBackOffice(
            userEmail: 'super-admin@fixture.com',
        );

        $this->client->request('GET', '/admin/fr/boulder-feedback');
        $this->assertResponseStatusCodeSame(200);

        $this->assertIndexPageEntityCount(2);

        $this->assertIndexEntityActionExists(Action::DELETE, 1);

        $this->indexDeleteEntity(1);

        $this->assertIndexPageEntityCount(1);
    }

    public function testShowDetails()
    {
        $this->visitBackOffice(
            userEmail: 'super-admin@fixture.com',
        );

        $crawler = $this->client->request('GET', '/admin/fr/boulder-feedback');

        $link = $crawler->filter($this->getIndexEntityActionSelector(Action::DETAIL, 2))->link();
        $this->client->click($link);

        $this->assertSelectorTextContains('body', 'I disagree with the current grade.');
    }
}
