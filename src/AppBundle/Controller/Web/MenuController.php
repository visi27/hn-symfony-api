<?php

/**
 * (c) Evis Bregu <evis.bregu@gmail.com>.
 */

namespace AppBundle\Controller\Web;

use AppBundle\Menu\MenuHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MenuController extends Controller
{
    public function renderMenuAction()
    {
        $em = $this->getDoctrine()->getManager();
        $menuObjects = $em->getRepository('AppBundle:Menu')->findBy(
            ['status' => true],
            ['parentId' => 'ASC', 'sort' => 'ASC']
        );

        $menuHelper = new MenuHelper($menuObjects);

        return $this->render('menu/menu.html.twig', ['menuItems' => $menuHelper->getMenuTree()]);
    }
}
