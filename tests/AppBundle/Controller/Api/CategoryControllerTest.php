<?php

/*
 *
 * (c) Evis Bregu <evis.bregu@gmail.com>
 *
 */

namespace Tests\AppBundle\Controller\Api;

use AppBundle\Test\ApiTestCase;

class CategoryControllerTest extends ApiTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->createUser('filanfisteku', 'I<3Pizza');
    }

    public function testGETBlogPostsByCategory()
    {
        $category = $this->createCategory('Awsome Category');

        for ($i = 0; $i < 25; ++$i) {
            $this->createBlogPostSingleCategory(
                [
                    'title' => 'Awsome Blog Post '.$i,
                    //"category" => "Awsome Category ".rand(1, 100),
                    'summary' => 'Lorem Ipsum Sit Amet',
                    'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum',
                    'isPublished' => true,
                    'publishedAt' => (new \DateTime('-1 month')),
                ], $category
            );
        }

        //First get the subfamily from its endpoint
        $response = $this->client->get('/api/v1.0/category/'.$category->getId(), [
            'headers' => $this->getAuthorizedHeaders('filanfisteku'),
        ]);
        $this->assertSame(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyExists($response, '_links.blogposts');

        //Get the link for the endpoint which lists a given categories blog posts
        $blogPostsLink = $this->asserter()->readResponseProperty($response, '_links.blogposts');
        $response = $this->client->get($blogPostsLink, [
            'headers' => $this->getAuthorizedHeaders('filanfisteku'),
        ]);

        $this->assertSame(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyExists($response, 'items');
    }
}
