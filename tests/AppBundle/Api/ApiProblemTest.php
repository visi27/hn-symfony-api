<?php
/**
 * Created by PhpStorm.
 * User: evis
 * Date: 6/23/17
 * Time: 4:35 PM
 */

namespace Tests\AppBundle\Api;

use AppBundle\Api\ApiProblem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class ApiProblemTest extends TestCase
{
    public function testApiProblem(){
        $notFoundApiProblem = new ApiProblem(404);

        $expectedTitle = isset(Response::$statusTexts[404]) ? Response::$statusTexts[404] : 'Unknown status code :(';
        $this->assertEquals($expectedTitle, $notFoundApiProblem->getTitle());



        $errorApiProblem = new ApiProblem(500, "validation_error");
        $errorApiProblem->set("extra_field", "extra_value");

        $expectedTitle = "There was a validation error";
        $this->assertEquals($expectedTitle, $errorApiProblem->getTitle());
        $this->assertJson($errorApiProblem->toJSON());
        $this->assertInternalType("array", $errorApiProblem->toArray());
        $this->assertArrayHasKey("extra_field", $errorApiProblem->toArray());
        $this->assertEquals("extra_value", $errorApiProblem->toArray()["extra_field"]);
        $this->assertEquals(500, $errorApiProblem->getStatusCode());

        $this->expectException(\InvalidArgumentException::class);
        $errorApiProblem = new ApiProblem(500, "inexistent title");

    }
}
