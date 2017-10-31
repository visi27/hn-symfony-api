<?php
/**
 * Created by Evis Bregu <evis.bregu@gmail.com>.
 * Date: 10/26/17
 * Time: 1:09 PM
 */

namespace Tests\AppBundle\Security;

use AppBundle\Entity\User;
use AppBundle\Security\JwtTokenAuthenticator;
use AppBundle\Test\ContainerDependableTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class JwtTokenAuthenticatorTest extends ContainerDependableTestCase
{
    public function testJwtAuthorization()
    {
        $jwtAuthenticator = new JwtTokenAuthenticator(
            $this->_container->get('lexik_jwt_authentication.encoder'),
            $this->_container->get('app.user_repository'),
            $this->_container->get('AppBundle\Api\ResponseFactory')
        );

        $user = new User();
        $user->setEmail("bar@foo.com")
            ->setPlainPassword('barfoo')
            ->setDefaultTwoFactorMethod('email')
            ->setTwoFactorCode('1234')
            ->setTwoFactorAuthentication(false)
            ->setRoles(['ROLE_USER']);

        $this->_container->get("doctrine")->getManager()->persist($user);
        $this->_container->get("doctrine")->getManager()->flush();

        $request = new Request();

        // Emulate start() method call. This method is called when authentication info is missing
        // from a request that requires it
        $noAuthHeaderResponse = $jwtAuthenticator->start($request);
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $noAuthHeaderResponse);
        $this->assertContains('Missing credentials', $noAuthHeaderResponse->getContent());

        $token = $this->_container->get('lexik_jwt_authentication.encoder')
            ->encode(
                [
                    'username' => $user->getUsername(),
                    'exp' => time() + 3600 // 1 hour expiration
                ]
            );

        $request->headers->set('Authorization', 'Bearer '.$token);

        // Get credentials (authorization token) from request headers
        $credentials = $jwtAuthenticator->getCredentials($request);
        // Assert that credentials are the same as the token that we sent in the request headers
        $this->assertEquals($token, $credentials);

        //Real user with good credentials
        $authUser = $jwtAuthenticator->getUser(
            $credentials,
            $this->_container->get('security.user.provider.concrete.our_users')
        );

        $this->assertNotNull($authUser);
        $this->assertInstanceOf('AppBundle\Entity\User', $authUser);
        $this->assertTrue($jwtAuthenticator->checkCredentials($credentials, $user));

        $authFailedResponse = $jwtAuthenticator->onAuthenticationFailure(
            new Request(),
            new AuthenticationException("Invalid Token")
        );
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $authFailedResponse);
        $this->assertEquals(401, $authFailedResponse->getStatusCode());

        $this->assertFalse($jwtAuthenticator->supportsRememberMe());

        //Emulate bad credentials. Keep at the end of test to allow other assertions to execute
        $badCredentials = "DummyText";
        $this->expectException(CustomUserMessageAuthenticationException::class);
        $jwtAuthenticator->getUser($badCredentials, $this->_container->get('security.user.provider.concrete.our_users'));
    }
}
