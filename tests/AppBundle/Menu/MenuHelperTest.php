<?php

/*
 *
 * (c) Evis Bregu <evis.bregu@gmail.com>
 *
 */

namespace Tests\AppBundle\Menu;

use AppBundle\Entity\Menu;
use AppBundle\Menu\MenuHelper;
use AppBundle\Test\DoctrineDependableTestCase;

class MenuHelperTest extends DoctrineDependableTestCase
{
    public function testMenuTransformation()
    {
        $menu1 = new Menu();
        $menu1->setNavHeader('')
            ->setIcon('glyphicon glyphicon-th-list')
            ->setName('Blog List')
            ->setLink('blog_list')
            ->setParentId(0)
            ->setSort(1)
            ->setStatus(true);

        $menu2 = new Menu();
        $menu2->setNavHeader('')
            ->setIcon('glyphicon glyphicon-lock')
            ->setName('Admin Area')
            ->setLink('admin_blog_list')
            ->setParentId(0)
            ->setSort(2)
            ->setStatus(true);

        $menu3 = new Menu();
        $menu3->setNavHeader('')
            ->setIcon('glyphicon glyphicon-user')
            ->setName('Dashboard')
            ->setLink('user_dashboard')
            ->setParentId(0)
            ->setSort(3)
            ->setStatus(true);

        $menu4 = new Menu();
        $menu4->setNavHeader('')
            ->setIcon('glyphicon glyphicon-user')
            ->setName('Dashboard Disabled')
            ->setLink('user_dashboard_inactive')
            ->setParentId(0)
            ->setSort(4)
            ->setStatus(false);

        $this->em->persist($menu1);
        $this->em->persist($menu3);
        $this->em->persist($menu2);
        $this->em->persist($menu4);
        $this->em->flush();

        $subMenu = new Menu();
        $subMenu->setNavHeader('')
            ->setIcon('glyphicon glyphicon-lock')
            ->setName('SubMenu')
            ->setLink('submenu')
            ->setParentId($menu1->getId())
            ->setSort(2)
            ->setStatus(true);

        $this->em->persist($subMenu);
        $this->em->flush();

        $menuObjects = $this->em->getRepository('AppBundle:Menu')->findBy(
            ['status' => true],
            ['parentId' => 'ASC', 'sort' => 'ASC']
        );

        $transformedMenuItems = (new MenuHelper($menuObjects))->getMenuTree();

        $this->assertCount(3, $transformedMenuItems);
        $this->assertArrayHasKey('icon', $transformedMenuItems[$menu3->getId()]);
        $this->assertTrue($transformedMenuItems[$menu1->getId()]['hasChildren']);
        $this->assertCount(1, $transformedMenuItems[$menu1->getId()]['childrens']);
    }
}
