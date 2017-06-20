<?php

namespace AppBundle\Test;


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
}