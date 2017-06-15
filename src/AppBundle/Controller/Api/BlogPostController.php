<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\BlogPost;
use AppBundle\Entity\Category;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BlogPostController
 * @package AppBundle\Controller\Api
 * @Security("is_granted('ROLE_USER')")
 */
class BlogPostController extends BaseController
{

    /**
     * @Route("/api/blog", name="api_create_blog_post")
     * @Method("POST")
     *
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $em = $this->getDoctrine()->getManager();

        $blogPost = new BlogPost();
        $form = $this->createForm("AppBundle\Form\BlogPostFormType", $blogPost);
        $this->processForm($request, $form);
        if (!$form->isValid()) {
            return $this->throwApiProblemValidationException($form);
        }

        $category = $em->getRepository("AppBundle:Category")->findOneBy(['id' => $data["category"]]);

        if (!$category) {
            $category = new Category();
            $category->setName($data["category"]);

            $em->persist($category);
        }
        $blogPost->setCategory($category);
        $blogPost->setUser($this->getUser());

        $em->persist($blogPost);

        $em->flush();

        $response = $this->createApiResponse($blogPost, 201);

        $blogPostUrl = $this->generateUrl(
            "api_show_blog_post",
            ['id' => $blogPost->getId()]
        );

        $response->headers->set('Location', $blogPostUrl);

        return $response;
    }

    /**
     * @Route("/api/blog/{id}", name = "api_show_blog_post")
     * @Method("GET")
     *
     * @param integer $id
     * @return Response
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $blogPost = $em->getRepository("AppBundle:BlogPost")->findOneBy(['id' => $id]);

        if (!$blogPost) {
            throw $this->createNotFoundException(
                (
                    'No blog post found with id "'.$id.'"!!'
                )
            );
        }

        $response = $this->createApiResponse($blogPost);

        return $response;
    }

    /**
     * @Route("/api/blog", name="api_list_blog_posts")
     * @Method("GET")
     *
     * @param Request $request
     * @return Response
     */
    public function listAction(Request $request)
    {
        $filter = $request->query->get('filter');

        $qb = $this->getDoctrine()
            ->getRepository('AppBundle:BlogPost')
            ->findAllQueryBuilder($filter);

        $paginatedCollection = $this->get('pagination_factory')
            ->createCollection($qb, $request, 'api_list_blog_posts');

        $response = $this->createApiResponse($paginatedCollection, 200);

        return $response;
    }

    /**
     * @Route("/api/blog/{id}", name="api_update_blog_post")
     * @Method({"PUT", "PATCH"})
     *
     * @param Request $request
     * @param integer $id
     *
     * @return Response
     */
    public function updateAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $blogPost = $em->getRepository("AppBundle:BlogPost")->findOneBy(['id' => $id]);

        if (!$blogPost) {
            throw $this->createNotFoundException(
                sprintf(
                    'No blog post found with id "%s"',
                    $id
                )
            );
        }

        $form = $this->createForm("AppBundle\Form\UpdateBlogPostFormType", $blogPost);
        $this->processForm($request, $form);

        if (!$form->isValid()) {
            $this->get("logger")->error($form->getErrors());

            return $this->throwApiProblemValidationException($form);
        }

        $response = $this->createApiResponse($blogPost);

        return $response;
    }

    /**
     * @Route("/api/blog/{id}", name="api_delete_blog_post")
     * @Method("DELETE")
     *
     * @param integer $id
     * @return Response
     * @internal param Request $request
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $blogPost = $em->getRepository("AppBundle:BlogPost")->findOneBy(['id' => $id]);
        if ($blogPost) {
            $em->remove($blogPost);
            $em->flush();
        }

        $response = $this->createApiResponse("OK", 204);

        return $response;
    }
}
