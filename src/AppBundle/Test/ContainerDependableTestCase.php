<?php

namespace AppBundle\Test;


use AppBundle\Entity\Menu;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;

class ContainerDependableTestCase extends KernelTestCase
{
    /**
     * @var Container
     */
    protected $_container;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        self::bootKernel();
        $this->_container = static::$kernel->getContainer();
    }

    protected function get($service)
    {
        return $this->_container->get($service);
    }

    public function createDummyMenuEntries($count = 20)
    {
        for ($i = 0; $i < $count; $i++) {
            $menu = new Menu();
            $menu->setNavHeader("")
                ->setIcon("glyphicon glyphicon-th-list")
                ->setName("Menu".$i)
                ->setLink("menu_".$i)
                ->setParentId(0)
                ->setSort(1)
                ->setStatus(true);
            $this->_container->get("doctrine")->getManager()->persist($menu);
        }
        $this->_container->get("doctrine")->getManager()->flush();
    }
}