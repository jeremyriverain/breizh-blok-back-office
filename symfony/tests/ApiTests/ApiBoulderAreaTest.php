<?php

namespace App\Tests\ApiTests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class ApiBoulderAreaTest extends ApiTestCase {

    public function setUp(): void
    {
        self::$alwaysBootKernel = false;
        self::bootKernel();
    }

    public function testListBoulderAreas () {
        $response = static::createClient()->request('GET', '/boulder_areas');

        $this->assertResponseIsSuccessful();

        $this->assertJsonContains([
            'hydra:totalItems' => 6,
        ]);

        $boulderArea = $response->toArray()['hydra:member'][0];
        $this->assertNotNull($boulderArea['name']);
        $this->assertArrayNotHasKey('parkingLocation', $boulderArea);
        $this->assertArrayNotHasKey('numberOfBouldersGroupedByGrade', $boulderArea);
    }

    public function testGetBoulderArea() {
        $response = static::createClient()->request('GET', '/boulder_areas/1');
        $boulderArea = $response->toArray();
        $this->assertEquals('Cremiou', $boulderArea['name']);
        $this->assertEquals('Kerlouan', $boulderArea['municipality']['name']);
        $this->assertEquals([
            '5' => 1,
            '6a' => 1,
            '6c' => 1,
        ], $boulderArea['numberOfBouldersGroupedByGrade']);
        
    }

    public function testParkingLocationIsReturnedIfNotNull() {
        $response = static::createClient()->request('GET', '/boulder_areas/1');
        $boulderArea = $response->toArray();
        $this->assertEquals('Cremiou', $boulderArea['name']);
        $this->assertNotNull($boulderArea['parkingLocation']['latitude']);
        $this->assertNotNull($boulderArea['parkingLocation']['longitude']);  
    }

    public function testParkingLocationPropertyIsUndefinedIfItDoesNotExist() {
        $response = static::createClient()->request('GET', '/boulder_areas/2');
        $boulderArea = $response->toArray();
        $this->assertEquals('Petit paradis', $boulderArea['name']);
        $this->assertArrayNotHasKey('parkingLocation', $boulderArea);
    }

    public function testCannotDeleteBoulderArea(): void
    {
        static::createClient()->request('DELETE', "/boulder_areas/1");

        $this->assertResponseStatusCodeSame(405);
    }
    
    public function testCannotCreateBoulderArea(): void
    {
        static::createClient()->request('POST', "/boulder_areas", [
            'json' => []
        ]);

        $this->assertResponseStatusCodeSame(405);
    }

    public function testCannotEditBoulderArea(): void
    {
        static::createClient()->request('PUT', "/boulder_areas/1", [
            'json' => []
        ]);

        $this->assertResponseStatusCodeSame(405);

        static::createClient()->request('PATCH', "/boulder_areas/1", [
            'json' => []
        ]);

        $this->assertResponseStatusCodeSame(405);
    }
}