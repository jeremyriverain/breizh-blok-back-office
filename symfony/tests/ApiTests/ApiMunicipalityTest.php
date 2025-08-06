<?php

namespace App\Tests\ApiTests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class ApiMunicipalityTest extends ApiTestCase
{
    public function setUp(): void
    {
        self::$alwaysBootKernel = false;
        self::bootKernel();
    }

    public function testListMunicipalities(): void
    {
        $response = static::createClient()->request('GET', '/municipalities');

        $this->assertResponseIsSuccessful();

        $this->assertJsonContains([
            'hydra:totalItems' => 2,
        ]);
    }

    public function testGetMunicipality(): void
    {
        $response = static::createClient()->request('GET', '/municipalities/2');

        $this->assertResponseIsSuccessful();

        $municipality = $response->toArray();

        $this->assertEquals('Kerlouan', $municipality['name']);

        $this->assertGreaterThan(48, floatval($municipality['centroid']['latitude']));
        $this->assertLessThan(0, floatval($municipality['centroid']['longitude']));

        $this->assertCount(5, $municipality['boulderAreas']);

        $bivouac = $municipality['boulderAreas'][0];
        $this->assertEquals(0, $bivouac['numberOfBoulders']);
        $this->assertArrayNotHasKey('lowestGrade', $bivouac);
        $this->assertArrayNotHasKey('highestGrade', $bivouac);

        $cremiou = $municipality['boulderAreas'][1];
        $this->assertNotNull($cremiou['centroid']['latitude']);
        $this->assertNotNull($cremiou['centroid']['longitude']);
        $this->assertEquals(3, $cremiou['numberOfBoulders']);
        $this->assertEquals('5', $cremiou['lowestGrade']['name']);
        $this->assertEquals('6c', $cremiou['highestGrade']['name']);
    }

    public function testCannotDeleteMunicipality(): void
    {
        static::createClient()->request('DELETE', '/municipalities/1');

        $this->assertResponseStatusCodeSame(405);
    }

    public function testCannotCreateMunicipality(): void
    {
        static::createClient()->request('POST', '/municipalities', [
            'json' => [],
        ]);

        $this->assertResponseStatusCodeSame(405);
    }

    public function testCannotEditMunicipality(): void
    {
        static::createClient()->request('PUT', '/municipalities/1', [
            'json' => [],
        ]);

        $this->assertResponseStatusCodeSame(405);

        static::createClient()->request('PATCH', '/municipalities/1', [
            'json' => [],
        ]);

        $this->assertResponseStatusCodeSame(405);
    }
}
