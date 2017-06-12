<?php

namespace AppBundle\Security\TwoFactor;

use AppBundle\Entity\User;
use AppBundle\Security\TwoFactor\Email\Helper;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;
use Symfony\Component\Security\Guard\Token\GuardTokenInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class InteractiveLoginListener
{
    /**
     * @var HelperInterface $helper
     */
    private $helper;
    /**
     * @var HelperFactory
     */
    private $helperFactory;

    /**
     * Construct a listener, which is handling successful authentication
     * @param HelperFactory $helperFactory
     * @internal param HelperInterface $helper
     */
    public function __construct(HelperFactory $helperFactory)
    {
        $this->helperFactory = $helperFactory;
    }

    /**
     * Listen for successful login events
     * @param \Symfony\Component\Security\Http\Event\InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        if (
            (!$event->getAuthenticationToken() instanceof GuardTokenInterface)
            || ($event->getAuthenticationToken() instanceof JWTUserToken)
        ) {
            return;
        }

        //Check if user can do two-factor authentication
        //$ip = $event->getRequest()->getClientIp();
        $token = $event->getAuthenticationToken();
        $user = $token->getUser();
        $this->helper = $this->helperFactory->getHelper($user);
        if (!$user instanceof User) {
            return;
        }
        if (!$this->helper->is2faActive($user)) {
            return;
        }

        //Set flag in the session
        $event->getRequest()->getSession()->set($this->helper->getSessionKey($token), null);

        if ($this->helper instanceof Helper) {
            $this->helper->generateAndSend($user);
        }

    }
}