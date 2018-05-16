<?php
/**
 * Created by Evis Bregu <evis.bregu@gmail.com>.
 * Date: 5/16/18
 * Time: 3:31 PM
 */

namespace AppBundle\Controller\Api\V1;


use AppBundle\Controller\Api\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends BaseController
{
    /**
     * @Route("/v2.0", name="api_v2.0_index")
     * @Method("GET")
     *
     *
     * @return Response
     */
    public function indexAction(){
        $response = $this->createApiResponse(["WELCOME"], 200);

        return $response;
    }
}