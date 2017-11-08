<?php

/**
 * (c) Evis Bregu <evis.bregu@gmail.com>.
 */

namespace AppBundle\Controller\Web;

use AppBundle\Entity\BlogPost;
use AppBundle\Form\BlogPostFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BlogAdminController.
 *
 * @Security("is_granted('ROLE_ADMIN')")
 */
class BlogAdminController extends Controller
{
    /**
     * @Route("/admin/blog")
     */
    public function adminAction()
    {
    }

    /**
     * @Route("/admin/blog/list", name="admin_blog_list")
     */
    public function listAction()
    {
        //$this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em = $this->getDoctrine()->getManager();

        $blogPosts = $em->getRepository('AppBundle:BlogPost')->findAll();

        //dump($_SESSION);
        return $this->render('admin/blog_post/list.html.twig', ['blogPosts' => $blogPosts]);
    }

    /**
     * @Route("/admin/blog/new", name="admin_blog_new")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $form = $this->createForm(BlogPostFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var BlogPost
             */
            $blogPost = $form->getData();

            $blogPost->setUser($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($blogPost);
            $em->flush();

            $this->addFlash('success', 'Blog Post Created!');

            return $this->redirectToRoute('admin_blog_list');
        }

        return $this->render(
            'admin/blog_post/new.html.twig',
            [
                'blogPostForm' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/admin/blog/{id}/edit", name="admin_blog_post_edit")
     *
     * @param Request  $request
     * @param BlogPost $blogPost
     *
     * @return Response
     */
    public function editAction(Request $request, BlogPost $blogPost)
    {
        $form = $this->createForm(BlogPostFormType::class, $blogPost);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $blogPost = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($blogPost);
            $em->flush();

            $this->addFlash('success', 'Blog Post Updated!');

            return $this->redirectToRoute('admin_blog_list');
        }

        return $this->render(
            'admin/blog_post/edit.html.twig',
            [
                'blogPostForm' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/admin/blog/{id}/delete", name="admin_blog_post_delete")
     * @Method("POST")
     *
     * @param BlogPost $blogPost
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(BlogPost $blogPost)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($blogPost);
        $em->flush();
        $this->addFlash('success', 'The blog post was deleted');

        return $this->redirectToRoute('admin_blog_list');
    }
}
