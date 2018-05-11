<?php
/**
 * Created by Evis Bregu <evis.bregu@gmail.com>.
 * Date: 5/11/18
 * Time: 12:46 PM
 */

namespace Tests\AppBundle\Controller\Api;

use AppBundle\Test\ApiTestCase;

class FavoriteControllerTest extends ApiTestCase
{
    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function setUp()
    {
        parent::setUp();
        $this->createUser('filanfisteku', 'I<3Pizza');
    }

    /**
     * @throws \Exception
     */
    public function testPostFavorite()
    {
        $data = [
            'author' => 'maluba',
            'createdAt' => '2018-05-11T08:39:23.000Z',
            'createdAtI' => 1526027963,
            'numComments' => 20,
            'objectID' => '123456789',
            'points' => 35,
            'storyText' => null,
            'title' => 'Super Awsome Favorite Article',
            'url' => 'http://www.dummy.com'
        ];

        $response = $this->client->post(
            '/api/v2.0/favorites/',
            [
                'body' => json_encode($data),
                'headers' => $this->getAuthorizedHeaders('filanfisteku'),
            ]
        );

        $this->assertSame('application/json', $response->getHeader('Content-Type')[0]);

        $this->assertSame(201, $response->getStatusCode());
        $finishedData = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('title', $finishedData);
        $this->assertSame('Super Awsome Favorite Article', $finishedData['title']);
        $this->asserter()->assertResponsePropertyContains($response, 'user', 'filanfisteku');
    }
}