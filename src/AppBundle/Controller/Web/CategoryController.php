<?php

namespace AppBundle\Controller\Web;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CategoryController extends Controller
{
    public function listCategoriesSidebarAction(){
        $em = $this->getDoctrine()->getManager();

        $categories = $em->getRepository('AppBundle:Category')->findAll();

        return $this->render(
            ':sidebar:_categories.html.twig',
            array('categories' => $categories)
        );
    }
}