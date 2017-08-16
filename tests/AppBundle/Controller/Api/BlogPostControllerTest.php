<?php

/*
 *
 * (c) Evis Bregu <evis.bregu@gmail.com>
 *
 */

namespace Tests\AppBundle\Controller\Api;

use AppBundle\Test\ApiTestCase;
use GuzzleHttp\Psr7\Response;
use JMS\Serializer\SerializationContext;

class BlogPostControllerTest extends ApiTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->createUser('filanfisteku', 'I<3Pizza');
    }

    public function testPostBlogPost()
    {
        $title = 'Super Awsome Blog Post';
        $category = $this->createCategory('Category'.rand(1, 100));
        $summary = 'Lorem Ipsum Sit Amet';
        $content = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum';
        $isPublished = true;
        $publishedAt = (new \DateTime('-1 month'));

        $data = [
            'title' => $title,
            'category' => $category->getId(),
            'summary' => $summary,
            'content' => $content,
            'isPublished' => $isPublished,
            'publishedAt' => $publishedAt->format('Y-m-d'),
        ];

        $response = $this->client->post(
            '/api/v1.0/blog',
            [
                'body' => json_encode($data),
                'headers' => $this->getAuthorizedHeaders('filanfisteku'),
            ]
        );

        $this->assertSame('application/json', $response->getHeader('Content-Type')[0]);

        $this->assertSame(201, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Location'));
        $finishedData = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('title', $finishedData);
        $this->assertSame('Super Awsome Blog Post', $finishedData['title']);
        $this->asserter()->assertResponsePropertyContains($response, 'user', 'filanfisteku');
    }

    public function testReadEmptyResponse(){
        $response = new Response();

        $this->expectException("\Exception");
        $this->asserter()->readResponseProperty($response, "inexisten_property");
    }

    public function testGETBlogPost()
    {
        $category = 'Category '.rand(1, 100);
        $createdBlogPost = $this->createBlogPost(
            [
                'title' => 'Super Awsome Blog Post',
                'category' => $category,
                'summary' => 'Lorem Ipsum Sit Amet',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum',
                'isPublished' => true,
                'publishedAt' => (new \DateTime('-1 month')),
            ]
        );

        $response = $this->client->get('/api/v1.0/blog/'.$createdBlogPost->getId(), [
            'headers' => $this->getAuthorizedHeaders('filanfisteku'),
        ]);
        //$data = $response->json();

        $this->assertSame(200, $response->getStatusCode());

        $this->asserter()->assertResponsePropertiesExist(
            $response,
            [
                'title',
                'category',
                'summary',
                'content',
                'isPublished',
                'publishedAt',
            ]
        );

        $this->asserter()->assertResponsePropertyEquals($response, '_embedded.category.name', $category);
        $this->asserter()->assertResponsePropertyEquals($response, 'title', 'Super Awsome Blog Post');
        $this->asserter()->assertResponsePropertyEquals($response, 'isPublished', $createdBlogPost->getisPublished());
        $this->asserter()->assertResponsePropertyEquals(
            $response,
            '_links.self',
            $this->adjustUri('/api/v1.0/blog/'.$createdBlogPost->getId())
        );
    }

    public function testGETBlogPostDeep()
    {
        $createdGenus = $this->createBlogPost(
            [
                'title' => 'Super Awsome Blog Post',
                'category' => 'Category '.rand(1, 100),
                'summary' => 'Lorem Ipsum Sit Amet',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum',
                'isPublished' => true,
                'publishedAt' => (new \DateTime('-1 month')),
            ]
        );

        $response = $this->client->get('/api/v1.0/blog/'.$createdGenus->getId().'?deep=1', [
            'headers' => $this->getAuthorizedHeaders('filanfisteku'),
        ]);
        //$data = $response->json();

        $this->assertSame(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertiesExist(
            $response,
            [
                'category.name',
            ]
        );
    }

    public function testGETBlogPostCollection()
    {
        $this->createBlogPost(
            [
                'title' => 'Super Awsome Blog Post',
                'category' => 'Awsome Category',
                'summary' => 'Lorem Ipsum Sit Amet',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum',
                'isPublished' => true,
                'publishedAt' => (new \DateTime('-1 month')),
            ]
        );

        $this->createBlogPost(
            [
                'title' => 'Ultra Awsome Blog Post',
                'category' => 'Ultra Awsome Category',
                'summary' => 'Lorem Ipsum Sit Amet',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum',
                'isPublished' => true,
                'publishedAt' => (new \DateTime('-2 month')),
            ]
        );

        $response = $this->client->get('/api/v1.0/blog', [
            'headers' => $this->getAuthorizedHeaders('filanfisteku'),
        ]);
        //$data = $response->json();

        $this->assertSame(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyExists($response, 'items');
        $this->asserter()->assertResponsePropertyIsArray($response, 'items');

        $this->asserter()->assertResponsePropertyEquals($response, 'items[0].title', 'Super Awsome Blog Post');
        $this->asserter()->assertResponsePropertyEquals($response, 'items[1].title', 'Ultra Awsome Blog Post');
    }

    public function testGETBlogPostaCollectionPaginated()
    {
        $this->createBlogPost(
            [
                'title' => 'WILLNOTMATCH',
                'category' => 'Awsome Category 999',
                'summary' => 'Lorem Ipsum Sit Amet',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum',
                'isPublished' => true,
                'publishedAt' => (new \DateTime('-12 month')),
            ]
        );

        for ($i = 0; $i < 25; ++$i) {
            $this->createBlogPost(
                [
                    'title' => 'Super Awsome Blog Post '.$i,
                    'category' => 'Awsome Cateogory'.rand(1, 100),
                    'summary' => 'Lorem Ipsum Sit Amet',
                    'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum',
                    'isPublished' => true,
                    'publishedAt' => (new \DateTime('-'.rand(1, 24).' month')),
                ]
            );
        }

        $response = $this->client->get('/api/v1.0/blog?filter=awsome', [
            'headers' => $this->getAuthorizedHeaders('filanfisteku'),
        ]);

        $this->assertSame(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyExists($response, 'items');
        $this->asserter()->assertResponsePropertyIsArray($response, 'items');
        $this->asserter()->assertResponsePropertyEquals(
            $response,
            'items[5].title',
            'Super Awsome Blog Post 5'
        );
        $this->asserter()->assertResponsePropertyCount($response, 'items', 10);
        $this->asserter()->assertResponsePropertyEquals($response, 'count', 10);
        $this->asserter()->assertResponsePropertyEquals($response, 'total', 25);
        $this->asserter()->assertResponsePropertyExists($response, '_links.next');

        // page 2
        $nextLink = $this->asserter()->readResponseProperty($response, '_links.next');
        $response = $this->client->get($nextLink, [
            'headers' => $this->getAuthorizedHeaders('filanfisteku'),
        ]);

        $this->assertSame(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals(
            $response,
            'items[5].title',
            'Super Awsome Blog Post 15'
        );
        $this->asserter()->assertResponsePropertyEquals($response, 'count', 10);

        $lastLink = $this->asserter()->readResponseProperty($response, '_links.last');
        $response = $this->client->get($lastLink, [
            'headers' => $this->getAuthorizedHeaders('filanfisteku'),
        ]);

        $this->assertSame(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals(
            $response,
            'items[4].title',
            'Super Awsome Blog Post 24'
        );
        $this->asserter()->assertResponsePropertyDoesNotExist($response, 'items[5].title');
        $this->asserter()->assertResponsePropertyEquals($response, 'count', 5);
    }

    public function testPUTBlogPost()
    {
        $createdBlogPost = $this->createBlogPost(
            [
                'title' => 'Super Awsome Blog Post',
                'category' => 'Awsome Category',
                'summary' => 'Lorem Ipsum Sit Amet',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum',
                'isPublished' => true,
                'publishedAt' => (new \DateTime('-1 month')),
            ]
        );

        $createdBlogPost->setTitle('Ultra Awsome Blog Post');

        $context = new SerializationContext();
        $context->setSerializeNull(true);

        //Serialize only Default group, otherwise form validation will fail for category
        $groups = ['Default'];
        $context->setGroups($groups);

        $data = $this->getService('jms_serializer')->serialize($createdBlogPost, 'json', $context);
        $response = $this->client->put(
            '/api/v1.0/blog/'.$createdBlogPost->getId(),
            [
                'body' => $data,
                'headers' => $this->getAuthorizedHeaders('filanfisteku'),
            ]
        );
        $this->assertSame(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals($response, 'title', 'Ultra Awsome Blog Post');
        $this->asserter()->assertResponsePropertyEquals($response, 'summary', 'Lorem Ipsum Sit Amet');
    }

    public function testPATCHBlogPost()
    {
        $createdBlogPost = $this->createBlogPost(
            [
                'title' => 'Super Awsome Blog Post',
                'category' => 'Awsome Category',
                'summary' => 'Lorem Ipsum Sit Amet',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum',
                'isPublished' => true,
                'publishedAt' => (new \DateTime('-1 month')),
            ]
        );

        $data = [
            'title' => 'Ultra Awsome Blog Post',
        ];

        $response = $this->client->patch(
            '/api/v1.0/blog/'.$createdBlogPost->getId(),
            [
                'body' => json_encode(($data)),
                'headers' => $this->getAuthorizedHeaders('filanfisteku'),
            ]
        );

        $this->assertSame(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals($response, 'title', 'Ultra Awsome Blog Post');
        $this->asserter()->assertResponsePropertyEquals($response, 'summary', 'Lorem Ipsum Sit Amet');
    }

    public function testDELETEBlogPost()
    {
        $createdBlogPost = $this->createBlogPost(
            [
                'title' => 'Super Awsome Blog Post',
                'category' => 'Awsome Category',
                'summary' => 'Lorem Ipsum Sit Amet',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum',
                'isPublished' => true,
                'publishedAt' => (new \DateTime('-1 month')),
            ]
        );

        $response = $this->client->delete('/api/v1.0/blog/'.$createdBlogPost->getId(), [
            'headers' => $this->getAuthorizedHeaders('filanfisteku'),
        ]);
        $this->assertSame(204, $response->getStatusCode());
    }

    public function testValidations()
    {
        $category = 'Awsome Category '.rand(1, 100);
        $summary = 'Lorem Ipsum Sit Amet';
        $content = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum';
        $isPublished = true;
        $publishedAt = (new \DateTime('-1 month'));

        $data = [
            'category' => $category,
            'summary' => $summary,
            'content' => $content,
            'isPublished' => $isPublished,
            'publishedAt' => $publishedAt->format('Y-m-d H:i:s'),
        ];

        $response = $this->client->post(
            '/api/v1.0/blog',
            [
                'body' => json_encode($data),
                'headers' => $this->getAuthorizedHeaders('filanfisteku'),
            ]
        );

        $this->assertSame('application/problem+json', $response->getHeader('Content-Type')[0]);
        $this->assertSame(400, $response->getStatusCode());
        $this->asserter()->assertResponsePropertiesExist(
            $response,
            [
                'type',
                'title',
                'errors',
            ]
        );
        $this->asserter()->assertResponsePropertyExists($response, 'errors.title');
        $this->asserter()->assertResponsePropertyEquals($response, 'errors.title[0]', 'Please enter a valid title');
        $this->asserter()->assertResponsePropertyDoesNotExist($response, 'errors.speciesCount');
        $this->asserter()->assertResponsePropertyDoesNotExist($response, 'errors.funFact');
        $this->asserter()->assertResponsePropertyDoesNotExist($response, 'errors.isPublished');
    }

    public function testInvalidJson()
    {
        //Invalid JSON is missing comma after "category" => "test"
        $invalidBody = <<<EOF
{
    "title" : "test",
    "category" => "test"
    "summary" => "Lorem Ipsum",
    "content" => "Lorem Ipsum",
    "isPublished" => 1,
    "publishedAt" => "2017-04-08 10:29:44"
}
EOF;
        $response = $this->client->post(
            '/api/v1.0/blog',
            [
                'body' => $invalidBody,
                'headers' => $this->getAuthorizedHeaders('filanfisteku'),
            ]
        );

        $this->assertSame(400, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyContains($response, 'type', 'invalid_body_format');
    }

    public function test404Exception()
    {
        $response = $this->client->get('/api/v1.0/blog/97108', [
            'headers' => $this->getAuthorizedHeaders('filanfisteku'),
        ]);
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('application/problem+json', $response->getHeader('Content-Type')[0]);
        $this->asserter()->assertResponsePropertyEquals($response, 'type', 'about:blank');
        $this->asserter()->assertResponsePropertyEquals($response, 'title', 'Not Found');
        $this->asserter()->assertResponsePropertyEquals($response, 'detail', 'No blog post found with id "97108"!!');
    }

    public function testRequiresAuthentication()
    {
        $subFamily = $this->createCategory('Awsome Category');
        $data = [
            'title' => 'Super Awsome Blog Post',
            'category' => $subFamily->getId(),
            'summary' => 'Lorem Ipsum',
            'content' => 'Lorem Ipsum',
            'isPublished' => 1,
            'publishedAt' => '2017-04-08 10:29:44',
        ];
        $response = $this->client->post(
            '/api/v1.0/blog',
            [
                'body' => json_encode($data),
            ]
        );

        $this->assertSame(401, $response->getStatusCode());
    }
}
