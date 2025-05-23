<?php
namespace App\Tests\Api;

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
        $response = static::createClient()->request('GET', '/departments');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            'hydra:totalItems' => 1,
        ]);

        $department = $response->toArray()['hydra:member'][0];
        
        $this->assertEquals($department['name'], 'Finistère');
        $this->assertCount(2, $department['municipalities']);

        $municipality = $department['municipalities'][0];
        $this->assertEquals($municipality['name'], 'Kerlouan');
        $this->assertCount(5, $municipality['boulderAreas']);

        $boulderArea = $municipality['boulderAreas'][0];
        $this->assertEquals($boulderArea['name'], 'Bivouac');
    }


    public function testGetItem(): void
    {
        static::createClient()->request('GET', '/departments/1');

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertResponseIsSuccessful();

    }

}