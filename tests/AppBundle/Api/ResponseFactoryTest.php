<?php

/**
 * (c) Evis Bregu <evis.bregu@gmail.com>.
 */

namespace Tests\AppBundle\Api;

use AppBundle\Api\ApiProblem;
use AppBundle\Api\ResponseFactory;
use PHPUnit\Framework\TestCase;

class ResponseFactoryTest extends TestCase
{
    public function testResponseFactory()
    {
        $notFoundApiProblem = new ApiProblem(500, 'validation_error');
        $responseFactory = new ResponseFactory();
        $response = $responseFactory->createResponse($notFoundApiProblem);

        $this->assertSame(500, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }
}
