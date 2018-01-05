<?php

/**
 * (c) Evis Bregu <evis.bregu@gmail.com>.
 */

namespace AppBundle\Controller\Api;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class TokenController extends BaseController
{
    /**
     * @Route("/api/tokens", name="api_token_new")
     * @Method("POST")
     *
     * @param Request $request
     *
     * @param UserPasswordEncoderInterface $encoder
     * @param JWTEncoderInterface $JWTEncoder
     *
     * @return JsonResponse
     * @throws \Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException
     */
    public function newTokenAction(
        Request $request,
        UserPasswordEncoderInterface $encoder,
        JWTEncoderInterface $JWTEncoder
    ) {
        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findOneBy(['email' => $request->getUser()]);

        if (!$user) {
            throw $this->createNotFoundException();
        }

        if (!$encoder->isPasswordValid($user, $request->getPassword())) {
            throw new BadCredentialsException();
        }

        $token = $JWTEncoder->encode(
            [
                'username' => $user->getUsername(),
                'exp' => time() + 3600, // 1 hour expiration
            ]
        );

        return new JsonResponse(['token' => $token]);
    }
}
