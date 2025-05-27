<?php

namespace App\Tests\WebTests;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGeneratorInterface;
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
    protected AdminUrlGeneratorInterface $adminUrlGenerator;
    protected EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();
        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->adminUrlGenerator = $container->get(AdminUrlGenerator::class);
    }

    public function visitBackOffice(string $userEmail): Crawler {

        $this->client->disableReboot();

        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail($userEmail);

        $this->client->loginUser($testUser);
        
        $this->client->followRedirects();
        return $this->client->request('GET', '/admin');
    }

    public function searchResults (string $query): Crawler {
        $form = $this->client->getCrawler()->filter('.form-action-search')->form();
        $form['query'] = $query;
        return $this->client->submit($form);
    }
}

