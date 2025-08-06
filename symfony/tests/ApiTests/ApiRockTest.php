<?php

namespace App\Tests\ApiTests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class ApiRockTest extends ApiTestCase
{
    public function setUp(): void
    {
        self::$alwaysBootKernel = false;
        self::bootKernel();
    }

    public function testListRocks()
    {
        $response = static::createClient()->request('GET', '/rocks');

        $this->assertResponseIsSuccessful();

        $this->assertJsonContains([
            'hydra:totalItems' => 4,
        ]);

        $rock = $response->toArray()['hydra:member'][0];
        $this->assertNotNull($rock['location']['latitude']);
        $this->assertNotNull($rock['location']['longitude']);
    }

    public function testGetRock()
    {
        $response = static::createClient()->request('GET', '/rocks/1');
        $this->assertCount(2, $response->toArray()['boulders']);
    }

    public function testCannotDeleteRock(): void
    {
        static::createClient()->request('DELETE', '/rocks/1');

        $this->assertResponseStatusCodeSame(405);
    }

    public function testCannotCreateRock(): void
    {
        static::createClient()->request('POST', '/rocks', [
            'json' => [],
        ]);

        $this->assertResponseStatusCodeSame(405);
    }

    public function testCannotEditRock(): void
    {
        static::createClient()->request('PUT', '/rocks/1', [
            'json' => [],
        ]);

        $this->assertResponseStatusCodeSame(405);

        static::createClient()->request('PATCH', '/rocks/1', [
            'json' => [],
        ]);

        $this->assertResponseStatusCodeSame(405);
    }
}
