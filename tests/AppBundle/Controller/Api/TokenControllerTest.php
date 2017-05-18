<?php

namespace Tests\AppBundle\Controller\Api;

use AppBundle\Test\ApiTestCase;

class TokenControllerTest extends ApiTestCase
{
    public function testPOSTCreateToken()
    {
        $this->createUser('filanfisteku', 'I<3Pizza');

        $response = $this->client->post('/api/tokens', [
            'auth' => ['filanfisteku@foo.com', 'I<3Pizza']
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyExists(
            $response,
            'token'
        );
    }

    public function testPOSTTokenInvalidCredentials()
    {
        $this->createUser('filanfisteku', 'I<3Pizza');
        $response = $this->client->post('/api/tokens', [
            'auth' => ['filanfisteku@foo.com', 'IH8Pizza']
        ]);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('application/problem+json', $response->getHeader('Content-Type')[0]);
        $this->asserter()->assertResponsePropertyEquals($response, 'type', 'about:blank');
        $this->asserter()->assertResponsePropertyEquals($response, 'title', 'Unauthorized');
        $this->asserter()->assertResponsePropertyEquals($response, 'detail', 'Invalid credentials.');
    }

    public function testPOSTTokenInexistentUser()
    {
        $this->createUser('filanfisteku', 'I<3Pizza');
        //Authenticating with a non existing user, assert 404 response
        $response = $this->client->post('/api/tokens', [
            'auth' => ['dummyuser@foo.com', 'dummypass']
        ]);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testBadToken()
    {
        $response = $this->client->post('/api/blog', [
            'body' => '[]',
            'headers' => [
                'Authorization' => 'Bearer WRONG'
            ]
        ]);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('application/problem+json', $response->getHeader('Content-Type')[0]);
    }
}
