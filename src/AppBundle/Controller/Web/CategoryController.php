<?php

/*
 *
 * (c) Evis Bregu <evis.bregu@gmail.com>
 *
 */

namespace AppBundle\Controller\Web;

use AppBundle\Entity\Category;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CategoryController extends Controller
{
    public function listCategoriesSidebarAction()
    {
        $em = $this->getDoctrine()->getManager();

        $categories = $em->getRepository('AppBundle:Category')->findAll();

        return $this->render(
            ':sidebar:_categories.html.twig',
            ['categories' => $categories]
        );
    }

    /**
     * @Route("/category/{id}", name="category_list")
     * @param Category $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Category $id)
    {
        $em = $this->getDoctrine()->getManager();

        $blogPosts = $em->getRepository('AppBundle:BlogPost')->findAllByCategoryQueryBuilder($id)->getQuery()->execute();

        return $this->render('blog/list.html.twig', ['blogPosts' => $blogPosts]);
    }
}
