<?php
namespace AppBundle\Security\TwoFactor\Email;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use AppBundle\Entity\User;

class InteractiveLoginListener
{
    /**
     * @var \AppBundle\Security\TwoFactor\Email\Helper $helper
     */
    private $helper;

    /**
     * Construct a listener, which is handling successful authentication
     * @param \AppBundle\Security\TwoFactor\Email\Helper $helper
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
        if (!$event->getAuthenticationToken() instanceof AbstractToken)
        {
            return;
        }

        //Check if user can do two-factor authentication
        $token = $event->getAuthenticationToken();
        $user = $token->getUser();
        if (!$user instanceof User)
        {
            return;
        }
        if (!$user->getTwoFactorAuthentication())
        {
            return;
        }

        //Set flag in the session
        $event->getRequest()->getSession()->set($this->helper->getSessionKey($token), null);

        //Generate and send a new security code
        $this->helper->generateAndSend($user);
    }

}