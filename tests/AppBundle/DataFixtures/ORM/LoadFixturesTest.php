<?php
/**
 * Created by PhpStorm.
 * User: evis
 * Date: 6/21/17
 * Time: 2:37 PM
 */

namespace Tests\AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\ORM\LoadFixtures;
use AppBundle\Test\ContainerDependableTestCase;

class LoadFixturesTest extends ContainerDependableTestCase
{
    public function testFixtureGetCustomCategory()
    {
        $fixtureLoader = new LoadFixtures();
        $objects = $fixtureLoader->load($this->_container->get('doctrine')->getManager());

        $this->assertEquals(28, count($objects));
        $this->assertInstanceOf("AppBundle\Entity\User", $objects["user_1"]);

        $categories = [
            'News',
            'How To',
            'Community',
            'Fun Facts',
            'Trivia',
            'Sport',
            'Q&A',
            'FAQ',
            'Technology',
        ];

        for ($i = 0; $i <= count($categories); $i++) {
            $this->assertContains($fixtureLoader->category(), $categories);
        }
    }
}
