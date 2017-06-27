<?php

namespace Tests\AppBundle\Api;

use AppBundle\Api\ApiProblem;
use AppBundle\Api\ApiProblemException;

class ApiProblemExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testApiProblemException(){
        $firstException = new \Exception("General Error");
        $apiProblem = new ApiProblem(500);
        $apiProblemException = new ApiProblemException($apiProblem, $firstException);

        $this->assertEquals(500, $apiProblemException->getStatusCode());
        $this->assertEquals(500, $apiProblemException->getApiProblem()->getStatusCode());
        $this->assertInstanceOf("\Exception", $apiProblemException->getPrevious());
    }
}
