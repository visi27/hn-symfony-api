<?php

namespace AppBundle\Security\TwoFactor\Google;

use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Guard\Token\GuardTokenInterface;

class RequestListener
{

    /**
     * @var \AppBundle\Security\TwoFactor\Google\Helper $helper
     */
    protected $helper;

    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface $securityContext
     */
    protected $securityContext;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface $templating
     */
    protected $templating;
    /**
     * @var Router
     */
    private $router;

    /**
     * @param \AppBundle\Security\TwoFactor\Google\Helper $helper
     * @param \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface $securityContext
     * @param \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface $templating
     * @param Router $router
     */
    public function __construct(
        Helper $helper,
        TokenStorageInterface $securityContext,
        EngineInterface $templating,
        Router $router
    ) {
        $this->helper = $helper;
        $this->securityContext = $securityContext;
        $this->templating = $templating;
        $this->router = $router;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     */
    public function onCoreRequest(GetResponseEvent $event)
    {
        $token = $this->securityContext->getToken();

        if (!$token) {
            return;
        }

        if ((!$token instanceof GuardTokenInterface) || ($token instanceof JWTUserToken)) {
            return;
        }

        $key = $this->helper->getSessionKey($this->securityContext->getToken());
        $request = $event->getRequest();
        /**
         * @var Session $session
         */
        $session = $event->getRequest()->getSession();
        $user = $this->securityContext->getToken()->getUser();

        //Check if user has to do two-factor authentication
        if (!$session->has($key)) {
            return;
        }
        if ($session->get($key) === true) {
            return;
        }

        if ($request->getMethod() == 'POST') {
            //Check the authentication code
            if ($this->helper->checkCode($user, $request->get('_auth_code')) == true) {
                //Flag authentication complete
                $session->set($key, true);

                //Redirect to user's dashboard
                $redirect = new RedirectResponse($this->router->generate("admin_blog_list"));
                $event->setResponse($redirect);

                return;
            } else {
                $session->getFlashBag()->set("error", "The verification code is not valid.");
            }
        }

        //Force authentication code dialog
        $response = $this->templating->renderResponse('twofactor/google.html.twig');
        $event->setResponse($response);
    }

}