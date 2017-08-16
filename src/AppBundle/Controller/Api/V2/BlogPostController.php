<?php

/*
 *
 * (c) Evis Bregu <evis.bregu@gmail.com>
 *
 */

namespace AppBundle\Controller\Api\V2;

use AppBundle\Controller\Api\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class BlogPostController.
 *
 * @Security("is_granted('ROLE_USER')")
 */
class BlogPostController extends BaseController
{
    /**
     * @Route("/api/v2.0/blog", name="api_v2.0_list_blog_posts")
     * @Method("GET")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function listAction(Request $request)
    {

        $response = $this->createApiResponse(["data" => "Showcasing api versioning. V2.0 HERE! YAY!"], 200);

        return $response;
    }
}
