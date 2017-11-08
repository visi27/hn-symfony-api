<?php

/**
 * (c) Evis Bregu <evis.bregu@gmail.com>.
 */

namespace AppBundle\Security\TwoFactor;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface HelperInterface
{
    public function is2faActive(UserInterface $user);

    public function checkCode($authCode, $code);

    public function getSessionKey(TokenInterface $token);

    public function getAuthKey(UserInterface $user);
}
