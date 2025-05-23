<?php 

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use PHPUnit\Framework\Attributes\TestDox;

class ApiGradeTest extends ApiTestCase {

    public function setUp(): void
    {
        self::$alwaysBootKernel = false;
        self::bootKernel();
    }

    public function testListGrades () {
        $response = static::createClient()->request('GET', '/grades');

        $this->assertResponseIsSuccessful();

        $this->assertJsonContains([
            'hydra:totalItems' => 22,
        ]);

        $grade = $response->toArray()['hydra:member'][0];
        $this->assertEquals('4', $grade['name']);
    }

    #[TestDox(<<<EOD
    Given I request /grades?pagination=false&order[name]=asc&exists[boulders]=true
    Then it returns grades containing boulders
    EOD)]
    public function testListGradesContainingBoulders() {
        $response = static::createClient()->request('GET', '/grades?pagination=false&order[name]=asc&exists[boulders]=true');

        $this->assertJsonContains([
            'hydra:totalItems' => 3,
        ]);

        $grades = $response->toArray()['hydra:member'];
        $this->assertEquals('5', $grades[0]['name']);
        $this->assertEquals('6a', $grades[1]['name']);
        $this->assertEquals('6c', $grades[2]['name']);
    }

    public function testGetGrade() {
        $response = static::createClient()->request('GET', '/grades/1');
        $this->assertEquals('4', $response->toArray()['name']);
    }

    public function testCannotDeleteGrade(): void
    {
        static::createClient()->request('DELETE', "/grades/1");

        $this->assertResponseStatusCodeSame(405);
    }
    
    public function testCannotCreateGrade(): void
    {
        static::createClient()->request('POST', "/grades", [
            'json' => []
        ]);

        $this->assertResponseStatusCodeSame(405);
    }

    public function testCannotEditGrade(): void
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