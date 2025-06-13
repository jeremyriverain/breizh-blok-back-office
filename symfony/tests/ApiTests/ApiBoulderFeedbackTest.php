<?php

namespace App\Tests\ApiTests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Auth0\Symfony\Models\Stateless\User;

class ApiBoulderFeedbackTest extends ApiTestCase {
    public function setUp(): void
    {
        self::$alwaysBootKernel = false;
        self::bootKernel();
    }

    public function testAnonymousUserCannotViewBoulderFeedback() {
        static::createClient()->request('GET', '/boulder_feedbacks/1');

        $this->assertResponseStatusCodeSame(401);
    }

    public function testAuthenticatedUserCanViewBoulderFeedback() {

        $user = new User(data: ['user_id' => 'bar']);

        $client = static::createClient();
        $client->loginUser($user); 
        $response = $client->request('GET', '/boulder_feedbacks/2');

        $this->assertResponseIsSuccessful();

        $boulderFeedback = $response->toArray();
        $this->assertEquals('8a', $boulderFeedback['newGrade']['name']);
        $this->assertEquals('I disagree with the current grade.', $boulderFeedback['message']);
        $this->assertEquals('Monkey', $boulderFeedback['boulder']['name']);
        $this->assertEquals('Cremiou', $boulderFeedback['boulder']['rock']['boulderArea']['name']);
        $this->assertEquals('bar', $boulderFeedback['sentBy']);
        $this->assertNotNull($boulderFeedback['createdAt']);
        $this->assertArrayNotHasKey('newLocation', $boulderFeedback);
    }

    public function testAuthenticatedUserCanViewBoulderFeedback2() {

        $user = new User(data: ['user_id' => 'foo']);

        $client = static::createClient();
        $client->loginUser($user); 
        $response = $client->request('GET', '/boulder_feedbacks/1');

        $this->assertResponseIsSuccessful();

        $boulderFeedback = $response->toArray();
        $this->assertArrayNotHasKey('newGrade', $boulderFeedback);
        $this->assertArrayNotHasKey('message', $boulderFeedback);
        $this->assertEquals('Stone', $boulderFeedback['boulder']['name']);
        $this->assertEquals('Cremiou', $boulderFeedback['boulder']['rock']['boulderArea']['name']);
        $this->assertEquals('foo', $boulderFeedback['sentBy']);
        $this->assertNotNull($boulderFeedback['createdAt']);
        $this->assertEquals('45', $boulderFeedback['newLocation']['latitude']);
        $this->assertEquals('54', $boulderFeedback['newLocation']['longitude']);
    }

    public function testAuthenticatedUserCannotViewBoulderFeedbackNotCreatedByHim() {
        $user = new User(data: ['user_id' => 'foo']);

        $client = static::createClient();
        $client->loginUser($user); 
        $client->request('GET', '/boulder_feedbacks/2');

        $this->assertResponseStatusCodeSame(404); 
    }

    public function testAuthenticatedUserCanListItsOwnBoulderFeedbacks() {
        $user = new User(data: ['user_id' => 'foo']);

        $client = static::createClient();
        $client->loginUser($user); 
        $response = $client->request('GET', '/boulder_feedbacks');


        $this->assertResponseIsSuccessful();

        $this->assertJsonContains([
            'hydra:totalItems' => 1,
        ]);

        $boulderFeedback = $response->toArray()['hydra:member'][0];
        $this->assertArrayNotHasKey('newGrade', $boulderFeedback);
        $this->assertArrayNotHasKey('message', $boulderFeedback);
        $this->assertEquals('Stone', $boulderFeedback['boulder']['name']);
        $this->assertEquals('Cremiou', $boulderFeedback['boulder']['rock']['boulderArea']['name']);
        $this->assertEquals('foo', $boulderFeedback['sentBy']);
        $this->assertNotNull($boulderFeedback['createdAt']);
        $this->assertEquals('45', $boulderFeedback['newLocation']['latitude']);
        $this->assertEquals('54', $boulderFeedback['newLocation']['longitude']);
    }

    public function testCanCreateBoulderFeedbackWithMessage() {
        $user = new User(data: ['user_id' => 'foo']);

        $client = static::createClient();
        $client->loginUser($user); 
        $response = $client->request('POST', '/boulder_feedbacks', [
            'json' => [
                'boulder' => '/boulders/2',
                'message' => 'this is a message',
                'newGrade' => '/grades/1',
                'newLocation' => [
                    'latitude' => 20,
                    'longitude' => 21
                ]
            ]
        ]);


        $this->assertResponseStatusCodeSame(201);

        $boulderFeedback = $response->toArray();
        $this->assertEquals('4', $boulderFeedback['newGrade']['name']);
        $this->assertEquals('this is a message', $boulderFeedback['message']);
        $this->assertEquals('Monkey', $boulderFeedback['boulder']['name']);
        $this->assertEquals('Cremiou', $boulderFeedback['boulder']['rock']['boulderArea']['name']);
        $this->assertEquals('foo', $boulderFeedback['sentBy']);
        $this->assertNotNull($boulderFeedback['createdAt']);
        $this->assertEquals('20', $boulderFeedback['newLocation']['latitude']);
        $this->assertEquals('21', $boulderFeedback['newLocation']['longitude']);
    }

    public function testCannotCreateBoulderFeedbackIfBoulderPropertyIsNotFilled() {
        $user = new User(data: ['user_id' => 'foo']);

        $client = static::createClient();
        $client->loginUser($user); 
        $response = $client->request('POST', '/boulder_feedbacks', [
            'json' => [
                'message' => 'this is a message',
            ]
        ]);


        $this->assertResponseStatusCodeSame(422);

        $violations = $response->toArray(throw: false);

        $this->assertStringContainsString(
            "boulder: Cette valeur ne doit pas être vide.",
            $violations['hydra:description']
        );
    }

    public function testCannotCreateBoulderFeedbackIfThereIsNoFeedback() {
        $user = new User(data: ['user_id' => 'foo']);

        $client = static::createClient();
        $client->loginUser($user); 
        $response = $client->request('POST', '/boulder_feedbacks', [
            'json' => [
                'boulder' => '/boulders/1'
            ]
        ]);


        $this->assertResponseStatusCodeSame(422);

        $violations = $response->toArray(throw: false);

        $this->assertStringContainsString(
            "message: Au moins un des champs suivants doit être présent (newGrade, newLocation ou message)",
            $violations['hydra:description']
        );
    }
}