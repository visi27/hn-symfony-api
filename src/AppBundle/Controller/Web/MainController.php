<?php

namespace AppBundle\Controller\Web;


use AppBundle\Security\TwoFactor\Email\Helper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller
{
    public function homepageAction()
    {
        $this->addFlash('success', 'System Ready');
        return $this->render("main/homepage.html.twig");
    }
}