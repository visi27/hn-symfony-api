<?php

/*
 *
 * (c) Evis Bregu <evis.bregu@gmail.com>
 *
 */

namespace AppBundle\Controller\Web;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BlogPostController.
 */
class BlogPostController extends Controller
{
    /**
     * @Route("/blog", name="blog_list")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

        $blogPosts = $em->getRepository('AppBundle:BlogPost')->findAllPublishedOrderedByPublishedDate();

        return $this->render('blog/list.html.twig', ['blogPosts' => $blogPosts]);
    }

    /**
     * @Route("/blog/{postId}", name="blog_post_show")
     *
     * @param $postId
     *
     * @return Response
     */
    public function showAction($postId)
    {
        $em = $this->getDoctrine()->getManager();
        $blogPost = $em->getRepository('AppBundle:BlogPost')->findOneBy(['id' => $postId]);

        if (!$blogPost) {
            throw $this->createNotFoundException('blog post not found');
        }

        return $this->render(
            'blog/show.html.twig',
            [
                'blog' => $blogPost,
            ]
        );
    }
}
