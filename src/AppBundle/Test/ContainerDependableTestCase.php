<?php

/*
 *
 * (c) Evis Bregu <evis.bregu@gmail.com>
 *
 */

namespace AppBundle\Test;

use AppBundle\Entity\Menu;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;

class ContainerDependableTestCase extends KernelTestCase
{
    /**
     * @var Container
     */
    protected $_container;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        self::bootKernel();
        $this->_container = static::$kernel->getContainer();

        $this->purgeDatabase();
    }

    protected function get($service)
    {
        return $this->_container->get($service);
    }

    private function purgeDatabase()
    {
        $purger = new ORMPurger($this->get('doctrine')->getManager());
        $purger->purge();
    }

    public function createDummyMenuEntries($count = 20)
    {
        for ($i = 0; $i < $count; ++$i) {
            $menu = new Menu();
            $menu->setNavHeader('')
                ->setIcon('glyphicon glyphicon-th-list')
                ->setName('Menu'.$i)
                ->setLink('menu_'.$i)
                ->setParentId(0)
                ->setSort(1)
                ->setStatus(true);
            $this->_container->get('doctrine')->getManager()->persist($menu);
        }
        $this->_container->get('doctrine')->getManager()->flush();
    }
}
