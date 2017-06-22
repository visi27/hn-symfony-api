<?php

namespace Tests\AppBundle\Repository;

use AppBundle\Entity\Category;
use AppBundle\Test\DoctrineDependableTestCase;

class CategoryRepositoryTest extends DoctrineDependableTestCase
{
    public function testCategoryRepository()
    {
        $this->createCategories();

        $categories = $this->em->getRepository("AppBundle:Category")->findAllQueryBuilder()->getQuery()->execute();

        $this->assertInternalType("array", $categories);
        $this->assertInstanceOf("AppBundle\Entity\Category", $categories[0]);
        $this->assertEquals(8, count($categories));
        $this->assertEquals("f", $categories[1]);

        $categories = $this->em->getRepository("AppBundle:Category")->findAllQueryBuilder("a")->getQuery()->execute();
        $this->assertInternalType("array", $categories);
        $this->assertInstanceOf("AppBundle\Entity\Category", $categories[0]);
        $this->assertEquals(2, count($categories));
        $this->assertEquals("ax", $categories[1]);

        $categories = $this->em->getRepository("AppBundle:Category")->createAlphabeticalQueryBuilder()->getQuery()->execute();

        $this->assertInternalType("array", $categories);
        $this->assertInstanceOf("AppBundle\Entity\Category", $categories[0]);
        $this->assertEquals(8, count($categories));
        $this->assertEquals("ax", $categories[1]);

    }

    private function createCategories()
    {
        $names = ['a', 'f', 'ax', 'b', 'z', 'c', 'd', 'e'];
        foreach ($names as $name) {
            $category = new Category();
            $category->setName($name);
            $this->em->persist($category);
        }
        $this->em->flush();
    }
}
