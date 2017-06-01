<?php

namespace AppBundle\Security\TwoFactor\Google;

use AppBundle\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;
use Symfony\Component\Security\Guard\Token\GuardTokenInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class InteractiveLoginListener
{
    /**
     * @var \AppBundle\Security\TwoFactor\Google\Helper $helper
     */
    private $helper;

    /**
     * @param \AppBundle\Security\TwoFactor\Google\Helper $helper
     */
    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
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
        $ip = $event->getRequest()->getClientIp();
        $token = $event->getAuthenticationToken();
        $user = $token->getUser();
        if (!$user instanceof User) {
            return;
        }
        if (!$user->getGoogleAuthenticatorCode()) {
            return;
        }

        //Set flag in the session
        $event->getRequest()->getSession()->set($this->helper->getSessionKey($token), null);
    }

}