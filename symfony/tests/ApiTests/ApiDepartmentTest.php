<?php
namespace App\Tests\ApiTests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use PHPUnit\Framework\Attributes\TestDox;

class ApiDepartmentTest extends ApiTestCase
{
    public function setUp(): void
    {
        self::$alwaysBootKernel = false;
        self::bootKernel();
    }

    public function testListDepartments(): void
    {
        $response = static::createClient()->request('GET', '/departments');

        $this->assertResponseIsSuccessful();

        $this->assertJsonContains([
            'hydra:totalItems' => 1,
        ]);

        $department = $response->toArray()['hydra:member'][0];
        
        $this->assertEquals('Finistère', $department['name']);
        $this->assertCount(2, $department['municipalities']);

        $municipality = $department['municipalities'][0];
        $this->assertEquals('Kerlouan', $municipality['name']);
        $this->assertCount(5, $municipality['boulderAreas']);

        $boulderArea = $municipality['boulderAreas'][0];
        $this->assertEquals('Bivouac', $boulderArea['name']);
    }

    #[TestDox(<<<EOD
    Given I request /departments?exists[municipalities.boulderAreas.rocks.boulders]=true
    Then it returns only departments, municipalities and boulder areas containing boulders
    EOD)] 
    public function testListDepartmentsWithExistingBoulders(): void
    {
        $response = static::createClient()->request('GET', "/departments?exists[municipalities.boulderAreas.rocks.boulders]=true");

        $this->assertResponseIsSuccessful();

        $this->assertJsonContains([
            'hydra:totalItems' => 1,
        ]);

        $department = $response->toArray()['hydra:member'][0];
        
        $this->assertEquals('Finistère', $department['name']);
        $this->assertCount(1, $department['municipalities']);

        $municipality = $department['municipalities'][0];
        $this->assertEquals('Kerlouan', $municipality['name']);
        $this->assertCount(2, $municipality['boulderAreas']);

        $boulderArea = $municipality['boulderAreas'][0];
        $this->assertEquals('Cremiou', $boulderArea['name']);
    }

    public function testGetDepartment(): void
    {
        $response = static::createClient()->request('GET', '/departments/1');

        $this->assertResponseIsSuccessful();

        $this->assertEquals('Finistère', $response->toArray()['name']);

    }

    public function testCannotDeleteDepartment(): void
    {
        static::createClient()->request('DELETE', "/departments/1");

        $this->assertResponseStatusCodeSame(405);
    }
    
    public function testCannotCreateDepartment(): void
    {
        static::createClient()->request('POST', "/departments", [
            'json' => []
        ]);

        $this->assertResponseStatusCodeSame(405);
    }

    public function testCannotEditDepartment(): void
    {
        static::createClient()->request('PUT', "/departments/1", [
            'json' => []
        ]);

        $this->assertResponseStatusCodeSame(405);

        static::createClient()->request('PATCH', "/departments/1", [
            'json' => []
        ]);

        $this->assertResponseStatusCodeSame(405);
    }

}