<?php 

namespace App\Tests\ApiTests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Repository\BoulderRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class ApiLineBoulderTest extends ApiTestCase {
    public function setUp(): void
    {
        self::$alwaysBootKernel = false;
        self::bootKernel();
    }

    public function testAccessIsDeniedIfAuthenticatedWithRoleUser() {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('user@fixture.com');

        $client->loginUser($testUser);

        $client->request('GET', '/admin/line_boulders');
        $this->assertResponseStatusCodeSame(403);
    }

    public function testLineBoulders() {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('contributor@fixture.com');

        $client->loginUser($testUser);

        $response = $client->request('GET', '/admin/line_boulders');
        $this->assertEquals(2, $response->toArray()['hydra:totalItems']);
    }

    public function testGetLineBoulder() {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('contributor@fixture.com');

        $client->loginUser($testUser);

        $response = $client->request('GET', '/admin/line_boulders/1');

        $lineBoulder = $response->toArray();

        $this->assertNotNull($lineBoulder['arrArrPoints']);
        $this->assertNotNull($lineBoulder['smoothLine']);
        $this->assertArrayHasKey('@id', $lineBoulder['rockImage']);
    }

    public function testAdminCanDeleteLineBoulder() {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('admin@fixture.com');

        $client->loginUser($testUser);

        $client->request('DELETE', '/admin/line_boulders/1');
        $this->assertResponseStatusCodeSame(204);
    }

    public function testALineBoulderRequiresBoulderAndSmoothLineAndPoints() {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('admin@fixture.com');

        $client->loginUser($testUser); 

        $response = $client->request('POST', '/admin/line_boulders', [
            'json' => []
        ]);

        $this->assertResponseStatusCodeSame(422);

        $violations = $response->toArray(throw: false);
        $this->assertStringContainsString(
            "boulder: Cette valeur ne doit pas être vide.",
            $violations['hydra:description']
        );

        $this->assertStringContainsString(
            "smoothLine: Cette valeur ne doit pas être vide.",
            $violations['hydra:description']
        );

        $this->assertStringContainsString(
            "arrArrPoints: Cette valeur ne doit pas être vide.",
            $violations['hydra:description']
        );
    }


    public function testALineBoulderShouldHaveAValidRockBoulderAssociation() {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('admin@fixture.com');

        $client->loginUser($testUser); 

        $response = $client->request('POST', '/admin/line_boulders', [
            'json' => [
                'boulder' => "/boulders/3",
                'rockImage' => "/media/1",
            ]
        ]);

        $this->assertResponseStatusCodeSame(422);

        $violations = $response->toArray(throw: false);
        $this->assertStringContainsString(
            "boulder: This boulder does not match with its rock associated",
            $violations['hydra:description']
        );

    }

    public function testCreateALineBoulder() {
        $client = static::createClient();
        $client->disableReboot();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('admin@fixture.com');

        $client->loginUser($testUser); 

        $client->request('DELETE', '/admin/line_boulders/1');

        $this->assertResponseStatusCodeSame(204);

        $client->request('POST', '/admin/line_boulders', [
            'json' => [
                'boulder' => "/boulders/1",
                'rockImage' => "/media/1",
                'arrArrPoints' => [[]],
                'smoothLine' => "M",
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);
    }

    public function testAdminCanDeleteALineBoulder() {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('admin@fixture.com');

        $client->loginUser($testUser); 

        $client->request('DELETE', '/admin/line_boulders/1');

        $this->assertResponseStatusCodeSame(204);
    }


    public function testEditLineBoulder() {
        $client = static::createClient();
        $client->disableReboot();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('admin@fixture.com');

        $client->loginUser($testUser);

        $response = $client->request('GET', '/admin/line_boulders/1');

        $expectedSmoothLine = 'M';

        $this->assertNotEquals($expectedSmoothLine, $response->toArray()['smoothLine']);

        $response = $client->request('PATCH', '/admin/line_boulders/1', [
            'json' => [
                'smoothLine' => $expectedSmoothLine,
            ],
            'headers' => [
                'Accept' => 'application/ld+json',
                'Content-Type' => 'application/merge-patch+json'
            ]
        ]);

        $this->assertResponseStatusCodeSame(200);

        $this->assertEquals($expectedSmoothLine, $response->toArray()['smoothLine']);
    }

    public function testCannotUpdateRockImageAndBoulderAfterCreation() {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('admin@fixture.com');

        $client->loginUser($testUser);

        $response = $client->request('PATCH', '/admin/line_boulders/1', [
            'json' => [
                'rockImage' => "/media/2",
                'boulder' => "/boulders/2"
            ],
            'headers' => [
                'Accept' => 'application/ld+json',
                'Content-Type' => 'application/merge-patch+json'
            ]
        ]);

        $this->assertResponseStatusCodeSame(200);

        $lineBoulder = $response->toArray();

        $this->assertEquals('/boulders/1', $lineBoulder['boulder']['@id']);
        $this->assertEquals('/media/1', $lineBoulder['rockImage']['@id']);
    }

    public function testContributorCannotDeleteLineBoulderNotCreatedByHim() {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('contributor@fixture.com');

        $client->loginUser($testUser);

        $client->request('DELETE', '/admin/line_boulders/1');
        $this->assertResponseStatusCodeSame(403);
    }

    public function testContributorCannotUpdateLineBoulderIfHeIsNotOwner() {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('contributor@fixture.com');

        $client->loginUser($testUser);

        $client->request('PATCH', '/admin/line_boulders/1', [
            'json' => [],
            'headers' => [
                'Accept' => 'application/ld+json',
                'Content-Type' => 'application/merge-patch+json'
            ]
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testContributorCanManageItsOwnLineBoulders() {

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('contributor@fixture.com');

        $boulderRepository = static::getContainer()->get(BoulderRepository::class);
        $boulder = $boulderRepository->find(1);
        $boulder->setCreatedBy($testUser);

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $em->flush();        

        $client = static::createClient();

        $client->disableReboot();

        $client->loginUser($testUser);

        $client->request('DELETE', '/admin/line_boulders/1');

        $this->assertResponseStatusCodeSame(204);

        $response = $client->request('POST', '/admin/line_boulders', [
            'json' => [
                'boulder' => "/boulders/1",
                'rockImage' => "/media/1",
                'arrArrPoints' => [[]],
                'smoothLine' => "M",
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);

        $lineBoulderId = $response->toArray()['@id'];

        $client->request('PATCH', $lineBoulderId, [
            'json' => [
                'smoothLine' => ' ',
            ],
            'headers' => [
                'Accept' => 'application/ld+json',
                'Content-Type' => 'application/merge-patch+json'
            ]
        ]);

        $this->assertResponseStatusCodeSame(200);

    }

}