<?php 

namespace App\Tests\WebTests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BoulderAreaBackOfficeTest extends WebTestCase {
    public function testListBoulderAreas () {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('contributor@fixture.com');

        $client->loginUser($testUser);
        
        $client->followRedirects();
        $crawler = $client->request('GET', '/admin');

        $this->assertSelectorCount(6, "table tbody tr");

        $form = $crawler->filter('.form-action-search')->form();
        $form['query'] = 'Cremiou';
        $client->submit($form);
        
        $this->assertSelectorTextContains('table tbody tr:first-child', 'Cremiou');
        
        $this->assertSelectorTextContains('.cy-boulders', '3');
    }
}