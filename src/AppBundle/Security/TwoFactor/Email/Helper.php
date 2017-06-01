<?php

namespace AppBundle\Security\TwoFactor\Email;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class Helper
{
    /**
     * @var \Doctrine\ORM\EntityManager $em
     */
    private $em;

    /**
     * @var object $mailer
     */
    private $mailer;

    /**
     * Construct the helper service for mail authenticator
     * @param \Doctrine\ORM\EntityManager $em
     * @param object $mailer
     */
    public function __construct(EntityManager $em, $mailer)
    {
        $this->em = $em;
        $this->mailer = $mailer;
    }

    /**
     * Generate a new authentication code an send it to the user
     * @param \AppBundle\Entity\User $user
     */
    public function generateAndSend(User $user)
    {
        $code = mt_rand(1000, 9999);
        $user->setTwoFactorCode($code);
        $this->em->persist($user);
        $this->em->flush();
        $this->sendCode($user);
    }

    /**
     * Send email with code to user
     * @param \AppBundle\Entity\User $user
     */
    private function sendCode(User $user)
    {
        $message = new \Swift_Message();
        $message
            ->setTo($user->getEmail())
            ->setSubject("Acme Authentication Code")
            ->setFrom("security@acme.com")
            ->setBody($user->getTwoFactorCode())
        ;
        $this->mailer->send($message);
    }

    /**
     * Validates the code, which was entered by the user
     * @param \AppBundle\Entity\User $user
     * @param $code
     * @return bool
     */
    public function checkCode(User $user, $code)
    {
        return $user->getTwoFactorCode() == $code;
    }

    /**
     * Generates the attribute key for the session
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @return string
     */
    public function getSessionKey(TokenInterface $token)
    {
        return sprintf('two_factor_%s_%s', $token->getProviderKey(), $token->getUsername());
    }
}