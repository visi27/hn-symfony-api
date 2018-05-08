<?php

/**
 * (c) Evis Bregu <evis.bregu@gmail.com>.
 */

namespace AppBundle\Controller\Api;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

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
                'id' => $user->getId(),
                'exp' => time() + 3600, // 1 hour expiration
            ]
        );

        return new JsonResponse(['token' => $token]);
    }

    /**
     * @Route("/api/user", name="api_user_details")
     * @Method("GET")
     *
     * @param Request $request
     *
     * @param JWTEncoderInterface $JWTEncoder
     *
     * @return JsonResponse
     * @throws \Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException
     */
    public function getUserFromToken(Request $request, JWTEncoderInterface $JWTEncoder) {
        $extractor = new AuthorizationHeaderTokenExtractor(
            'Bearer',
            'Authorization'
        );
        $token = $extractor->extract($request);

        try {
            $data = $JWTEncoder->decode($token);
        } catch (JWTDecodeFailureException $e) {
            // if you want to, use can use $e->getReason() to find out which of the 3 possible things went wrong
            // and tweak the message accordingly
            // https://github.com/lexik/LexikJWTAuthenticationBundle/blob/05e15967f4dab94c8a75b275692d928a2fbf6d18/Exception/JWTDecodeFailureException.php
            throw new CustomUserMessageAuthenticationException('Invalid Token');
        }

        return new JsonResponse($data);
    }
}
