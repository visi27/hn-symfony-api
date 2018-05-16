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







// Create the tcp client
$client = new swoole_client(SWOOLE_SOCK_TCP);

// Connect to the tcp server
if(!$client->connect('127.0.0.1', 9501, 0.5))
{
    die("connect failed");
}

// Send data to the tcp server
if(!$client->send("Hello World"))
{
    die("send failed");
}

// Receive data from the tcp server
$data = $client->recv();
if(!$data)
{
    die("recv failed");
}
echo $data;

// Close the connection
$client->close();