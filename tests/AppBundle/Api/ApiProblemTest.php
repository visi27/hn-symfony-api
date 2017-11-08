<?php

/**
 * (c) Evis Bregu <evis.bregu@gmail.com>.
 */

namespace Tests\AppBundle\Api;

use AppBundle\Api\ApiProblem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class ApiProblemTest extends TestCase
{
    public function testApiProblem()
    {
        $notFoundApiProblem = new ApiProblem(404);

        $expectedTitle = isset(Response::$statusTexts[404]) ? Response::$statusTexts[404] : 'Unknown status code :(';
        $this->assertSame($expectedTitle, $notFoundApiProblem->getTitle());

        $errorApiProblem = new ApiProblem(500, 'validation_error');
        $errorApiProblem->set('extra_field', 'extra_value');

        $expectedTitle = 'There was a validation error';
        $this->assertSame($expectedTitle, $errorApiProblem->getTitle());
        $this->assertJson($errorApiProblem->toJSON());
        $this->assertInternalType('array', $errorApiProblem->toArray());
        $this->assertArrayHasKey('extra_field', $errorApiProblem->toArray());
        $this->assertSame('extra_value', $errorApiProblem->toArray()['extra_field']);
        $this->assertSame(500, $errorApiProblem->getStatusCode());

        $this->expectException(\InvalidArgumentException::class);
        $errorApiProblem = new ApiProblem(500, 'inexistent title');
    }
}
