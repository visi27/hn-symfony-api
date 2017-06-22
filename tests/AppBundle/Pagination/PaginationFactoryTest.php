<?php

/*
 *
 * (c) Evis Bregu <evis.bregu@gmail.com>
 *
 */

namespace Tests\AppBundle\Pagination;

use AppBundle\Pagination\PaginatedCollection;
use AppBundle\Test\ContainerDependableTestCase;
use Symfony\Component\HttpFoundation\Request;

class PaginationFactoryTest extends ContainerDependableTestCase
{
    public function testPaginationFactory()
    {
        $paginationFactory = $this->_container->get('pagination_factory');

        $this->createDummyMenuEntries(25);
        $filter = 'Menu';
        $qb = $this->_container->get('doctrine')
            ->getRepository('AppBundle:Menu')
            ->findAllQueryBuilder($filter);

        $request = new Request([], [], [], [], [], [], json_encode(['menu' => 'test']));
        $request->query->set('page', 2);
        /**
         * @var PaginatedCollection
         */
        $paginationObject = $paginationFactory->createCollection($qb, $request, 'homepage');

        $this->assertInstanceOf("AppBundle\Pagination\PaginatedCollection", $paginationObject);

        //Prepare the reflection class to read private property _links
        $reflectionClass = new \ReflectionClass('AppBundle\Pagination\PaginatedCollection');
        $reflectionLinksProperty = $reflectionClass->getProperty('_links');
        $reflectionLinksProperty->setAccessible(true);

        $links = $reflectionLinksProperty->getValue($paginationObject);
        $this->assertInternalType('array', $links);
        $this->assertArrayHasKey('self', $links);
        $this->assertArrayHasKey('first', $links);
        $this->assertArrayHasKey('last', $links);
        $this->assertArrayHasKey('next', $links);
        $this->assertArrayHasKey('prev', $links);

        $request->query->set('page', 1);
        $paginationObject = $paginationFactory->createCollection($qb, $request, 'homepage');
        $links = $reflectionLinksProperty->getValue($paginationObject);
        $this->assertArrayNotHasKey('prev', $links);
    }
}
