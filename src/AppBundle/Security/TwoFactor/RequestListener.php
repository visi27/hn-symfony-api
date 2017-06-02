<?php

namespace AppBundle\Security\TwoFactor;

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
     * @var HelperInterface $helper
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
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router $router
     */
    protected $router;
    /**
     * @var HelperFactory
     */
    private $helperFactory;

    /**
     * Construct the listener
     * @param HelperFactory $helperFactory
     * @param \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface $securityContext
     * @param \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface $templating
     * @param \Symfony\Bundle\FrameworkBundle\Routing\Router $router
     * @internal param HelperInterface $helper
     */
    public function __construct(
        HelperFactory $helperFactory,
        TokenStorageInterface $securityContext,
        EngineInterface $templating,
        Router $router
    ) {
        $this->securityContext = $securityContext;
        $this->templating = $templating;
        $this->router = $router;
        $this->helperFactory = $helperFactory;
    }

    /**
     * Listen for request events
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
        $user = $token->getUser();
        $this->helper = $this->helperFactory->getHelper($user);

        $key = $this->helper->getSessionKey($this->securityContext->getToken());
        $request = $event->getRequest();
        /**
         * @var Session $session
         */
        $session = $event->getRequest()->getSession();

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
        $response = $this->templating->renderResponse('twofactor/2fa_code.html.twig');
        $event->setResponse($response);
    }
}