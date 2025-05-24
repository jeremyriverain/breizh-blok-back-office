<?php

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PrivacyPolicyTest extends WebTestCase {
    public function testPrivacyPolicyIsAccessible () {
        
        static::createClient()->request('GET', '/privacy-policy');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1','POLITIQUE DE CONFIDENTIALITÉ DE BREIZH BLOK');
    }
}