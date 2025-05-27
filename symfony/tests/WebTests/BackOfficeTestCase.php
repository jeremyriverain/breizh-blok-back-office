<?php

namespace App\Tests\WebTests;

use App\Entity\User;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestActions;
use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestIndexAsserts;
use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestUrlGeneration;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class BackOfficeTestCase extends WebTestCase {

    use CrudTestActions;
    use CrudTestIndexAsserts;
    use CrudTestUrlGeneration;

    protected KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();
    }

    public function visitBackOffice(string $userEmail, ?string $locale = 'fr'): Crawler {

        $this->client->disableReboot();

        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail($userEmail);

        $this->client->loginUser($testUser);
        
        $this->client->followRedirects();
        return $this->client->request('GET', "/admin/$locale");
    }

    public function indexSearch (string $query): Crawler {
        $form = $this->client->getCrawler()->filter('.form-action-search')->form();
        $form['query'] = $query;
        return $this->client->submit($form);
    }

    public function indexDeleteEntity(int $id) {
        $crawler = $this->client->getCrawler();
        $uri = $crawler->getUri();
        $link = $crawler->filter($this->getIndexEntityActionSelector(Action::DELETE, $id))->link();

        $form = $crawler->filter('#delete-form')->form();
        $form->getNode()->setAttribute(
            'action',
           $link->getUri() 
        );
        $this->client->submit($form);
        $this->client->request('GET', $uri);
    }

    public function findUser(string $email): User {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => $email]);
        $this->assertNotNull($user);
        return $user;
    }

    public function assertFieldIsInvalid(string $fieldName) {
        $this->assertSelectorExists($this->getFormFieldSelector($fieldName).'.is-invalid');
    }

    public function assertInvalidFeedback(string $fieldName, string $message) {
        $selector = $this->getFormFieldSelector($fieldName). ' + .invalid-feedback';
        $this->assertSelectorExists($selector);
        $this->assertSelectorTextContains($selector, $message);
    }
}

