<?php

namespace App\Tests\ApiTests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class ApiBoulderTest extends ApiTestCase
{
    public function setUp(): void
    {
        self::$alwaysBootKernel = false;
        self::bootKernel();
    }

    public function testListBoulders()
    {
        $response = static::createClient()->request('GET', '/boulders');

        $this->assertResponseIsSuccessful();

        $this->assertJsonContains([
            'hydra:totalItems' => 3,
        ]);

        $boulder = $response->toArray()['hydra:member'][0];
        $this->assertEquals('Stone', $boulder['name']);
        $this->assertEquals(true, $boulder['isUrban']);
        $this->assertEquals('5', $boulder['grade']['name']);
        $this->assertNotNull($boulder['rock']['location']['latitude']);
        $this->assertNotNull($boulder['rock']['location']['longitude']);
        $this->assertEquals('Cremiou', $boulder['rock']['boulderArea']['name']);
        $this->assertEquals('Kerlouan', $boulder['rock']['boulderArea']['municipality']['name']);
        $this->assertArrayNotHasKey('description', $boulder);
        $this->assertStringContainsString('%filter%', $boulder['lineBoulders'][0]['rockImage']['filterUrl']);
        $this->assertStringContainsString('.jpg', $boulder['lineBoulders'][0]['rockImage']['contentUrl']);
        $this->assertArrayNotHasKey('height', $boulder);
    }

    public function testListMarkers()
    {
        $response = static::createClient()->request(
            'GET',
            '/boulders?pagination=false&groups[]=Boulder:map',
            [
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);

        $this->assertEquals([
            [
                'id' => 1,
                'rock' => [
                    'location' => [
                        'latitude' => '48.673149748436',
                        'longitude' => '-4.3580819451625',
                    ],
                ],
            ],
            [
                'id' => 2,
                'rock' => [
                    'location' => [
                        'latitude' => '48.673149748436',
                        'longitude' => '-4.3580819451625',
                    ],
                ],
            ],
            [
                'id' => 3,
                'rock' => [
                    'location' => [
                        'latitude' => '48.673314470371',
                        'longitude' => '-4.357883461703',
                    ],
                ],
            ],
        ], $response->toArray());
    }

    public function testGetAdditionnalDetailsByPassingRelevantGroups()
    {
        $response = static::createClient()->request('GET', '/boulders?pagination=false&groups[]=Boulder:read&groups[]=read&groups[]=Boulder:item-get');

        $this->assertResponseIsSuccessful();

        $boulder = $response->toArray()['hydra:member'][0];
        $this->assertEquals('Stone', $boulder['name']);
        $this->assertEquals('Un rétablissement sur 2 bonnes réglettes', $boulder['description']);
        $this->assertNotNull($boulder['rock']['location']['latitude']);
    }

    public function testGetBoulder()
    {
        $response = static::createClient()->request('GET', '/boulders/1');

        $this->assertResponseIsSuccessful();

        $boulder = $response->toArray();
        $this->assertEquals('Stone', $boulder['name']);
        $this->assertEquals(true, $boulder['isUrban']);
        $this->assertEquals('5', $boulder['grade']['name']);
        $this->assertNotNull($boulder['rock']['location']['latitude']);
        $this->assertNotNull($boulder['rock']['location']['longitude']);
        $this->assertEquals('Cremiou', $boulder['rock']['boulderArea']['name']);
        $this->assertEquals('Kerlouan', $boulder['rock']['boulderArea']['municipality']['name']);
        $this->assertNotNull($boulder['description']);
        $this->assertStringContainsString('%filter%', $boulder['lineBoulders'][0]['rockImage']['filterUrl']);
        $this->assertStringContainsString('.jpg', $boulder['lineBoulders'][0]['rockImage']['contentUrl']);
        $this->assertEquals(0, $boulder['height']['min']);
        $this->assertEquals(3, $boulder['height']['max']);
    }

    public function testCanSearchBouldersByEnteringTheirName()
    {
        $response = static::createClient()->request('GET', '/boulders?term=Onk');
        $this->assertEquals(1, $response->toArray()['hydra:totalItems']);
        $this->assertEquals('Monkey', $response->toArray()['hydra:member'][0]['name']);
    }

    public function testCanSearchBouldersByEnteringBoulderAreaName()
    {
        $response = static::createClient()->request('GET', '/boulders?term=cre');
        $this->assertEquals(3, $response->toArray()['hydra:totalItems']);
        $boulders = $response->toArray()['hydra:member'];

        for ($i = 0; $i < 3; ++$i) {
            $this->assertEquals('Cremiou', $boulders[$i]['rock']['boulderArea']['name']);
        }
    }

    public function testCanSearchBouldersByEnteringMunicipalityName()
    {
        $response = static::createClient()->request('GET', '/boulders?term=ker');
        $total = 3;
        $this->assertEquals($total, $response->toArray()['hydra:totalItems']);
        $boulders = $response->toArray()['hydra:member'];

        for ($i = 0; $i < $total; ++$i) {
            $this->assertEquals('Kerlouan', $boulders[$i]['rock']['boulderArea']['municipality']['name']);
        }

        $response = static::createClient()->request('GET', '/boulders?term=plaintel');
        $this->assertEquals(0, $response->toArray()['hydra:totalItems']);
    }

    public function testCannotDeleteBoulder(): void
    {
        static::createClient()->request('DELETE', '/boulders/1');

        $this->assertResponseStatusCodeSame(405);
    }

    public function testCannotCreateBoulder(): void
    {
        static::createClient()->request('POST', '/boulders', [
            'json' => [],
        ]);

        $this->assertResponseStatusCodeSame(405);
    }

    public function testCannotEditBoulder(): void
    {
        static::createClient()->request('PUT', '/boulders/1', [
            'json' => [],
        ]);

        $this->assertResponseStatusCodeSame(405);

        static::createClient()->request('PATCH', '/boulders/1', [
            'json' => [],
        ]);

        $this->assertResponseStatusCodeSame(405);
    }
}
