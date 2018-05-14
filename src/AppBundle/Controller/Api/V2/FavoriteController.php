<?php
/**
 * Created by Evis Bregu <evis.bregu@gmail.com>.
 * Date: 5/11/18
 * Time: 12:05 PM
 */

namespace AppBundle\Controller\Api\V2;

use AppBundle\Controller\Api\BaseController;
use AppBundle\Entity\Favorite;
use AppBundle\Entity\User;
use AppBundle\Pagination\PaginationFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FavoriteController.
 *
 * @Security("is_granted('ROLE_USER')")
 */
class FavoriteController extends BaseController
{
    /**
     * @Route("/api/v2.0/favorites/", name="api_v2.0_get_favorites")
     * @Method("GET")
     *
     * @param Request $request
     *
     * @param PaginationFactory $paginationFactory
     *
     * @return Response
     */
    public function listAction(Request $request, PaginationFactory $paginationFactory)
    {
        $user = $this->getUser();

        $qb = $this->getDoctrine()
            ->getRepository('AppBundle:Favorite')
            ->findAllByUserQueryBuilder($user);

        $paginatedCollection = $paginationFactory
            ->createCollection($qb, $request, 'api_v2.0_get_favorites', ['user' => $user->getId()]);

        $response = $this->createApiResponse($paginatedCollection, 200);

        return $response;
    }

    /**
     * @Route("/api/v2.0/favorites/", name="api_v2.0_add_favorite")
     * @Method("POST")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $favorite = new Favorite();
        $form = $this->createForm('AppBundle\Form\FavoriteFormType', $favorite);
        $this->processForm($request, $form);
        if (!$form->isValid()) {
            return $this->throwApiProblemValidationException($form);
        }

        $favorite->setUser($this->getUser());

        $em->persist($favorite);

        $em->flush();

        $response = $this->createApiResponse($favorite, 201);

        return $response;
    }

    /**
     * @Route("/api/v2.0/favorites/{objectID}", name="api_v2.0_delete_favorite")
     * @Method("DELETE")
     *
     * @param $objectID
     *
     * @return Response
     */
    public function deleteAction($objectID)
    {
        $em = $this->getDoctrine()->getManager();

        $favorite = $em->getRepository('AppBundle:Favorite')->findOneBy(['user' => $this->getUser(), 'objectID' => $objectID]);
        if ($favorite) {
            $em->remove($favorite);
            $em->flush();
        }

        $response = $this->createApiResponse('OK', 204);

        return $response;
    }
}