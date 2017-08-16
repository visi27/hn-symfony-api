<?php

/*
 *
 * (c) Evis Bregu <evis.bregu@gmail.com>
 *
 */

namespace AppBundle\Controller\Api\V1;

use AppBundle\Controller\Api\BaseController;
use AppBundle\Entity\BlogPost;
use AppBundle\Entity\Category;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;

/**
 * Class BlogPostController.
 *
 * @Security("is_granted('ROLE_USER')")
 */
class BlogPostController extends BaseController
{
    /**
     * @Route("/api/v1.0/blog", name="api_v1.0_create_blog_post")
     * @Method("POST")
     *
     * consumes={"application/json"},
     * produces={"application/json"},
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="BlogPost object that needs to be added",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/BlogPost"),
     * ),
     *
     * @SWG\Response(
     *     response=200,
     *     description="Creates a new Blog Post",
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=BlogPost::class, groups={"full"})
     *     )
     * )
     * @SWG\Tag(name="Blog Posts")
     *
     * @param Request $request
     *
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

        $category = $em->getRepository('AppBundle:Category')->findOneBy(['id' => $data['category']]);

        if (!$category) {
            $category = new Category();
            $category->setName($data['category']);

            $em->persist($category);
        }
        $blogPost->setCategory($category);
        $blogPost->setUser($this->getUser());

        $em->persist($blogPost);

        $em->flush();

        $response = $this->createApiResponse($blogPost, 201);

        $blogPostUrl = $this->generateUrl(
            'api_v1.0_show_blog_post',
            ['id' => $blogPost->getId()]
        );

        $response->headers->set('Location', $blogPostUrl);

        return $response;
    }

    /**
     * @Route("/api/v1.0/blog/{id}", name = "api_v1.0_show_blog_post")
     * @Method("GET")
     *
     * @param int $id
     *
     * @return Response
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $blogPost = $em->getRepository('AppBundle:BlogPost')->findOneBy(['id' => $id]);

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
     * @Route("/api/v1.0/blog", name="api_v1.0_list_blog_posts")
     * @Method("GET")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function listAction(Request $request)
    {
        $filter = $request->query->get('filter');

        $qb = $this->getDoctrine()
            ->getRepository('AppBundle:BlogPost')
            ->findAllQueryBuilder($filter);

        $paginatedCollection = $this->get('pagination_factory')
            ->createCollection($qb, $request, 'api_v1.0_list_blog_posts');

        $response = $this->createApiResponse($paginatedCollection, 200);

        return $response;
    }

    /**
     * @Route("/api/v1.0/blog/{id}", name="api_v1.0_update_blog_post")
     * @Method({"PUT", "PATCH"})
     *
     * @param Request $request
     * @param int     $id
     *
     * @return Response
     */
    public function updateAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $blogPost = $em->getRepository('AppBundle:BlogPost')->findOneBy(['id' => $id]);

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
            $this->get('logger')->error($form->getErrors());

            return $this->throwApiProblemValidationException($form);
        }

        $response = $this->createApiResponse($blogPost);

        return $response;
    }

    /**
     * @Route("/api/v1.0/blog/{id}", name="api_v1.0_delete_blog_post")
     * @Method("DELETE")
     *
     * @param int $id
     *
     * @return Response
     *
     * @internal param Request $request
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $blogPost = $em->getRepository('AppBundle:BlogPost')->findOneBy(['id' => $id]);
        if ($blogPost) {
            $em->remove($blogPost);
            $em->flush();
        }

        $response = $this->createApiResponse('OK', 204);

        return $response;
    }
}
