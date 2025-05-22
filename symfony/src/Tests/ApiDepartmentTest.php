<?php
namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class ApiDepartmentTest extends ApiTestCase
{
    public function setUp(): void
    {
        self::$alwaysBootKernel = false;
        self::bootKernel();
    }

    

    public function testGetCollection(): void
    {
        static::createClient()->request('GET', '/departments');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            'hydra:totalItems' => 1,
        ]);

    }

    public function testGetItem(): void
    {
        static::createClient()->request('GET', '/departments/1');

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertResponseIsSuccessful();

    }

}