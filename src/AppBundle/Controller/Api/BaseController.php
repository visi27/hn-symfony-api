<?php

/*
 *
 * (c) Evis Bregu <evis.bregu@gmail.com>
 *
 */

namespace AppBundle\Controller\Api;

use AppBundle\Api\ApiProblem;
use AppBundle\Api\ApiProblemException;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', ['name' => $name]);
    }

    /**
     * Use JMS Serialiser to serialize objects in our controllers.
     *
     * @param object|array $data
     * @param string       $format
     *
     * @return mixed|string
     */
    protected function serialize($data, $format = 'json')
    {
        $context = new SerializationContext();
        $context->setSerializeNull(true);

        $request = $this->get('request_stack')->getCurrentRequest();
        $groups = ['Default'];

        if ($request->query->get('deep')) {
            $groups[] = 'deep';
        }

        $context->setGroups($groups);

        return $this->get('jms_serializer')->serialize($data, $format, $context);
    }

    /**
     * Centralise Response creation for our controllers.
     *
     * @param $data
     * @param int $statusCode
     *
     * @return Response
     */
    protected function createApiResponse($data, $statusCode = 200)
    {
        $json = $this->serialize($data);

        return new Response(
            $json, $statusCode, [
                'Content-Type' => 'application/json',
            ]
        );
    }

    private function getErrorsFromForm(FormInterface $form)
    {
        $errors = [];
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }

        return $errors;
    }

    protected function throwApiProblemValidationException(FormInterface $form)
    {
        $errors = $this->getErrorsFromForm($form);

        $apiProblem = new ApiProblem(
            400,
            ApiProblem::TYPE_VALIDATION_ERROR
        );

        $apiProblem->set('errors', $errors);
        throw new ApiProblemException($apiProblem);
    }

    protected function processForm(Request $request, FormInterface $form)
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            $apiProblem = new ApiProblem(400, ApiProblem::TYPE_INVALID_REQUEST_BODY_FORMAT);
            throw new ApiProblemException($apiProblem);
        }

        $clearMissing = $request->getMethod() !== 'PATCH';
        $form->submit($data, $clearMissing);
    }
}
