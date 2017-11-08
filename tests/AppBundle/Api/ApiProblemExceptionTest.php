<?php

/**
 * (c) Evis Bregu <evis.bregu@gmail.com>.
 */

namespace Tests\AppBundle\Api;

use AppBundle\Api\ApiProblem;
use AppBundle\Api\ApiProblemException;
use PHPUnit\Framework\TestCase;

class ApiProblemExceptionTest extends TestCase
{
    public function testApiProblemException()
    {
        $firstException = new \Exception('General Error');
        $apiProblem = new ApiProblem(500);
        $apiProblemException = new ApiProblemException($apiProblem, $firstException);

        $this->assertSame(500, $apiProblemException->getStatusCode());
        $this->assertSame(500, $apiProblemException->getApiProblem()->getStatusCode());
        $this->assertInstanceOf("\Exception", $apiProblemException->getPrevious());
    }
}
