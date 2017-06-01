<?php
namespace AppBundle\Security\TwoFactor\Email;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class RequestListener
{
    /**
     * @var \AppBundle\Security\TwoFactor\Email\Helper $helper
     */
    protected $helper;

    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface  $securityContext
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
     * Construct the listener
     * @param \AppBundle\Security\TwoFactor\Email\Helper $helper
     * @param \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface  $securityContext
     * @param \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface $templating
     * @param \Symfony\Bundle\FrameworkBundle\Routing\Router $router
     */
    public function __construct(Helper $helper, TokenStorageInterface $securityContext, EngineInterface $templating, Router $router)
    {
        $this->helper = $helper;
        $this->securityContext = $securityContext;
        $this->templating = $templating;
        $this->router = $router;
    }

    /**
     * Listen for request events
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     */
    public function onCoreRequest(GetResponseEvent $event)
    {
        $token = $this->securityContext->getToken();
        if (!$token)
        {
            return;
        }
        if (!$token instanceof AbstractToken)
        {
            return;
        }

        if($token instanceof AnonymousToken){
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
        if (!$session->has($key))
        {
            return;
        }
        if ($session->get($key) === true)
        {
            return;
        }

        if ($request->getMethod() == 'POST')
        {
            //Check the authentication code
            if ($this->helper->checkCode($user, $request->get('_auth_code')) == true)
            {
                //Flag authentication complete
                $session->set($key, true);

                //Redirect to user's dashboard
                $redirect = new RedirectResponse($this->router->generate("admin_blog_list"));
                $event->setResponse($redirect);
                return;
            }
            else
            {
                $session->getFlashBag()->set("error", "The verification code is not valid.");
            }
        }

        //Force authentication code dialog
        $response = $this->templating->renderResponse('twofactor/email.html.twig');
        $event->setResponse($response);
    }
}