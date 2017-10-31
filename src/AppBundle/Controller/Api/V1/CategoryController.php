<?php

/*
 *
 * (c) Evis Bregu <evis.bregu@gmail.com>
 *
 */

namespace AppBundle\Controller\Api\V1;

use AppBundle\Controller\Api\BaseController;
use AppBundle\Entity\Category;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends BaseController
{
    /**
     * @Route("/api/v1.0/category/{id}", name = "api_v1.0_show_category")
     * @Method("GET")
     *
     * @param int $id
     *
     * @return Response
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('AppBundle:Category')->findOneBy(['id' => $id]);

        if (!$category) {
            throw $this->createNotFoundException(
                (
                    'No category found with id "'.$id.'"!!'
                )
            );
        }

        $response = $this->createApiResponse($category);

        return $response;
    }

    /**
     * @Route("/api/v1.0/category", name="api_v1.0_list_categories")
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
            ->getRepository('AppBundle:Category')
            ->findAllQueryBuilder($filter);

        $paginatedCollection = $this->get('AppBundle\Pagination\PaginationFactory')
            ->createCollection($qb, $request, 'api_v1.0_list_categories');

        $response = $this->createApiResponse($paginatedCollection, 200);

        return $response;
    }

    /**
     * @Route("/api/v1.0/category/{id}/blog", name="api_v1.0_list_blog_posts_by_category")
     * @Method("GET")
     *
     * @param Category $category
     * @param Request  $request
     *
     * @return Response
     *
     * @internal param $id
     */
    public function listBlogPostsAction(Category $category, Request $request)
    {
        $qb = $this->getDoctrine()
            ->getRepository('AppBundle:BlogPost')
            ->findAllByCategoryQueryBuilder($category);

        $paginatedCollection = $this->get('AppBundle\Pagination\PaginationFactory')
            ->createCollection($qb, $request, 'api_v1.0_list_blog_posts_by_category', ['id' => $category]);

        $response = $this->createApiResponse($paginatedCollection, 200);

        return $response;
    }
}
