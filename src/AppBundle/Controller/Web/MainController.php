<?php

/*
 *
 * (c) Evis Bregu <evis.bregu@gmail.com>
 *
 */

namespace AppBundle\Controller\Web;

use Highlight\Highlighter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller
{
    public function homepageAction()
    {
        $hl = new Highlighter();
        $highlighted = $hl->highlight("markdown", file_get_contents('../README.md'));

        $content = $this->stripFirstLine($this->stripFirstLine($highlighted->value));

        return $this->render('main/homepage.html.twig', ["content" => $content, "language"=>$highlighted->language]);
    }

    private function stripFirstLine($text)
    {
        return substr( $text, strpos($text, "\n")+1 );
    }
}
